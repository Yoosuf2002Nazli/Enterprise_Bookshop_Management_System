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
        $customer = trim($post['customer'] ?? $post['customer_name'] ?? '');
        $email = trim($post['email'] ?? $post['customer_email'] ?? '');
        $bookId = isset($post['book_id']) ? (int)$post['book_id'] : 0;
        $bookTitle = trim($post['book_title'] ?? $post['title'] ?? '');
        $qty = isset($post['qty']) ? (int)$post['qty'] : (isset($post['quantity']) ? (int)$post['quantity'] : 1);
        $price = isset($post['price']) ? (float)$post['price'] : (isset($post['total']) ? (float)$post['total'] : 0.0);

        // 1. Validate required fields
        if (empty($customer) || empty($email) || $bookId <= 0 || empty($bookTitle) || $qty <= 0 || $price < 0) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid or incomplete order parameter fields.'
            ], 400);
            return;
        }

        // 2. Fetch ISBN from Catalog Service (soft check)
        $isbn = '';
        $warning = null;
        require_once __DIR__ . '/../../catalog-service/models/BookModel.php';
        $catalogConfig = require __DIR__ . '/../../catalog-service/config/config.php';
        try {
            $catalogPdo = createPdoConnection($catalogConfig);
            $bookModel = new BookModel($catalogPdo);
            $book = $bookModel->getBookById($bookId);
            if ($book !== null) {
                $isbn = $book['isbn'];
            } else {
                $warning = "Inventory reduction skipped: Book ID $bookId not found in Catalog Service.";
            }
        } catch (Exception $e) {
            $warning = "Inventory reduction skipped: Catalog Service unavailable (" . $e->getMessage() . ").";
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
        $orderId = $this->model->createOrder($orderRef, $customer, $email, $total, $items);
        if ($orderId <= 0) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to log order into order database.'
            ], 500);
            return;
        }

        // 5. Call Inventory Service stock reduction if ISBN resolved
        if (!empty($isbn)) {
            require_once __DIR__ . '/../../inventory-service/models/InventoryModel.php';
            $inventoryConfig = require __DIR__ . '/../../inventory-service/config/config.php';
            try {
                $inventoryPdo = createPdoConnection($inventoryConfig);
                $inventoryModel = new InventoryModel($inventoryPdo);
                $stockReduced = $inventoryModel->reduceStock($isbn, $qty);
                
                if (!$stockReduced) {
                    $warning = "Inventory reduction skipped/failed: Insufficient stock or ISBN '$isbn' does not exist.";
                }
            } catch (Exception $e) {
                $warning = "Inventory reduction skipped/failed: Inventory Service connection error (" . $e->getMessage() . ").";
            }
        }

        // 6. Return success response (supports both Course Frontend and Postman expectations)
        $response = [
            'status' => 'success',
            'message' => 'Order created successfully.',
            'order_ref' => $orderRef,
            'data' => [
                'id' => $orderId,
                'order_ref' => $orderRef
            ]
        ];

        if ($warning !== null) {
            $response['warning'] = $warning;
        }

        jsonResponse($response, 201);
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

    /**
     * Handles retrieving an order by ID.
     */
    public function handleGetOrderById(int $id): void {
        $order = $this->model->getOrderById($id);
        if ($order === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Order not found.'
            ], 404);
            return;
        }
        jsonResponse([
            'status' => 'success',
            'data' => $order
        ], 200);
    }

    /**
     * Handles updating an existing order.
     */
    public function handleUpdateOrder(int $id, array $data): void {
        $customer = trim($data['customer'] ?? '');
        $email = trim($data['email'] ?? '');
        $total = isset($data['total']) ? (float)$data['total'] : -1.0;
        $status = trim($data['status'] ?? '');

        if (empty($customer) || empty($email) || $total < 0 || empty($status)) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Customer, email, non-negative total, and status are required fields.'
            ], 400);
            return;
        }

        $order = $this->model->getOrderById($id);
        if ($order === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Order not found.'
            ], 404);
            return;
        }

        $success = $this->model->updateOrder($id, $customer, $email, $total, $status);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Order updated successfully.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to update order. Invalid status value.'
            ], 400);
        }
    }

    /**
     * Handles deleting/cancelling an order.
     */
    public function handleDeleteOrder(int $id): void {
        $order = $this->model->getOrderById($id);
        if ($order === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Order not found.'
            ], 404);
            return;
        }

        $success = $this->model->deleteOrder($id);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Order cancelled successfully.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to cancel order.'
            ], 500);
        }
    }
}

