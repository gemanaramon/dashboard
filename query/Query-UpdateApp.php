<?php

//approve application status update
  include 'w_conn.php';session_start();
  if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
  else{ header ('location: login.php'); }

  try{
    $pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
  }

   date_default_timezone_set("Asia/Manila");
   $tdy= date("Y-m-d H:i:s"); 
   $todaydt=date("Y-m-d H:i:s"); 

    if ($_SESSION['UserType']==3){
        $stat = 2;
    }else if ($_SESSION['UserType']==2){
        $stat = 2;
    }else{
        $stat = 4;
    }
     
   if (isset($_GET['ntype'])){
      if ($_GET['ntype']=="EO"){
          $sql = "UPDATE earlyout SET Status=:st,DateTimeUpdated=:dtu where SID=".$_GET['id'];
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':st' ,$stat);
          $stmt->bindParam(':dtu' ,$tdy);
          $stmt->execute();

          $sql2="select employees.EmpLN as LN,employees.EmpFN as FN from employees inner join earlyout on employees.EmpID=earlyout.EMPID where SID=" . $_GET['id'];
          $stmt = $pdo->prepare($sql2);
          $stmt->execute();
          $row=$stmt->fetch();
          $nameE=$row['FN'] . " " . $row['LN'];

          $id=$_SESSION['id'];
          $ch="Approved EO of " .  $nameE;
          $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id' , $id);
          $stmt->bindParam(':empact', $ch);
          $stmt->bindParam(':ddt', $todaydt);
          $stmt->execute(); 
      }else if($_GET['ntype']=="OB"){
          // update table obhd
          $sql2 = "UPDATE obshbd SET OBStatus=:st,OBUpdated=:dtu where OBID=".$_GET['id'];
          $stmt2 = $pdo->prepare($sql2);
          $stmt2->bindParam(':st' ,$stat);
          $stmt2->bindParam(':dtu' ,$tdy);
          $stmt2->execute();
          //ob 
          $sql = "UPDATE obs SET OBStatus=:st,OBUpdated=:dtu where OBID=".$_GET['id'];
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':st' ,$stat);
          $stmt->bindParam(':dtu' ,$tdy);
          $stmt->execute();

          $sql2="select employees.EmpLN as LN,employees.EmpFN as FN from employees inner join obs on employees.EmpID=obs.EmpID where OBID=" . $_GET['id'];
          $stmt = $pdo->prepare($sql2);
          $stmt->execute();
          $row=$stmt->fetch();
          $nameE=$row['FN'] . " " . $row['LN'];

          $id=$_SESSION['id'];
          $ch="Approved OB of " . $nameE;
          // insert into dars
          $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id' , $id);
          $stmt->bindParam(':empact', $ch);
          $stmt->bindParam(':ddt', $todaydt);
          $stmt->execute(); 
      }else if($_GET['ntype']=="HL"){//for leave function loading and approving
            {//select employee details and leave
                $sql2="SELECT employees.EmpLN as LN,employees.EmpFN as FN from employees inner join hleaves on employees.EmpID=hleaves.EmpID where LeaveID=:idd";
                $stmt = $pdo->prepare($sql2);
                $stmt->bindParam(':idd' ,$_GET['id']);
                $stmt->execute();
                $row=$stmt->fetch();
                $nameE=$row['FN'] . " " . $row['LN'];
            }

            if ($_SESSION['UserType']==200){//this is for IS function
                //if immedaite
                $sql = "UPDATE hleaves set LStatus=:st,LDateTimeUpdated=:ldtup  where LeaveID=:lid";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':st' ,$stat);
                $stmt->bindParam(':ldtup' ,$todaydt);
                $stmt->bindParam(':lid' ,$_GET['id']);
                $stmt->execute();

                $sql = "UPDATE hleavesbd set LStatus=:st,LDateTimeUpdated=:ldtup  where FID=:lid";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':st' ,$stat);
                $stmt->bindParam(':ldtup' ,$todaydt);
                $stmt->bindParam(':lid' ,$_GET['id']);
                $stmt->execute();

                $id=$_SESSION['id'];
                $ch="Approved Leaves of " . $nameE;
                // insert into dars
                $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id' , $id);
                $stmt->bindParam(':empact', $ch);
                $stmt->bindParam(':ddt', $todaydt);
                $stmt->execute(); 
                echo json_encode(array("uid" =>0, "dd" => '0')); 
            }else{//for hr function
                try { 
                        {//get leave
                            $sql = "SELECT * FROM hleaves as a where a.LeaveID=" . $_GET['id']." order by LStart";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $rowh = $stmt->fetch();
                            $rowhcount = $stmt->rowCount();

                            $leaveType=0;
                            $creditEarned=0;
                            $lduration= 0;
                            $creditloop=0;
                            $earnedCredit=0;
                        }

                        if ($rowhcount==1){//if found load all the nessesary 
                            {//initiallizing the data to be use
                                $leaveType= $rowh['LType'];
                                $EmplID=  $rowh['EmpID'];
                                $datestart= $rowh['LStart'];
                                $dateend= $rowh['LEnd'];
                                $lduration= $rowh['LDuration'];
                                $date1=date_create($dateend);
                                $date2=date_create($datestart);
                                $diff=date_diff($date1,$date2);
                                $DayDur= $diff->format("%a");
                                

                            }
                            
                            if ($EmplID=="WeDoinc-0003" and $leaveType == 34){//for terminal leave function
                                $sql="select SUM(LDuration) as SumOfDur from hleavesbd where LType=12 and Lstatus=4";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                $leaveterminal = $stmt->fetch();
                                if ((($leaveterminal['SumOfDur']/60) /10) < 8){
                                        // $sql = "UPDATE hleaves SET LStatus=9,LDateTimeUpdated=:dtu where LeaveID=:idd";
                                        // $stmt = $pdo->prepare($sql);                       
                                        // $stmt->bindParam(':dtu' ,$tdy);
                                        // $stmt->bindParam(':idd' ,$_GET['id']);
                                        // // $stmt->execute();

                                        // $id=$_SESSION['id'];
                                        // $ch="Approved Leaves of " . $nameE;
                                        //      // insert into dars
                                        // $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                                        // $stmt = $pdo->prepare($sql);
                                        // $stmt->bindParam(':id' , $id);
                                        // $stmt->bindParam(':empact', $ch);
                                        // $stmt->bindParam(':ddt', $todaydt);
                                        // // $stmt->execute();   

                                        // $sql = "UPDATE hleavesbd SET LStatus=:st,LDateTimeUpdated=:dtu where FID=:idd";
                                        //       $stmt = $pdo->prepare($sql);
                                        // $stmt->bindParam(':st' ,$stat);

                                        // $stmt->bindParam(':dtu' ,$tdy);
                                        // $stmt->bindParam(':idd' ,$_GET['id']);
                                        // $stmt->execute();  
                                        // return;
                                }else{
                                        $reas="0 Terminal Leave";
                                        $sql = "UPDATE hleaves SET LStatus=6,LDateTimeUpdated=:dtu,LHRReason=:rsn where LeaveID=:idd";
                                        $stmt = $pdo->prepare($sql);                       
                                        $stmt->bindParam(':dtu' ,$todaydt);
                                        $stmt->bindParam(':rsn' ,$reas);
                                        $stmt->bindParam(':idd' ,$_GET['id']);
                                        $stmt->execute();
                
                                        $sql = "UPDATE hleavesbd SET LStatus=6,LDateTimeUpdated=:dtu,LHRReason=:rsn where FID=:idd";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindParam(':dtu' ,$todaydt);
                                        $stmt->bindParam(':rsn' ,$reas);
                                        $stmt->bindParam(':idd' ,$_GET['id']);
                                        $stmt->execute();   
                
                
                                        $id=$_SESSION['id'];
                                        $ch="Disapproved Leaves of " . $nameE . " Reason : 0 Terminal Leave";
                                            // insert into dars
                                        $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindParam(':id' , $id);
                                        $stmt->bindParam(':empact', $ch);
                                        $stmt->bindParam(':ddt', $todaydt);
                                        $stmt->execute(); 
                                        return;
                                }
                            }

                            {//get the leave credit (15 or 10)
                                $varTH=0;
                                $varCT=0;
                             
                                // $getTH = "select * from credit where EmpID = :id";
                                $sqlEmp = "SELECT a.EmpDOR, c.CT, c.CTH 
                                FROM empdetails as a 
                                INNER JOIN credit as c ON a.EmpID = c.EmpID 
                                WHERE a.EmpID = :id";
                                $stmtTH = $pdo->prepare($sqlEmp);
                                $stmtTH->bindParam(':id',$EmplID );
                                $stmtTH->execute();
                                $crdetailTH = $stmtTH->fetch();
                                $crcntTH = $stmtTH->rowCount();

                                if ($crcntTH > 0) {

                                    if($EmplID!="WeDoinc-0145"){
                                        // if( $crdetailTH['CTH']==10){
                                            $varTH= $crdetailTH['CTH'];
                                            $varCT= $crdetailTH['CT'];
                                        //
                                    }else{
                                        //special case for WeDoinc-0145
                                        $cth = $crdetailTH['CTH'];
                                        $ct  = $crdetailTH['CT'];
                                        $dor = $crdetailTH['EmpDOR']; // Ensure EmpDOR is available in your scope
                                        
                                        if (!empty($dor)) {
                                            $currentYear = date("Y");
                                            $hireYear    = date("Y", strtotime($dor));
                                            
                                            // 1. Set start date: Jan 1 or Date of Regularization
                                            $calcStart = ($hireYear < $currentYear) 
                                                ? date_create("1/1/" . $currentYear) 
                                                : date_create($dor);
                                            
                                            $dateNow = date_create(date("Y-m-d"));

                                            // 2. Calculate days in the current year
                                            $dateJan1     = date_create("1/1/" . $currentYear);
                                            $dateNextJan1 = date_create("1/1/" . ($currentYear + 1));
                                            $daysInYear   = date_diff($dateJan1, $dateNextJan1)->format("%a");

                                            // 3. Pro-rated math
                                            $cdPerDay   = $cth / $daysInYear;
                                            $daysActive = date_diff($calcStart, $dateNow)->format("%a");
                                            $usedCredit = $cth - $ct;

                                            // 4. Set $varCT to the LIVE earned value for the approval loop
                                            $calculatedEarnings = ($cdPerDay * $daysActive) +4 - $usedCredit;
                                            
                                            // We use floor() to ensure we only approve full days earned
                                            $varCT = floor($calculatedEarnings); 
                                            $varTH = $cth;
                                        } else {
                                            // Fallback if no DOR found
                                            $varCT = $ct;
                                            $varTH = $cth;
                                        }
                                    }
                                    
                                }    
                            }

    echo $varCT;
    return false;
                            {//no earning
                                    
                               try {
                                $durData = $varCT;
                                    // 1. Start the transaction
                                    $pdo->beginTransaction();
                                    
                                        // Define leave types for better readability
                                        $medicalSilTypes = [22, 30, 38, 24]; // Vacation, Medical, Force, Emergency
                                        $emergencyTH = 0;

                                        // Fetch the employee's role
                                        $getRoleSql = "SELECT EmpRoleID FROM empdetails WHERE EmpID = :id";
                                        $stmtRole = $pdo->prepare($getRoleSql);
                                        $stmtRole->execute([':id' => $EmplID]);
                                        $empData = $stmtRole->fetch();

                                        $emergencyTH = 0; 
                                        if ($empData && $leaveType == 24) { // Emergency Leave
                                            $role = $empData['EmpRoleID'];
                                            // Setting thresholds based on your requirements
                                            if ($role == 1)     { $emergencyTH = 15; }
                                            elseif ($role == 2) { $emergencyTH = 5; }
                                            elseif ($role == 3) { $emergencyTH = 4; }
                                        }

                                        if (in_array($leaveType, $medicalSilTypes)) {
                                            $currentYear = date('Y');
                                            $localCount = 0;
                                            
                                            // 1. Get how many days have ALREADY been used this year before this loop
                                            $totalApprovedCount = 0;
                                            if ($leaveType == 24) {
                                                $sqlCount = "SELECT COUNT(*) FROM hleavesbd hb 
                                                            JOIN hleaves h ON hb.FID = h.LeaveID 
                                                            WHERE h.EmpID = :empid 
                                                            AND h.LType = 24 
                                                            AND hb.LStatus = 4 
                                                            AND YEAR(hb.LStart) = :year";
                                                $stmtCount = $pdo->prepare($sqlCount);
                                                $stmtCount->execute([':empid' => $EmplID, ':year' => $currentYear]);
                                                $totalApprovedCount = (int)$stmtCount->fetchColumn();
                                            }
                                        
                                            while ($DayDur >= 0) {
                                                // 1. Determine the status based on current count + historical approved
                                                if ($leaveType == 24) {
                                                    if ($leaveType == 24 && ($totalApprovedCount + $localCount) >= $emergencyTH) {
                                                        $statusFiD = 6; // Excess threshold -> Without Pay
                                                    } else {
                                                        $statusFiD = 4; // Under threshold -> With Pay
                                                    }
                                                } else {
                                                    if ($durData > 0) {
                                                        $statusFiD = 4; // With Pay
                                                        $durData--;
                                                    } else {
                                                        $statusFiD = 6; // Without Pay
                                                    }
                                                }

                                                // 2. Attempt the Update
                                                $sql = "UPDATE hleavesbd SET LStatus=:st, LDateTimeUpdated=:dtu 
                                                        WHERE FID=:idd AND LStart=:dtstart";
                                                $stmt = $pdo->prepare($sql);
                                                $stmt->execute([
                                                    ':st'      => $statusFiD,
                                                    ':dtu'     => $todaydt,
                                                    ':idd'     => $_GET['id'],
                                                    ':dtstart' => $datestart
                                                ]);

                                                // 3. VALIDATION: Only increment counters if a row was actually updated
                                                // This ensures Rest Days or days without schedules don't consume your threshold/credits
                                                if ($stmt->rowCount() > 0) {
                                                    if ($statusFiD == 4) {
                                                        $localCount++; 
                                                    }
                                                }

                                                // Advance date and decrement duration regardless of update success
                                                $datestart = date('Y-m-d', strtotime($datestart . ' + 1 days'));
                                                $DayDur--;
                                            }

                                            // 4. Update the main leave header status
                                            $sqlHLeaves = "UPDATE hleaves SET LStatus=9, LDateTimeUpdated=:dtu WHERE LeaveID=:idd";
                                            $stmtHLeaves = $pdo->prepare($sqlHLeaves);
                                            $stmtHLeaves->execute([':dtu' => $todaydt, ':idd' => $_GET['id']]);

                                            // 5. Deduct only the credits that were marked as 'With Pay' (LStatus 4)
                                            $xcrd = $varCT - $localCount;

                                            $sqlCredit = "UPDATE credit SET CT=:ncrd WHERE EmpID=:idd";
                                            $stmtCredit = $pdo->prepare($sqlCredit);
                                            $stmtCredit->execute([':ncrd' => $xcrd, ':idd' => $EmplID]);
                                                echo json_encode(array("uid" => $_SESSION['UserType'], "dd" => 35, "lc" => $localCount)); 
                                        } else {
                                            // Logic for other leave types
                                            $sqlHLeaves = "UPDATE hleaves SET LStatus=9, LDateTimeUpdated=:dtu WHERE LeaveID=:idd";
                                            $stmtHLeaves = $pdo->prepare($sqlHLeaves);
                                            $stmtHLeaves->execute([':dtu' => $tdy, ':idd' => $_GET['id']]);

                                            // Insert into DARS (Activity Log)
                                            $ch = "Approved Leaves of " . $nameE;
                                            $sqlDar = "INSERT INTO dars (EmpID, EmpActivity, DarDateTime) VALUES (:id, :empact, :ddt)";
                                            $stmtDar = $pdo->prepare($sqlDar);
                                            $stmtDar->execute([
                                                ':id'     => $_SESSION['id'],
                                                ':empact' => $ch,
                                                ':ddt'    => $todaydt
                                            ]);

                                            // Update hleavesbd status
                                            $sqlBd = "UPDATE hleavesbd SET LStatus=:st, LDateTimeUpdated=:dtu WHERE FID=:idd";
                                            $stmtBd = $pdo->prepare($sqlBd);
                                            $stmtBd->execute([':st' => $stat, ':dtu' => $tdy, ':idd' => $_GET['id']]);

                                            echo json_encode(array("uid" => 0, "dd" => '0'));
                                        }
                                 

                                    // 2. Commit the transaction if everything is successful
                                    $pdo->commit();

                                } catch (Exception $e) {
                                    // 3. Rollback the transaction if anything goes wrong
                                    if ($pdo->inTransaction()) {
                                        $pdo->rollBack();
                                    }
                                    // Handle error (log it or show a message)
                                    error_log($e->getMessage());
                                    echo json_encode(array("error" => "Transaction failed: " . $e->getMessage()));
                                }   

                            }
                         
                            //start p1 for earning credit process and treshhold clsssifcation
                            // if($varTH==15){
                              
                            //     if ( $leaveType == 22 || $leaveType == 30 || $leaveType == 35 ){//if leave type is medical and sil

                            //         {//get the total used credit for particular leave
                                       
                            //             $durData=0;
                            //             $yr=date("Y");
                            //             $sql="select LDuration as duration from hleavesbd 
                            //             where (EmpID=:id and year(LStart) =$yr and LStatus=4 and LType= $leaveType)";
                            //             $stmt2 = $pdo->prepare($sql);
                            //             $stmt2->bindParam(':id' ,$EmplID);
                            //             $stmt2->execute();
                            //             $crcnt = $stmt2->rowCount();
                                       
                            //             if($crcnt > 0){ //return false if validation meet true
                            //                 $x=0;
                            //                 while ($getDuration = $stmt2->fetch()) {
                            //                     $durData+=$getDuration['duration'] ;
                            //                     $x+=1;
                            //                 }
                            //             }
                            //             $durData = (5 - ($durData / 60 / 10 )); //get the total unused credit for applied leave type

                            //         }
                                    
                            //         {//loop and update each filing on hleavesbd
                            //             $statusFiD=0;
                            //             $newdur=0;
                                      
                            //             while ($DayDur >= 0 ){         
                            //                 if( $durData != 0 ){
                            //                     $statusFiD=4;
                                              
                            //                 }else{
                            //                     $statusFiD=6;
                            //                 }

                            //                 $sql = "UPDATE hleavesbd SET LStatus=:st,LDateTimeUpdated=:dtu where FID=:idd AND LStart=:dtstart";
                            //                 $stmt = $pdo->prepare($sql);
                            //                 $stmt->bindParam(':st' ,$statusFiD);
                            //                 $stmt->bindParam(':dtu' ,$todaydt);
                            //                 $stmt->bindParam(':idd' ,$_GET['id']);
                            //                 $stmt->bindParam(':dtstart' ,$datestart);
                            //                 $stmt->execute();
                            //                 $count = $stmt->rowCount();

                            //                 if($count!='0'){
                            //                     if ($durData !=0){
                            //                         $durData= $durData - 1;
                            //                         {//get the duration of the updated leave
                            //                              $yr=date("Y");
                            //                            $sqlGetDuration = "SELECT * FROM hleavesbd as a where a.FID=:idd order by LStart";
                            //                             $stmtGetDuration = $pdo->prepare($sqlGetDuration);
                            //                             $stmtGetDuration->bindParam(':idd' ,$_GET['id']);

                            //                             $stmtGetDuration->execute();
                            //                             $rowhGetDuration = $stmtGetDuration->fetch();
                            //                             $rowhcountGetDuration = $stmtGetDuration->rowCount();
                            //                             $newdur+=  $rowhGetDuration['LDuration'] / 60 / 10;
                            //                         }
                                                
                            //                     }else{
                            //                         $durData= 0;
                            //                     }  
                            //                 }
                            //                 $datestart=date('Y-m-d', strtotime($datestart . ' + 1 days'));
                            //                 $DayDur = $DayDur - 1;
                            //             }                                      
                            //         }  

                            //         {//update hleaves to process
                            //             $sql = "UPDATE hleaves SET LStatus=9,LDateTimeUpdated=:dtu where LeaveID=:idd";
                            //             $stmt = $pdo->prepare($sql);                       
                            //             $stmt->bindParam(':dtu' ,$todaydt);
                            //             $stmt->bindParam(':idd' ,$_GET['id']);
                            //             $stmt->execute();
                            //         }

                            //         {//update the credit deduct total used credit for this leave
                                     
                            //             $val15Credit=$varCT;
                            //             $xcrd = $val15Credit - $newdur;
                            //             $sql = "UPDATE credit SET CT=:ncrd where EmpID=:idd";
                            //             $stmt = $pdo->prepare($sql);
                            //             $stmt->bindParam(':ncrd' ,$xcrd);
                            //             $stmt->bindParam(':idd' ,$EmplID);
                            //             $stmt->execute();
                            //         }
                            //     echo json_encode(array("uid" => $_SESSION['UserType'], "dd" => 35)); 


                            //     }else{//code for non sil leave here
                            //         $sql = "UPDATE hleaves SET LStatus=9,LDateTimeUpdated=:dtu where LeaveID=:idd";
                            //         $stmt = $pdo->prepare($sql);                       
                            //         $stmt->bindParam(':dtu' ,$tdy);
                            //         $stmt->bindParam(':idd' ,$_GET['id']);
                            //         $stmt->execute();

                            //         $id=$_SESSION['id'];
                            //         $ch="Approved Leaves of " . $nameE;
                            //             // insert into dars
                            //         $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                            //         $stmt = $pdo->prepare($sql);
                            //         $stmt->bindParam(':id' , $id);
                            //         $stmt->bindParam(':empact', $ch);
                            //         $stmt->bindParam(':ddt', $todaydt);
                            //         $stmt->execute();   

                            //         $sql = "UPDATE hleavesbd SET LStatus=:st,LDateTimeUpdated=:dtu where FID=:idd";
                            //             $stmt = $pdo->prepare($sql);
                            //         $stmt->bindParam(':st' ,$stat);
                            //         $stmt->bindParam(':dtu' ,$tdy);
                            //         $stmt->bindParam(':idd' ,$_GET['id']);
                            //         $stmt->execute();   
                                    
                            //         echo json_encode(array("uid" =>0, "dd" => '0'));
                            //     }

                            // }else{//if 10
                            //     //update this to capture the other leave type
                            //     if ( $leaveType == 22 || $leaveType == 30 ){//if leave type is medical ans sil
                            //         $sql = "select * from credit where EmpID = :id";
                            //         $stmt = $pdo->prepare($sql);
                            //         $stmt->bindParam(':id' ,$EmplID);
                            //         $stmt->execute();
                            //         $crdetail = $stmt->fetch();
                            //         $crcnt = $stmt->rowCount();

                            //         {//get the total used credit for particular leave
                                        
                            //             $durData=0;
                            //             $yr=date("Y");
                            //             $sql222="select * from hleavesbd 
                            //             where (EmpID=:id and year(LStart)=$yr and LStatus=4 and LType= $leaveType)";
                            //             $stmt222 = $pdo->prepare($sql222);
                            //             $stmt222->bindParam(':id',$EmplID);
                            //             $stmt222->execute();
                            //             $crcntb1 = $stmt222->rowCount();
                                        
                            //             $x=0;
                            //             if($crcntb1 > 0){ //return false if validation meet true
                                         
                            //                 while ($getDuration222 = $stmt222->fetch()) {
                            //                     $durData+= $getDuration222['LDuration'];
                                                
                            //                 }
                            //             }
                                        
                            //             $durData = ( 5 - ((($durData / 60) / 10))); //get the total unused credit for applied leave type
                                        
                                       
                            //         }
                                                        
                            //         if ( $crcnt > 0) {//if you have credit
                            //                 $crh= $crdetail['CTH'];
                            //                 $crth= $crdetail['CT'];
                            //                 $tdy=date("Y");
                            //                 $tdy1=date("Y" , strtotime(date("Y") . "+1 years"));
                            //                 $date1=date_create("1/1/" . $tdy);
                            //                 $date2=date_create("1/1/" . $tdy1);
                            //                 $diff=date_diff($date1,$date2);
                            //                 $noOfDays= $diff->format("%a")/12;          //count the total number days in a year
                            //                 $cdPerMonth= $crh / 12;                     //get the credit earned per month
                            //                 $cdPerDay= $cdPerMonth / $noOfDays;         //get the credit earned per day
                            //                 $todaydate=date("Y");                       //get the current year
                            //                 $todaydate1=date("m/d/Y");                  //get and format the current date
                            //                 $gnOfdJan=date_create("1/1/" . $todaydate); //create the date january using this year
                            //                 $gnOfdCur=date_create($todaydate1);         //create the current date assign above
                            //                 // $gnOfdCur=date_create("11/1/2024"  );         //create the current date assign above
                            //                 $diff2=date_diff($gnOfdJan,$gnOfdCur);      //get the diffirence of two date january to present 
                            //                 $gnOfdJanCur= $diff2->format("%a");         //format the get date to total number of days
                            //                 $useCredit= $crh - $crth ;                  //getting the availbale credits by subtracting the treshold and the used credit
                            //                 $creditEarned = ($cdPerDay * ($gnOfdJanCur + 1)) - $useCredit; //get the total earned credit 
                            //                 $earnedCredit = floor($creditEarned);                     //this will remove any decimal place 
                            //         }else{//this will return 
                            //                 echo "Missing credits";
                            //                 return;
                            //         }

                            //         if (floor($creditEarned) == 0 or floor($creditEarned) < 1 ){//if no availble credit 

                            //                 {//dashboard update the filling to disapproved the filling
                            //                     $reas=" No Credits";
                            //                     $sql = "UPDATE hleaves SET LStatus=6,LDateTimeUpdated=:dtu,LHRReason=:rsn where LeaveID=:idd";
                            //                     $stmt = $pdo->prepare($sql);                       
                            //                     $stmt->bindParam(':dtu' ,$todaydt);
                            //                     $stmt->bindParam(':rsn' ,$reas);
                            //                     $stmt->bindParam(':idd' ,$_GET['id']);
                            //                     $stmt->execute();
                            //                 }

                            //                 {//dashboard update the filling to disapproved the filling 
                            //                     $sql = "UPDATE hleavesbd SET LStatus=6,LDateTimeUpdated=:dtu,LHRReason=:rsn where FID=:idd";
                            //                     $stmt = $pdo->prepare($sql);
                            //                     $stmt->bindParam(':dtu' ,$todaydt);
                            //                     $stmt->bindParam(':rsn' ,$reas);
                            //                     $stmt->bindParam(':idd' ,$_GET['id']);
                            //                     $stmt->execute();   
                            //                 }

                            //                 {// insert to dar
                            //                     $id=$_SESSION['id'];
                            //                     $ch="Disapproved Leaves of " . $nameE;
                                                    
                            //                     $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                            //                     $stmt = $pdo->prepare($sql);
                            //                     $stmt->bindParam(':id' , $id);
                            //                     $stmt->bindParam(':empact', $ch);
                            //                     $stmt->bindParam(':ddt', $todaydt);
                            //                     $stmt->execute(); 
                            //                 }
                                    
                            //         }else{    
                            //                 if(floor($creditEarned) < $lduration ){//validate if the credit earned is less than the total duration file system will automatically tag paid or not

                            //                     {//loop and update each filing on hleavesbd
                            //                         $newdur=0;
                            //                         while ($DayDur>=0 ){ 

                            //                             if($durData!=0){
                                                           
                            //                                 if( $earnedCredit != 0 ){
                            //                                     $statusFiD=4;
                            //                                 }else{
                            //                                     $statusFiD=6;
                            //                                 }
                                                            
                            //                             }else{
                            //                                 $statusFiD=6; 
                            //                             }
                                                      
                                                        
                            //                             $sql = "UPDATE hleavesbd SET LStatus=:st,LDateTimeUpdated=:dtu where FID=:idd AND LStart=:dtstart";
                            //                             $stmt = $pdo->prepare($sql);
                            //                             $stmt->bindParam(':st' ,$statusFiD);
                            //                             $stmt->bindParam(':dtu' ,$todaydt);
                            //                             $stmt->bindParam(':idd' ,$_GET['id']);
                            //                             $stmt->bindParam(':dtstart' ,$datestart);
                            //                             $stmt->execute();

                            //                             $count = $stmt->rowCount();
                            //                             if($count!='0'){
                            //                                 if ($earnedCredit != 0){
                            //                                     $earnedCredit= $earnedCredit - 1;
                                                                
                            //                                      {//get the duration of the updated leave
                            //                                         $sqlGetDuration = "SELECT LDuration as duration FROM hleavesbd as a  order by LStart";
                            //                                         $stmtGetDuration = $pdo->prepare($sqlGetDuration);
                            //                                         // $stmtGetDuration->bindParam(':idd' ,$_GET['id']);
            
                            //                                         $stmtGetDuration->execute();
                            //                                         $rowhGetDuration = $stmtGetDuration->fetch();
                            //                                         $rowhcountGetDuration = $stmtGetDuration->rowCount();
                            //                                         $newdur+=  $rowhGetDuration['duration'] / 60 / 10;
                            //                                     }
                            //                                 }else{
                            //                                     $earnedCredit= 0;
                            //                                 }  

                            //                                 if($durData==0){
                            //                                     $durData=0;
                            //                                 }else{
                            //                                     $durData=$durData-1; 
                            //                                 }
                            //                             }

                            //                             $datestart=date('Y-m-d', strtotime($datestart . ' + 1 days'));
                            //                             $DayDur = $DayDur - 1;     
                            //                         }
                            //                     }

                            //                     {//update hleaves to process
                            //                         $sql = "UPDATE hleaves SET LStatus=9,LDateTimeUpdated=:dtu where LeaveID=:idd";
                            //                         $stmt = $pdo->prepare($sql);                       
                            //                         $stmt->bindParam(':dtu' ,$todaydt);
                            //                         $stmt->bindParam(':idd' ,$_GET['id']);
                            //                         $stmt->execute();
                            //                     }

                            //                     {//update the credit deduct total used credit for this leave
                            //                         $xcrd = $crth - $newdur;
                            //                         $sql = "UPDATE credit SET CT=:ncrd where EmpID=:idd";
                            //                         $stmt = $pdo->prepare($sql);
                            //                         $stmt->bindParam(':ncrd' ,$xcrd);
                            //                         $stmt->bindParam(':idd' ,$EmplID);
                            //                         $stmt->execute();
                            //                     }
                                            
                            //                 }else{//if credit earned is greather than duration save all as paid
                                               
                            //                         {//get the total used credit for particular leave
                                    
                            //                             $durData=0;
                            //                             $yr=date("Y");
                            //                             $sql222="select SUM(LDuration) as duration from hleavesbd 
                            //                             where (EmpID=:id and year(LStart)=$yr and LStatus=4 and LType= $leaveType)";
                            //                             $stmt222 = $pdo->prepare($sql222);
                            //                             $stmt222->bindParam(':id',$EmplID);
                            //                             $stmt222->execute();
                            //                             $crcntb1 = $stmt222->rowCount();
                            //                             $getDuration222 = $stmt222->fetch();
                                                       
                            //                             $durData = ( 5 - ((($getDuration222['duration'] / 60) / 10))); //get the total unused credit for applied leave type
                                                        
                            //                         }
                                                   
                            //                         if($crcntb1>0){
                            //                             if($durData==0){
                            //                                 $stat=6;
                            //                                 $lduration=0;
                            //                             }else{
                                                            
                            //                             }
                            //                         }
                                                    
                                                   
                            //                         {//update hleaves data to process by hr
                            //                             $sql = "UPDATE hleaves SET LStatus=9,LDateTimeUpdated=:dtu where LeaveID=:idd";
                            //                             $stmt = $pdo->prepare($sql);                       
                            //                             $stmt->bindParam(':dtu' ,$todaydt);
                            //                             $stmt->bindParam(':idd' ,$_GET['id']);
                            //                             $stmt->execute();
                            //                         }
                        
                            //                         {//update all filing and set to approve
                            //                             $sql = "UPDATE hleavesbd SET LStatus=:st,LDateTimeUpdated=:dtu where FID=:idd";
                            //                             $stmt = $pdo->prepare($sql);
                            //                             $stmt->bindParam(':st' ,$stat);
                            //                             $stmt->bindParam(':dtu' ,$todaydt);
                            //                             $stmt->bindParam(':idd' ,$_GET['id']);
                            //                             $stmt->execute();
                            //                         }                          
                                                    
                            //                         { //update the credit deduct total used credit for this leave
                            //                             $xcrd = $crth - $lduration;
                            //                             $sql = "UPDATE credit SET CT=:ncrd where EmpID=:idd";
                            //                             $stmt = $pdo->prepare($sql);
                            //                             $stmt->bindParam(':idd' ,$EmplID);
                            //                             $stmt->bindParam(':ncrd' ,$xcrd);
                            //                             $stmt->execute();
                            //                         }

                            //                         {//insert to dar 
                            //                             $sql2="SELECT employees.EmpLN as LN,employees.EmpFN as FN from employees inner join hleaves on employees.EmpID=hleaves.EmpID where LeaveID=:idd";
                            //                             $stmt = $pdo->prepare($sql2);
                            //                             $stmt->bindParam(':idd' ,$_GET['id']);
                            //                             $stmt->execute();
                            //                             $row=$stmt->fetch();
                            //                             $nameE=$row['FN'] . " " . $row['LN']; 
                                                    
                            //                             $id=$_SESSION['id'];
                            //                             $ch="Approved Leaves of " . $nameE ;   
                            //                             $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                            //                             $stmt = $pdo->prepare($sql);
                            //                             $stmt->bindParam(':id' , $id);
                            //                             $stmt->bindParam(':empact', $ch);
                            //                             $stmt->bindParam(':ddt', $todaydt);
                            //                             $stmt->execute();
                            //                         }
                            //                 }     
                            //         }
                            //         echo json_encode(array("uid" => $_SESSION['UserType'], "dd" => 35)); 

                            //     }else{//code for non sil leave here
                                    
                            //         $sql = "UPDATE hleaves SET LStatus=9,LDateTimeUpdated=:dtu where LeaveID=:idd";
                            //         $stmt = $pdo->prepare($sql);                       
                            //         $stmt->bindParam(':dtu' ,$tdy);
                            //         $stmt->bindParam(':idd' ,$_GET['id']);
                            //         $stmt->execute();

                            //         $id=$_SESSION['id'];
                            //         $ch="Approved Leaves of " . $nameE;
                            //             // insert into dars
                            //         $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
                            //         $stmt = $pdo->prepare($sql);
                            //         $stmt->bindParam(':id' , $id);
                            //         $stmt->bindParam(':empact', $ch);
                            //         $stmt->bindParam(':ddt', $todaydt);
                            //         $stmt->execute();   

                            //         $sql = "UPDATE hleavesbd SET LStatus=:st,LDateTimeUpdated=:dtu where FID=:idd";
                            //             $stmt = $pdo->prepare($sql);
                            //         $stmt->bindParam(':st' ,$stat);
                            //         $stmt->bindParam(':dtu' ,$tdy);
                            //         $stmt->bindParam(':idd' ,$_GET['id']);
                            //         $stmt->execute();   
                                    
                            //         echo json_encode(array("uid" =>0, "dd" => '0')); 
                            //     }
                            // }

                            //end p1
                            
                        }else{//nothing here
                            print 1;
                        }
                
                } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            }
        
      }else if($_GET['ntype']=="OT"){
            $sql = "UPDATE otattendancelog SET Status=:st,DateTimeUpdate=:dtu where OTLOGID=".$_GET['id'];
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':st' ,$stat);
            $stmt->bindParam(':dtu' ,$tdy);
            $stmt->execute();

            $sql2="select employees.EmpLN as LN,employees.EmpFN as FN from employees inner join otattendancelog on employees.EmpID=otattendancelog.EmpID where OTLOGID=" . $_GET['id'];
            $stmt = $pdo->prepare($sql2);
            $stmt->execute();
            $row=$stmt->fetch();
            $nameE=$row['FN'] . " " . $row['LN'];

            $id=$_SESSION['id'];
            $ch="Approved OT of " .  $nameE;
            // insert into dars
            $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id' , $id);
            $stmt->bindParam(':empact', $ch);
            $stmt->bindParam(':ddt', $todaydt);
            $stmt->execute(); 
      }
   }
?>