<?php session_start();
if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
  else{ header ('location: login.php'); }
?>
<?php
    include 'w_conn.php';
      date_default_timezone_set("Asia/Manila"); 
?>

<!DOCTYPE html>
<html>
<head>

    <title>Early Out Filling System </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
 <!--  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!--  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
  <script src="assets/js/script-eo.js"></script>
  <script src="assets/js/script-reports.js"></script>
      <script type="text/javascript" src="assets/js/script-home.js"></script>
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
     <script type="text/javascript" src="assets/js/script-reports.js"></script>
     <script type="text/javascript" src="assets/js/script.js"></script>
  <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
<style>
      html body{
		font-family: Tahoma !important;
	}
</style>
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
            <h4 class="page-title" style="<?php echo "color: " . $_SESSION['CompanyColor']; ?>">Early Out Filling System </h4>
              <!-- coding in content -->
                <div class="row">
                  <div class="col-lg-12">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newform">+ Early Out Form</button>

                        <!-- The Modal -->
                <div class="modal" id="newform">
                  <div class="modal-dialog">
                    <div class="modal-content">

                      <!-- Modal Header -->
                      <div class="modal-header">
                        <h4 class="modal-title">Early Out Form</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
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
                  $statement = $pdo->prepare("SELECT *                                          
                              FROM employees 
                              INNER JOIN empdetails ON employees.EmpID=empdetails.EmpID
                              INNER JOIN companies ON empdetails.EmpCompID=companies.CompanyID
                              INNER JOIN departments ON empdetails.EmpdepID=departments.DepartmentID 
                              INNER JOIN positions ON positions.PSID=employees.PosID 
                              WHERE employees.EmpID=:id ORDER BY employees.EmpLN ASC ");
                  $statement->bindParam(':id' , $id);
                  $statement->execute();
                  $row = $statement->fetch();
                ?>
                      <!-- Modal body -->
                      <div class="modal-body">
                              <form id="eodata" action="">
                        <div class="row">
                            <div class="col-lg-6">
                              <div class="form-group">
                                <label >Personnel Name:</label>
                                <input type="text" disabled class="form-control" value="<?php echo $row['EmpLN']. ' ' . $row['EmpFN']. ' ' .$row['EmpMN'] ?>">
                              </div>
                               <div class="form-group">
                                <label >Company Name:</label>
                                <input type="text" disabled class="form-control" value="<?php echo $row['CompanyDesc'] ?>">
                              </div>
                               <div class="form-group">
                                <label >Department:</label>
                                <input type="text" disabled class="form-control" value="<?php echo $row['DepartmentDesc'] ?>">
                              </div>
                              <div class="form-group">
                                <label >Designation:</label>
                                <input type="text" disabled class="form-control" value="<?php echo $row['PositionDesc'] ?>">
                              </div>
                             
                            </div>
                            <div class="col-lg-6">
                                 <div class="form-group">
                                <label >Filling Date:</label>
                                <input type="text" disabled value="<?php echo date('F d, Y h:i:s A'); ?>" class="form-control" >
                                </div>
                                   <div class="form-group">
                                <label >EO Date:</label>
                                <input type="date" name="dttd" value="<?php echo date('Y-m-d');?>"  class="form-control" >
                                </div>
                                 <div class="form-group">
                                <label >Purpose:</label>
                                <textarea class="form-control" name="pur" rows="4"id="comment"></textarea>
                              </div>

                                <!-- s -->
                              <!--    <div class="form-group">
                                    <label >Work Schedule:</label>
                                    <br>
                                     <label>From:</label>
                                    <input type="date" class="form-control">

                                    <label>To:</label>
                                    <input type="date" class="form-control">
                                   
                                  </div> -->
                                 <button type="button" class="btn btn-success btn-block"   data-toggle="modal" data-target="#myModalnew">Submit</button>
                            <!--<button type="button" class="btn btn-success btn-block">Submit</button>-->
                                
                            </div>

                          </div>

                          </form>
                      </div>

                      <!-- Modal footer -->
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                      </div>

                    </div>
                  </div>
                </div>
            </div>
          </div>
<br>

      <div class="dtpar">
      <h5>Date Parameters:</h5>
      <label>From:</label>
      <input type="date" class="form-control" id="dtp1"   value="<?php echo date('Y-m-d', strtotime(date("Y-m-d")  . ' - 15 days'));?>" >
      <label>To:</label>
      <input type="date" class="form-control"  id="dtp2"  value="<?php echo date("Y-m-d");?>">

      <button class="btn" id="eohistory" type="button"><img src="assets/images/refreshicon.png" data-toggle="tooltip" data-placement="right" title="Refresh" width="25px"></button>
      </div>
<div class="container-formatss">
  <th><h5 class="title">Early Out History</h5></th>

</div>

  <div class="container-format">       
  <table class="table table-bordered">
    <thead>
      <tr>
        <th style="width:25%">Purpose</th>
        <th style="width:15%">Date Filed</th>
        
        <th style="width:25%">Status</th>
        <th style="width:15%">Date Time Inputed</th>
        <th style="width:5%"> Delete</th>
      </tr>
    </thead>

    <tbody id="tbeodata">
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
                  $dt1=date('Y-m-d', strtotime(date("Y-m-d")  . ' - 15 days'));
                  $dt2=date('Y-m-d', strtotime(date("Y-m-d")  . ' + 1 days'));
                  $statement = $pdo->prepare("SELECT * FROM earlyout AS a INNER JOIN status AS b ON a.Status=b.StatusID  WHERE a.EmpID=:id and status<>7 and DFile  between :d1 and :d2  ORDER BY DateTimeInputed DESC");
                  $statement->bindParam(':id' , $id);
                  $statement->bindParam(':d1' , $dt1);
                  $statement->bindParam(':d2' , $dt2);
                  $statement->execute();
                while ($row21 = $statement->fetch())
                {
                  ?>
                   <tr>
                   <td><?php echo $row21['Purpose']; ?></td>
                   <td><?php echo date("F j, Y", strtotime($row21['DFile'])); ?></td> 
                   <td><?php echo $row21['StatusDesc']; ?></td>
                    <td><?php  echo date("F j, Y h:i:s A", strtotime($row21['DateTimeInputed'])); ?></td>
                    <?php
                        if($row21['Status']==1){
                    ?>
                         <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModalw<?php echo $row21['SID']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button> </td>    
                    <?php
                        }else{
                            
                        }
                    ?>
               
                  </tr> 
                  
                  
                  <!-- The Modal -->
                    <div class="modal eo-delete" id="myModalw<?php echo $row21['SID']; ?>">
                      <div class="modal-dialog">
                        <div class="modal-content">
                    
                          <!-- Modal Header -->
                          <div class="modal-header">
                            <h4 class="modal-title">Are you sure you want to remove this ??</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>
                    
                          <!-- Modal body -->
                          <div class="modal-body">
                              <button type="button" id="<?php echo $row21['SID']; ?>"  class="btn btn-success ys_eo">Yes</button> 
                               <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                          </div>
                    
                          <!-- Modal footer -->
                       
                        </div>
                      </div>
                    </div>
              <?php 
              }
                       
              ?>

      </tr>
    </tbody>
  </table>
</div>
</div>
</div>
  
          </div>      
        </div>
    </div>
  </div>  
  <!-- The Modal -->
        <div class="modal" id="modalWarning">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            
              <!-- Modal Header --> 
              <div class="modal-header" style="padding: 7px 8px;background-color: #327e0b;color: #fff;">
                <label>Message</label>
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
        <div class="modal" id="modalSuccess">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            
              <!-- Modal Header --> 
              <div class="modal-header" style="padding: 7px 8px;">
                    <h1 style="font-size: 25px; padding-left: 10px;color:green;"><i class="fa fa-check" aria-hidden="true"></i></h1>
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
        
            <!-- The Modal -->
                    <div class="modal eo-delete" id="myModalnew">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                    
                          <!-- Modal Header -->
                          <div class="modal-header">
                            <h4 class="modal-title">Are you sure you want to Apply Earlyout ?</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>
                    
                          <!-- Modal body -->
                          <div class="modal-body">
                              <button type="button" id="eosave"   class="btn btn-success yestoeo">Yes</button> 
                               <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                          </div>
                    
                          <!-- Modal footer -->
                       
                        </div>
                      </div>
                    </div>
                            <!-- modal end -->
</body>
</html>
