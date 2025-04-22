<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    public function getByUsername($username) {
        return $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
    }
    
    public function getByEmail($email) {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    public function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, email, first_name, last_name, role) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['username'],
            $hashedPassword,
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['role']
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE users SET 
                username = ?, 
                email = ?, 
                first_name = ?, 
                last_name = ?, 
                role = ?, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $params = [
            $data['username'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['role'],
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function updatePassword($id, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET 
                password = ?, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        return $this->db->query($sql, [$hashedPassword, $id]);
    }
    
    public function block($id) {
        $sql = "UPDATE users SET status = 'blocked' WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function unblock($id) {
        $sql = "UPDATE users SET status = 'active', failed_login_attempts = 0 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function incrementLoginAttempts($id) {
        $sql = "UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        $user = $this->getById($id);
        if ($user['failed_login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $this->block($id);
        }
        
        return $user['failed_login_attempts'];
    }
    
    public function resetLoginAttempts($id) {
        $sql = "UPDATE users SET failed_login_attempts = 0 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function createResetToken($userId, $token, $expiresAt) {
        $sql = "INSERT INTO password_reset_tokens (user_id, token, expires_at) 
                VALUES (?, ?, ?)";
        
        return $this->db->insert($sql, [$userId, $token, $expiresAt]);
    }
    
    public function getResetToken($token) {
        return $this->db->fetch(
            "SELECT * FROM password_reset_tokens WHERE token = ? AND expires_at > NOW()",
            [$token]
        );
    }
    
    public function deleteResetToken($token) {
        $sql = "DELETE FROM password_reset_tokens WHERE token = ?";
        return $this->db->query($sql, [$token]);
    }
}