<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 15/10/18
 * Time: 01:53
 */

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Request;

class CategoryShowAction
{

    /**
     * Gestion de l'affichage
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var CategoryTable
     */
    private $categoryTable;

    use RouterAwareAction;

    /**
     * BlogAction constructor.
     * @param RendererInterface $renderer
     * @param PostTable $postTable
     * @param CategoryTable $categoryTable
     */
    public function __construct(
        RendererInterface $renderer,
        PostTable $postTable,
        CategoryTable $categoryTable
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
    }

    /**
     * Méthode afin que BlogAction soit callable par une string
     * Vérifie si slug alors alors ->show()
     * Sinon ->index()
     * @param Request $request
     * @return string
     * @throws \App\Framework\Database\NoRecordException
     */
    public function __invoke(Request $request)
    {
        $params = $request->getQueryParams();
        $category = $this->categoryTable->findBy('slug', $request->getAttribute('slug'));
        // [php -v >= PHP 7.0] opérateur Null coalescent (??) ternaire
        $posts = $this->postTable->findPaginatedPublicForCategory(12, $params['p'] ?? 1, $category->id);
        $categories = $this->categoryTable->findAll();
        $page = $params['p'] ?? 1;
        // vue index et Toutes les infos de tous les articles
        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'category', 'page'));
    }
}
