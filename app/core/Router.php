<?php

class Router
{

    private $routes = [];

    /**
     * Register GET route
     */
    public function get($uri,$action)
    {
        $this->routes['GET'][$uri] = $action;
    }

    /**
     * Register POST route
     */
    public function post($uri,$action)
    {
        $this->routes['POST'][$uri] = $action;
    }

    /**
     * Dispatch Route
     */
    public function dispatch($method,$uri)
    {

        $uri = trim($uri,'/');

        if(isset($this->routes[$method][$uri]))
        {

            list($controller,$methodName) =
                explode('@',$this->routes[$method][$uri]);

            require_once "../app/controllers/$controller.php";

            $controller = new $controller();

            call_user_func([$controller,$methodName]);

            return;

        }

        http_response_code(404);

        echo "404 Page Not Found";

    }

}