<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 17/10/18
 * Time: 18:24
 */

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use App\Blog\Table\UserTable;
use App\Framework\Validator;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class UserCrudAction extends CrudAction
{
    protected $viewPath = '@blog/admin/users';

    protected $routePrefix = 'blog.user.admin';

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        UserTable $userTable
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->userTable = $userTable;
    }

    /**
     * Récupérer les différents champs à manipuler en base
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getParams(ServerRequestInterface $request, $objet = null): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return \in_array($key, ['name', 'pseudo', 'email']);
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
            ->required('name', 'pseudo', 'email')
            ->length('name', 6, 250)
            ->length('pseudo', 6, 250)
            ->length('email', 6, 50)
            ->unique('name', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'))
            ->unique('pseudo', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'))
            ->unique('email', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'));
    }
}
