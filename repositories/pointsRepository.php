
<?php
include 'db_config.php';
class PointsRepository {
        
    public $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=$b_host;dbname=$db_name", $db_user, $db_pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    public function savePoint($datajson){
        
        $point_name = $datajson['point_name'];
        $judgment = $datajson['judgment'];
        $judgment_phone = $datajson['judgment_phone'];
        $judgment_email = $datajson['judgment_email'];

        $sql = "INSERT INTO points(point_name, judgment,judgment_phone,judgment_email) 
        VALUES ('$point_name', '$judgment','$judgment_phone','$judgment_email');";
        $this->db->exec($sql);
    } 


    public function getPoints(){

        $querypoints = "SELECT * FROM points ;";
        $stmtpoints = $this->db->query($querypoints);
        $resultpoints = $stmtpoints->fetchAll(PDO::FETCH_ASSOC);

        $points=array();

        foreach($resultpoints as $point){

            $point_name=$point['point_name'];
            $judgment=$point['judgment'];
            $judgment_phone=$point['judgment_phone'];
            $judgment_email=$point['judgment_email'];
		
		//return all the points
//	    $points[]=$point['point_name'];

            $point_row = array(
		"point_name"=>$point_name,
                "judgment" => $judgment,
                "judgment_phone" =>$judgment_phone,
                "judgment_email" =>$judgment_email
            );   

            array_push($points,$point_row);
        }
        $reducedJson = json_encode($points);
        return $reducedJson;

      


    } 

public function deletePoint($datajson) {
    $point_name = $datajson['point_name'];

    // Get the point_id of the point to be deleted
    $sql_point_id = "SELECT point_id FROM points WHERE point_name=:point_name";
    $stmt = $this->db->prepare($sql_point_id);
    $stmt->bindParam(':point_name', $point_name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $point_id = $result['point_id'];

    // Delete references associated with the point
    $sql_refs = "DELETE FROM refs WHERE point_id=:point_id";
    $stmt_refs = $this->db->prepare($sql_refs);
    $stmt_refs->bindParam(':point_id', $point_id);
    $stmt_refs->execute();

    // Delete the point from the points table
    $sql_point = "DELETE FROM points WHERE point_name=:point_name";
    $stmt_point = $this->db->prepare($sql_point);
    $stmt_point->bindParam(':point_name', $point_name);
    $stmt_point->execute();
}



public function upadatePoint($datajson){

try {
//    $point_id = $datajson['point_id'];
    $point_name = $datajson['point_name'];
    $judgment = $datajson['judgment'];
    $judgment_phone = $datajson['judgment_phone'];
    $judgment_email = $datajson['judgment_email'];

    // Κάνουμε update την εγγραφή
    $sql = "UPDATE points SET point_name = :point_name, judgment = :judgment, judgment_phone = :judgment_phone, judgment_email = :judgment_email WHERE point_name = :point_name";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':point_name', $point_name);
    $stmt->bindParam(':judgment', $judgment);
    $stmt->bindParam(':judgment_phone', $judgment_phone);
    $stmt->bindParam(':judgment_email', $judgment_email);
//    $stmt->bindParam(':point_id', $point_id); // Προσαρμόστε το point_id ανάλογα με την εφαρμογή σας
    $stmt->execute();
    
    echo "Η ενημέρωση ολοκληρώθηκε με επιτυχία.";
} catch(PDOException $e) {
    echo "Σφάλμα κατά την ενημέρωση: " . $e->getMessage();
}

}

}

?>
