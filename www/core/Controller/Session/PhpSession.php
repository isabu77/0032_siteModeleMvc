<?php

namespace Core\Controller\Session;

/**
 * classe de session sur $_SESSION
 */
class PhpSession implements SessionInterface, \ArrayAccess
{
    /**
     * démarrage de la session
     */
    private function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    /**
     * récupère la session entière
     * @param mixed $default
     * @return array
     */
    public function getSessionGlobale($default = null): ?array
    {
        $this->ensureStarted();

        if (isset($_SESSION)) {
            return $_SESSION;
        } else {
            return $default;
        }
    }

    /**
     * récupère une info en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $this->ensureStarted();
        if (isset($_SESSION[$key]) && $_SESSION[$key] && array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    /**
     * mettre une info en session
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->ensureStarted();
        
        $_SESSION[$key][] = $value;
    }

    /**
     * supprimer une info en session
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    public function offsetExists($offset): bool
    {
        $this->ensureStarted();
        return array_key_exists($offset, $_SESSION);
    }

    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }
}
