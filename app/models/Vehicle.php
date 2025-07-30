<?php

require_once __DIR__ . '/Model.php';

class Vehicle extends Model {
    protected $table = 'vehicles';
    
    /**
     * Crée un nouveau véhicule
     */
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO vehicles (marque, modele, annee, prix, type, statut, description, user_id, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $statut = $data['statut'] ?? 'disponible';
        
        $stmt->bind_param("ssisssssi", 
            $data['marque'], 
            $data['modele'], 
            $data['annee'], 
            $data['prix'], 
            $data['type'], 
            $statut,
            $data['description'],
            $data['user_id'],
            $data['image']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    /**
     * Met à jour un véhicule
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        $types = '';
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
            $types .= 's';
        }
        
        $values[] = $id;
        $types .= 'i';
        
        $sql = "UPDATE vehicles SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    /**
     * Récupère les véhicules par type (vente/location)
     */
    public function findByType($type) {
        $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE type = ? AND statut = 'disponible' ORDER BY created_at DESC");
        $stmt->bind_param("s", $type);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Récupère les véhicules d'un utilisateur
     */
    public function findByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Recherche de véhicules avec filtres
     */
    public function search($filters) {
        $conditions = ["statut = 'disponible'"];
        $params = [];
        $types = '';
        
        if (!empty($filters['marque'])) {
            $conditions[] = "marque LIKE ?";
            $params[] = '%' . $filters['marque'] . '%';
            $types .= 's';
        }
        
        if (!empty($filters['type'])) {
            $conditions[] = "type = ?";
            $params[] = $filters['type'];
            $types .= 's';
        }
        
        if (!empty($filters['prix_min'])) {
            $conditions[] = "prix >= ?";
            $params[] = $filters['prix_min'];
            $types .= 'i';
        }
        
        if (!empty($filters['prix_max'])) {
            $conditions[] = "prix <= ?";
            $params[] = $filters['prix_max'];
            $types .= 'i';
        }
        
        $sql = "SELECT * FROM vehicles WHERE " . implode(' AND ', $conditions) . " ORDER BY created_at DESC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $result = $this->db->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    
    /**
     * Change le statut d'un véhicule
     */
    public function changeStatus($vehicleId, $newStatus) {
        return $this->update($vehicleId, ['statut' => $newStatus]);
    }
    
    /**
     * Récupère les véhicules avec pagination
     */
    public function getAllWithPagination($page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->db->prepare("SELECT * FROM vehicles ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}