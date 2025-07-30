<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Affiche la page de connexion
     */
    public function showLogin() {
        $this->view('auth/login');
    }
    
    /**
     * Traite la connexion
     */
    public function login() {
        session_start();
        
        $data = $this->getJsonInput();
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        
        // Validation
        $errors = $this->validateRequired($data, ['email', 'password']);
        if (!empty($errors)) {
            $this->json(['status' => 'error', 'message' => implode(' ', $errors)], 400);
        }
        
        // Recherche de l'utilisateur
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            $this->json(['status' => 'error', 'message' => 'Aucun compte avec cet email.'], 401);
        }
        
        // Vérification du mot de passe
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            $this->json(['status' => 'error', 'message' => 'Mot de passe incorrect.'], 401);
        }
        
        // Vérification du statut
        if ($user['statut'] !== 'actif') {
            $this->json(['status' => 'error', 'message' => "Compte {$user['statut']}, accès refusé."], 403);
        }
        
        // Création de la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['statut'] = $user['statut'];
        
        $redirect = ($user['role'] === 'admin') ? '/dashboard_admin.php' : '/dashboard_user.php';
        
        $this->json([
            'status' => 'success', 
            'message' => 'Connexion réussie !', 
            'redirect' => $redirect
        ]);
    }
    
    /**
     * Affiche la page d'inscription
     */
    public function showRegister() {
        $this->view('auth/register');
    }
    
    /**
     * Traite l'inscription
     */
    public function register() {
        $data = $this->getJsonInput();
        
        // Validation
        $errors = $this->validateRequired($data, ['username', 'email', 'password']);
        if (!empty($errors)) {
            $this->json(['status' => 'error', 'message' => implode(' ', $errors)], 400);
        }
        
        // Vérification si l'email existe déjà
        if ($this->userModel->findByEmail($data['email'])) {
            $this->json(['status' => 'error', 'message' => 'Cet email est déjà utilisé.'], 409);
        }
        
        // Validation du mot de passe
        if (strlen($data['password']) < 6) {
            $this->json(['status' => 'error', 'message' => 'Le mot de passe doit contenir au moins 6 caractères.'], 400);
        }
        
        // Création de l'utilisateur
        $userId = $this->userModel->create([
            'username' => trim($data['username']),
            'email' => trim($data['email']),
            'password' => $data['password'],
            'role' => 'user'
        ]);
        
        if ($userId) {
            $this->json([
                'status' => 'success', 
                'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.'
            ]);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de l\'inscription.'], 500);
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        session_start();
        session_destroy();
        $this->redirect('/index.php');
    }
    
    /**
     * Affiche la page mon compte
     */
    public function showAccount() {
        $this->requireAuth();
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        $this->view('auth/account', ['user' => $user]);
    }
    
    /**
     * Met à jour le profil utilisateur
     */
    public function updateProfile() {
        $this->requireAuth();
        
        $data = $this->getJsonInput();
        $allowedFields = ['username', 'email'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field]) && !empty(trim($data[$field]))) {
                $updateData[$field] = trim($data[$field]);
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $this->json(['status' => 'error', 'message' => 'Le mot de passe doit contenir au moins 6 caractères.'], 400);
            }
            $updateData['password'] = $data['password'];
        }
        
        if (empty($updateData)) {
            $this->json(['status' => 'error', 'message' => 'Aucune donnée à mettre à jour.'], 400);
        }
        
        if ($this->userModel->update($_SESSION['user_id'], $updateData)) {
            $this->json(['status' => 'success', 'message' => 'Profil mis à jour avec succès.']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.'], 500);
        }
    }
}