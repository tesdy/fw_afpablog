<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 09/10/18
 * Time: 20:33
 */

use App\Blog\BlogWidget;
use function DI\get;

return [
    'blog.prefix' => '/index.php/blog',
    'admin.widgets' => \DI\add([
        get(BlogWidget::class)
    ])
];
