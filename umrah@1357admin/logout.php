<?php

session_start();
error_reporting(0);
require '../config/config.php';
require 'functions/authentication.php';

$logout = new Authentication();
$logout->SignOut();

?>
