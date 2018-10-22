<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Gestion du contenu de Blog
 * Class BlogAction
 * @package App\Blog\Action
 */
class PostShowAction
{

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
     * @var PostTable
     */
    private $postTable;

    use RouterAwareAction;

    /**
     * BlogAction constructor.
     * @param RendererInterface $renderer
     * @param PostTable $postTable
     * @param Router $router
     */
    public function __construct(
        RendererInterface $renderer,
        PostTable $postTable,
        Router $router
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
    }

    /**
     * Méthode afin que BlogAction soit callable par une string
     * Vérifie si slug alors alors ->show()
     * Sinon ->index()
     * @param Request $request
     * @return string
     */
    public function __invoke(Request $request)
    {
        $slug = $request->getAttribute('slug');
//        $post = $this->postTable->find($request->getAttribute('id'));
        // Ou une réquête qui récupère Article et Catégories :
        $post = $this->postTable->findWithCategoryAndCreator($request->getAttribute('id'));

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}
