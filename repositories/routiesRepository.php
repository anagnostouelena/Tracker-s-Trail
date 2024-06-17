<?php

    include 'refsRepository.php';
    include 'db_config.php';

    class RoutiesRepository {
                
        public $db;
	public $refsRepository;
        public function __construct() {
            try {
                $this->db = new PDO("mysql:host=$b_host;dbname=$db_name", $db_user, $db_pass);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
$this-> refsRepository = new RefsRepository();

        }

    public function getRoutes(){
             try {
             
               //getting routs data
                 $queryrouts = "select distinct route_name from routes;";
                $stmtrouts = $this->db->query($queryrouts);
                 $resultrouts = $stmtrouts->fetchAll(PDO::FETCH_ASSOC);
                // $reducedJson = json_encode($resultrouts);
 

                 $route_names=array();
               	$routes=array();

                 foreach($resultrouts as $routs){
        	        $route_name=$routs['route_name'];
			$route=$this->refsRepository->getRefs($route_name);
//			$routes[]=$route;
                	$route_names[]=$route_name;

		 	$row = array(
				"route_name"=>$route_name,
                        	"route" => $route
                    	);
    
                        array_push($routes,$row);
                 }

		$json=array();

//		$json[]=$route_names;
                 $reducedJson = json_encode($routes);
                 return $reducedJson;

             } catch (PDOException $e) {
                 die("Error: " . $e->getMessage());
             }   

         }




public function postRoutes($datajson) {

    try {

        $route_name = $datajson['route_name'];
        // Insert the route into the routes table and get the last inserted route_id
        $sql_route = "INSERT INTO routes (route_name) VALUES (:route_name)";
        $stmt_route = $this->db->prepare($sql_route);
        $stmt_route->bindParam(':route_name', $route_name);
        $stmt_route->execute();
        $route_id = $this->db->lastInsertId();


        // Insert the points into the refs table
        $points = $datajson['points'];
        $this->refsRepository->postRefs($route_id,$points);
        echo "Επιτυχής εισαγωγή δεδομένων στον πίνακα routes και refs.";

    } catch(PDOException $e) {
        echo "Σφάλμα κατά την εισαγωγή δεδομένων: " . $e->getMessage();

    }

}




public function deleteRoute($datajson) {
    $route_name = $datajson['route_name'];

    // Επιλογή point_id από τον πίνακα refs
    $sql_route = "SELECT point_id FROM refs WHERE route_name = :route_name";
    $stmt_route = $this->db->prepare($sql_route);
    $stmt_route->bindParam(':route_name', $route_name);
    $stmt_route->execute();
    $result_points = $stmt_route->fetchAll(PDO::FETCH_ASSOC);

    // Διαγραφή αναφορών
    foreach ($result_points as $result_point) {
        $point_id = $result_point['point_id'];
        $this->refsRepository->deleteRefs($point_id, $route_name); // Περνάμε και το route_name
    }

    // Διαγραφή διαδρομής από τον πίνακα routes
    try {
        $sql_route = "DELETE FROM routes WHERE route_name = :route_name";
        $stmt_route = $this->db->prepare($sql_route);
        $stmt_route->bindParam(':route_name', $route_name);
        $stmt_route->execute();
        echo "Η διαδρομή με το όνομα '$route_name' διαγράφηκε επιτυχώς.";
    } catch(PDOException $e) {
        echo "Σφάλμα κατά τη διαγραφή της διαδρομής: " . $e->getMessage();
    }
}


}
?>
	
	
