<?php

namespace Lib;

class Router {
    private static array $routes = [];

    public static function get(string $uri, callable|string|array $callback = null) {
        $uri = trim($uri,"/");
        self::$routes['GET'][$uri] = $callback;
    }

    // Add a route to the array of routes
    public static function post(string $uri, callable|string|array $callback = null) {
        $uri = trim($uri,'/');
        self::$routes['POST'][$uri] = $callback;
    }

    public static function dispatch(){
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach(self::$routes[$method] as $route => $callback) {
            // If the route has params
            if(strpos($route, ':') !== false) {
                $route = preg_replace('#:[a-zA-Z0-9]+#', '([a-zA-Z0-9]+)', $route);
            }

            // If the route matches
            if(!preg_match("#^$route$#", $uri, $matches)) {
                continue;
            }

            $params = array_slice($matches,1);
            $response = null;

            // If is array of controller
            if(is_array($callback) && isset($callback[0]) && isset($callback[1])) {
                $controller = new $callback[0];
                $response = $controller->{$callback[1]}(...$params);
            }
            
            if(is_callable($callback)) { 
                $response = $callback(...$params);
            }
            
            if(is_string($response)) { 
                echo $response;
            }

            if(is_array($response) || is_object($response)) {
                header('Content-Type: application/json');
                echo json_encode($response);
            }
            return; 
        }

        echo '404 Not Found';
    }
}