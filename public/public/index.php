<?php

// Pour démarrer le server web PHP :
/**
 * php -S localhost:8000 -d display_errors=1 -t public/
 * Pour bien fermer php, Ctrl/C et si besoin : commande : killall -9 php
 */

use App\Admin\AdminModule;
use App\Blog\BlogModule;
use Framework\App;

// chargement de l'autoloader psr-4
require dirname(__DIR__) . '/vendor/autoload.php';
$modules = [
    AdminModule::class,
    BlogModule::class
];


// Concernant PHPDI (conteneur d'injection de dépendance) :
// https://www.grafikart.fr/tutoriels/php/php-di-injection-dependance-898
// Instance du builder
$builder = new DI\ContainerBuilder();

// Fichier de définition pour dire comment le builder doit fonctionner
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

$builder->addDefinitions(dirname(__DIR__) . '/config.php');

// Construction du Builder
$container = $builder->build();

// Initialisation du renderer avec l'ajout du chemin pour accès aux views à la volée

// Initialisation de l'application avec les module
$app = new App($container, $modules);
// si on execute pas l'appli en ligne de commande ^^
if (php_sapi_name() !== 'cli') {
// initialisation de la réponse avec les variables globales qu'offre GuzzleHttp
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
