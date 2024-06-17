<?php 

include 'db_config.php';
 class TimeRepository {
             

        public function __construct() {
            try {
                $this->db = new PDO("mysql:host=$b_host;dbname=$db_name", $db_user, $db_pass);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }


        } 

public function getTimeForEachTeams() {

    $sql_groups = "SELECT group_id, group_name, group_status FROM groups where group_info=1;";
    $stmt_groups = $this->db->prepare($sql_groups);
    $stmt_groups->execute();
    $groups_result = $stmt_groups->fetchAll(PDO::FETCH_ASSOC);
//	$group_status = $group_result["group_status"];

    $time_json = array();

    foreach ($groups_result as $group_row) {
 $group_status = $group_row["group_status"];

        $group_id = $group_row['group_id'];
        $group_name = $group_row['group_name'];


        $group_times = array();


	$sql_point_time = "SELECT time_sheets.time, points.point_name
	FROM time_sheets JOIN points ON time_sheets.point_id = points.point_id 
	WHERE time_sheets.group_id = (SELECT group_id FROM groups WHERE group_name=:group_name);";
            $stmt_point_time = $this->db->prepare($sql_point_time);
            $stmt_point_time->bindParam(':group_name', $group_name);

            $stmt_point_time->execute();
            $result_point_time = $stmt_point_time->fetchAll(PDO::FETCH_ASSOC);

            $times = array_column($result_point_time, 'time');
	     $point_names=array_column($result_point_time,  'point_name');

	    

            $point_times = array();

	for($i=0; $i<count($point_names); $i++){
		 $point_times[] = array("point" => $point_names[$i],"time"=>$times[$i]);
	}

            $group_times = array_merge($group_times, $point_times);


        $groups[] = array("group_name" => $group_name,"group_status"=>$group_status,"times"=>$group_times);
    }
	$time_json["groups"] = $groups;

    $reducedJson = json_encode($time_json);

    return $reducedJson;
}

} 
 
  ?>
