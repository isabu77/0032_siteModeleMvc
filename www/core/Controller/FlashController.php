<?php

namespace core\Controller;

class FlashController
{
    private $message;
    private $sessionKey = "flash";

    /**
     * constructeur
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * ajout d'un message dans la session success
     */
    public function success(string $message)
    {
        $flash = $this->sessionGet($this->sessionKey);
        $flash['success'][] = $message;
        $this->sessionSet($this->sessionKey, $flash);
    }

    /**
     * ajout d'un message dans la session error
     */
    public function error(string $message)
    {
        $flash = $this->sessionGet($this->sessionKey);
        $flash['error'][] = $message;
        $this->sessionSet($this->sessionKey, $flash);
    }

    /**
     * retourne les messages de la session $key
     */
    public function get(string $type)
    {
        if (is_null($this->message)) {
            $this->message = $this->sessionGet($this->sessionKey, []);
            $this->sessionDelete($this->sessionKey);
        }
        if (array_key_exists($type, $this->message)) {
            return $this->message[$type];
        }
    }

    public function sessionGet(string $key, $default = []): ?array
    {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    public function sessionSet(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * y-a-t-il un message dans la session $key ?
     */
    public function sessionDelete(string $key)
    {
        unset($_SESSION[$key]);
    }
}
