<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 16/10/18
 * Time: 11:11
 */

namespace App\Admin;

use Framework\Renderer\RendererInterface;

class DashboardAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var AdminWidgetInterface
     */
    private $widgets;

    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    public function __invoke()
    {
        $widgets = array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->render();
        }, '');
        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }
}
