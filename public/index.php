<?php

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


// Après avoir fait les tests via le terminal avec la commande :
// vendor/bin/phpunit tests/Framework/AppTest.php
// pour afficher le contenu sur notre navigateur, utilisation de http-interop/response-sender :
// Dispose une fonction qui convertie une Response PSR7 vers un output HTTP.
// Choix personnel, d'autres librairies offre les mêmes méthodes, tant que la librairie choisiegit
// prend en entrée un objet de type ResponseInterface du psr7 c'est OK !
// https://packagist.org/packages/http-interop/response-sender
// https://github.com/http-interop/response-sender
// Commande terminale : composer require http-interop/response-sender
// Il ne suffit plus que d'utiliser la fonction send de cette librairie :
//$test = new \Tests\Framework\AppTest();
