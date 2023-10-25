<?php
namespace Routes;

use App\Controllers\HomeController;
use Lib\Router;

Router::get("/", [HomeController::class, "index"]);

Router::dispatch();