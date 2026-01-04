<?php 
include 'w_conn.php';
// $conn = new mysqli($servername, $username, $password, $db);
// $name=$_POST['name'];

// $sql="INSERT INTO `test` (`nm`) VALUES ('$name')";
// if ($conn->query($sql) === TRUE) {
//     echo "data inserted";
try{
$pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
catch(PDOException $e)
   {
die("ERROR: Could not connect. " . $e->getMessage());
   }
   $sql = "UPDATE employees SET EmpFN = :empfn WHERE EmpID = :empidn";
   $stmt = $pdo->prepare($sql);
   $stmt->bindParam(':empidn', $_POST['empidn']);
   $stmt->bindParam(':empfn', $_POST['empfn']);
   $stmt->bindParam(':empmn', $_POST['empmn']);
   $stmt->bindParam(':empln', $_POST['empln']);
   $stmt->bindParam(':empposition', $_POST['empposition']);
   $stmt->execute(); 

?>