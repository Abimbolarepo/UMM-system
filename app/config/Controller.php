<?php

class Controller
{
    /**
     * Load a view
     */
    protected function view($view, $data = [])
    {
        extract($data);

        $path = "../app/views/" . $view . ".php";

        if(file_exists($path))
        {
            require_once $path;
        }
        else
        {
            die("View does not exist.");
        }
    }

    /**
     * Redirect helper
     */
    protected function redirect($url)
    {
        header("Location: " . APP_URL . "/" . $url);
        exit;
    }
}