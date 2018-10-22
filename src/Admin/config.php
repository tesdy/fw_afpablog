<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 11/10/18
 * Time: 01:24
 */

use App\Admin\AdminModule;
use App\Admin\AdminTwigExtension;
use App\Admin\DashboardAction;

return [
    'admin.prefix' => '/index.php/admin',
    'admin.widgets' => [],
    AdminTwigExtension::class => DI\autowire()->constructor(DI\get('admin.widgets')),
    AdminModule::class => \DI\autowire()->constructorParameter('prefix', DI\get('admin.prefix')),
    DashboardAction::class => \DI\autowire()->constructorParameter('widgets', \DI\get('admin.widgets'))
];
