<?php

class Authentication {

	private $email;
	private $pass;
	private $conn;
	
	public function adminLogin($email,$pass) {
	
		$conn = new dbClass();
		$this->conn = $conn;
		$this->email = $email;
		$this->pass = $pass;
		
		$result = $conn->getData("SELECT * FROM `admin` WHERE `email` = '$email' AND `password` = '$pass' AND `status` = '1'");
		
		if($result!=''){
		
			$conn->updateExecute("UPDATE `admin` SET 
			`login_ip` = '".$_SERVER['REMOTE_ADDR']."', `login_date` = now() WHERE `email` = '$email'");
		
			$_SESSION['ADMIN_USER'] = $result['username'];
			$_SESSION['ADMIN_USER_ID'] = $result['id'];
			$_SESSION['ADMIN_USER_TYPE'] = $result['type'];
			$_SESSION['ADMIN_USER_IP'] = $_SERVER['REMOTE_ADDR'];
			
			return true; 
		
		} else {
			return false;
		}
	}

	public function checkSession() {
        if (
            !isset($_SESSION['ADMIN_USER_ID']) || $_SESSION['ADMIN_USER_ID'] == '' ||
            !isset($_SESSION['ADMIN_USER_TYPE']) || $_SESSION['ADMIN_USER_TYPE'] != 'Master Admin'
        ) {
            header('Location: index.php');
            exit();
        }
    }
	
	public function SignOut() {
		unset($_SESSION['ADMIN_USER']);
		unset($_SESSION['ADMIN_USER_ID']);
		unset($_SESSION['ADMIN_USER_TYPE']);
		unset($_SESSION['ADMIN_USER_IP']);
		session_destroy();
		echo "<script>window.location.href='index.php'</script>";
	}
}

?>