<?php

function dbGetConnection()
{
    require __DIR__ . '/db.php';
    $connection = new mysqli($db_config['server'], $db_config['login'], $db_config['password'], $db_config['database']);
    if ($connection->connect_error) {
        die("Could not connect to the database");
    }
    return $connection;
}

function dbGetAllItems($connection, $databaze = "items")
{
    $query = "SELECT * FROM $databaze";
    $query_result = $connection->query($query) or handle_error($connection);

    $items = array();
    while ($row = $query_result->fetch_assoc()) {
        $items[] = $row;
    }
    return $items;
}

function dbGetList($connection, $list = "list", $databaze = "items")
{
    $query =   "SELECT $databaze.id, $databaze.name, $list.position, $list.amount
                FROM $databaze 
                INNER JOIN $list ON $list.item_id = $databaze.id
                ORDER BY $list.position ASC
                ";
    $query_result = $connection->query($query) or handle_error($connection);

    $items = array();
    while ($row = $query_result->fetch_assoc()) {
        $items[] = $row;
    }

    return $items;
}

function dbGetListOrderedByName($connection, $order="ASC", $list = "list", $databaze = "items"){
    $query =   "SELECT $databaze.id, $databaze.name, $list.position, $list.amount
    FROM $databaze 
    INNER JOIN $list ON $list.item_id = $databaze.id
    ORDER BY $databaze.name $order
    ";
    $query_result = $connection->query($query) or handle_error($connection);

    $items = array();
    while ($row = $query_result->fetch_assoc()) {
    $items[] = $row;
}

return $items;
}

function tryAddItemToItems($connection, $name)
{
    $stmt = new mysqli_stmt($connection, "INSERT INTO `items` (name) VALUES ? ON DUPLICATE KEY UPDATE id=id");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    return $stmt->get_result();
}

function AddItemToList($connection, $name, $amount, $position)
{
    $query =
    "INSERT INTO list (item_id, amount, position) 
    VALUES ((SELECT id from items WHERE name='$name'), $amount, $position)
    ON DUPLICATE KEY UPDATE id=id
    ";
    return $connection->query($query) or handle_error($connection);
}

function dbAddItem($connection, $name = "porks", $amount = "5", $position = "56")
{
    $q1result = tryAddItemToItems($connection, $name);
    $q2result = AddItemToList($connection, $name, $amount, $position);
    // var_dump($q1result);
    // var_dump($q2result);
}

function handle_error($connection)
{
    die("Query error: " . $connection->error);
}


function json_response($payload = null, $error = '')
{
    header('Content-Type: application/json');
    $res = ['ok' => !$error];
    if ($error) $res['error'] = $error;
    if ($payload) $res['payload'] = $payload;
    echo json_encode($res);
    exit;
}

function dbUpdateList($connection, $list){
    $i = 1;
    foreach ($list as $item){
        var_dump($item);
        $itemID = $item["id"];
        $query = "UPDATE list SET position = '$i' WHERE item_id=$itemID;";
        $query_result = $connection->query($query) or handle_error($connection);
        $i++;
    }
    json_response($query_result);
}

function safe_get(array $params, string $name, $default = null, $regexCheck = null)
{
    if (!array_key_exists($name, $params)) return $default;
    if ($regexCheck && !preg_match($regexCheck, $params[$name])) return $default;
    return $params[$name];
}

function sql_post_change()
{
    $connection = dbGetConnection();
    $id = (int)safe_get($_GET, 'id', null, '/^[0-9]+$/');
    $amount = (int)safe_get($_GET, 'amount', null, '/^[0-9]+$/');
    
    $query = "UPDATE list SET amount = '$amount' WHERE item_id=$id;";
    $query_result = $connection->query($query) or handle_error($connection);
    
    json_response($query_result);
}

function sql_delete_default()
{
    $id = (int)safe_get($_GET, 'id', null, '/^[0-9]+$/');
    $connection = dbGetConnection();
    $query = "DELETE FROM list WHERE item_id=$id;";
    $query_result = $connection->query($query) or handle_error($connection);
    json_response($query_result);
}

function sql_post_switch()
{
    $id1 = (int)safe_get($_GET, 'id1', null, '/^[0-9]+$/');
    $id2 = (int)safe_get($_GET, 'id2', null, '/^[0-9]+$/');
    $connection = dbGetConnection();
    $data = dbGetList($connection);
    $newPosition1 = 0;
    $newPosition2 = 0;
    foreach ($data as $item) {
        if ($item["id"] == $id1) {
            $newPosition2 = $item["position"];
        }
        if ($item["id"] == $id2) {
            $newPosition1 = $item["position"];
        }
    }
    
    $query = "UPDATE list SET position = '$newPosition2' WHERE item_id=$id2;";
    $query_result = $connection->query($query) or handle_error($connection);
    
    $query = "UPDATE list SET position = '$newPosition1' WHERE item_id=$id1;";
    $query_result = $connection->query($query) or handle_error($connection);
    
    json_response($query_result);
}

function sql_sort($order="ASC"){
    $connection = dbGetConnection();
    $newList = dbGetListOrderedByName($connection, $order);
    return dbUpdateList($connection, $newList);
}

function sql_post_sortdown(){
    return sql_sort("DESC");
}

function sql_post_sortup(){
    return sql_sort("ASC");
}
