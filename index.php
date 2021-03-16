<?php

require __DIR__ . "/sqlSupport.php";

function save_reqest($data, $connection)
{
    dbAddItem($connection, $_POST["name"], $_POST["amount"], $_POST["position"]);
}

function resolvePOST($connection)
{
    $invalide_fields = getInvalidFields($_POST);

    if (empty($invalide_fields)) {
        save_reqest($_POST, $connection);
        header("HTTP/1.1 303 See Other");
        header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    } else {

        print("invalid input:");
        var_dump($invalide_fields);
    }
}

function resolveGET($connection)
{
    require __DIR__ . "/templates/_header.php";
    $data = dbGetList($connection);
    $items = dbGetAllItems($connection);

    require __DIR__ . "/templates/_body.php";

    require __DIR__ . "/templates/_footer.php";
}

function isInt($value)
{
    return preg_match("/^[0-9]+$/", $value);
}

function getInvalidFields($parameters)
{
    $invalid_fields = [];
    if (!preg_match("/^[a-zA-Z ]+$/", $parameters["name"])) {
        $invalid_fields[] = "name";
    }
    if (!isInt($parameters["amount"])) {
        $invalid_fields[] = "amount";
    }
    if (!isInt($parameters["position"])) {
        $invalid_fields[] = "position";
    }
    return $invalid_fields;
}

function main()
{
    $connection = dbGetConnection();

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        resolvePOST($connection);
    } else if ($_SERVER['REQUEST_METHOD'] === "GET") {
        resolveGET($connection);
    }
    $connection->close();
}

main();
