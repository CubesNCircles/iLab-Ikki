<?php
require_once 'MysqlDB.php';
$loc_id = $_POST['loc_id'];

$db = new MysqlDb('localhost', 'root', 'root', 'ikki');

// Get old likes count
// add 1
// update this to db


$results = $db->query('SELECT * FROM locations WHERE loc_id =' . $loc_id);
$oldLikes = $results[0]['likes'];

$updateData = [
    'likes' => $oldLikes + 1,
];
$db->where( 'loc_id', $loc_id );
$updates = $db->update('locations', $updateData);
