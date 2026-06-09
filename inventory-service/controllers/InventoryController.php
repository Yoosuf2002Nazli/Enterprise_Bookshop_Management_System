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
}
