<?php session_start();
  if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
  else{ header ('location: login.php'); 			               
}
?>

<?php
include 'w_conn.php';
try{
$pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
catch(PDOException $e)
   {
die("ERROR: Could not connect. " . $e->getMessage());
   }
   
  $id=$_SESSION['id'];
  $isid=$_SESSION['EmpISID'];  
  $statement = $pdo->prepare("SELECT * from accessrights where EmpID=:id");
  $statement->bindParam(':id' , $id);
  $statement->execute();
  $r = $statement->fetch();

  if ($r['ot']==1)
  	{
  	    
  	}
  else{

	date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Overtime Filling System</title>
	  <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script> 
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	
	  <script type="text/javascript" src="assets/js/script.js"></script>
	  <script src="assets/js/script-reports.js"></script>
	  <script type="text/javascript" src="assets/js/script-modules.js"></script>
  <script type="text/javascript" src="assets/js/administrative.js"></script>

	  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
<style>  html body{
		font-family: Tahoma !important;
	}</style>
</head>
<body style="background-image: none">
	<?php
		include 'includes/header.php';
	?>
	<div class="w-container">
        <div class="row">
          <div class="col-lg-3"></div>
         <!-- website content -->
         <div class="col-lg-9 module-content">
         <h4 class="page-title" style="<?php echo "color: " . $_SESSION['CompanyColor']; ?>">Overtime Filling System</h4>
        	
        	<div class="row">
        		<div class="col-lg-12">
        			<button type="button" class="btn btn-primary" id="eventListener" data-toggle="modal" data-target="#newform">+ Overtime Filling Form</button>
        			<!-- The Modal -->

					<div class="modal" id="newform">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Overtime Filing Form</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- PHP Section: Fetching Employee Data -->
            <?php
            include 'w_conn.php';

            try {
                $pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("ERROR: Could not connect. " . $e->getMessage());
            }

            $id = $_SESSION['id'];
            $isid = $_SESSION['EmpISID'];

            // SQL Query to fetch employee details
            $statement = $pdo->prepare("
                SELECT employees.*, empdetails.*, companies.CompanyDesc, departments.DepartmentDesc, positions.PositionDesc
                FROM employees
                INNER JOIN empdetails ON employees.EmpID = empdetails.EmpID
                INNER JOIN companies ON empdetails.EmpCompID = companies.CompanyID
                INNER JOIN departments ON empdetails.EmpdepID = departments.DepartmentID
                INNER JOIN positions ON positions.PSID = employees.PosID
                WHERE employees.EmpID = :id
            ");
            $statement->bindParam(':id', $id);
            $statement->execute();
            $row = $statement->fetch();
            ?>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="otdata" action="">

                    <div class="row">
                        <!-- Left Column: Employee and Company Details -->
                        <div class="col-lg-6">
                            <!-- Personnel Name -->
                            <div class="form-group">
                                <label>Personnel Name:</label>
                                <input type="text" disabled class="form-control" value="<?php echo $row['EmpLN'] . ' ' . $row['EmpFN'] . ' ' . $row['EmpMN']; ?>">
                            </div>
                            <!-- Company Name -->
                            <div class="form-group">
                                <label>Company Name:</label>
                                <input type="text" disabled class="form-control" value="<?php echo $row['CompanyDesc']; ?>">
                            </div>
                            <!-- Department -->
                            <div class="form-group">
                                <label>Department:</label>
                                <input type="text" disabled class="form-control" value="<?php echo $row['DepartmentDesc']; ?>">
                            </div>
                            <!-- Designation -->
                            <div class="form-group">
                                <label>Designation:</label>
                                <input type="text" disabled class="form-control" value="<?php echo $row['PositionDesc']; ?>">
                            </div>

							<!-- New Section: Radio Buttons for OT Type -->
							<div class="form-group">
                                <label>Overtime Type:</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="overtimeType" id="singleOT" value="1" >
                                    <label class="form-check-label" for="singleOT">Single</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="overtimeType" id="multiOT" value="2">
                                    <label class="form-check-label" for="multiOT">Multiple</label>
                                </div>
                            </div>

                            <!-- Purpose -->
                            <div class="form-group">
                                <label>Purpose:</label>
                                <textarea class="form-control" rows="4" id="otpurpose" name="otpur"></textarea>
                            </div>

						
                        </div>

                        <!-- Middle Column: Date Fields -->
                        <div class="col-lg-3">
                            <!-- Filing Date -->
                            <div class="form-group">
                                <label>Filing Date:</label>
                                <input type="date" name="fdate" readonly="readonly" value="<?php echo date('Y-m-d'); ?>" class="form-control fdate">
                            </div>
                            <!-- OT Date From -->
                            <div class="form-group">
                                <label>OT Date From:</label>
                                <input type="date" name="datefrom" class="form-control datefrom">
                            </div>
                            <!-- OT Date To -->
                            <div class="form-group">
                                <label>OT Date To:</label>
                                <input type="date" name="dateto" class="form-control dateto">
                            </div>
                        </div>

                        <!-- Right Column: Time Fields -->
                        <div class="col-lg-3">
                            <!-- Filing Time -->
                            <div class="form-group">
                                <label>Filing Time:</label>
                                <input type="time" readonly="readonly" name="ftime" value="<?php echo date('H:i:s'); ?>" class="form-control">
                            </div>
                            <!-- OT Time From -->
                            <div class="form-group">
                                <label>OT Time From:</label>
                                <input type="time" name="timefrom" class="form-control timefrom">
                            </div>
                            <!-- OT Time To -->
                            <div class="form-group">
                                <label>OT Time To:</label>
                                <input type="time" name="timeto" class="form-control timeto">
                            </div>
                        </div>


                    </div>

                    <!-- Alert for Result (if needed) -->
                    <div id="result" class="alert alert-success" style="display:none">Result Message Here</div>

                    <!-- Submit Button -->
                    <button type="button" id="saveot" class="btn btn-success btn-block">Submit</button>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




        			<br>
        			<h5 class="module-title-history">Overtime History</h5>
				<div class="dtpar">
      			<h5>Date Parameters:</h5>
            	<label>From:</label>
            	<input type="date" class="form-control" id="dtp1"   value="<?php echo date('Y-m-d', strtotime(date("Y-m-d")  . ' - 15 days'));?>" >
              	<label>To:</label>
            	<input type="date" class="form-control"  id="dtp2"  value="<?php echo date("Y-m-d");?>">
                <button class="btn" id="ot" type="button"><img src="assets/images/refreshicon.png" data-toggle="tooltip" data-placement="right" title="Refresh" width="25px"></button>
              </div>
        			<table class="table table-striped">
        				<thead>
        					<tr>
        						<th>No</th>
        						<th>Filling DateTime</th>
        						<th>Time IN</th>
        						<th>Time OUT</th>
        						<th>Purpose</th>
        						<th>Duration</th>
        						<th>Status</th>
        						<th>Action</th>
        						</tr>

        				</thead>
        				<tbody id="tbot">
        					
        					<?php
                        try{
                        include 'w_conn.php';
                        $pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                           }
                        catch(PDOException $e)
                           {
                        die("ERROR: Could not connect. " . $e->getMessage());
                          }
			                  $id=$_SESSION['id'];
			                  $date1=date('Y-m-d', strtotime(date("Y-m-d")  . ' - 15 days'));
			                  $date2=date("Y-m-d", strtotime(' + 1 days'));
			                  $statement = $pdo->prepare("SELECT * FROM otattendancelog as a 
			                                  INNER JOIN status AS b ON a.Status=b.StatusID  
			                                  where a.EmpID=:id and TimeIn between :dt1 and :dt2 and a.Status<>7 order by a.DateTimeInputed DESC");

			                  $statement->bindParam(':id' , $id);
			                  $statement->bindParam(':dt1' , $date1);
			                  $statement->bindParam(':dt2' , $date2);
			                  $statement->execute();
			                  $cnt=0;
                while ($row21 = $statement->fetch())
                {
                	$cnt=$cnt+1;
                  ?>
                   <tr>
                   <td><?php echo $cnt ?></td>  
                   <td><?php echo date("F j, Y h:i:s A", strtotime($row21['DateTimeInputed']));  ?></td>
                   <td><?php echo date("F j, Y h:i:s A", strtotime($row21['TimeIn'])); ?></td>
                   <td><?php echo date("F j, Y h:i:s A", strtotime($row21['TimeOut'])); ?></td> 
  				   <td><?php echo $row21['Purpose']; ?></td>
                   <td><?php echo $row21['Duration']; ?></td>
                   <td><?php echo $row21['StatusDesc']; ?></td>
                 <?php
                        if($row21['Status']==1){
                    ?>
                    <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModalob<?php echo $row21['OTLOGID']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button> </td>
                      <?php
                        }else{
                            
                        }
                        ?>
                  </tr> 
                  
                   <!-- The Modal -->
                    <div class="modal ob-viewdel" id="myModalob<?php echo $row21['OTLOGID']; ?>">
                      <div class="modal-dialog">
                        <div class="modal-content">
                    
                          <!-- Modal Header -->
                          <div class="modal-header">
                            <h4 class="modal-title">Are you sure you want to remove this ??</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>
                    
                          <!-- Modal body -->
                          <div class="modal-body">
                              <button type="button" id="<?php echo $row21['OTLOGID']; ?>" class="btn btn-success ys_ot">Yes</button> 
                               <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                          </div>
                    
                          <!-- Modal footer -->
                      
                    
                        </div>
                      </div>
                    </div>
              <?php 
              }

              ?>
        				</tbody>
        			</table>
        		</div>
        	</div>
        	 <!-- The Modal -->
			  <div class="modal" id="modalWarning">
			    <div class="modal-dialog modal-dialog-centered">
			      <div class="modal-content">
			      
			        <!-- Modal Header --> 
			        <div class="modal-header" style="padding: 7px 8px;"><h1 style="font-size: 25px; padding-left: 10px;color:red;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></h1>
			          <button type="button" class="close" data-dismiss="modal">&times;</button>
			        </div>
			        
			        <!-- Modal body -->
			        <div class="modal-body">
			          <div class="alert alert-danger">
						
						</div>
			        </div>
			        
			        <!-- Modal footer -->
			     
			        
			      </div>
			    </div>
			  </div>
			  <!-- modal end -->

			   <!-- The Modal -->
			  <div class="modal" id="modalSave">
			    <div class="modal-dialog modal-dialog-centered">
			      <div class="modal-content">
			      
			        <!-- Modal Header --> 
			        <div class="modal-header" style="padding: 7px 8px;"><h1 style="font-size: 25px; padding-left: 10px;color:green;"><i class="fa fa-check" aria-hidden="true"></i></h1>
			          <button type="button" class="close" data-dismiss="modal">&times;</button>
			        </div>
			        
			        <!-- Modal body -->
			        <div class="modal-body">
			          <div class="alert alert-success">
						
						</div>
			        </div>
			        
			        <!-- Modal footer -->
			     
			        
			      </div>
			    </div>
			  </div>
			  <!-- modal end -->
         <!-- end of website content -->
         </div>
     	</div>
 	</div>
</body>
</html>
<?php }?>