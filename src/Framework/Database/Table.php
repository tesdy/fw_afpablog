<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 14/10/18
 * Time: 16:38
 */

namespace Framework\Database;

use App\Framework\Database\NoRecordException;
use Pagerfanta\Pagerfanta;
use PDO;

/**
 * Class Table
 * @package Framework\Database
 */
class Table
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * Nom de la table
     * @var string
     */
    protected $table;

    /**
     * Nom de l'entité
     * @var string|null
     */
    protected $entity;


    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * PostTable constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Pagination des éléments
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            'SELECT COUNT(id) FROM ' . $this->table,
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Requête pour Pagination
     * @return string
     */
    protected function paginationQuery(): string
    {
        return 'SELECT * FROM ' . $this->table;
    }

    /**
     * Récupère tous les enregistrements
     * @return mixed
     */
    public function findAll()
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table}");
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        return $query->fetchAll();
    }

    /**
     * Requête une liste clé/valeur des enregistrements
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(\PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Récupère une ligne en fonction d'un champ
     * @param string $field
     * @param string $value
     * @return mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
    }

    /**
     * Récupère un élément à partir de l'id
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ?;", [$id]);
    }

    /**
     * Récupère le nombre d'enregistrement
     * @return int
     */
    public function count(): int
    {
        return $this->fetchColumn("SELECT COUNT(id) FROM {$this->table}");
    }

    /**
     * Met à jour un enregistrement
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $query = $this->pdo->prepare("UPDATE {$this->table} SET {$fieldQuery} WHERE id = :id");
        return $query->execute($params);
    }

    /**
     * Ajouter un élément
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
        $query = $this->pdo->prepare("INSERT INTO {$this->table} ({$fields}) VALUES ({$values})");
        return $query->execute($params);
    }

    /**
     * Supprimer un élément
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $query->execute([$id]);
    }

    /**
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Vérifie qu'un enregistrement existe
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Permet d'éxécuter une requête et récupérer le 1er résultat
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            // vue show et toutes les infos de l'article
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    protected function fetchOrFailAll(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        $record = $query->fetchAll();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * Récupère la première colonne
     * @param string $query
     * @param array $params
     * @return mixed
     */
    protected function fetchColumn(string $query, $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            // vue show et toutes les infos de l'article
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchColumn();
    }
}
