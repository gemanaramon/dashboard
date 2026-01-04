<?php session_start();
    if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
  else{ 
    if(!isset($_COOKIE["WeDoID"])) {

        header ('location: login'); 
    }else{
        if(!isset($_COOKIE["WeDoID"])) {
          session_destroy();
          header ('location: login'); 
        }else{
              try{
              include 'w_conn.php';
              $pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
              $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                 }
              catch(PDOException $e)
                 {
              die("ERROR: Could not connect. " . $e->getMessage());
                 }
              $statement = $pdo->prepare("select * from empdetails");
              $statement->execute();    

              while ($row=$statement->fetch()) {
                if (password_verify($row['EmpID'], $_COOKIE["WeDoID"])){
                        $_SESSION['id']=$row['EmpID'];
                
                        $statement = $pdo->prepare("select * from empdetails where EmpID = :un");
                        $statement->bindParam(':un' , $_SESSION['id']);
                        $statement->execute(); 
                        $count=$statement->rowCount();
                        $row=$statement->fetch();
                        $hash = $row['EmpPW'];
                        $_SESSION['UserType']=$row['EmpRoleID'];
                        $cid=$row['EmpCompID'];
                        $_SESSION['CompID']=$row['EmpCompID'];
                        $_SESSION['EmpISID']=$row['EmpISID'];
                        $statement = $pdo->prepare("select * from companies where CompanyID = :pw");
                        $statement->bindParam(':pw' , $cid);
                        $statement->execute(); 
                        $comcount=$statement->rowCount();
                        $row=$statement->fetch();
                        if ($comcount>0){
                          $_SESSION['CompanyName']=$row['CompanyDesc'];
                          $_SESSION['CompanyLogo']=$row['logopath'];
                          $_SESSION['CompanyColor']=$row['comcolor'];
                        }else{
                          $_SESSION['CompanyName']="ADMIN";
                          $_SESSION['CompanyLogo']="";
                          $_SESSION['CompanyColor']="red";
                        }
                         $_SESSION['PassHash']=$hash;

                }
                else{

                }
              }
            }
    }

  }
