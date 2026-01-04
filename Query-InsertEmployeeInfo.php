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
// insert into employees
$sql = "INSERT INTO employees (EmpID,EmpFN,EmpMN,EmpLN,PosID) VALUES (:empidn,:empfn,:empmn,:empln,:empposition)";
   $stmt = $pdo->prepare($sql);
   $stmt->bindParam(':empidn', $_POST['empidn']);
   $stmt->bindParam(':empfn', $_POST['empfn']);
   $stmt->bindParam(':empmn', $_POST['empmn']);
   $stmt->bindParam(':empln', $_POST['empln']);
   $stmt->bindParam(':empposition', $_POST['empposition']);
   $stmt->execute(); 
  


  $sql = "INSERT INTO empprofiles (EmpID,EmpAddress,EmpPhone,EmpDOB,EmpGender,EmpCS,EmpMobile,EmpEmail,EmpPPNo,EmpPPED,EmpPPIA,EmpSSS,EmpTIN,EmpCP,EmpCPSD,EmpCPDept,EmpCPPos,EmpPP,EmpPPSD,EmpPPDept, EmpPPPos,EmpPINo,EmpPHNo,EmpUMIDNo,EmpPPath) VALUES (:empidn, :pempadd, :pempno, :pempdob, :pempgender, :pempcs, :emphomenumber, :pempeadd, :pempppno, :pemppped, :pempppia, :pempsss, :pemptin, :pempcp, :pempcpsd, :pempcpdept, :pempcpd, :pemppp, :pemppsd, :pemppdept, :pempppd, :pemppagibig, :pempphno, :pempumid, :pathpic)";

$targetPath="assets/images/" . $_POST['empidn'] . ".jpg" ;

   $stmt = $pdo->prepare($sql);
   $stmt->bindParam(':empidn', $_POST['empidn']);
   $stmt->bindParam(':pempadd', $_POST['pempadd']);
   $stmt->bindParam(':pempno', $_POST['pempn0']);
   $stmt->bindParam(':pempdob', $_POST['pempdob']);
   $stmt->bindParam(':pempgender', $_POST['pempgender']);
   $stmt->bindParam(':pempcs', $_POST['pempcs']);
   $stmt->bindParam(':emphomenumber', $_POST['emphomenumber']);
   $stmt->bindParam(':pempeadd', $_POST['pempeadd']);
   $stmt->bindParam(':pempppno', $_POST['pempppno']);
   $stmt->bindParam(':pemppped', $_POST['pemppped']);
   $stmt->bindParam(':pempppia', $_POST['pempppia']);
   $stmt->bindParam(':pempsss', $_POST['pempsss']);
   $stmt->bindParam(':pemptin', $_POST['pemptin']);
   $stmt->bindParam(':pempcp', $_POST['pempcp']);
   $stmt->bindParam(':pempcpsd', $_POST['pempcpsd']);
   $stmt->bindParam(':pempcpdept', $_POST['pempcpdept']);
   $stmt->bindParam(':pempcpd', $_POST['pempcpd']);
   $stmt->bindParam(':pemppp', $_POST['pemppp']);
   $stmt->bindParam(':pemppsd', $_POST['pemppsd']);
   $stmt->bindParam(':pemppdept', $_POST['pemppdept']);
   $stmt->bindParam(':pempppd', $_POST['pempppd']);
   $stmt->bindParam(':pemppagibig', $_POST['pemppagibig']);
   $stmt->bindParam(':pempphno', $_POST['pempphno']);
   $stmt->bindParam(':pempumid', $_POST['pempumid']);
   $stmt->bindParam(':pathpic', $targetPath);
   $stmt->execute();


   $roleid="9"; 
   $statID="2";
   $us = ucfirst($_POST['empfn'][0]) . ucfirst($_POST['empln']);
   $pass=ucfirst($_POST['empln']);

   $sql = "INSERT INTO empdetails (EmpID,EmpUN,EmpPW,EmpRoleID,EmpISID,EmpdepID,EmpCompID,EmpWSID,EmpRDID,EmpDateHired,EmpDateResigned,EmpStatID) VALUES (:empid, :empun,:emppw, :id, :empis,:EmpdepID, :empcompid, :empwsid, :emprdid, :empdth, :empdtr, :empclassification)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':empid', $_POST['empidn']);
      $stmt->bindParam(':empun',  $us);
      $stmt->bindParam(':emppw', $pass);
      $stmt->bindParam(':id', $roleid);
      $stmt->bindParam(':empis', $_POST['empis']);
      $stmt->bindParam(':EmpdepID', $_POST['empdep']);
      $stmt->bindParam(':empcompid', $_POST['empcompany']);
      $stmt->bindParam(':empwsid', $_POST['empworktime']);
      $stmt->bindParam(':emprdid', $_POST['empworkdays']);
      $stmt->bindParam(':empdth', $_POST['empdatehired']);
      $stmt->bindParam(':empdtr', $_POST['empdateresigned']);
      $stmt->bindParam(':empclassification', $_POST['empclassification']);
      $stmt->execute();  
   
   $sql = "INSERT INTO empdetails2 (EmpID,EmpBasic,EmpAllowance,EmpHRate) VALUES (:empide, :empbasic, :empallow, :emphrate)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':empide', $_POST['empidn']);
      $stmt->bindParam(':empbasic', $_POST['empbasic']);
      $stmt->bindParam(':empallow', $_POST['empallowance']);
      $stmt->bindParam(':emphrate', $_POST['emphourlyrate']);
      $stmt->execute();     
                        

 ?>