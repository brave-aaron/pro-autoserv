<?php

require_once __DIR__ . '/Controller.php';

class HomeController extends Controller {
    
    /**
     * Affiche la page d'accueil
     */
    public function index() {
        $this->view('pages/home');
    }
}