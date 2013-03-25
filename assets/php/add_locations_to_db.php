<?php
    require_once 'MysqlDB.php';
    $locations = $_POST['locations'];

    $db = new MysqlDb('localhost', 'root', 'root', 'ikki');

    $dbLocations = $db->get('locations');

    $n = 0;
    foreach ( $locations as $location )
    {
        // Check uniqueness
        if ( $dbLocations[$n]['loc_id'] != (int)$location['id'] )
        {
            $data = [
                'title' => $location['title'],
                'distance' => $location['distance'],
                'loc_id' => $location['id'],
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
                'url' => $location['url'],
                'mobile_url' => $location['mobileurl'],
                'distance' => $location['distance'],
                'type' => $location['type'],
                'likes' => 0,
            ];
            if ( $db->insert('locations', $data) ) echo 'Insert success!';
        }
        $n++;
    }
