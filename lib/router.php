<?php

namespace Lib;

class Router {
    private static array $routes = [];

    public static function get(string $uri, callable|string $callback = null) {
        self::$routes['GET'][$uri] = $callback;
    }

    public static function post(string $uri, callable|string $callback = null) {
        self::$routes['POST'][$uri] = $callback;
    }

    
}