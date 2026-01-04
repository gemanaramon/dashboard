<?php 

//connection
{
  include 'w_conn.php';
  session_start();
  if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
  else{ header ('location: login.php'); }
  try{
  $pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  date_default_timezone_set("Asia/Manila"); 
    }
  catch(PDOException $e)
    {
  die("ERROR: Could not connect. " . $e->getMessage());
    }
}

try{
      // initialization
        ################################################
        // $datenow = date("Y-m-d");
        // $datenow1 = date("Y-m-d H:i");
        // $timenow = strtotime($datenow1);
        // $startTime = strtotime($datenow ." 08:00");
        // $id=$_SESSION['id'];
        // if($id=="WeDoinc-012"){
        // }else{
        //  if($timenow > $startTime){
        //   echo "The OT application must be completed on or before 8:30 AM.";
        //   return;
        //   }
        // }
        ################################################
      //initilaization

      {
        $id = $_SESSION['id'];
        $isid = $_SESSION['EmpISID'];
        $statid = 1;
        $compid = $_SESSION['CompID'];
        $today = date("Y-m-d"); 
        $today2 = date("Y-m-d H:i:s");
        $today3 = date("Y-m-d");
        $day = date("l");
        $day2 = date("l", strtotime($_POST['datefrom']));
        $ddfrom=$_POST['datefrom'];
        $dteStart = new DateTime($_POST['datefrom']. ' ' .$_POST['timefrom']); 
        $dteEnd   = new DateTime($_POST['dateto']. ' ' .$_POST['timeto']); 
        $dteDiff  = $dteStart->diff($dteEnd); 
        $interval = $dteDiff->format("%h");
        $fillingdatetime = $_POST['datefrom'];
        $fillingtime=$_POST['ftime'];
        $d1 = $_POST['datefrom']. ' ' .$_POST['timefrom']; 
        $d2  = $_POST['dateto']. ' ' .$_POST['timeto'];
      }
  
      //get date diff for ot before
      {
        $otstart= date_create($_POST['datefrom']);
        $otend=  date_create($_POST['fdate']);
        $otdiff= $otstart->diff($otend);
        $otInterval=$otdiff->format("%a");
      }

      //parameter this value
      {
        $sql = "select * from empdetails INNER JOIN cal_values ON (empdetails.CatType=cal_values.category) where EmpID = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id' ,$id);
        $stmt->execute();
        $calrow = $stmt->fetch();
        $calload = $stmt->rowCount();
    
        if( $calload > 0){ 
          if($calrow['CatType'] < 1)
            {
              echo "Missing Schedule Category!";
              return;
            }
          $schedcat=$calrow['CatType'];
          $nhours=$calrow['HrsPerDay'];
          $avgDWork=$calrow['avgNoDaysYr'];
        }else{
          echo "Missing Schedule Category!";
          return;
        }
      }

      //getot mainteance
      {
        $sql = "select * from otfsmaintenance where compid = :comp";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':comp' ,$compid);
        $stmt->execute();
        $valrow = $stmt->fetch();
        $valload = $stmt->rowCount();

        if ($valload>0){
          //initialized validation data
          $isbefore = $valrow['IsBefore'];
          $nobefore = $valrow['NoDaysBefore'];
          $isafter =  $valrow['IsAfter'];
          $noafter = $valrow['NoDaysAfter'];
          $isholiday = $valrow['IsHoliday'];
          $istardy = $valrow['IsTardy'];

            if (($otstart < $otend) and $isbefore == 1 && $otInterval > $noafter ){
              echo "Maximum Is After day reach";//max is before days count
              return;
            }
                
            if (($otstart > $otend) and $isafter == 1 && $otInterval >  $nobefore) {
              echo "Maximum Is  Before day reach" . $otInterval . ">" . $noafter; //max is after days count
              return;
            }      
          }else{
              echo "Missing OT Validation. Please Contact your system administrator.";
              return;
          }
      }

      //if duplicate filing
      {
        $sql = "select * from otattendancelog where EmpID=:id and EmpISID=:is and TimeIn=:ti and TimeOut=:to and Status=:st and Purpose=:prs";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id' ,$id);
        $stmt->bindParam(':is', $isid);
        $stmt->bindParam(':ti' ,$d1);
        $stmt->bindParam(':to' ,$d2);
        $stmt->bindParam(':st' ,$statid);
        $stmt->bindParam(':prs' ,$_POST['otpur']);
        $stmt->execute();
        $cntload = $stmt->rowCount();
        if ($cntload>=1){
              echo "System cant accept duplicate filing ! ";
              return;
        }
      }

      //if pding application serve
      {
        $idstatus="1";
        $sql = "select * from otattendancelog where EmpID=:id and Status=:idstatus";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id' ,$id);
        $stmt->bindParam(':idstatus' ,$idstatus);
        $stmt->execute();
        $cntload = $stmt->rowCount();
          if ($cntload>=1){
                echo "You have Pending application. Please contact your System Administrator! ";
                return;
          }
      }
      
      //if employee's on leave cant file 
      {
        $dtfromloop=$_POST['datefrom'];
        $dttoo=$_POST['dateto'];
        $cntload=0;
        while($dtfromloop<=$dttoo){
            
            $slscts=$pdo->prepare("select * from hleaves where EmpID=:idn and LStart>=:dtstart and LEnd>=:dtstart");
            $slscts->bindParam(':idn' , $id);
            $slscts->bindParam(':dtstart', $dtfromloop);
            $slscts->execute();
            // $cntload = $slscts->rowCount();
            if ($cntload==1){
              break 1;
            }
            
            $dtfromloop= date('Y-m-d', strtotime($dtfromloop  . ' +1 days'));
        }
        if ($cntload>0){
            echo "OT filing not allowed during Leave! ";
            return;
        }
      }

      //get rate
      {
        $sql = "select * from empdetails2 where EmpID =:id ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id' ,$id);
        $stmt->execute();
        $payrow = $stmt->fetch();
        $cntpay = $stmt->rowCount();

        if ($cntpay > 0){
            $hrate = ((($payrow['EmpBasic'] * 12) / $avgDWork)  / $nhours );
            $basic = $payrow['EmpBasic'];
        
            if ( $basic <=1 ){
                echo "Please specify basic salary correctly!";
                return;
              }
          }else{
            echo "Please specify basic salary correctly!";
            return;
          }
      }
      
      ######################## LOOP INSERT START#######################

      $otS=  $_POST['datefrom'];
      $otT= $_POST['dateto'];

      if (isset($_POST['overtimeType'])) {
          $overtimeType = $_POST['overtimeType'];
      } else {
          echo "No Overtime Type selected.";
          return false;
      }

      if($overtimeType==1){//single
        $otT=$otS;
      }
     
      while ($otS<=$otT) {
        //get work schedule
        {
          $sql="SELECT * from workdays INNER JOIN 
          workschedule ON workdays.SchedTime=workschedule.WorkSchedID 
          inner join schedeffectivity as c on workdays.EFID=c.efids
          where (workdays.empid='$id') and (workdays.Day_s='$day2')
          and ('$otS' >= dfrom) and ('$otS' <= dto) and workschedule.WorkSchedID <> 0";  
          // $stmt->bindParam(':id' ,$id);
          // $stmt->bindParam(':day' ,$day2); 
          $stmt = $pdo->prepare($sql);
    
          $stmt->execute();
          $row = $stmt->fetch();
          $cntload1 = $stmt->rowCount();
        } 
        
        $ordinaryday=0;
        $restday=0;
        if($cntload1 > 0){
          $schedfrom=$row['TimeFrom'];
          $schedto=$row['TimeTo'];

          $tcross=$row['TimeCross'];
          $SF =  strtotime($otS. ' ' .$row['TimeFrom']); 
          $ST   =  strtotime($otS. ' ' .$row['TimeTo']); 

          $FDF = strtotime($otS. ' ' .$_POST['timefrom']); 

          if($overtimeType==1){//single
            // $otT=$otS;
            $FDT   =  strtotime($_POST['dateto']. ' ' .$_POST['timeto']); 
          }else{
            $FDT   =  strtotime($otS. ' ' .$_POST['timeto']); 
 
          }
          // if($overtimeType==2){
            if( $FDF >= $FDT ){
              echo "Invalid inclusive dates.";
              return false;
            }
            if( $FDF == $FDT ){
              echo "Invalid inclusive dates.";
              return false;
            }
          // }  

          $ordinaryday=1;
          $sql = "select * from holidays where Hdate=:date ";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':date' ,$otS);   
          $stmt->execute();
          $hrow = $stmt->fetch();
          $hload= $stmt->rowCount();
          $holidaytype=0;
          $holiday=0;
          if($hload > 0)
            {
              // $holidaytype=$hrow['Htype'];
              // $holiday=1;
            }else{
            if($ordinaryday=1){
            
                  if($FDF >= $SF and $FDT <= $ST){
                    echo "Filing not allowed within your respective work Schedule.";
                    return;
                  }

                  if($FDF <= $SF and $FDT >= $ST){
                    echo "Filing not allowed within your respective work Schedule.";
                    return;
                  }

                  if($FDF <= $SF and $FDT <= $ST){
                      if($FDF <= $SF and $FDT <= $SF){
                        
                      }else{
                        echo "Filing not allowed within your respective work Schedule.";
                        return;
                      }
                  }

                  if($FDF >= $SF and $FDT >= $ST){
                    if($FDF >= $ST and $FDT >= $ST){
                      
                    }else{
                      echo "Filing not allowed within your respective work Schedule.";
                      return;
                    }

                }


              //get today date lilo
              $sql = "select * from attendancelog where EmpID=:id AND WSFrom=:day";
              $stmt = $pdo->prepare($sql);
              $stmt->bindParam(':id' ,$id);
              $stmt->bindParam(':day' ,$today);    
              $stmt->execute();
              $rowlilo = $stmt->fetch();
              $cntlilo = $stmt->rowCount();
              $tardymin=0;
              if ($cntlilo > 0 )
              {
                $tardymin = $rowlilo['MinsLack'] + $rowlilo['MinsLack2'];
              }

              if ($istardy == 0 and $tardymin > 0)
                {
                  echo "Filing not allowed. MinsLack Detected. Please contact system administrator.";//filling not allowed
                  return;
                }
            }
          }
        }else{
          
          $restday=1;
        }

        //check if holiday
        {

          $d1 =$otS. ' ' .$_POST['timefrom']; 

          if($overtimeType==1){//single
            $d2  =$_POST['dateto']. ' ' .$_POST['timeto'];
          }else{
            $d2  =$otS. ' ' .$_POST['timeto'];

          }

          // $hdate=$_POST['datefrom'];
          $sql = "select * from holidays where Hdate=:date ";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':date' ,$otS);   
          $stmt->execute();
          $hrow = $stmt->fetch();
          $hload= $stmt->rowCount();
          $holidaytype=0;
          $holiday=0;
          if($hload > 0)
            {
              $holidaytype=$hrow['Htype'];
              $holiday=1;
            }

            if ($isholiday == 0 && $holiday == 1){
                // echo "Sorry OT Filing not allowed during Holiday.";
                // return;
            }
                $x=0;
            if ($holiday == 1 && $restday == 1){
                $x=1;
            }else{
              
            }
            if ($holiday == 1 or $restday == 1) {
                //validate duration
                if ( $interval >=1 )    
                {
                if ( $schedcat == 1 )
                    {
                      if ( $interval >= 6 && $interval <= 10)
                      {
                        $consDuration = 10-1;
                      }
                      elseif ( $interval >= 6 && $interval > 10)
                      {
                        $consDuration = $interval - 1 ;
                      }
                      else 
                      {
                        $consDuration = 5;
                      }
                    }
              
                  else
                    {
                      if ( $interval >=5 && $interval <= 8 )
                      {
                        $consDuration = 8 -1;
                      }
                      elseif ( $interval >=5 && $interval > 8 )
                      {
                        $consDuration = $interval -1;
                      }
                      else 
                      {
                        $consDuration = 4;
                      }
                    }
                }
                else
                  {
                    // echo $interval;
                    echo "Minimum Overtime 1 hr. ";
                      return;
                  }
              
            }else{  
            // $consDuration = $interval;  
              //validate duration
              if ( $interval >=1 )  {
                if ( $schedcat == 1 ){
                      if ( $interval >= 6 && $interval <= 10)
                      {
                        $consDuration = 10-1;
                      }
                      elseif ( $interval >= 6 && $interval > 10)
                      {
                        $consDuration = $interval - 1 ;
                      }
                      else 
                      {
                        $consDuration = 5;
                      }
                    }else{
                      if ( $interval >=5 && $interval <= 8 )
                      {
                        $consDuration = 8 -1;
                      }
                      elseif ( $interval >=5 && $interval > 8 )
                      {
                        $consDuration = $interval -1;
                      }
                      else 
                      {
                        $consDuration = 4;
                      }
                    }
                }else{
                    // echo $interval;
                    echo "Minimum Overtime 1 hr. ";
                      return;
                  }
            }
        }
      
        $consDuration2=0;
        //vation validate holiday and restday
        if ($consDuration > $nhours ){
            $consDuration2 =$consDuration - $nhours;
        }

        $pay1=0;$pay2=0;
        //special holiday or rest day ot
        if ($consDuration > $nhours ){
            $consDuration = $nhours;
        }

        //if computation
        {
          if( ($holidaytype == 2 or $restday == 1) and $x==0)
          {
              $pay1 = (($hrate * 1.3)) * floor($consDuration);
                if ($consDuration2>0){
              $pay2 = (($hrate * 1.3) * 1.3) * floor($consDuration2);
                }
        
            $otpay = $pay1 + $pay2;
            $calculationtype = "SHorR";
          }
          //special holiday and restday ot
          elseif(($holidaytype == 2 and $restday == 1) and $x==1)
          {
              $pay1 = (($hrate * 1.5)) * floor($consDuration);
        
                if ($consDuration2>0){
              $pay2 = (($hrate * 1.5) * 1.3) *  floor($consDuration2);
                }
        
            $otpay = $pay1 + $pay2;
            $calculationtype = "SHandR";
          }
          //regular and rest day ot
          elseif( $holidaytype == 1 and $restday == 1 )
          {
              $pay1 = (($hrate * 2.6)) * floor($consDuration);
            
                if ($consDuration2>0){
              $pay2 = (($hrate * 2.6) * 1.3) *  floor($consDuration2);
                }
        
            $otpay = $pay1 + $pay2;
            $calculationtype = "RHandR";
          }
          //regular day ot
          elseif( $holidaytype == 1 and $restday == 0 ){
              $pay1 = (($hrate * 2)) * floor($consDuration);
                if ($consDuration2>0){
              $pay2 = (($hrate * 2) * 1.3) *  floor($consDuration2);
                }
        
            $otpay = $pay1 + $pay2;
            $calculationtype = "RH";
          }else{
            //calculate ordinary day
            $otpay=(($hrate * 1.25) * floor($consDuration)); 
              $calculationtype = "Ordinary";
          }
          //echo $otpay . " Cal- " . $calculationtype . " ConsDuration -  " . $consDuration . " Basic -  " . $basic . " Hrate - " . $hrate . " AvgWork - " . $avgDWork . " Hours - " . $nhours;
        }

        // insert into ot query
        {
          $sql = "INSERT INTO otattendancelog (EmpID,EmpISID,TimeIn,TimeOut,DateFiling,TimeFiling,Status,Duration,OTPay,Purpose,DateTimeUpdate,ctype,DateTimeInputed) 
          VALUES (:id,:is,:ti,:to,:fd,:ft,:st,:dur,:otpy,:prs,:dtu,:ctype,:dti)";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id' ,$id);
          $stmt->bindParam(':is', $isid);
          $stmt->bindParam(':ti' ,$d1);
          $stmt->bindParam(':to' ,$d2);
          $stmt->bindParam(':fd' ,$today3);
          $stmt->bindParam(':ft' ,$fillingtime);
          $stmt->bindParam(':st' ,$statid);
          $stmt->bindParam(':dur' ,$interval);  
          $stmt->bindParam(':prs' ,$_POST['otpur']);
          $stmt->bindParam(':otpy' ,$otpay);
          $stmt->bindParam(':dtu' ,$today2);
          $stmt->bindParam(':ctype' ,$calculationtype);
          $stmt->bindParam(':dti' ,$today2);
          // $stmt->execute();
          $ida = $pdo->lastInsertId();
        }
        
        // start 7/11/2024 gemana lookback initialization   
        {
          $sqlst = "SELECT * FROM payrol WHERE (:dts BETWEEN PYDateFrom AND PYDateTo) or (:dtt BETWEEN PYDateFrom AND PYDateTo) LIMIT 1";
          $state = $pdo->prepare($sqlst);
          $state->bindParam(':dts', $otS);
          $state->bindParam(':dtt', $otS);
          $state->execute();

          if($state->rowCount()>0){//if true then 
            while ($checkerstate = $state->fetch()) {
              $pdateData = $checkerstate['PYDate'];
            }

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
         
            //trigger exception in a "try" block
            try {
                $tagSQL="INSERT INTO ot_lookback (refID,refEmpID,refPayDate,inputdate) VALUES(:id,:empid,:pdate,:inputdate)";
                $tagSQL=$pdo->prepare($tagSQL);
                $tagSQL->bindParam(':id',$ida);
                $tagSQL->bindParam(':empid',$id);
                $tagSQL->bindParam(':pdate', $pdateData);
                $tagSQL->bindParam(':inputdate',$today2);
                $tagSQL->execute(); 
            }

            //catch exception
            catch(Exception $e) {
              echo 'Message: ' .$e->getMessage();
            }
          }
        
        }
        // end 7/11/2024 gemana lookback initialization

        //dar
        {
          $id=$_SESSION['id'];
          $ch="Applied OT";
          // insert into dars
          $sql = "INSERT INTO dars (EmpID,EmpActivity) VALUES (:id,:empact)";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id' , $id);
          $stmt->bindParam(':empact', $ch);
          $stmt->execute(); 
        }
        ################### LOOP INSERT END ############################
        $otS = date ("Y-m-d", strtotime($otS. "+1 day"));

      }

      echo "Application succesfully save! ";                  
  } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
  }


 ?>