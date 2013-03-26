<?php
    // require_once 'MysqlDB.php';
    // $db = new MysqlDb('localhost', 'root', 'root', 'ikki');

    require_once 'PDOclass.php';
    $db = new DBquery;

    $locations = $_POST['locations'];
    $dbLocations = $db->selectAll('locations');

    $n = 0;
    foreach ( $locations as $location )
    {
        // var_dump($location['id']);
        // var_dump($dbLocations[$n]['loc_id']);

        // Check uniqueness
        if ( $dbLocations[$n]['loc_id'] != $location['id'] )
        {
            var_dump( 'dbloc:' . $dbLocations[$n]['loc_id'] . ' | loc:' . $location['id'] );
            $data = [
                'title' => $location['title'],
                'loc_id' => $location['id'],
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
                'type' => $location['type'],
                'url' => $location['url'],
                'mobile_url' => $location['mobileurl'],
                'distance' => $location['distance'],
                'likes' => 0,
            ];
            if ( $db->insert('locations', $data) ) echo 'Insert success!';
        }
        $n++;
    }
