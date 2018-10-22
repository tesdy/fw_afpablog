<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 14/10/18
 * Time: 20:54
 */

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Framework\Validator;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Administration des articles
 *
 * Class AdminBlogAction
 * @package App\Blog\Actions
 */
class CategoryCrudAction extends CrudAction
{

    protected $viewPath = '@blog/admin/categories';

    protected $routePrefix = 'blog.category.admin';

    public function __construct(RendererInterface $renderer, Router $router, CategoryTable $table, FlashService $flash)
    {
        parent::__construct($renderer, $router, $table, $flash);
    }

    /**
     * Récupérer les différents champs à manipuler en base
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getParams(ServerRequestInterface $request, $objet = null): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return \in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Définition de la longueur mini et max des champs de form
     * @param ServerRequestInterface $request
     * @return \App\Framework\Validator
     */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 5, 250)
            ->length('slug', 6, 50)
            ->unique('slug', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'))
            ->slug('slug');
    }
}
