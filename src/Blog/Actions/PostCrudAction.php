<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 11/10/18
 * Time: 01:33
 */

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\PostUpload;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use App\Blog\Table\UserTable;
use App\Framework\Database\NoRecordException;
use App\Framework\Validator;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Administration des articles
 *
 * Class AdminBlogAction
 * @package App\Blog\Actions
 */
class PostCrudAction extends CrudAction
{

    protected $viewPath = '@blog/admin/posts';


    protected $routePrefix = 'blog.admin';

    /**
     * @var CategoryTable
     */
    private $categoryTable;

    /**
     * @var UserTable
     */
    private $userTable;
    /**
     * @var PostUpload
     */
    private $postUpload;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable,
        UserTable $userTable,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
        $this->userTable = $userTable;
        $this->postUpload = $postUpload;
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $post = $this->table->find($request->getAttribute('id'));
        } catch (NoRecordException $e) {
        }
        $this->postUpload->delete($post->image);
        return parent::delete($request);
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        $params['users'] = $this->userTable->findList();
        $params['categories']['12365478'] = 'catégorie fake';
        return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime();
        return $post;
    }

    /**
     * Récupérer les différents champs à manipuler en base
     * @param ServerRequestInterface $request
     * @param Post $post
     * @return array
     */
    protected function getParams(ServerRequestInterface $request, $post): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        // uploader le fichier
        $params['image'] = $this->postUpload->upload($params['image'], $post->image);
        $params = array_filter($params, function ($key) {
            return \in_array($key, ['name', 'content', 'slug', 'created_at', 'category_id', 'image', 'user_id']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Définition de la longueur mini et max des champs de form
     * @param ServerRequestInterface $request
     * @return \App\Framework\Validator
     */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        $validator = parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 4, 250)
            ->length('slug', 6, 50)
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->dateTime('created_at')
            ->extension('image', ['jpg', 'png'])
            ->slug('slug');

        if ($request->getAttribute('id') === null) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}
