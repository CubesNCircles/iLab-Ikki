<?php
    require_once 'PDOclass.php';
    $locations = $_POST['locations'];

    $db = new DBquery;

    $dbLocations = $db->getAll('locations');
    $n = 0;
    foreach ( $locations as $location )
    {
        // Check uniqueness
        if ( $dbLocations[$n]['loc_id'] != $location['id'] )
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
            echo ( $db->insert('locations', $data) ) ? 'true' : 'false';
        }
        $n++;
    }
