<?php
class Supplier {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM suppliers ORDER BY name");
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM suppliers WHERE id = ?", [$id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO suppliers (name, contact_name, phone, email, address) 
                VALUES (?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['contact_name'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE suppliers SET 
                name = ?, 
                contact_name = ?, 
                phone = ?, 
                email = ?, 
                address = ? 
                WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['contact_name'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM suppliers WHERE id = ?", [$id]);
    }
}