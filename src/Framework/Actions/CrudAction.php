<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 14/10/18
 * Time: 17:40
 */

namespace Framework\Actions;

use App\Framework\Validator;
use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
{


    /**
     * format de date pour la base
     * @var
     */
    private $dateFormat;

    /**
     * Gestion de l'affichage
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Gestion des routes
     * @var Router
     */
    private $router;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var string
     */
    protected $messages = [
        'create' => 'L\'élément a bien été créé.',
        'edit' => 'L\'élément a bien été modifié.'
    ];

    use RouterAwareAction;

    /**
     * BlogAction constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param $table
     * @param FlashService $flash
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
        $this->dateFormat = date('Y-m-d H:i:s');
        $this->flash = $flash;
    }

    /**
     * Méthode afin que BlogAction soit callable par une string
     * Carrefour des routes
     * Si ID alors ->edit()
     * Si Fin d'url = 'new' -> insert()
     * Si Method 'Delete' -> delete
     * Donc Rien ->index()
     * @param Request $request
     * @return string
     * @throws \App\Framework\Database\NoRecordException
     */
    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
        // SI Delete méthode genre Ajax et géré par FastRoute)
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        // SI Create
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        // SI Update
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }

        return $this->index($request);
    }

    /**
     * Récupérer / afficher la liste des éléments
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findPaginated(12, $params
            ['p'] ?? 1); // [php -v >= PHP 7.0] opérateur Null coalescent (??) ternaire
        // vue index et Toutes les infos de tous les articles
        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * Editer un élément
     * @param Request $request
     * @return ResponseInterface|string
     * @throws \App\Framework\Database\NoRecordException
     */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            // récupérer les champs à mettre à jour en base
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($item->id, $this->getParams($request, $item));
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $params = $request->getParsedBody();
            $params['id'] = $item->id;
            $item = $params;
        }
        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Ajout d'un nouvel élément
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            // Ajout nom, slug, content, et date de creation & de updated_ad
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $item = $request->getParsedBody();
            $errors = $validator->getErrors();
        }
        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * @param Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request): ResponseInterface
    {
        $this->table->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Récupérer les différents champs à manipuler en base
     * @param Request $request
     * @param $item
     * @return array
     */
    protected function getParams(Request $request, $item): array
    {
        return array_filter(
            $request->getParsedBody(),
            function ($key) {
                return \in_array($key, []);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Définition de la longueur mini et max des champs de form
     * @param Request $request
     * @return Validator
     */
    protected function getValidator(Request $request): Validator
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    /**
     * @return array
     */
    protected function getNewEntity()
    {
        return [];
    }

    /**
     * Permet de traiter les paramètres pour la vue
     * @param array $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
