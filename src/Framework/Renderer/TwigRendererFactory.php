<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 09/10/18
 * Time: 18:00
 */

namespace Framework\Renderer;

use Psr\Container\ContainerInterface;
use Ajgl\Twig\Extension\BreakpointExtension;
use Twig\Extension\DebugExtension;

class TwigRendererFactory
{

    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $viewPath = $container->get('views.path');
        $loader = new \Twig_Loader_Filesystem($viewPath);
        $twig = new \Twig_Environment($loader, ['debug' => true]); // paramètres actives le debug de twig !
        // et ajouter l'extension, ainsi {{ debug() }} fonction dans les vues Twig !
        $twig->addExtension(new DebugExtension());
        $twigbrakpoint = new BreakpointExtension;
        $twig->addExtension($twigbrakpoint);
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
        return new TwigRenderer($twig);
    }
}
