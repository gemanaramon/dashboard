
<?php


$servername = "localhost";
$username = "root";
$password = "";
$db="wedodb2020";
$con = mysqli_connect($servername,$username,$password,$db);

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  
  // server should keep session data for AT LEAST 1 hour
// ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
// session_set_cookie_params(3600);
?>
