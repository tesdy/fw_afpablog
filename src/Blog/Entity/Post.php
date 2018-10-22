<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 10/10/18
 * Time: 16:20
 */

namespace App\Blog\Entity;

class Post
{
    public $id;

    public $name;

    public $slug;

    public $content;

    public $created_at;

    public $updated_at;

    public $category_name;

    public $user_name;

    public $image;

    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = new \DateTime($this->created_at);
        }
        if ($this->updated_at) {
            $this->updated_at = new \DateTime($this->updated_at);
        }
    }

    public function setCreatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->created_at = new \DateTime($datetime);
        }
    }

    public function setUpdatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->updated_at = new \DateTime($datetime);
        }
    }

    public function getThumb()
    {
        if ($this->image) {
            ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/public/uploads/posts/' . $filename . '_thumb.' . $extension;
        }
        return '/public/no-image.png';
    }

    public function getFullImage()
    {
        if ($this->image) {
            ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
            return '/public/uploads/posts/' . $filename . '.' . $extension;
        }
        return '/public/no-image.png';
    }
}
