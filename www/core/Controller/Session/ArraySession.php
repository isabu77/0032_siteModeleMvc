<?php

namespace Core\Controller\Session;

/**
 * Classe de session dans un tableau
 */
class ArraySession implements SessionInterface
{
    private $session = [];

    /**
     * récupère une info en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */

    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
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
        $this->session[$key][] = $value;
    }

    /**
     * mettre une info en session
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }
}
