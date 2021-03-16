<?php


function run()
{
    require __DIR__ . "/sqlSupport.php";
    $action = safe_get($_GET, 'action', 'default', '/^[a-z_]+$/');
    $method = strtolower($_SERVER['REQUEST_METHOD']);
    $target = "sql_${method}_${action}";

    if (function_exists($target))
        $target();
}


run();