//   if ($_SESSION['UserType']<>1){
//   	header('location: index.php');
//   }
?>
<?php
	date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Leave Credit</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!--  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
      <script src="assets/js/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script><!-- 
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <!--   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/script.js"></script>
	<script src="assets/js/script-reports.js"></script>
	<script type="text/javascript" src="assets/js/script-modules.js"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
  	<link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
	<script>
	 function FunctionChangedate(){
              var d = new Date();
              var n = d.getFullYear();
              var x = document.getElementById("lstarts").max = n + "-12-31" ;
              var x = document.getElementById("lenddate").max = n + "-12-31" ;
              var x = document.getElementById("lstarts").min = n + "-01-01" ;
              var x = document.getElementById("lenddate").min = n + "-01-01" ;
        }
	</script>
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
				<h2>Leave Credits</h2>
				<h4>as of Today</h4>
				<table  class="table table-striped">
        				<thead>
        					<tr><th>Employee Name</th>
        						<th>Used Credit</th>
        						<th>Current Credit Earned</th>
        						<th>Remaining Credit</th>
        						<th>View Used Credit</th>
        					</tr>
        				</thead>
        				<tbody>
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
     							 $sql="SELECT * from empdetails as a inner join employees as b on a.EmpID=b.EmpID 
								 INNER JOIN  credit as c on b.EmpID=c.EmpID WHERE EmpDOR IS NOT NULL AND b.EmpStatusID=1 order by EmpLN asc";
									$statement = $pdo->prepare($sql);
									$statement->execute();
									while ($row = $statement->fetch()){
										$id=$row['EmpID'];

										//get the leave credit (15 or 10) start
										$varTH=0;
										$getTH = "select * from credit where EmpID = :id";
										$stmtTH = $pdo->prepare($getTH);
										$stmtTH->bindParam(':id',$id);
										$stmtTH->execute();
										$crdetailTH = $stmtTH->fetch();
										$crcntTH = $stmtTH->rowCount();
		
										if ($crcntTH > 0) {
											if( $crdetailTH['CTH']==15){
												$varTH=15;
											}
										}    

										if($varTH==15){
											?>
												<tr>
													<td><?php echo $row['EmpLN'] . ", " . $row['EmpFN'];  ?></td>
													<td> <?php echo ($row['CTH'] - $row['CT']);  ?> </td>
													<td> <?php echo ($row['CT']);  ?> </td>

													<td> <?php echo "-"  ?> </td>
													<td> <button class="btn btn-warning" data-toggle="modal" data-target="#myModal<?php echo  $row['EmpID']; ?>"><i class="fa fa-eye" aria-hidden="true"></i></button></td>
												<div class="modal fade" id="myModal<?php echo  $row['EmpID']; ?>" role="dialog">
													<div class="modal-dialog">
													<!-- Modal content-->
													<div class="modal-content" >
															<div class="modal-header"  style="<?php echo "background-color: " . $_SESSION['CompanyColor']; ?>;padding: 7px;">
															<button type="button" class="close" style="color: #fff;opacity:1;" data-dismiss="modal">&times;</button>
									
															</div>
															<div class="modal-body">
																<div class="tbl" style="width: 100%;padding: 5px;">
																	<div>
																		<label style="width: 45%">Date</label>
																		<label style="width: 45%">Leave Type</label>
																	</div>
										
																	<?php
																
																		$yr=date("Y");
																		$sql="select * from hleavesbd inner join leaves on hleavesbd.LType=leaves.LeaveID 
																		where EmpID=:id and year(LStart)=:dyyear and LStatus=4";
																		// $sql="select * from hleavesbd inner join leaves_validation on hleavesbd.LType=leaves_validation.sid inner join leaves on leaves_validation.lid=leaves.LeaveID where EmpID=:id and year(LStart)=:dyyear and LStatus=4";
																		$stmt2 = $pdo->prepare($sql);
																		$stmt2->bindParam(':id' ,$id);
																		$stmt2->bindParam(':dyyear' ,$yr);
																		$stmt2->execute();
																		while($row2 = $stmt2->fetch()){
																			if ($row2['LeaveID']==22 || $row2['LeaveID']==34 || $row2['LeaveID']==30 || $row2['LeaveID']==35){

																	?>
																	<div style="border-bottom: 2px solid #ddd;padding: 5px;">
																		<label style="width: 45%"><?php echo date("F d, Y", strtotime($row2['LStart'])); ?></label>
																		<label style="width: 45%"><?php echo $row2['LeaveDesc']; ?></label>
																	</div>
																	<?php
																		}
																			}
																	?>
																</div>
															</div>
														</div>
													</div>
												</div>
												<tr>
											<?php

										}else{
										?>
											<tr>
												<td><?php echo $row['EmpLN'] . ", " . $row['EmpFN'];  ?></td>
												<td> <?php echo ($row['CTH'] - $row['CT']);  ?> </td>
												<td>
													<?php 
														if (is_null($row['EmpDOR'])){
															$creditEarned= "Missing Regularization Date";
														}else{
															$dth=$row['EmpDOR'];
															$yr=date("Y", strtotime($dth));
															$cyr=date("Y");

															if ($yr < $cyr ){													      			
																	$sql = "select * from credit where EmpID = :id";
																	$stmt = $pdo->prepare($sql);
																	$stmt->bindParam(':id' ,$id);
																	$stmt->execute();
																	$crdetail = $stmt->fetch();
																	$crcnt = $stmt->rowCount();
																		if ( $crcnt > 0){
																			$crh= $crdetail['CTH'];
																			$crth= $crdetail['CT'];
																			$tdy=date("Y");
																			$tdy1=date("Y" , strtotime(date("Y") . "+1 years"));
																			$date1=date_create("1/1/" . $tdy);
																			$date2=date_create("1/1/" . $tdy1);
																			$diff=date_diff($date1,$date2);
																			//output data
																			$noOfDays= $diff->format("%a")/12;
																			//credit per month earning
																			$cdPerMonth= $crh / 12;
																			//credit per day earning
																			$cdPerDay= $cdPerMonth / $noOfDays;
																			//get no of days from jan to present
																			$todaydate=date("Y");
																			$todaydate1=date("m/d/Y");
																			$gnOfdJan=date_create("1/1/" . $todaydate);
																			$gnOfdCur=date_create($todaydate1);
																				//$gnOfdCur=date_create("01/01/2021");
																			$diff2=date_diff($gnOfdJan,$gnOfdCur);
																			//output data
																			$gnOfdJanCur= $diff2->format("%a");
																			//get use credits and subtract to total earned credits ramon
																				$useCredit= $crh - $crth ;
																				//get total earned creidit
																				
																				//echo $cdPerDay . "-" .$cdPerMonth ." - ". $gnOfdJanCur;
																				// echo  number_format($creditEarned, 9, '.', '');
																				// if(($gnOfdJanCur / $noOfDays) < 3 )
																				// {
																					
																				//     $creditEarned = 0;
																				// }
																				// else
																				// {
																					//$creditEarned = floor(($cdPerDay * $gnOfdJanCur ) - $useCredit);
																					$creditEarned = ($cdPerDay * $gnOfdJanCur ) - $useCredit;
																						echo  number_format($creditEarned, 4, '.', '');
																						
																						
																				// }
																		}else{
																				$creditEarned= "Missing credit logs";
																				//return;
																		}
															}else{
																$sql = "select * from credit where EmpID = :id";
																$stmt = $pdo->prepare($sql);
																	$stmt->bindParam(':id' ,$id);
																	$stmt->execute();
																	$crdetail = $stmt->fetch();
																	$crcnt = $stmt->rowCount();
																		if ( $crcnt > 0){
																			$crh= $crdetail['CTH'];
																			$crth= $crdetail['CT'];
																			$tdy=date("Y");
																			$tdy1=date("Y" , strtotime(date("Y") . "+1 years"));
																			$date1=date_create("1/1/" . $tdy);
																			$date2=date_create("1/1/" . $tdy1);
																			$diff=date_diff($date1,$date2);
																			//output data
																			$noOfDays= $diff->format("%a")/12;
																			//credit per month earning
																			$cdPerMonth= $crh / 12;
																			//credit per day earning
																			$cdPerDay= $cdPerMonth / $noOfDays;
																			//get no of days from jan to present
																			$todaydate=date("Y");
																			$todaydate1=date("m/d/Y");
																			$gnOfdJan=date_create($dth);
																				$gnOfdCur=date_create($todaydate1);
																			//$gnOfdCur=date_create("12/31/2020");
																			$diff2=date_diff($gnOfdJan,$gnOfdCur);
																			//output data
																			$gnOfdJanCur= $diff2->format("%a");
																			//get use credits and subtract to total earned credits ramon
																				$useCredit= $crh - $crth ;
																				//get total earned creidit
																				//$creditEarned = floor(($cdPerDay * $gnOfdJanCur ) - $useCredit);
																				//echo $cdPerDay . "-" .$cdPerMonth ." - ". $gnOfdJanCur;
																				
																				// echo $gnOfdJanCur . " /" . $noOfDays;
																			// 	echo  number_format($creditEarned, 4, '.', '');
																			// 	if(($gnOfdJanCur / $noOfDays) < 3 )
																			// 	{
																					
																			// 	    $creditEarned = 0;
																			// 	}
																			// 	else
																			// 	{
																					// $creditEarned = floor(($cdPerDay * $gnOfdJanCur ) - $useCredit);
																					$creditEarned =($cdPerDay * $gnOfdJanCur ) - $useCredit;
																					
																					
																					echo  number_format($creditEarned, 4, '.', '');
																					
																					
																			// 	}
																	//if greater than
																	//return;
																		}
															}
														}
													?>
												</td>
												
												<td> <?php  $lcred = ($row['CTH'] - ($creditEarned + ($row['CTH'] - $row['CT']) ));  echo  number_format($lcred, 4, '.', ''); ?></td>
												<td> <button class="btn btn-warning" data-toggle="modal" data-target="#myModal<?php echo  $row['EmpID']; ?>"><i class="fa fa-eye" aria-hidden="true"></i></button></td>
												<div class="modal fade" id="myModal<?php echo  $row['EmpID']; ?>" role="dialog">
													<div class="modal-dialog">
													<!-- Modal content-->
													<div class="modal-content" >
															<div class="modal-header"  style="<?php echo "background-color: " . $_SESSION['CompanyColor']; ?>;padding: 7px;">
															<button type="button" class="close" style="color: #fff;opacity:1;" data-dismiss="modal">&times;</button>
									
															</div>
															<div class="modal-body">
																<div class="tbl" style="width: 100%;padding: 5px;">
																	<div>
																		<label style="width: 45%">Date</label>
																		<label style="width: 45%">Leave Type</label>
																	</div>
										
																	<?php
																
																	$yr=date("Y");
																		$sql="select * from hleavesbd inner join leaves on hleavesbd.LType=leaves.LeaveID 
																		where EmpID=:id and year(LStart)=:dyyear and LStatus=4";
																		// $sql="select * from hleavesbd inner join leaves_validation on hleavesbd.LType=leaves_validation.sid inner join leaves on leaves_validation.lid=leaves.LeaveID where EmpID=:id and year(LStart)=:dyyear and LStatus=4";
																		$stmt2 = $pdo->prepare($sql);
																		$stmt2->bindParam(':id' ,$id);
																		$stmt2->bindParam(':dyyear' ,$yr);
																		$stmt2->execute();
																		while($row2 = $stmt2->fetch()){
																			if ($row2['LeaveID']==22 || $row2['LeaveID']==34 || $row2['LeaveID']==30 || $row2['LeaveID']==35){

																	?>
																	<div style="border-bottom: 2px solid #ddd;padding: 5px;">
																		<label style="width: 45%"><?php echo date("F d, Y", strtotime($row2['LStart'])); ?></label>
																		<label style="width: 45%"><?php echo $row2['LeaveDesc']; ?></label>
																	</div>
																	<?php
																		}
																			}
																	?>
																</div>
															</div>
														</div>
													</div>
												</div>
											</tr>
										<?php
										}
									// get the leave credit (15 or 10) end 
										?>	
		                    <?php  		
		                      	}
             				?>
        					
        				</tbody>
				</table>
			</div>
		</div>
	</div>
</body>
</html>