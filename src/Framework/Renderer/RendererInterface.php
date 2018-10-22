<?php
/**
 * Interface crée avec la commande
 * ctrl+Alt+Maj+T > sélectionner Interface du Menu contextuel 'Refactor This'
 * Ajouter Interface à la suite du nom,
 * modifier le namespace pour correspondre à l'arborescence
 * et valider
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 09/10/18
 * Time: 14:25
 */

namespace Framework\Renderer;

/**
 * Class Renderer
 * @package Framework
 */
interface RendererInterface
{
    /**
     * Ajouter un chemin pour charger les vues
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Permet d'envoyer les vues
     * Le chemin peut être précisé avec des namespaces rajouté via le addPath()
     * $this->render('@blog/view')
     * $this->render('view')
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

    /**
     * Permet de rajoute des variables globales à toutes les vues.
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void;
}
