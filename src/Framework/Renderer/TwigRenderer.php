<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 09/10/18
 * Time: 14:51
 */

namespace Framework\Renderer;

/**
 * Class TwigRenderer
 * @package Framework\Renderer
 */
class TwigRenderer implements RendererInterface
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @return \Twig_Environment
     */
    public function getTwig(): \Twig_Environment
    {
        return $this->twig;
    }

    /**
     * TwigRenderer constructor.
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Ajouter un chemin pour charger les vues
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->twig->getLoader()->addPath($path, $namespace);
    }

    /**
     * Permet d'envoyer les vues
     * Le chemin peut être précisé avec des namespaces rajouté via le addPath()
     * $this->render('@blog/view')
     * $this->render('view')
     * @param string $view
     * @param array $params
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Permet de rajoute des variables globales à toutes les vues.
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
