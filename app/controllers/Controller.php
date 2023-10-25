<?php

namespace App\Controllers;

class Controller {

    public function view(string $viewName, array $data = [], $layout = "layout") {
        $viewNameAux = str_replace(".","/", $viewName);
        $layoutAux = str_replace(".","/", $layout);
        $layoutRoute = "../resources/views/layouts/$layoutAux.php";
        $viewRoute = "../resources/views/$viewNameAux.php";

        if(!file_exists($layoutRoute)) {
            echo "The layout $layoutRoute don't exists.";
            return;
        }

        if(!file_exists($viewRoute)) {
            echo "The view $viewRoute dont't exists.";
            return;
        }

        foreach($data as $key => $value) {
            $$key = $value;
        }

        ob_clean();
        ob_start();
        include_once $viewRoute;
        $content = ob_get_clean();
        include_once $layoutRoute;
    }
}