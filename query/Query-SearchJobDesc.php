    <?php
     include 'w_conn.php';session_start();
  if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
  else{ header ('location: login.php'); }


  try{
    $pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       }
    catch(PDOException $e)
       {
    die("ERROR: Could not connect. " . $e->getMessage());
       }
if (isset($_GET['delete'])){
	       $res12=mysqli_query($con,"select * from jobdescription inner join empjobdesc on jobdescription.JD_ID=empjobdesc.JID where empjobdesc.EmpID='" . $_GET['delete'] . "'");
                                            while ($row3=mysqli_fetch_array($res12)) {
                                          ?>
                                              <a  class="btn"><?php echo $row3["JDescription"]; ?> <i class="fa fa-times" id="<?php echo $row3['EJID']; ?>" aria-hidden="true"></i></a>
                                          <?php
                                            }
                           
}elseif(isset($_GET['jd'])){

        
          $statement = $pdo->prepare("SELECT * from jobdescription INNER JOIN empjobdesc on 
          (jobdescription.JD_ID=empjobdesc.JID) where empjobdesc.EmpID='". trim($_GET['jd']) ."'");

          $statement->execute();
          $calload = $statement->rowCount();
          while ( $row3 = $statement->fetch()) { 
	        //  $res12=mysqli_query($con,"select * from jobdescription inner join empjobdesc on jobdescription.JD_ID=empjobdesc.JID where empjobdesc.EmpID='" . $_GET['jd'] . "'");
           
          //  while ($row3=mysqli_fetch_array($res12)) {?>
                                           
              <tr class="cdd-tr"><th class="det-f"><?php echo $row3["JDescription"]; ?></th></tr>
            <?php
             }
}
elseif(isset($_GET['displayjd'])){

                                            $res13=mysqli_query($con,"select * from jobdescription order by JDescription asc");
                                            while ($row=mysqli_fetch_array($res13)) {
                                          ?>
                                          <a  class="btn"><?php echo $row[1]; ?> <i class="fa fa-check-circle" id="<?php echo $row[0]; ?>" aria-hidden="true"></i></a>
                                          <?php
                                            }
                                         
}
elseif (isset($_GET['srchjd'])){
		$sjD=$_POST['JobId'];

	  	$res13=mysqli_query($con,"select * from jobdescription where JDescription like '%" . $sjD . "%'  order by JDescription asc");
            while ($row=mysqli_fetch_array($res13)) {
      	?>
            <a  class="btn"><?php echo $row[1]; ?> <i class="fa fa-check-circle" id="<?php echo $row[0]; ?>" aria-hidden="true"></i></a>
        <?php
            }
}
                                     