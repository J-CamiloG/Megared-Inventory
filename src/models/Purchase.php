<?php
class Purchase {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($startDate = null, $endDate = null) {
        $sql = "SELECT p.*, u.username, s.name as supplier_name 
                FROM purchases p 
                JOIN users u ON p.user_id = u.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE 1=1";
        $params = [];
        
        if ($startDate) {
            $sql .= " AND p.purchase_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND p.purchase_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY p.purchase_date DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getById($id) {
        return $this->db->fetch(
            "SELECT p.*, u.username, s.name as supplier_name, s.contact_name, s.phone, s.email 
            FROM purchases p 
            JOIN users u ON p.user_id = u.id 
            LEFT JOIN suppliers s ON p.supplier_id = s.id 
            WHERE p.id = ?", 
            [$id]
        );
    }
    
    public function getDetailsByPurchaseId($purchaseId) {
        $sql = "SELECT pd.*, p.name as product_name, p.code as product_code 
                FROM purchase_details pd
                JOIN products p ON pd.product_id = p.id
                WHERE pd.purchase_id = ?";
        
        return $this->db->fetchAll($sql, [$purchaseId]);
    }
    
    public function create($data) {
        $this->db->getConnection()->beginTransaction();
        
        try {
            $sql = "INSERT INTO purchases (reference_number, user_id, supplier_id, subtotal, tax_amount, total_amount, notes, purchase_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['subtotal'];
            }

            $tax_rate = 0.18;
            $tax_amount = $subtotal * $tax_rate;
            $total_amount = $subtotal + $tax_amount;
            
            $params = [
                $data['reference_number'],
                $data['user_id'],
                $data['supplier_id'],
                $subtotal,
                $tax_amount,
                $total_amount,
                $data['notes'] ?? null,
                $data['purchase_date'] ?? date('Y-m-d H:i:s')
            ];
            
            $purchaseId = $this->db->insert($sql, $params);

            foreach ($data['items'] as $item) {
                $sql = "INSERT INTO purchase_details (purchase_id, product_id, quantity, unit_price, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                
                $this->db->query($sql, [
                    $purchaseId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['subtotal']
                ]);
                
                $productModel = new Product();
                $productModel->updateStock($item['product_id'], $item['quantity']);
            }
            
            $this->db->getConnection()->commit();
            
            return $purchaseId;
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }

    public function generateReferenceNumber() {
        $prefix = 'PO-' . date('Y') . '-';
        
        $sql = "SELECT MAX(CAST(SUBSTRING(reference_number, LENGTH(?) + 1) AS UNSIGNED)) as max_number 
                FROM purchases 
                WHERE reference_number LIKE ?";
        
        $result = $this->db->fetch($sql, [$prefix, $prefix . '%']);
        
        $nextNumber = 1;
        if ($result && $result['max_number']) {
            $nextNumber = $result['max_number'] + 1;
        }
        
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}