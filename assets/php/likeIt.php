<?php
require_once 'MysqlDB.php';
$loc_id = $_POST['loc_id'];

$db = new MysqlDb('localhost', 'root', 'root', 'ikki');


$results = $db->query('SELECT * FROM locations WHERE loc_id =' . $loc_id);
var_dump($results);

// $db->where( 'loc_id', $loc_id );
// $userData = [
//     'likes' =>
// ];
// $db->update();
