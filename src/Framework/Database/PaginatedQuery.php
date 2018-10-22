<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 10/10/18
 * Time: 14:31
 */

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;

/**
 * Class PaginatedQuery
 * @package Framework\Database
 */
class PaginatedQuery implements AdapterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $countQuery;

    /**
     * @var string|null
     */
    private $entity;
    /**
     * @var array
     */
    private $params;

    /**
     *
     * PaginatedQuery constructor.
     * @param \PDO $pdo
     * @param string $query : Requête qui récupère X résultats
     * @param string $countQuery : Requête qui compte le nombre de résultat
     * @param string|null $entity
     * @param array $params
     */
    public function __construct(
        \PDO $pdo,
        string $query,
        string $countQuery,
        ?string $entity,
        array $params = []
    ) {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
        $this->params = $params;
    }


    /**
     * @return integer The number of result
     */
    public function getNbResults(): int
    {
        if (!empty($this->params)) {
            $query = $this->pdo->prepare($this->countQuery);
            $query->execute($this->params);
            return $query->fetchColumn();
        }
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * @param int $offset
     * @param int $length
     * @return array|\Traversable|void
     */
    public function getSlice($offset, $length): array
    {
        // (TYPECASTING)
//        $offset = (int)$offset;
//        $length = (int)$length;
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        foreach ($this->params as $key => $param) {
            $statement->bindParam($key, $param);
        }
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);
        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $statement->execute();
        return $statement->fetchAll();
    }
}
