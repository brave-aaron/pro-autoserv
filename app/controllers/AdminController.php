<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Vehicle.php';

class AdminController extends Controller {
    private $userModel;
    private $vehicleModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->vehicleModel = new Vehicle();
    }
    
    /**
     * Affiche le tableau de bord admin
     */
    public function showDashboard() {
        $this->requireAdmin();
        
        $users = $this->userModel->getAllWithPagination();
        $vehicles = $this->vehicleModel->getAllWithPagination();
        
        $this->view('admin/dashboard', [
            'users' => $users,
            'vehicles' => $vehicles
        ]);
    }
    
    /**
     * Affiche la liste des utilisateurs
     */
    public function showUsers() {
        $this->requireAdmin();
        
        $page = $_GET['page'] ?? 1;
        $users = $this->userModel->getAllWithPagination($page, 20);
        
        $this->view('admin/users', ['users' => $users]);
    }
    
    /**
     * Change le statut d'un utilisateur
     */
    public function changeUserStatus() {
        $this->requireAdmin();
        
        $data = $this->getJsonInput();
        
        if (empty($data['user_id']) || empty($data['statut'])) {
            $this->json(['status' => 'error', 'message' => 'Données manquantes.'], 400);
        }
        
        $allowedStatuses = ['actif', 'suspendu', 'banni'];
        if (!in_array($data['statut'], $allowedStatuses)) {
            $this->json(['status' => 'error', 'message' => 'Statut invalide.'], 400);
        }
        
        if ($this->userModel->changeStatus($data['user_id'], $data['statut'])) {
            $this->json(['status' => 'success', 'message' => 'Statut mis à jour avec succès.']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.'], 500);
        }
    }
    
    /**
     * Affiche la liste des véhicules
     */
    public function showVehicles() {
        $this->requireAdmin();
        
        $page = $_GET['page'] ?? 1;
        $vehicles = $this->vehicleModel->getAllWithPagination($page, 20);
        
        $this->view('admin/vehicles', ['vehicles' => $vehicles]);
    }
    
    /**
     * Change le statut d'un véhicule
     */
    public function changeVehicleStatus() {
        $this->requireAdmin();
        
        $data = $this->getJsonInput();
        
        if (empty($data['vehicle_id']) || empty($data['statut'])) {
            $this->json(['status' => 'error', 'message' => 'Données manquantes.'], 400);
        }
        
        $allowedStatuses = ['disponible', 'vendu', 'loue', 'suspendu'];
        if (!in_array($data['statut'], $allowedStatuses)) {
            $this->json(['status' => 'error', 'message' => 'Statut invalide.'], 400);
        }
        
        if ($this->vehicleModel->changeStatus($data['vehicle_id'], $data['statut'])) {
            $this->json(['status' => 'success', 'message' => 'Statut mis à jour avec succès.']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.'], 500);
        }
    }
    
    /**
     * Supprime un utilisateur
     */
    public function deleteUser($id) {
        $this->requireAdmin();
        
        if ($this->userModel->delete($id)) {
            $this->json(['status' => 'success', 'message' => 'Utilisateur supprimé avec succès.']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de la suppression.'], 500);
        }
    }
    
    /**
     * Supprime un véhicule
     */
    public function deleteVehicle($id) {
        $this->requireAdmin();
        
        if ($this->vehicleModel->delete($id)) {
            $this->json(['status' => 'success', 'message' => 'Véhicule supprimé avec succès.']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de la suppression.'], 500);
        }
    }
    
    /**
     * Statistiques générales
     */
    public function getStats() {
        $this->requireAdmin();
        
        $totalUsers = count($this->userModel->findAll());
        $totalVehicles = count($this->vehicleModel->findAll());
        $activeUsers = count(array_filter($this->userModel->findAll(), function($user) {
            return $user['statut'] === 'actif';
        }));
        $availableVehicles = count($this->vehicleModel->findByType('vente')) + count($this->vehicleModel->findByType('location'));
        
        $this->json([
            'status' => 'success',
            'stats' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_vehicles' => $totalVehicles,
                'available_vehicles' => $availableVehicles
            ]
        ]);
    }
}