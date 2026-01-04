<?php 

  // DATABASE CONNECTION 
  {
    include 'w_conn.php';session_start();
        date_default_timezone_set("Asia/Manila"); 


    if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
    else{ header ('location: login.php'); }

    try{
      $customTime = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('P');
      $pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET time_zone='$customTime';");
    }
    catch(PDOException $e)
    {
      die("ERROR: Could not connect. " . $e->getMessage());
    }
  }

  //admin time validation
  {   
     $datenow = date("Y-m-d");
     $datenow1 = date("Y-m-d H:i");
     $timenow = strtotime($datenow1);
     $startTime = strtotime($datenow ." 8:30:00");
     $id=$_SESSION['id'];
     if($id=="WeDoinc-012"){
     }else{
         if($timenow > $startTime){
            echo "The leave application must be completed on or before 8:30 AM.";
            return;
        }
     }
  }
   
  #declaration
  {
    global $ifPayroll;
    $companyid=$_SESSION['CompID']; 
    $id=$_SESSION['id'];
    $isid=$_SESSION['EmpISID'];
    $statid=1;
    $statiddiss=3;
    $today =date("Y-m-d"); 
    $today2 =date("Y-m-d H:i:s");
    $used = "used";
    $newdurationleave=$_POST['leavedur'];
    $start=$_POST['lstarts'];
    $end=$_POST['lenddate'];
    $dteStart = new DateTime($_POST['lstarts']);
    $dteEnd   = new DateTime($_POST['lenddate']); 
    $lfdate = new DateTime($_POST['lfdates']);

    $dteDiff  = $dteStart->diff($dteEnd); 
    $numdays = ($dteDiff->format("%D")) + 1;
    $ltype = $_POST['leavetype'];

    $filingTOstart  = $lfdate->diff($dteStart); 
    $numdays_frm_fdate_sdate = $filingTOstart->format("%D");
    
    //for validation
    $dteStartValid=date("Y-m-d",strtotime($_POST['lstarts']));
    $dteEndValid=date("Y-m-d",strtotime($_POST['lenddate']));
    $ifLacking=0;
  }

  
  //validate if schedule is not correct
  {    
    while ($dteEndValid>=$dteStartValid){

      $day_descValid = date ("l", strtotime($dteStartValid));
      $statement = $pdo->prepare("SELECT * FROM workdays INNER JOIN workschedule ON workdays.SchedTime=workschedule.WorkSchedID 
      INNER JOIN schedeffectivity AS c ON workdays.EFID=c.efids 
      WHERE (workdays.empid='$id') AND (workdays.Day_s='$day_descValid') AND ('$dteStartValid' >= dfrom) AND ('$dteStartValid' <= dto) ");
      $statement->execute();
        if ($statement->rowCount()>0 ){
          
        }else{
          //this will signal the system that employee is lacking schedule
          $ifLacking= $ifLacking+1;
        }
        $dteStartValid = date ("Y-m-d", strtotime($dteStartValid. "+1 day"));
    }
  }

  //validate the  $ifLacking if value > 0
  if( $ifLacking >0){
    echo "Missing Schedule. Please contact your systems administrator.";
    return ;
  }

  // if leave date is already filled
  {
    $statement = $pdo->prepare(" SELECT * FROM hleaves WHERE EmpID=:id AND ((LStart BETWEEN :dts AND :dte) OR (LEnd BETWEEN :dts AND :dte)) AND (LStatus=1 OR LStatus=2 OR LStatus=4 OR LStatus=9)");
    $statement->bindParam(':id' , $id);
    $statement->bindParam(':dts' , $_POST['lstarts']);
    $statement->bindParam(':dte' , $_POST['lenddate']);
    $statement->execute();
    if ($statement->rowCount()>0){
      echo "You have already applied in this inclusive date ";
      return;
    }
  }

  $maxid=0;

  //if applying leave without attendance
  {
  //  $dy=date("d"); 
  //  $yr=date("Y"); 
  //  $mnth=date("m"); 
  //  $sql = "SELECT * FROM attendancelog WHERE EmpID=:id AND day(TimeIn)=:dy AND year(TimeIn)=:yr AND month(TimeIn)=:mnth ORDER BY TimeIn DESC";
  //  $stmt = $pdo->prepare($sql);
  //  $stmt->bindParam(':id' , $id);
  //  $stmt->bindParam(':dy' , $dy);
  //  $stmt->bindParam(':mnth' , $mnth);
  //  $stmt->bindParam(':yr' , $yr);
  //  $stmt->execute(); 
  //  $rowcenar=$stmt->fetch();


  //  if ($stmt->rowCount()>0 and $rowcenar['TimeOut']==NULL){

  //  }else{
  //    echo "You cant apply you have no Attendance";
  //    return;
  //  }
  }

  //if employee is regular but no Date of Regularization
  {
    if ($_POST['lcredit']=="Missing Regularization Date"){
      echo "Missing Regularization Date.";
      return;
    }
  }

  //if employee is IC
  {
    $statement = $pdo->prepare(" SELECT * FROM empdetails WHERE EmpID=:id");
    $statement->bindParam(':id' , $id);
    $statement->execute();
    $EmpRow=$statement->fetch();

    if ($EmpRow['EmpStatID']<>1 and $_POST['leavepay']==1){
      echo "You cant apply leave pay ";
      return;   
    }
    else if($statement->rowCount()<1) {
      echo "You cant Apply Leave Pay ";
      return; 
    }
  }

  // find parental related 
  {
    // if ($_POST['leavetype']==8){
    //   $mystring= strtoupper($_POST['reason']);
    //   $findme   = 'BIRTHDAY';
    //   $pos = strpos($mystring, $findme);
    //   if ($pos === false){

    //   }else{
    //     $sql="SELECT * FROM parentalrel WHERE EmpID=:id AND DateofBirth=:dob";
    //     $stmt = $pdo->prepare($sql);
    //     $stmt->bindParam(':id' , $id);
    //     $stmt->bindParam(':dob' , $_POST['lstarts']);
    //     $stmt->execute();
    //     $resfam=$stmt->fetch();
    //     if ($stmt->rowCount()<1)
    //     {
    //       echo "You cant Apply Parental Leave No Family Details related on this Date";
    //       return;
    //     }
    //   }
    // }
  }

  // if leave kind is paid 
  if ($_POST['leavepay']==1){
    //no leave validation
    { 
      $statement = $pdo->prepare(" SELECT * FROM leaves INNER JOIN leaves_validation ON leaves.LeaveID=leaves_validation.lid WHERE leaves_validation.lid=:lid AND compid=:id");
      $statement->bindParam(':id' , $companyid);
      $statement->bindParam(':lid' , $_POST['leavetype']);
      $statement->execute();
      $valid = $statement->fetch();
      if ($statement->rowCount()>0){
        $vl_leave_id=$valid['lid'];
        $vl_credits=$valid['leave_credits'];
        $vl_short=$valid['leave_short'];
        $vl_long=$valid['leave_long'];
        $vl_before=$valid['leave_before'];
        $vl_after=$valid['leave_file_after'];
        $vl_min=$valid['leave_min'];
        $vl_duration_after=$valid['filing_after_duration'];
        $vl_duration_before=$valid['filing_before_duration'];
        $vl_max_day=$valid['max_days_before'];
        $vl_duringfile=$valid['file_during'];
        $vl_halfday=$valid['IsHalfDay'];
      }else{
        echo "No Leave Data Found ! ";
        return;
      }
    }

    //validate the paternity
    {
      if($_POST['leavetype'] == 29){
        if( $vl_credits <> $_POST['leavedur']){
          echo "Invalid Duration for Paternity Leave";
          return;
        }
      } 
    }
    //validate maternity
    { 
      if($_POST['leavetype']=='27'){
        if( $vl_credits <> $_POST['leavedur']){
          echo "Invalid Duration for Maternity Leave";
          return;
        }
      }
    }

     #2024 script ---------------------------------
    {   
      $id=$_SESSION['id'];
      $varTH=0;
      //get the total credit threshold
      {
        $getTH = "select * from credit where EmpID = :id";
        $stmtTH = $pdo->prepare($getTH);
        $stmtTH->bindParam(':id', $id);
        $stmtTH->execute();
        $crdetailTH = $stmtTH->fetch();
        $crcntTH = $stmtTH->rowCount();

        if ($crcntTH > 0) {
          if( $crdetailTH['CTH']==15){
            $varTH=15;
          }
        }    
      }

      //validate total credit per type
      {
        $yr=date("Y");
  
        if(($ltype!='27') && ($ltype!='29')){//skip the maternity,paternity validation
       
          if($varTH==15){//for 15 credits
            
            $sql="select LDuration as duration from hleavesbd 
            where (EmpID=:id and year(LStart) =$yr and LStatus=4 and LType= $ltype)";
            $stmt2 = $pdo->prepare($sql);
            $stmt2->bindParam(':id' ,$id);
            $stmt2->execute();
            $crcnt = $stmt2->rowCount();
            
              if($crcnt > 0){ //return false if validation meet true
                  
                  //sumup the duration
                  $durData=0;
                while ($getDuration = $stmt2->fetch()) {
                      $durData=$durData + $getDuration['duration'];
                  }
    
                if((($durData/ 600 ) + $_POST['leavedur']) > 5 ){
                  echo "Overfilling the allotted leave credits for this leave type. <br> <br> Used Credit " . $durData/ 600 . " days";
                  return;
                }
              }else{
                if(($_POST['leavedur']) > 5 ){
                  echo "Overfilling the allotted leave 5 credits for this leave type.";
                  return;
                }

              }
              
          }else{//for 10 cridets 

            $sql="select LDuration as duration from hleavesbd 
            where (EmpID=:id and year(LStart) =$yr and LStatus=4 and LType= $ltype)";
            $stmt2 = $pdo->prepare($sql);
            $stmt2->bindParam(':id' ,$id);
            $stmt2->execute();
            $crcnt = $stmt2->rowCount();

            if($crcnt > 0){ //return false if validation meet true
              //sumup the duration
              $durData=0;
            while ($getDuration = $stmt2->fetch()) {
                  $durData=$durData + $getDuration['duration'];
              }

              if((($durData/ 600 ) + $_POST['leavedur']) >5 ){
                echo "Overfilling the allotted leave credits for this leave type. <br> <br>Used Credit " . $durData/ 600  ." days";
                return;
              }
            }else{
              if(($_POST['leavedur']) > 5 ){
                echo "Overfilling the allotted leave 5 credits for this leave type.";
                return;
              }

            }
          }
        }
      }

    }

    #2024 script end --------------------------------

    //function get total number of restday between startdate and end date
    { 
       $num_off=0;
      if($numdays==1){
        $num_off=0;
      }else if($numdays>1){
        $ctr=0;
        while ($numdays>$ctr){
          //$tdom = date('Y-m-d',strtotime($dnow . "+1 days"));
          $day_desc = date ("l", strtotime($_POST['lstarts']. "+".$ctr." day"));
          $statement = $pdo->prepare("SELECT * FROM workdays WHERE empid='$id' AND Day_s='$day_desc' AND SchedTime='0' ");
          $statement->execute(); 
          $count=$statement->rowCount();
            if($count>0){
              $num_off=$num_off+1;
            }
          $ctr=$ctr+1;
        } 

      }else{
        Print "Error";
      }

      $newduration=$numdays-$num_off;
      $dtd=date("Y-m-d",strtotime($_POST['lstarts']));
      $dateend=date("Y-m-d",strtotime($_POST['lenddate']));
    }
    
    //updated 8-5-24
    if ($today>=$dtd){
      // filing after leave
      $date1=date_create($today);
      $date2=date_create($dateend);
      $diff=date_diff($date1,$date2);
      //number of days after leave
      $DaysAL = floatval($diff->format('%a')); 
      $cnt=0;
      //number of days you login after leave
      $DaysLogin=0;

      //number of working days after leave
        while ($today>$dateend){
          $day_desc = date ("l", strtotime($dateend));
          $statement = $pdo->prepare("SELECT * FROM workdays INNER JOIN 
          workschedule ON workdays.SchedTime=workschedule.WorkSchedID 
          INNER JOIN schedeffectivity AS c ON workdays.EFID=c.efids
          WHERE (workdays.empid='$id') AND (workdays.Day_s='$day_desc') AND ('$today' >= dfrom) AND ('$today' <= dto)  AND SchedTime <> 0 ");
          
          $statement->execute();
          $dateend = date ("Y-m-d", strtotime($dateend. "+1 day"));
          if ($statement->rowCount()>0 && $today>=$dateend){
            $cnt=$cnt+1;
            $dy=date("d", strtotime($dateend));
            $mnth=date("m", strtotime($dateend));
            $yr=date("Y", strtotime($dateend));
            $sql = "SELECT * FROM attendancelog WHERE EmpID=:id AND day(TimeIn)=:dy AND year(TimeIn)=:yr AND month(TimeIn)=:mnth";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id' , $id);
            $stmt->bindParam(':dy' , $dy);
            $stmt->bindParam(':mnth' , $mnth);
            $stmt->bindParam(':yr' , $yr);
            $stmt->execute(); 
            if ($stmt->rowCount()>0){
              $DaysLogin=$DaysLogin+1;
            }
          }
        }

        $WDaysAL=$DaysLogin;;

    }else{
      // filing before leave
      $date1=date_create($today);
      $date2=date_create($dtd);
      $diff=date_diff($date2,$date1);
      //number of days after leave
      $DaysBL = floatval($diff->format('%a')); 
      $cnter=0;
      //number of working days after leave
      $dtend=$dtd;
      //   while ($today<$dtend){
      //     $day_desc = date ("l", strtotime($dtend));
      //     $statement = $pdo->prepare("SELECT * FROM workdays INNER JOIN 
      //       workschedule ON workdays.SchedTime=workschedule.WorkSchedID 
      //       INNER JOIN schedeffectivity AS c ON workdays.EFID=c.efids
      //       WHERE (workdays.empid='$id') AND (workdays.Day_s='$day_desc') AND ('$today' >= c.dfrom) AND ('$today' <= c.dto)  AND workdays.SchedTime <> 0 ");
      //     $statement->execute(); 
      //     $dtend = date ("Y-m-d", strtotime($dtend. "-1 day"));
      //     if ($statement->rowCount()>0  && $today<$dtend){
      //       $cnter=$cnter+1;
      //     }
      //   }

      //   $WDaysBL=$cnter;
      $WDaysBL=$DaysBL; 
    }
  
    //filing leave validation
    if ($today==$dtd){
      if ($vl_duringfile==0){
        // if during 
        echo "Filing not allowed";
        return false;
      }else if ($_POST['leavedur']==0.5){
          // if half day
          if ($vl_halfday==0){
            echo "You cant file halfday on this Leave";
          }else{
            echo 1;
          }
      }else{
        echo 1;
      }
    }else if ($today>=$dtd){

        // filing after leave
      if ($vl_after==1){//if allowed to file after, validate duration from start up to filing date 8/5/2024
        //days after leave
        if ($WDaysAL > $vl_duration_after){
          // echo 1;
        // }else{
          echo "Your leave request couldn't be processed as the deadline has passed."; 
          return;
        }
      }else{
          echo "You Cant File After a Leave in this LeaveType";
          return;
      }
      echo 1;
    }else{
      //validating leave prior days
      if ($valid['lid']==22){
        if ($_SESSION['UserType']==3){

          if($_SESSION['id']=='WeDoinc-0010' || $_SESSION['id']=='WeDoinc-009' ){
            if ($WDaysBL<10){
              echo "You cannot request a vacation leave with fewer than ten days' notice.";
              return;
            }
          }else{
            if ($WDaysBL<5){
              echo "You cannot request a vacation leave with fewer than five days' notice.";
              return;
            }
          }
            
        }else{
            if ($WDaysBL<15){
              echo "You cannot request a vacation leave with fewer than fifteen days' notice.";
              return;
            }
        
        }
      }

      //end
      if ($vl_before==1){
        if ($vl_max_day<$DaysBL and $vl_max_day<>0){
          echo "You cant file ! Maximum" . $vl_max_day . " days";
          return;
        }else{
          if ($vl_duration_before==0){
              echo 1;
          }else{
              if ($WDaysBL>=$vl_duration_before || $vl_duration_before==0){
                echo 1;
              }else{
                echo "You Cant File Before Leave (Validation)";
                return;
              }
          }
        }

      }else{
        echo "You Cant File Before a Leave in this LeaveType";
        return;
      }
    
    }
    //updated 8-5-24

    $lvtype=$_POST['leavetype'];
    insert_alas($pdo,$id,$isid,$today,$newdurationleave,$statid,$today2);
    
  }else{
    #notpaid leave
    insert_alas2($pdo,$id,$isid,$today,$newdurationleave,$statid,$today2);
    
  }

  function insert_alas2(PDO $pdo,$id,$isid,$today,$newduration,$statid,$today2){
    try{
      global $sql1;
        if( $id=="WeDoinc-012"){
                $statid=9;
            }
      $streason = str_replace('\'', '', $_POST['reason']);
      $sql1 = "INSERT INTO hleaves (EmpID,EmpSID,LType,LFDate,LStart,LEnd,LPurpose,LDuration,LStatus,LInputDate,Lpaid,LDateTimeUpdated) VALUES (:id,:is,:ltype,:lfdate,:lstart,:lend,:LPurpose,:lduration,:LStatus,:DTin,:lpay,:ldupdated)";
      $stmt = $pdo->prepare($sql1);
      $stmt->bindParam(':id' , $id);
      $stmt->bindParam(':is', $isid);
      $stmt->bindParam(':ltype' , $_POST['leavetype']);
      $stmt->bindParam(':lfdate', $today);
      $stmt->bindParam(':lstart' ,$_POST['lstarts']);
      $stmt->bindParam(':lend', $_POST['lenddate']);
      $stmt->bindParam(':LPurpose', $streason);
      $stmt->bindParam(':lduration', $_POST['leavedur']);
      $stmt->bindParam(':LStatus', $statid);
      $stmt->bindParam(':DTin', $today2);
      $stmt->bindParam(':lpay', $_POST['leavepay']);
      $stmt->bindParam(':ldupdated', $today2);
      $stmt->execute(); 
    }catch(Exception $e){
      echo 'Message: ' .$e->getMessage();
    }
  }

  function insert_alas(PDO $pdo,$id,$isid,$today,$newduration,$statid,$today2){
      try{
        global $sql1;
          if( $id=="WeDoinc-012"){
                $statid=9;
            }
        $streason = str_replace('\'', '', $_POST['reason']);
        $sql1 = "INSERT INTO hleaves (EmpID,EmpSID,LType,LFDate,LStart,LEnd,LPurpose,LDuration,LStatus,LInputDate,Lpaid,LDateTimeUpdated) VALUES (:id,:is,:ltype,:lfdate,:lstart,:lend,:LPurpose,:lduration,:LStatus,:DTin,:lpay,:ldupdated)";
        $stmt = $pdo->prepare($sql1);
        $stmt->bindParam(':id' , $id);
        $stmt->bindParam(':is', $isid);
        $stmt->bindParam(':ltype' , $_POST['leavetype']);
        $stmt->bindParam(':lfdate', $today);
        $stmt->bindParam(':lstart' ,$_POST['lstarts']);
        $stmt->bindParam(':lend', $_POST['lenddate']);
        $stmt->bindParam(':LPurpose', $streason);
        $stmt->bindParam(':lduration', $_POST['leavedur']);
        $stmt->bindParam(':LStatus', $statid);
        $stmt->bindParam(':DTin', $today2);
        $stmt->bindParam(':lpay', $_POST['leavepay']);
        $stmt->bindParam(':ldupdated', $today2);
        $stmt->execute(); 
      }catch(Exception $e){
        echo 'Message: ' .$e->getMessage();
      }

  }
  
  try{
    //view or search max id of leave
    $statement = $pdo->prepare("SELECT MAX(LeaveID) FROM hleaves ");
    $statement->execute();
    $roww=$statement->fetch();
    if ($statement->rowCount()>0){
      $maxid= $roww[0];
    }else{
      $maxid= 1;
    }

    //inserting multiple leave table
    $dtendleave = $_POST['lenddate'];
    $dtstartleave = $_POST['lstarts'];
    if ($_POST['leavedur']>=1){
      $drt=600;
    }else{
      $drt=300; 
    }
    $streason = str_replace('\'', '', $_POST['reason']);
    $fkeyid=$maxid;

    if($ltype==27){ 
      while ($dtstartleave<=$dtendleave) {

        $sql = "INSERT INTO hleavesbd (FID,EmpID,EmpSID,LType,LFDate,LStart,LEnd,LPurpose,LDuration,LStatus,LInputDate,Lpaid,LDateTimeUpdated) VALUES (:fidsl,:id,:is,:ltype,:lfdate,:lstart,:lend,:LPurpose,:lduration,:LStatus,:DTin,:lpay,:ldupdated)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fidsl' , $fkeyid);
        $stmt->bindParam(':id' , $id);
        $stmt->bindParam(':is', $isid);
        $stmt->bindParam(':ltype' , $_POST['leavetype']);
        $stmt->bindParam(':lfdate', $today);
        $stmt->bindParam(':lstart' ,$dtstartleave);
        $stmt->bindParam(':lend', $dtstartleave);
        $stmt->bindParam(':LPurpose', $streason);
        $stmt->bindParam(':lduration', $drt);
        $stmt->bindParam(':LStatus', $statid);
        $stmt->bindParam(':DTin', $today2);
        $stmt->bindParam(':lpay', $_POST['leavepay']);
        $stmt->bindParam(':ldupdated', $today2);
        
        $stmt->execute(); 
        $ida = $pdo->lastInsertId();

        //validate if payroll is generated
        {  
          $sqlst = "SELECT * FROM payrol WHERE (:dts BETWEEN PYDateFrom AND PYDateTo)";
          $state = $pdo->prepare($sqlst);
          $state->bindParam(':dts', $dtstartleave);
          $state->execute();

          while ($checkerstate = $state->fetch()) {
            $pdateData = $checkerstate['PYDate'];
          }

          if($state->rowCount()>=1){
            //ramon
            if(date_format(date_create($pdateData),"d")==5){
              $pdateData=date_format(date_create($pdateData),"Y-m"."-20");
            }else{
              $pdateData=date_format(date_create($pdateData),"Y-m"."-5");
              $pdateData = date ("Y-m-d", strtotime($pdateData. "+ 1 month"));
            }

            $sqlmaxid = "SELECT MAX(PYDate) as PYDate FROM payrol";
            $statemaxid = $pdo->prepare($sqlmaxid);
            $statemaxid->execute();
            $maxdate='';
            while ($checkerstatemaxdate = $statemaxid->fetch()) {
              $maxdate='';
              $maxdate = $checkerstatemaxdate['PYDate'];
            }

            if($statemaxid->rowCount()>=1){
              if($maxdate >= $pdateData ){
                if(date_format(date_create($maxdate),"d")==5){
                  $pdateData=date_format(date_create($maxdate),"Y-m"."-20");
                }else{
                  $pdateData=date_format(date_create($maxdate),"Y-m"."-5");
                  $pdateData = date ("Y-m-d", strtotime($pdateData. "+ 1 month"));
                }
              }
            }

            $tagSQL="INSERT INTO tbl (refID,refEmpID,refPayDate,inputdate) VALUES(:id,:empid,:pdate,:inputdate)";
            $tagSQL=$pdo->prepare($tagSQL);
            $tagSQL->bindParam(':id',$ida);
            $tagSQL->bindParam(':empid',$id);
            $tagSQL->bindParam(':pdate', $pdateData);
            $tagSQL->bindParam(':inputdate',$today2);
            $tagSQL->execute(); 
          }
        }
          
        

        $dtstartleave = date ("Y-m-d", strtotime($dtstartleave. "+1 day"));
      }
    }else{
      while ($dtstartleave<=$dtendleave) {
        //30-10
        $day_desc = date ("l", strtotime($dtstartleave));
        $statementleaveadd = $pdo->prepare("Select * from workdays INNER JOIN 
        workschedule ON workdays.SchedTime=workschedule.WorkSchedID 
        inner join schedeffectivity as c on workdays.EFID=c.efids
        where (workdays.empid='$id') and (workdays.Day_s='$day_desc') and ('$dtstartleave' >= dfrom) and ('$dtstartleave' <= dto)  and (SchedTime > 0)");
        $statementleaveadd->execute();

        $mtnh = date("Y-m-d", strtotime($dtstartleave));
        $statementhol = $pdo->prepare("select * from holidays where Hdate=:mnth order by sid desc");
        $statementhol->bindParam(':mnth', $mtnh);
        $statementhol->execute();
        $counthol = $statementhol->rowCount();
      
        if($counthol == 0){
          if ($statementleaveadd->rowCount()>0){
                if( $id=="WeDoinc-012"){
                $statid=4;
          }
            $sql = "INSERT INTO hleavesbd (FID,EmpID,EmpSID,LType,LFDate,LStart,LEnd,LPurpose,LDuration,LStatus,LInputDate,Lpaid,LDateTimeUpdated) VALUES (:fidsl,:id,:is,:ltype,:lfdate,:lstart,:lend,:LPurpose,:lduration,:LStatus,:DTin,:lpay,:ldupdated)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':fidsl' , $fkeyid);
            $stmt->bindParam(':id' , $id);
            $stmt->bindParam(':is', $isid);
            $stmt->bindParam(':ltype' , $_POST['leavetype']);
            $stmt->bindParam(':lfdate', $today);
            $stmt->bindParam(':lstart' ,$dtstartleave);
            $stmt->bindParam(':lend', $dtstartleave);
            $stmt->bindParam(':LPurpose', $streason);
            $stmt->bindParam(':lduration', $drt);
            $stmt->bindParam(':LStatus', $statid);
            $stmt->bindParam(':DTin', $today2);
            $stmt->bindParam(':lpay', $_POST['leavepay']);
            $stmt->bindParam(':ldupdated', $today2);
            
            $stmt->execute(); 
            $ida = $pdo->lastInsertId();

            //validate if payroll is generated
            {  
              $sqlst = "SELECT * FROM payrol WHERE (:dts BETWEEN PYDateFrom AND PYDateTo)";
              $state = $pdo->prepare($sqlst);
              $state->bindParam(':dts', $dtstartleave);
              $state->execute();

              while ($checkerstate = $state->fetch()) {
                $pdateData = $checkerstate['PYDate'];
              }

              if($state->rowCount()>=1){
                //ramon
                if(date_format(date_create($pdateData),"d")==5){
                  $pdateData=date_format(date_create($pdateData),"Y-m"."-20");
                }else{
                  $pdateData=date_format(date_create($pdateData),"Y-m"."-5");
                  $pdateData = date ("Y-m-d", strtotime($pdateData. "+ 1 month"));
                }

                $sqlmaxid = "SELECT MAX(PYDate) as PYDate FROM payrol";
                $statemaxid = $pdo->prepare($sqlmaxid);
                $statemaxid->execute();
                $maxdate='';
                while ($checkerstatemaxdate = $statemaxid->fetch()) {
                  $maxdate='';
                  $maxdate = $checkerstatemaxdate['PYDate'];
                }

                if($statemaxid->rowCount()>=1){
                  if($maxdate >= $pdateData ){
                    if(date_format(date_create($maxdate),"d")==5){
                      $pdateData=date_format(date_create($maxdate),"Y-m"."-20");
                    }else{
                      $pdateData=date_format(date_create($maxdate),"Y-m"."-5");
                      $pdateData = date ("Y-m-d", strtotime($pdateData. "+ 1 month"));
                    }
                  }
                }

                $tagSQL="INSERT INTO tbl (refID,refEmpID,refPayDate,inputdate) VALUES(:id,:empid,:pdate,:inputdate)";
                $tagSQL=$pdo->prepare($tagSQL);
                $tagSQL->bindParam(':id',$ida);
                $tagSQL->bindParam(':empid',$id);
                $tagSQL->bindParam(':pdate', $pdateData);
                $tagSQL->bindParam(':inputdate',$today2);
                $tagSQL->execute(); 
              }
            }
          }
        }

        $dtstartleave = date ("Y-m-d", strtotime($dtstartleave. "+1 day"));
      }
    }
  
     


  } catch(Exception $e) {
    echo 'Message: ' .$e->getMessage();
  }

  // insert into dars
  {
    $id=$_SESSION['id'];
    $ch="Applied Leave";

    $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:tdy)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id' , $id);
    $stmt->bindParam(':empact', $ch);
    $stmt->bindParam(':tdy', $today2);
    $stmt->execute(); 
    // print 1;
  }
?>