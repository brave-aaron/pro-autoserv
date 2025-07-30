<?php

// Point d'entrée principal de l'application MVC

// Chargement de l'autoloader et des classes de base
require_once __DIR__ . '/config/Router.php';

// Démarrage de la session
session_start();

// Création et configuration du routeur
$router = new Router();
$router->defineRoutes();

// Résolution de la route
$router->resolve();
