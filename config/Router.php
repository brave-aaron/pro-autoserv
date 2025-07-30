<?php

class Router {
    private $routes = [];
    
    /**
     * Ajoute une route GET
     */
    public function get($path, $controller, $action) {
        $this->addRoute('GET', $path, $controller, $action);
    }
    
    /**
     * Ajoute une route POST
     */
    public function post($path, $controller, $action) {
        $this->addRoute('POST', $path, $controller, $action);
    }
    
    /**
     * Ajoute une route DELETE
     */
    public function delete($path, $controller, $action) {
        $this->addRoute('DELETE', $path, $controller, $action);
    }
    
    /**
     * Ajoute une route PUT
     */
    public function put($path, $controller, $action) {
        $this->addRoute('PUT', $path, $controller, $action);
    }
    
    /**
     * Ajoute une route
     */
    private function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    /**
     * Résout la route actuelle
     */
    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Recherche de la route correspondante
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $method, $path)) {
                return $this->executeRoute($route, $path);
            }
        }
        
        // Route non trouvée
        $this->handleNotFound();
    }
    
    /**
     * Vérifie si une route correspond
     */
    private function matchRoute($route, $method, $path) {
        if ($route['method'] !== $method) {
            return false;
        }
        
        // Conversion du pattern en regex
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route['path']);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $path);
    }
    
    /**
     * Exécute la route
     */
    private function executeRoute($route, $path) {
        // Extraction des paramètres
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route['path']);
        $pattern = '#^' . $pattern . '$#';
        
        preg_match($pattern, $path, $matches);
        array_shift($matches); // Supprime le match complet
        
        // Chargement du contrôleur
        $controllerName = $route['controller'];
        $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            die("Contrôleur non trouvé: {$controllerName}");
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            die("Classe de contrôleur non trouvée: {$controllerName}");
        }
        
        $controller = new $controllerName();
        $action = $route['action'];
        
        if (!method_exists($controller, $action)) {
            die("Méthode non trouvée: {$action} dans {$controllerName}");
        }
        
        // Appel de la méthode avec les paramètres
        return call_user_func_array([$controller, $action], $matches);
    }
    
    /**
     * Gère les erreurs 404
     */
    private function handleNotFound() {
        http_response_code(404);
        echo "<h1>404 - Page non trouvée</h1>";
        exit();
    }
    
    /**
     * Définit les routes de l'application
     */
    public function defineRoutes() {
        // Routes des pages
        $this->get('/', 'HomeController', 'index');
        $this->get('/index.php', 'HomeController', 'index');
        
        // Routes d'authentification
        $this->get('/login.php', 'AuthController', 'showLogin');
        $this->post('/login_process.php', 'AuthController', 'login');
        $this->get('/register.php', 'AuthController', 'showRegister');
        $this->post('/register_process.php', 'AuthController', 'register');
        $this->get('/logout.php', 'AuthController', 'logout');
        $this->get('/mon_compte.php', 'AuthController', 'showAccount');
        $this->post('/update_profile.php', 'AuthController', 'updateProfile');
        
        // Routes des véhicules
        $this->get('/vente.php', 'VehicleController', 'showSale');
        $this->get('/location.php', 'VehicleController', 'showRental');
        $this->get('/details.php', 'VehicleController', 'showDetails');
        $this->get('/publication.php', 'VehicleController', 'showPublish');
        $this->post('/publication_process.php', 'VehicleController', 'publish');
        $this->get('/my-vehicles.php', 'VehicleController', 'showMyVehicles');
        $this->get('/modifier.php', 'VehicleController', 'showEdit');
        $this->post('/update_vehicle.php', 'VehicleController', 'update');
        $this->post('/supprimer.php', 'VehicleController', 'delete');
        $this->get('/search.php', 'VehicleController', 'search');
        
        // Routes d'administration
        $this->get('/dashboard_admin.php', 'AdminController', 'showDashboard');
        $this->get('/admin/users.php', 'AdminController', 'showUsers');
        $this->post('/changer_statut.php', 'AdminController', 'changeUserStatus');
        $this->get('/admin/vehicles.php', 'AdminController', 'showVehicles');
        $this->post('/admin/change_vehicle_status.php', 'AdminController', 'changeVehicleStatus');
        $this->delete('/admin/delete_user/{id}', 'AdminController', 'deleteUser');
        $this->delete('/admin/delete_vehicle/{id}', 'AdminController', 'deleteVehicle');
        $this->get('/admin/stats.php', 'AdminController', 'getStats');
        
        // Route par défaut pour le dashboard utilisateur
        $this->get('/dashboard_user.php', 'VehicleController', 'showMyVehicles');
    }
}