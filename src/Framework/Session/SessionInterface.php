<?php

namespace Framework\Session;

/**
 * Interface SessionInterface
 * @package Framework\Session
 */
interface SessionInterface
{

    /**
     * Récupérer une information en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Ajout d'une information en session
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, $value): void;


    /**
     * Supprime une information en session
     * @param string $key
     */
    public function delete(string $key): void;
}
