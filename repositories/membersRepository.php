<?php

include 'db_config.php';

class MembersRepository {
            
   public $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=$b_host;dbname=$db_name", $db_user, $db_pass);

            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function saveMembers($group_members,$group_id){

        foreach ($group_members as $member){
            $memberName = $member['member_name'];
            $memberPhone = $member['member_phone'];
            $memberEmail = $member['member_email'];

            $sql = "INSERT INTO members (member_name, group_id,member_phone,member_email) 
                VALUES ('$memberName', '$group_id','$memberPhone','$memberEmail');";
                
            $this->db->exec($sql);
            
        } 
    }


    public function getMembers($group_id) {
        try {
    
            $querymembers = "SELECT * from members where group_id='$group_id'";
            $stmtmembers = $this->db->query($querymembers);
            $resultmembers = $stmtmembers->fetchAll(PDO::FETCH_ASSOC);
            
            $members = array();

            foreach ($resultmembers as $row){
                $member=array(
                    'member_name'=>$row['member_name'],
                    'member_phone'=>$row['member_phone'],
                    'member_email'=>$row['member_email']
                );
                $members[] = $member;
            }
            
            return $members;



        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }   

    }

public function deleteMembers($group_id){

        $sqlDelete="DELETE from members where group_id=:group_id;";
        $stmt = $this->db->prepare($sqlDelete);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->execute();
    }


public function deleteOnlyOneMember($datajson){
 $group_id=$datajson['group_name'];
         $member_name=$datajson['member_name'];
        $sqlDelete="delete from members where member_name=:member_name and group_name=:group_name;";
        $stmt = $this->db->prepare($sqlDelete);
        $stmt->bindParam(':group_name', $group_name);
	$stmt->bindParam(':member_name', $member_name);

        $stmt->execute();
    }














}







?>
