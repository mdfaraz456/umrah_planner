<?php

class dbClass {

	private $host;
	private $user;
	private $pass;
	private $dbname;
	private $conn;
	private $error;
	
	public function __construct(){
		$this->connect();
	}
	
	public function __destruct() {
		$this->disconnect();
	}
	
	private function connect(){
	
		$this->host = 'localhost';
		$this->user = 'root';
		$this->pass = '';
		$this->dbname = 'umrahplanner_db';

		// $this->host = 'localhost';
		// $this->user = 'awsdemoco_umrah_db';
		// $this->pass = 'mZuYbvZ3Ud6VpM9ERVCL';
		// $this->dbname = 'awsdemoco_umrah_db';
		
		try {
			
			$this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname.'', $this->user, $this->pass);
			
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		} catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		
		if(!$this->conn) {
			$this->error = 'Fatal Error :'.$e->getMessage();
		}
		
		return $this->conn;
	
	}
	
	public function disconnect() {
		if ($this->conn) {
			$this->conn = null;
		}
	}
	
	public function getData($query) {
		$result = $this->conn->prepare($query);
		$query = $result->execute();
		if ($query == false) {
		   echo 'Error SQL: '.$query;
		   die();
		}
		
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$reponse = $result->fetch();
		
		return $reponse;
	}
	
	public function getAllData($query) {
		$result = $this->conn->prepare($query);
		$ret = $result->execute();
		if (!$ret) 
		{
		 	echo 'Error SQL: '.$ret;
		    die();
		}
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$reponse = $result->fetchAll();
		
		return $reponse;
	}
	
	public function getRowCount($query) {
		$result = $this->conn->prepare($query);
		$ret = $result->execute();
		if (!$ret) 
		{
		  return false;
		}
		$reponse = $result->rowCount();
		
		return $reponse;
	}
	
	public function execute($query) {
	  $response = $this->conn->exec($query);
		
		if($response == false)
			return false;
		else	
			return true;
	} 

	public function executeStatement($query, $params = []) {
		$statement = $this->conn->prepare($query);
		
		foreach ($params as $key => &$value) {
			$statement->bindParam($key, $value);
		}
		
		return $statement->execute();
	}
	
	public function updateExecute($query) {
	  $response = $this->conn->exec($query);
		
		if($response == false)
			return false;
		else	
			return true;
	}
	
	public function addStr($val) {
		$res = addslashes(trim($val));
		return $res;
	}
	
	public function removeStr($val) {
		$res = stripslashes(trim($val));
		return $res;
	}
	
	public function lastInsertId() {
	
		$res = $this->conn->lastInsertId();
		
		return $res;
	}
	
	public function slug($string){
		$slug = strtolower(trim(preg_replace("/[\s-]+/", "-", preg_replace( "/[^a-zA-Z0-9\-]/", '-', addslashes($string))),"-"));
   		return $slug;
	}
}

date_default_timezone_set("Asia/Kolkata");
$dateTime = date('Y-m-d H:i:s');
$date = date('Y-m-d');
$time = date('H:i:s');

 

?>