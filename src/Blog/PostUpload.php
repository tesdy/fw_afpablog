<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 18/10/18
 * Time: 15:42
 */

namespace App\Blog;

use Framework\Upload;

class PostUpload extends Upload
{
    protected $path = 'public/uploads/posts';

    protected $formats = [
        'thumb' => [320, 180]
    ];
}
