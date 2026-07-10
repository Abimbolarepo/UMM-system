<?php

class Controller
{
    /**
     * Load a view
     */
    protected function view($view, $data = [])
    {
        extract($data);

        $file = __DIR__ . "/../views/" . $view . ".php";

        if (file_exists($file)) {

            require_once $file;

        } else {

            die("View not found: " . $view);

        }
    }

    /**
     * Redirect helper
     */
    protected function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }
}