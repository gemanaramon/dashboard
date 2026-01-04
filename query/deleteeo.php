 <?php
 
 include 'w_conn.php';
  session_start();
  if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
  else{ header ('location: login.php'); }
   date_default_timezone_set("Asia/Manila");
   $todaydt=date("Y-m-d H:i:s"); 
try{
$pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
catch(PDOException $e)
   {
die("ERROR: Could not connect. " . $e->getMessage());
   }
$id=$_SESSION['id'];
  try {
        if (isset($_GET['eo'])){
            $lgid=0001;
        $sql2="update earlyout set Status=7,LogID=:ldig where SID=:id";
        $stmt = $pdo->prepare($sql2);
        $stmt->bindParam(':id' , $_REQUEST['data']);
        $stmt->bindParam(':ldig' , $lgid);
        $stmt->execute();
        

                     $ch="Removed EO.";
                  // insert into dars
                       $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                      $stmt = $pdo->prepare($sql);
                      $stmt->bindParam(':id' , $id);
                      $stmt->bindParam(':empact', $ch);
                      $stmt->bindParam(':ddt', $todaydt);
                     $stmt->execute(); 
        }
        else if(isset($_GET['ob'])){
        $sql2="update obs set OBStatus=7 where OBID=:id";
        $stmt = $pdo->prepare($sql2);
        $stmt->bindParam(':id' , $_REQUEST['data']);
        $stmt->execute();
        
                      
                     $ch="Removed OB.";
                  // insert into dars
                       $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                      $stmt = $pdo->prepare($sql);
                      $stmt->bindParam(':id' , $id);
                      $stmt->bindParam(':empact', $ch);
                      $stmt->bindParam(':ddt', $todaydt);
                     $stmt->execute();
        }
        else if(isset($_GET['ot'])){
          $sql2="update otattendancelog set Status=7 where OTLOGID=:id";
          $stmt = $pdo->prepare($sql2);
          $stmt->bindParam(':id' , $_REQUEST['data']);
          $stmt->execute();
          
                     $ch="Removed OT.";
                  // insert into dars
                       $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                      $stmt = $pdo->prepare($sql);
                      $stmt->bindParam(':id' , $id);
                      $stmt->bindParam(':empact', $ch);
                      $stmt->bindParam(':ddt', $todaydt);
                     $stmt->execute();
        }
       else if(isset($_GET['leave'])){
          $sql2="update hleaves set LStatus=7 where LeaveID=:id";
          $stmt = $pdo->prepare($sql2);
          $stmt->bindParam(':id' , $_REQUEST['data']);
          $stmt->execute();
          
          $sql2="update hleavesbd set LStatus=7 where FID=:id";
          $stmt = $pdo->prepare($sql2);
          $stmt->bindParam(':id' , $_REQUEST['data']);
          $stmt->execute();
          
                      $ch="Removed Leave " . $_REQUEST['data'];
                  // insert into dars
                      $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                      $stmt = $pdo->prepare($sql);
                      $stmt->bindParam(':id' , $id);
                      $stmt->bindParam(':empact', $ch);
                      $stmt->bindParam(':ddt', $todaydt);
                     $stmt->execute();
        }
  } catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
  } 
?>