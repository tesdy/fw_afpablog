<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 20/10/18
 * Time: 22:00
 */

namespace App\Blog\Table;

use Framework\Database\Table;

class PostAuthorTable extends Table
{

    /**
     * Nom de la table
     * @var string
     */
    protected $table = 'creators';
}
