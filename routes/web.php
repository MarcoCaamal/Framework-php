<?php
namespace Routes;

use App\Controllers\HomeController;
use Lib\Router;

Router::get("/", [HomeController::class, "index"]);

Router::get("/courses/:id/categories/:idCategory", function ($id, $idCategory) { 
    return "El curso es $id con la categoria $idCategory";
});

Router::get("/courses/:id", function ($id) {
    return "Courses Page con el id: $id";
 });

Router::dispatch();