<?php
class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($limit = null, $offset = null) {
        $sql = "SELECT * FROM products ORDER BY name";
        
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        }
        
        return $this->db->fetchAll($sql);
    }
    
    public function getById($id) {
        $result = $this->db->fetch("SELECT * FROM products WHERE id = ?", [$id]);
        return $result;
    }
    
    public function getByCode($code) {
        return $this->db->fetch("SELECT * FROM products WHERE code = ?", [$code]);
    }
    
    public function search($term) {
        $term = "%$term%";
        $sql = "SELECT * FROM products 
                WHERE name LIKE ? OR code LIKE ? OR description LIKE ? 
                ORDER BY name";
        
        return $this->db->fetchAll($sql, [$term, $term, $term]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO products (code, name, description, purchase_price, sale_price, stock) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['code'],
            $data['name'],
            $data['description'],
            $data['purchase_price'],
            $data['sale_price'],
            $data['stock'] ?? 0
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE products SET 
                code = ?, 
                name = ?, 
                description = ?, 
                purchase_price = ?, 
                sale_price = ?, 
                stock = ?, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $params = [
            $data['code'],
            $data['name'],
            $data['description'],
            $data['purchase_price'],
            $data['sale_price'],
            $data['stock'],
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function updateStock($id, $quantity) {
        $sql = "UPDATE products SET 
                stock = stock + ?, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        return $this->db->query($sql, [$quantity, $id]);
    }
    
    public function count() {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM products");
        return $result['count'];
    }
}