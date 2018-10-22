<?php

/*************
*  SERVICES  *
*************/
// Affichage
use Framework\Renderer\{TwigRendererFactory,RendererInterface};
// Router
use Framework\Router;
use Framework\Router\RouterTwigExtension;
// Session
use Framework\Session\SessionInterface;
// helpers pour Twig
use Framework\Twig\{FlashExtension, FormExtension, PagerFantaExtension, TextExtension, TimeExtension};

return [
    'database.host' => 'localhost',
    'database.user' => 'tesdy',
    'database.pass' => 'Root1234.',
    'database.name' => 'monsupersite',
    'database.charset' => 'utf8',
    'database.collation' => 'utf8_general_ci',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        DI\get(RouterTwigExtension::class),
        DI\get(PagerFantaExtension::class),
        DI\get(TextExtension::class),
        DI\get(TimeExtension::class),
        DI\get(FlashExtension::class),
        DI\get(FormExtension::class)
    ],
    SessionInterface::class => \DI\create(\Framework\Session\PHPSession::class),
    Router::class => DI\create(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),

    \PDO::class => function (\Psr\Container\ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') .
            ';dbname=' . $c->get('database.name'),
            $c->get('database.user'),
            $c->get('database.pass'),
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];
