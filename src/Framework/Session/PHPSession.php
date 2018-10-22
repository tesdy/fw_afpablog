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
class PHPSession implements SessionInterface
{

    /**
     * Vérifie que la session est démarrée
     */
    private function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Récupérer une information en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $this->ensureStarted();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
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
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Supprime une information en session
     * @param string $key
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }
}
