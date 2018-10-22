<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 12/10/18
 * Time: 14:22
 */

namespace Framework\Session;

/**
 * Class PHPSession
 * @package Framework\Session
 */
class ArraySession implements SessionInterface
{

    private $session = [];


    /**
     * Récupérer une information en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        }
        return $default;
    }

    /**
     * Ajout d'une information en session
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }

    /**
     * Supprime une information en session
     * @param string $key
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }
}
