<?php
class Sale {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($startDate = null, $endDate = null) {
        $sql = "SELECT s.*, u.username FROM sales s 
                JOIN users u ON s.user_id = u.id 
                WHERE 1=1";
        $params = [];
        
        if ($startDate) {
            $sql .= " AND s.sale_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND s.sale_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY s.sale_date DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getById($id) {
        return $this->db->fetch(
            "SELECT s.*, u.username, u.first_name, u.last_name FROM sales s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.id = ?", 
            [$id]
        );
    }
    
    public function getDetailsBySaleId($saleId) {
        $sql = "SELECT sd.*, p.name as product_name, p.code as product_code 
                FROM sale_details sd
                JOIN products p ON sd.product_id = p.id
                WHERE sd.sale_id = ?";
        
        return $this->db->fetchAll($sql, [$saleId]);
    }
    
    public function create($data) {
        $this->db->getConnection()->beginTransaction();
        
        try {
            $sql = "INSERT INTO sales (invoice_number, user_id, customer_name, total_amount, payment_method, notes, sale_date) 
                    VALUES (?, ?, ?, ?, ?, ?, CURDATE())";
            
            $params = [
                $data['invoice_number'],
                $data['user_id'],
                $data['customer_name'],
                $data['total_amount'],
                $data['payment_method'] ?? 'Efectivo',
                $data['notes'] ?? null
            ];
            
            $saleId = $this->db->insert($sql, $params);

            foreach ($data['items'] as $item) {
                $sql = "INSERT INTO sale_details (sale_id, product_id, quantity, unit_price, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                
                $this->db->query($sql, [
                    $saleId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['subtotal']
                ]);
                
                $productModel = new Product();
                $productModel->updateStock($item['product_id'], -$item['quantity']);
            }

            $this->db->getConnection()->commit();
            
            return $saleId;
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }
    
    public function generateInvoiceNumber() {
        $prefix = 'INV-' . date('Y') . '-';
        
        $sql = "SELECT MAX(CAST(SUBSTRING(invoice_number, LENGTH(?) + 1) AS UNSIGNED)) as max_number 
                FROM sales 
                WHERE invoice_number LIKE ?";
        
        $result = $this->db->fetch($sql, [$prefix, $prefix . '%']);
        
        $nextNumber = 1;
        if ($result && $result['max_number']) {
            $nextNumber = $result['max_number'] + 1;
        }
        
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}