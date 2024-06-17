<?php 

class StartStopTimeRepository {
             
    public $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=game", "game", "game16");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

	}


public function post_start($datajson){
    date_default_timezone_set('Europe/Athens');
                $current_time=date('H:i:s');
$group_status=$datajson['group_status'];
	

	$sql = "INSERT INTO start_stop_time (group_status,start_time) VALUES (:group_status,:current_time);";
	$stmt = $this->db->prepare($sql);
           $stmt->execute(['group_status'=>$group_status,'current_time'=>$current_time]);
	


}



public function post_stop($datajson){

    date_default_timezone_set('Europe/Athens');
                $current_time=date('H:i:s');
$group_status=$datajson['group_status'];


        $sql = "update start_stop_time set stop_time=:current_time where group_status=:group_status";
        $stmt = $this->db->prepare($sql);
           $stmt->execute(['group_status'=>$group_status,'current_time'=>$current_time]);


}



public function duration($group_status){
    $sql = "SELECT start_time, stop_time FROM start_stop_time WHERE group_status=:group_status";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['group_status' => $group_status]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $durations = [];
    foreach ($results as $result) {
        $start_time = $result['start_time'];
        $stop_time = $result['stop_time'];

        $start = explode(':', $start_time);
        $end = explode(':', $stop_time);

        // Convert times to seconds
        $startSeconds = $start[0] * 3600 + $start[1] * 60 + $start[2];
        $endSeconds = $end[0] * 3600 + $end[1] * 60 + $end[2];

        // Calculate difference
        $duration = abs($endSeconds - $startSeconds);

        // Convert duration back to hours, minutes, seconds
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $seconds = $duration % 60;

        $duration_formatted = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        $durations = $duration_formatted;
    }

    return json_encode(['duration' => $durations]);
}

















}
?>
