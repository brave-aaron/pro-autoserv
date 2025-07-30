<?php

require_once __DIR__ . '/Model.php';

class User extends Model {
    protected $table = 'users';
    
    /**
     * Trouve un utilisateur par email
     */
    public function findByEmail($email) {
        return $this->findWhere('email', $email);
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role, statut) VALUES (?, ?, ?, ?, ?)");
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'] ?? 'user';
        $statut = $data['statut'] ?? 'actif';
        
        $stmt->bind_param("sssss", 
            $data['username'], 
            $data['email'], 
            $hashedPassword, 
            $role, 
            $statut
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        $types = '';
        
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            $fields[] = "{$key} = ?";
            $values[] = $value;
            $types .= 's';
        }
        
        $values[] = $id;
        $types .= 'i';
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    /**
     * Vérifie le mot de passe
     */
    public function verifyPassword($plainPassword, $hashedPassword) {
        return password_verify($plainPassword, $hashedPassword);
    }
    
    /**
     * Vérifie si l'utilisateur est actif
     */
    public function isActive($userId) {
        $user = $this->findById($userId);
        return $user && $user['statut'] === 'actif';
    }
    
    /**
     * Change le statut d'un utilisateur
     */
    public function changeStatus($userId, $newStatus) {
        return $this->update($userId, ['statut' => $newStatus]);
    }
    
    /**
     * Récupère tous les utilisateurs avec pagination
     */
    public function getAllWithPagination($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->db->prepare("SELECT id, username, email, role, statut FROM users LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}