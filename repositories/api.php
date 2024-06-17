<?php
include 'db_config.php';
class Api {
            
    public $db;
    public $foundCorrect = 0;
 
     public function __construct() {
         try {
            $this->db = new PDO("mysql:host=$b_host;dbname=$db_name", $db_user, $db_pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         } catch (PDOException $e) {
             die("Connection failed: " . $e->getMessage());
         }
     }

    public function getApi($group_name, $point_name) {
        if (!empty($group_name) && !empty($point_name)) {
            // Επιλογή του point_id βάσει του ονόματος του σημείου
            $point_client = "SELECT point_id FROM points WHERE point_name=:point_name;";
            $stmt_client = $this->db->prepare($point_client);
            $stmt_client->execute([':point_name' => $point_name]);
            $result_client = $stmt_client->fetch(PDO::FETCH_ASSOC);
    
            $pointID = $result_client['point_id'];
 
    
            // Επιλογή της ομάδας βάσει του ονόματος της ομάδας
            $group_name_client = "SELECT group_id, group_info FROM groups WHERE group_name=:group_name;";
            $stmt_group_name_client = $this->db->prepare($group_name_client);
            $stmt_group_name_client->execute([':group_name' => $group_name]);
            $result_group_name = $stmt_group_name_client->fetch(PDO::FETCH_ASSOC);
    
            $groupID = $result_group_name['group_id'];
            $groupINFO = $result_group_name['group_info'];
    

		date_default_timezone_set('Europe/Athens');
		$current_time=date('H:i:s');

    
            // Επιλογή της διαδρομής βάσει του group_id
            $route = "SELECT route_name FROM routes WHERE route_id=(select route_id from groups where group_id= :group_id);";
            $stmt_route = $this->db->prepare($route);
            $stmt_route->execute([':group_id' => $groupID]);
            $result_route = $stmt_route->fetch(PDO::FETCH_ASSOC);
    
            $routeName = $result_route['route_name'];
    
            $route_point = "SELECT point_id FROM refs WHERE route_name=:route_name;";
            $stmt_route_point = $this->db->prepare($route_point);
            $stmt_route_point->execute([':route_name' => $routeName]);
            $result_route_point = $stmt_route_point->fetchAll(PDO::FETCH_ASSOC);

            $group_route=[];
            foreach ($result_route_point as $row) {
                $group_route[] = $row['point_id'];
            }

            $length = count($group_route);
                          
            if ($groupINFO == 1) {
                $previousPointSql = "SELECT point_id FROM previous_points WHERE group_id=:group_id;";
                $stmt_previous_point = $this->db->prepare($previousPointSql);
                $stmt_previous_point->execute([':group_id' => $groupID]);
                $result_previous_point = $stmt_previous_point->fetch(PDO::FETCH_ASSOC);


                $previous_point = $result_previous_point['point_id'];
                $success = 0;

                if ($previous_point == -1 && $pointID == $group_route[0]) {
                    $new_point = $group_route[0];
                    $update = "UPDATE previous_points SET point_id = $new_point WHERE group_id = :group_id;";
                    $stmt_update = $this->db->prepare($update);
                    $stmt_update->execute([':group_id' => $groupID]);
                    $success = 1;

$sql = "INSERT INTO time_sheets (group_id, point_id, time) VALUES (:group_id, :point_id, :current_time)";
$stmt = $this->db->prepare($sql);
$stmt->execute([
    ':group_id' => $groupID,
    ':point_id' => $pointID,
    ':current_time' => $current_time
]);

                } else {
                    for ($i = 1; $i < $length; $i++) {
                        if ($previous_point ==  $group_route[$i-1] && $pointID == $group_route[$i]) {
                            $new_point = $group_route[$i];
                            $success = 1;
                            $update = "UPDATE previous_points SET point_id = $new_point WHERE group_id = :group_id;";
                            $stmt_update = $this->db->prepare($update);
                            $stmt_update->execute([':group_id' => $groupID]);

$sql = "INSERT INTO time_sheets (group_id, point_id, time) VALUES (:group_id, :point_id, :current_time)";
$stmt = $this->db->prepare($sql);
$stmt->execute([
    ':group_id' => $groupID,
    ':point_id' => $pointID,
    ':current_time' => $current_time
]);

                            break;
                        }
                        
                    }
                }
                
                
                $json = array(
                    "success" => $success,
		   "routeName" => $routeName,
		    "group_info" =>$groupINFO
                ); 

                $reducedJson = json_encode($json);
                echo $reducedJson;
               
                
            } else {



		 $json = array(
                    "success" =>0,
                   "routeName" => $routeName,
                    "group_info" =>$groupINFO
                ); 

                $reducedJson = json_encode($json);
                echo $reducedJson;


            }
        }
    }
}
    

?>
