<?php
    require_once 'PDOclass.php';
    $locations = $_POST['locations'];

    $db = new DBquery;

    $dbLocations = $db->getAll('locations');
    var_dump($dbLocations[0]['loc_id']);
    $n = 0;
    foreach ( $locations as $location )
    {
        // if $location['id'] doesnt exist in $dbLocations[n]['loc_id']
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
            ];
            echo ( $db->insert('locations', $data) ) ? 'true' : 'false';
        }

        $n++;

    }
