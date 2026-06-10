<?php
/**
 * InventoryController
 * Handles requests relating to stock queries, restocking, and stock reductions.
 */
class InventoryController {
    private InventoryModel $model;

    public function __construct(InventoryModel $model) {
        $this->model = $model;
    }

    /**
     * Fetch inventory logs (all or low-stock only).
     */
    public function handleGetInventory(array $get): void {
        $filter = $get['filter'] ?? '';
        
        if ($filter === 'low') {
            // Retrieve only inventory falling below or equal to threshold
            $inventory = $this->model->getLowStock();
        } else {
            // Retrieve all inventory levels
            $inventory = $this->model->getAllInventory();
        }

        jsonResponse([
            'status' => 'success',
            'data' => $inventory
        ], 200);
    }

    /**
     * Handle stock replenishment actions.
     */
    public function handleRestock(array $get): void {
        $id = isset($get['id']) ? (int)$get['id'] : 0;
        $qty = isset($get['qty']) ? (int)$get['qty'] : 0;

        // 1. Validate variables
        if ($id <= 0 || $qty <= 0) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid parameters. ID and Qty must be positive integers.'
            ], 400);
            return;
        }

        // 2. Perform the update
        $success = $this->model->restockItem($id, $qty);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Stock successfully replenished.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to replenish stock due to database error.'
            ], 500);
        }
    }

    /**
     * Handle stock reduction actions (triggered by orders).
     */
    public function handleReduceStock(array $post): void {
        $isbn = trim($post['isbn'] ?? '');
        $qty = isset($post['qty']) ? (int)$post['qty'] : 0;

        // 1. Validate variables
        if (empty($isbn) || $qty <= 0) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid parameters. ISBN and Qty must be provided.'
            ], 400);
            return;
        }

        // 2. Perform stock subtraction
        $success = $this->model->reduceStock($isbn, $qty);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Stock successfully reduced.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Insufficient stock or invalid ISBN identifier.'
            ], 400);
        }
    }

    /**
     * Handles fetching an inventory record by ID.
     */
    public function handleGetInventoryById(int $id): void {
        $item = $this->model->getById($id);
        if ($item === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Inventory record not found.'
            ], 404);
            return;
        }
        jsonResponse([
            'status' => 'success',
            'data' => $item
        ], 200);
    }

    /**
     * Handles creating a new inventory record.
     */
    public function handleCreateInventory(array $data): void {
        $bookId = isset($data['book_id']) ? (int)$data['book_id'] : 0;
        $isbn = trim($data['isbn'] ?? '');
        $title = trim($data['title'] ?? '');
        $category = trim($data['category'] ?? '');
        $stock = isset($data['stock']) ? (int)$data['stock'] : 0;
        $threshold = isset($data['threshold']) ? (int)$data['threshold'] : 5;

        if ($bookId <= 0 || empty($isbn) || empty($title) || empty($category)) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Book ID, isbn, title, and category are required fields.'
            ], 400);
            return;
        }

        $success = $this->model->createInventory($bookId, $isbn, $title, $category, $stock, $threshold);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Inventory record created successfully.'
            ], 201);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to create inventory record.'
            ], 500);
        }
    }

    /**
     * Handles updating an existing inventory record.
     */
    public function handleUpdateInventory(int $id, array $data): void {
        $bookId = isset($data['book_id']) ? (int)$data['book_id'] : 0;
        $isbn = trim($data['isbn'] ?? '');
        $title = trim($data['title'] ?? '');
        $category = trim($data['category'] ?? '');
        $stock = isset($data['stock']) ? (int)$data['stock'] : 0;
        $threshold = isset($data['threshold']) ? (int)$data['threshold'] : 5;

        if ($bookId <= 0 || empty($isbn) || empty($title) || empty($category)) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Book ID, isbn, title, and category are required fields.'
            ], 400);
            return;
        }

        $item = $this->model->getById($id);
        if ($item === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Inventory record not found.'
            ], 404);
            return;
        }

        $success = $this->model->updateInventory($id, $bookId, $isbn, $title, $category, $stock, $threshold);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Inventory record updated successfully.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to update inventory record.'
            ], 500);
        }
    }

    /**
     * Handles deleting an inventory record.
     */
    public function handleDeleteInventory(int $id): void {
        $item = $this->model->getById($id);
        if ($item === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Inventory record not found.'
            ], 404);
            return;
        }

        $success = $this->model->deleteInventory($id);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Inventory record deleted successfully.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to delete inventory record.'
            ], 500);
        }
    }
}

