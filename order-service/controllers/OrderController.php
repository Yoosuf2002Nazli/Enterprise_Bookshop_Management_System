<?php
/**
 * OrderController
 * Handles order queries, creation, and status transitions.
 */
class OrderController {
    private OrderModel $model;

    public function __construct(OrderModel $model) {
        $this->model = $model;
    }

    /**
     * Fetch order log list.
     */
    public function handleGetOrders(array $get): void {
        $orders = $this->model->getAllOrders();
        jsonResponse([
            'status' => 'success',
            'data' => $orders
        ], 200);
    }

    /**
     * Handle order checkouts.
     */
    public function handleCreateOrder(array $post): void {
        $customer = trim($post['customer'] ?? '');
        $email = trim($post['email'] ?? '');
        $bookId = isset($post['book_id']) ? (int)$post['book_id'] : 0;
        $bookTitle = trim($post['book_title'] ?? '');
        $qty = isset($post['qty']) ? (int)$post['qty'] : 1;
        $price = isset($post['price']) ? (float)$post['price'] : 0.0;

        // 1. Validate required fields
        if (empty($customer) || empty($email) || $bookId <= 0 || empty($bookTitle) || $qty <= 0 || $price < 0) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid or incomplete order parameter fields.'
            ], 400);
            return;
        }

        // 2. Fetch ISBN from Catalog Service
        require_once __DIR__ . '/../../catalog-service/models/BookModel.php';
        $catalogConfig = require __DIR__ . '/../../catalog-service/config/config.php';
        try {
            $catalogPdo = createPdoConnection($catalogConfig);
            $bookModel = new BookModel($catalogPdo);
            $book = $bookModel->getBookById($bookId);
            if ($book === null) {
                jsonResponse([
                    'status' => 'error',
                    'message' => 'Specified book not found in catalog.'
                ], 404);
                return;
            }
            $isbn = $book['isbn'];
        } catch (Exception $e) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Catalog connection failure: ' . $e->getMessage()
            ], 500);
            return;
        }

        // 3. Generate Reference ID and compute total
        $orderRef = 'ORD-' . date('Y') . '-' . rand(10000, 99999);
        $total = $qty * $price;

        $items = [
            [
                'title' => $bookTitle,
                'qty' => $qty,
                'price' => $price
            ]
        ];

        // 4. Create Order inside order_db
        $orderCreated = $this->model->createOrder($orderRef, $customer, $email, $total, $items);
        if (!$orderCreated) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to log order into order database.'
            ], 500);
            return;
        }

        // 5. Call Inventory Service stock reduction
        require_once __DIR__ . '/../../inventory-service/models/InventoryModel.php';
        $inventoryConfig = require __DIR__ . '/../../inventory-service/config/config.php';
        try {
            $inventoryPdo = createPdoConnection($inventoryConfig);
            $inventoryModel = new InventoryModel($inventoryPdo);
            $stockReduced = $inventoryModel->reduceStock($isbn, $qty);
            
            if (!$stockReduced) {
                // If stock reduction fails, return error response. Do not silently continue.
                // We should also transition order status to 'Cancelled' to maintain consistency.
                $this->model->updateOrderStatus($orderRef, 'Cancelled');
                
                jsonResponse([
                    'status' => 'error',
                    'message' => 'Inventory reduction failed. Item is out of stock or does not exist.'
                ], 400);
                return;
            }
        } catch (Exception $e) {
            // Update status to Cancelled on connection/execution exception
            $this->model->updateOrderStatus($orderRef, 'Cancelled');
            
            jsonResponse([
                'status' => 'error',
                'message' => 'Inventory system connection failure: ' . $e->getMessage()
            ], 500);
            return;
        }

        // 6. Return success response
        jsonResponse([
            'status' => 'success',
            'message' => 'Order created successfully.',
            'order_ref' => $orderRef
        ], 201);
    }

    /**
     * Transition order status.
     */
    public function handleUpdateStatus(array $get): void {
        $orderRef = trim($get['id'] ?? '');
        $status = trim($get['status'] ?? '');
        if (empty($status) || $status === 'update_status') {
            $status = trim($get['action'] ?? '');
        }

        if (empty($orderRef) || empty($status)) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Order ID and Action/Status parameter are required.'
            ], 400);
            return;
        }

        $success = $this->model->updateOrderStatus($orderRef, $status);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Order status successfully transitioned.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to transition status. Invalid status value or order reference.'
            ], 400);
        }
    }
}
