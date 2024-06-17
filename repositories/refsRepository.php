<?php
include 'db_config';
class RefsRepository {
             
    public $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=$b_host;dbname=$db_name", $db_user, $db_pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }



    }

public function getRefs($route_name) {
    try {
        // Query to fetch point_id from refs table
        $sql_route_ref = "SELECT point_id FROM refs WHERE route_name = :route_name";
        $stmt_route_ref = $this->db->prepare($sql_route_ref);
        $stmt_route_ref->bindParam(':route_name', $route_name);
        $stmt_route_ref->execute();
        $resultroute_ref = $stmt_route_ref->fetchAll(PDO::FETCH_ASSOC);

        $route_points = array();

        // Loop through each point_id obtained from the refs table
        foreach ($resultroute_ref as $row) {
            // Fetch additional information about each point from the points table
            $point_id = $row['point_id'];
            $sql_point = "SELECT point_name FROM points WHERE point_id = :point_id";
            $stmt_point = $this->db->prepare($sql_point);
            $stmt_point->bindParam(':point_id', $point_id);
            $stmt_point->execute();
            $result_point = $stmt_point->fetch(PDO::FETCH_ASSOC);

            // Add the fetched point_name to the route_points array
            if ($result_point) {
                $route_points[] = $result_point['point_name'];
            }
        }

        // Encode the route_points array to JSON
//        $json_route_points = json_encode($route_points);

        // Return the JSON-encoded route points
        return $route_points;

    } catch (PDOException $e) {
        // Handle any errors
        die("Error: " . $e->getMessage());
    }
}
	


public function postRefs($route_id, $points) {
    try {
        // Retrieve the route_name based on the route_id
        $sql_route = "SELECT route_name FROM routes WHERE route_id = :route_id";
        $stmt_route = $this->db->prepare($sql_route);
        $stmt_route->bindParam(':route_id', $route_id);
        $stmt_route->execute();
        $result_route = $stmt_route->fetch(PDO::FETCH_ASSOC);

        if ($result_route) {
            $route_name = $result_route['route_name'];

            // Insert the points into the refs table
            foreach ($points as $point_name) {
                // Ανάκτηση point_id για το σημείο
                $sql_point = "SELECT point_id FROM points WHERE point_name = :point_name";
                $stmt_point = $this->db->prepare($sql_point);
                $stmt_point->bindParam(':point_name', $point_name);
                $stmt_point->execute();
                $result_point = $stmt_point->fetch(PDO::FETCH_ASSOC);

                if ($result_point) {
                    $point_id = $result_point['point_id'];

                    // Εισαγωγή στον πίνακα refs
                    $sql_refs = "INSERT INTO refs (route_name, point_id) VALUES (:route_name, :point_id)";
                    $stmt_refs = $this->db->prepare($sql_refs);
                    $stmt_refs->bindParam(':route_name', $route_name);
                    $stmt_refs->bindParam(':point_id', $point_id);
                    $stmt_refs->execute();
                } else {
                    echo "Δεν βρέθηκε αντιστοίχιση για το σημείο: $point_name";
                }
            }
        } else {
            echo "Δεν βρέθηκε αντιστοίχιση για την διαδρομή: $route_id";
        }
    } catch(PDOException $e) {
        echo "Σφάλμα κατά την εισαγωγή δεδομένων: " . $e->getMessage();
    }
}
	
public function deleteRefs($point_id, $route_name) {
    try {
        $sql = "DELETE FROM refs WHERE point_id = :point_id AND route_name = :route_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':point_id', $point_id);
        $stmt->bindParam(':route_name', $route_name);
        $stmt->execute();
        echo "Οι αναφορές για το point_id $point_id και το route_name '$route_name' διαγράφηκαν επιτυχώς.";
    } catch(PDOException $e) {
        echo "Σφάλμα κατά τη διαγραφή των αναφορών: " . $e->getMessage();
    }
}









}


?>
