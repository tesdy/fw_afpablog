<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 10/10/18
 * Time: 03:47
 */

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Framework\Database\Table;
use Pagerfanta\Pagerfanta;

/**
 * Class PostTable
 * @package App\Blog\Table
 */
class PostTable extends Table
{
    /**
     * Nom de l'entité
     * @var string
     */
    protected $entity = Post::class;

    /**
     * Nom de la table
     * @var string
     */
    protected $table = 'posts';

    /**
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            'SELECT p.*, c.name as category_name, c.slug as category_slug
                    FROM posts p 
                    LEFT JOIN categories c 
                    ON p.category_id = c.id
                    ORDER BY p.created_at DESC',
            "SELECT COUNT(p.id) FROM {$this->table} p",
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            'SELECT p.*, c.name as category_name, c.slug as category_slug
                    FROM posts p 
                    LEFT JOIN categories c 
                    ON p.category_id = c.id
                    WHERE p.category_id = :category
                    ORDER BY p.created_at DESC',
            "SELECT COUNT(id) FROM {$this->table} WHERE category_id = :category",
            $this->entity,
            ['category' => $categoryId]
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail(
            'SELECT p.*, c.name category_name, c.slug category_slug, u.pseudo user_pseudo
        FROM posts as p 
        LEFT JOIN categories c 
        ON c.id = p.category_id
        LEFT JOIN users u 
        ON u.id = p.user_id
        WHERE p.id = ?',
            [$id]
        );
    }

    /**
     * @return string
     */
    protected function paginationQuery(): string
    {
        return "SELECT p.id, p.name, c.name category_name, c.slug as category_slug, p.created_at, p.updated_at 
        FROM {$this->table} p
        LEFT JOIN categories c
        ON p.category_id = c.id
        ORDER BY p.updated_at DESC";
    }

    public function findWithCategoryAndCreator(int $id)
    {
        $creators = $this->fetchOrFailAll('SELECT CONCAT(cr.firstname, " ",cr.lastname) as creator_name
        FROM creators cr 
        LEFT JOIN post_creator pc ON cr.id = pc.creator_id
        LEFT JOIN posts p ON pc.post_id = p.id
        WHERE p.id = ?',
            [$id]
        );
        $postAndCategory = $this->fetchOrFail(
            'SELECT p.*, c.name category_name, c.slug category_slug, u.pseudo user_pseudo
        FROM posts as p 
        LEFT JOIN categories c ON c.id = p.category_id
        LEFT JOIN users u ON u.id = p.user_id
        WHERE p.id = ?',
            [$id]
        );
        $i = 0;
        $creatorName = array();
        foreach ($creators as $creator => $name) {
            $postAndCategory->creatorName[$i] = $name->creator_name;
            $i++;
        }
        return $postAndCategory;

    }
}
