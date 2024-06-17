<?php
    include 'membersRepository.php';
    include 'db_config.php';

    class TeamsRepository {
             
        public $membersRepository;
        public $db;
        public function __construct() {
            try {
	 $this->db = new PDO("mysql:host=$b_host;dbname=$db_name", $db_user, $db_pass);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }

          $this->membersRepository=new MembersRepository();

        }

        public function getTeams($group_status) {
            try {


//getting groups data
                $querygroups = "SELECT * FROM groups where group_status='$group_status';";
                $stmtgroups = $this->db->query($querygroups);
                $resultgroups = $stmtgroups->fetchAll(PDO::FETCH_ASSOC);
                $reducedJson = json_encode($resultgroups);
                               

             
                $teams=array();
            
                foreach($resultgroups as $group){
                    $group_id=$group['group_id'];
                    $group_name=$group['group_name'];

                    $queryroutefromrefs = "SELECT point_id FROM refs WHERE route_name = (SELECT route_name FROM routes WHERE route_id=(select route_id from groups where group_id=$group_id));";
                    $stmtroutefromrefs = $this->db->query($queryroutefromrefs);
                    $resultgroupsroutefromrefs = $stmtroutefromrefs->fetchAll(PDO::FETCH_ASSOC);




$sql_previous_point = "SELECT point_id FROM previous_points WHERE group_id = :group_id";
$stmt_previous_point = $this->db->prepare($sql_previous_point);
$stmt_previous_point->bindParam(':group_id', $group_id, PDO::PARAM_INT);
$stmt_previous_point->execute();
$result_previous_point = $stmt_previous_point->fetch(PDO::FETCH_ASSOC);
    $previous_point = $result_previous_point['point_id'];                                   

$previous_point_name="";

                    $route = array();
                    foreach($resultgroupsroutefromrefs as $point) {
                        $pointID = $point['point_id'];
                        $queryPoint = "SELECT point_name FROM points WHERE point_id=$pointID;";
                        $stmt = $this->db->query($queryPoint);
                        $pointNameTable = $stmt->fetch(PDO::FETCH_ASSOC);
                        $route[] = $pointNameTable["point_name"];


			if($previous_point==$pointID){	
					$previous_point_name=$pointNameTable["point_name"];
			}


                    }
    

                    $members=$this->membersRepository->getMembers($group_id);

                    $queryChecIn = "SELECT group_info FROM groups WHERE group_id=$group_id;";
                    $stmt = $this->db->query($queryChecIn);
                    $chechInTable = $stmt->fetch(PDO::FETCH_ASSOC);
                    $checkIn=$chechInTable['group_info'];

                    $teams_row = array(
                        "group_name" => $group_name,
                        "group_members" => $members,
                        "group_route" => $route,
                        "check_in" => $checkIn,
			"previous_point_name" =>$previous_point_name
                    );    
                    array_push($teams,$teams_row);
                    
                }
                $reducedJson = json_encode($teams);

                return $reducedJson;


            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }   
                

        }
        
        

        public function saveTeam($datajson){
            try {   
                            
                $group_name = $datajson['group_name'];
                $group_status = $datajson['group_status'];
                $group_members = $datajson['group_members'];

		 $group_info = $datajson['group_info'];


                // save a new team
                $sql = "INSERT INTO groups (group_name, group_status, group_info) 
                    VALUES ('$group_name', '$group_status',0);";                // echo "SQL Query group: $sql";
                $this->db->exec($sql); 
                $group_id = $this->db->lastInsertId();   
                $this->membersRepository->saveMembers($group_members,$group_id);



		 include'/var/www/html/game/phpqrcode-2010100721_1.1.4/phpqrcode/qrlib.php'; // Φορτώνει τη βιβλιοθήκη QR Code

          
 $qrCodePath = "/var/www/html/game/qrCodes/{$group_name}.png"; // Ορίζει τη διαδρομή του αρχείου QR κώδικα

            QRcode::png($group_name, $qrCodePath,"L", 4, 4);





                echo "Team and member data inserted successfully.";
                
            }catch(PDOException $e) {
              echo $sql . "<br>" . $e->getMessage();
            }

        }

        public function checkIn($datajson){

            $group_name = $datajson['group_name'];

            $group_name_client = "SELECT group_id FROM groups WHERE group_name=:group_name;";
            $stmt_group_name_client = $this->db->prepare($group_name_client);
            $stmt_group_name_client->execute([':group_name' => $group_name]);
            $result_group_name = $stmt_group_name_client->fetch(PDO::FETCH_ASSOC);
    
            $groupID = $result_group_name['group_id'];
        
            $this->membersRepository->deleteMembers($group_id);
            $update = "UPDATE groups SET group_info = 1 WHERE group_id = :group_id;";
            $stmt_update = $this->db->prepare($update);
            $stmt_update->execute([':group_id' => $groupID]);

            $sql = "INSERT INTO previous_points (group_id,point_id) VALUES ($groupID , -1);";
            $this->db->exec($sql); 





        }


public function setGroupRoute($datajson) {
    $group_name = $datajson['group_name'];
    $name_route = $datajson['route_name'];

    $sqlname = "SELECT route_id FROM routes WHERE route_name = :route_name";
    $stmt = $this->db->prepare($sqlname);
    $stmt->bindParam(':route_name', $name_route);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $route_id = $result['route_id'];

        $sql = "UPDATE groups SET route_id = :route_id WHERE group_name = :group_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':route_id', $route_id);
        $stmt->bindParam(':group_name', $group_name);
        $stmt->execute();
    } else {
        // Handle case where route_id is not found
    }
}

public function deleteTeams($datajson){

            
            $group_name=$datajson["group_name"];

            $sqlgroupID="SELECT group_id from groups where group_name=:group_name;";
            $stmt = $this->db->prepare($sqlgroupID);
            $stmt->bindParam(':group_name', $group_name);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            $group_id=$result['group_id'];
            
            $this->membersRepository->deleteMembers($group_id);

	     $sqlDeleteGroupIDfromPreviousPointFirst="DELETE from previous_points where group_id=:group_id;";
             $stmtdelete = $this->db->prepare($sqlDeleteGroupIDfromPreviousPointFirst);
             $stmtdelete->bindParam(':group_id', $group_id);
             $stmtdelete->execute();

            $sqlDelete="DELETE from groups where group_name=:group_name;";
            $stmt = $this->db->prepare($sqlDelete);
            $stmt->bindParam(':group_name', $group_name);
            $stmt->execute();
        }
        


}

?>
