<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Vehicle.php';

class VehicleController extends Controller {
    private $vehicleModel;
    
    public function __construct() {
        $this->vehicleModel = new Vehicle();
    }
    
    /**
     * Affiche la liste des véhicules en vente
     */
    public function showSale() {
        $vehicles = $this->vehicleModel->findByType('vente');
        $this->view('vehicles/sale', ['vehicles' => $vehicles]);
    }
    
    /**
     * Affiche la liste des véhicules en location
     */
    public function showRental() {
        $vehicles = $this->vehicleModel->findByType('location');
        $this->view('vehicles/rental', ['vehicles' => $vehicles]);
    }
    
    /**
     * Affiche les détails d'un véhicule
     */
    public function showDetails($id) {
        $vehicle = $this->vehicleModel->findById($id);
        
        if (!$vehicle) {
            $this->redirect('/vente.php');
        }
        
        $this->view('vehicles/details', ['vehicle' => $vehicle]);
    }
    
    /**
     * Affiche le formulaire de publication
     */
    public function showPublish() {
        $this->requireAuth();
        $this->view('vehicles/publish');
    }
    
    /**
     * Traite la publication d'un véhicule
     */
    public function publish() {
        $this->requireAuth();
        
        // Récupération des données du formulaire
        $data = $_POST;
        
        // Validation des champs requis
        $requiredFields = ['marque', 'modele', 'annee', 'prix', 'type', 'description'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ {$field} est requis.";
            }
        }
        
        if (!empty($errors)) {
            $this->json(['status' => 'error', 'message' => implode(' ', $errors)], 400);
        }
        
        // Gestion de l'upload d'image
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $imageName = $this->uploadImage($_FILES['image']);
            if (!$imageName) {
                $this->json(['status' => 'error', 'message' => 'Erreur lors du téléchargement de l\'image.'], 500);
            }
        }
        
        // Création du véhicule
        $vehicleData = [
            'marque' => trim($data['marque']),
            'modele' => trim($data['modele']),
            'annee' => intval($data['annee']),
            'prix' => floatval($data['prix']),
            'type' => $data['type'],
            'description' => trim($data['description']),
            'user_id' => $_SESSION['user_id'],
            'image' => $imageName
        ];
        
        $vehicleId = $this->vehicleModel->create($vehicleData);
        
        if ($vehicleId) {
            $this->json(['status' => 'success', 'message' => 'Véhicule publié avec succès !']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de la publication.'], 500);
        }
    }
    
    /**
     * Affiche les véhicules de l'utilisateur connecté
     */
    public function showMyVehicles() {
        $this->requireAuth();
        
        $vehicles = $this->vehicleModel->findByUser($_SESSION['user_id']);
        $this->view('vehicles/my-vehicles', ['vehicles' => $vehicles]);
    }
    
    /**
     * Affiche le formulaire de modification
     */
    public function showEdit($id) {
        $this->requireAuth();
        
        $vehicle = $this->vehicleModel->findById($id);
        
        if (!$vehicle || $vehicle['user_id'] != $_SESSION['user_id']) {
            $this->redirect('/dashboard_user.php');
        }
        
        $this->view('vehicles/edit', ['vehicle' => $vehicle]);
    }
    
    /**
     * Traite la modification d'un véhicule
     */
    public function update($id) {
        $this->requireAuth();
        
        $vehicle = $this->vehicleModel->findById($id);
        
        if (!$vehicle || $vehicle['user_id'] != $_SESSION['user_id']) {
            $this->json(['status' => 'error', 'message' => 'Véhicule non trouvé.'], 404);
        }
        
        $data = $_POST;
        $updateData = [];
        
        $allowedFields = ['marque', 'modele', 'annee', 'prix', 'type', 'description'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field]) && !empty(trim($data[$field]))) {
                $updateData[$field] = trim($data[$field]);
            }
        }
        
        // Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $imageName = $this->uploadImage($_FILES['image']);
            if ($imageName) {
                $updateData['image'] = $imageName;
            }
        }
        
        if (empty($updateData)) {
            $this->json(['status' => 'error', 'message' => 'Aucune donnée à mettre à jour.'], 400);
        }
        
        if ($this->vehicleModel->update($id, $updateData)) {
            $this->json(['status' => 'success', 'message' => 'Véhicule mis à jour avec succès.']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.'], 500);
        }
    }
    
    /**
     * Supprime un véhicule
     */
    public function delete($id) {
        $this->requireAuth();
        
        $vehicle = $this->vehicleModel->findById($id);
        
        if (!$vehicle || $vehicle['user_id'] != $_SESSION['user_id']) {
            $this->json(['status' => 'error', 'message' => 'Véhicule non trouvé.'], 404);
        }
        
        if ($this->vehicleModel->delete($id)) {
            $this->json(['status' => 'success', 'message' => 'Véhicule supprimé avec succès.']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de la suppression.'], 500);
        }
    }
    
    /**
     * Recherche de véhicules
     */
    public function search() {
        $filters = $_GET;
        $vehicles = $this->vehicleModel->search($filters);
        
        $this->json(['status' => 'success', 'vehicles' => $vehicles]);
    }
    
    /**
     * Upload d'image
     */
    private function uploadImage($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $uploadPath = __DIR__ . '/../../assets/images/' . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $fileName;
        }
        
        return false;
    }
}