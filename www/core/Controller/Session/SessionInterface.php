<?php

namespace Core\Controller\Session;

interface SessionInterface
{
    /**
     * récupère une info en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */

    public function get(string $key, $default = null);

    /**
     * mettre une info en session
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * mettre une info en session
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}
