<?php

abstract class Controller {
    
    /**
     * Charge une vue
     */
    protected function view($viewName, $data = []) {
        // Extrait les variables pour la vue
        extract($data);
        
        // Inclut la vue
        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Vue introuvable: " . $viewName);
        }
    }
    
    /**
     * Redirige vers une URL
     */
    protected function redirect($url) {
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Retourne une réponse JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    protected function requireAuth() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login.php');
        }
    }
    
    /**
     * Vérifie si l'utilisateur est admin
     */
    protected function requireAdmin() {
        session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/index.php');
        }
    }
    
    /**
     * Récupère les données POST en JSON
     */
    protected function getJsonInput() {
        return json_decode(file_get_contents("php://input"), true);
    }
    
    /**
     * Valide les champs requis
     */
    protected function validateRequired($data, $requiredFields) {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ {$field} est requis.";
            }
        }
        
        return $errors;
    }
}