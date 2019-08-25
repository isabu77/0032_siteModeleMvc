<?php

namespace Core\Controller\Session;

use Core\Controller\Session\SessionInterface;

class FlashService
{
    private $session;

    /**
     * constructeur
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * ajout d'un message dans la session success
     */
    public function addSuccess(string $message)
    {
        $this->session->set("success", $message);
    }

    /**
     * ajout d'un message dans la session error
     */
    public function addAlert(string $message)
    {
        $this->session->set("alert", $message);
    }

    /**
     * retourne les messages de la session $key
     * puis supprime la session
     */
    public function getMessages(string $key): array
    {
        $message = $this->session->get($key, []);
        $this->session->delete($key);
        return $message;
    }

    /**
     * y-a-t-il un message dans la session $key ?
     */
    public function hasMessage(string $key): bool
    {
        if ($this->session->get($key, false)) {
            return true;
        }
        return false;
    }
}
