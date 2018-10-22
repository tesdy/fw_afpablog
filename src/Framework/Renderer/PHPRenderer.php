<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 09/10/18
 * Time: 01:05
 */

namespace Framework\Renderer;

/**
 * Class Renderer
 * @package Framework
 */
class PHPRenderer implements RendererInterface
{
    /**
     *
     */
    public const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * @var array
     */
    private $paths = [];

    /**
     * variables globalement accessibles pour toutes les vues
     * @var array
     */
    private $globals = [];

    /**
     * PHPRenderer constructor.
     * @param null|string $defaultPath
     */
    public function __construct(?string $defaultPath = null) // chaîne de caractères nullable et par défaut = null
    {
        if ($defaultPath !== null) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Ajouter un chemin pour charger les vues
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if ($path === null) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Permet d'envoyer les vues
     * Le chemin peut être précisé avec des namespaces rajouté via le addPath()
     * $this->render('@blog/view')
     * $this->render('view')
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals, EXTR_OVERWRITE);
        extract($params, EXTR_OVERWRITE);
        require $path;
        return ob_get_clean();
    }

    /**
     * Permet de rajoute des variables globales à toutes les vues.
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }


    /**
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') -1);
    }

    /**
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
