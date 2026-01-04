<?php 
  include 'w_conn.php';
  session_start();
  if (isset($_SESSION['id']) && $_SESSION['id']!="0"){}
  else{ header ('location: login.php'); }
   date_default_timezone_set("Asia/Manila");
    try{
        $pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        die("ERROR: Could not connect. " . $e->getMessage());
    }
  
    ################################################
        $datenow = date("Y-m-d");
        $datenow1 = date("Y-m-d H:i");
        $timenow = strtotime($datenow1);
        $startTime = strtotime($datenow ." 08:00");
        
        
        $id=$_SESSION['id'];
        if($id=="WeDoinc-012"){
        }else{
        if($timenow > $startTime){
            echo 4;
            return;
            }
        }
    ################################################

    $todaydar = date("Y-m-d H:i:s");
    $id=$_SESSION['id'];
    $isid=$_SESSION['EmpISID'];
    $start = $_POST['obdf'];
    $end = $_POST['obdt'];
    //user type validation
    {
        if ($_SESSION['UserType']==1){
            $statid=4;
            $obtype=2;
            $ESID= $_POST['empidob'];
        }else{
            if ($_POST['empidob']==$_SESSION['id']){
                $ESID= $_POST['empidob'];
                $statid=1;
                $obtype=1;
                $id=$_SESSION['EmpISID'];
            }else{
                $ESID=$_POST['empidob'];
                $statid=2;
                $obtype=2;
            }
        }
    }

    //if filing ob not in schedule time
    {
        $day_desc=date("l", strtotime($_POST['obdf']));  
        $dstarts=date("Y-m-d", strtotime($_POST['obdf']));

        $workschedst = $pdo->prepare("Select * from workdays INNER JOIN workschedule 
            ON workdays.SchedTime=workschedule.WorkSchedID 
            inner join schedeffectivity as c 
            on workdays.EFID=c.efids
            where (workdays.empid=:id) and (workdays.Day_s=:daydesc)
            and ('$dstarts' >= dfrom) and ('$dstarts' <= dto)");
        $workschedst->bindParam(':id' , $ESID);
        $workschedst->bindParam(':daydesc' ,$day_desc);
        $workschedst->execute();

        $rowwrktime=$workschedst->fetch();
        $ifNoAttendance=$workschedst->rowCount(); 

        if($ifNoAttendance==0){
            echo 3;
            return;
        }
        
        $wrktimein = $rowwrktime['TimeFrom'];
        $wrktimeout = $rowwrktime['TimeTo'];

        $filetimefrom =  date("H:i", strtotime($_POST['depart']));  
        $filetimeto =  date("H:i", strtotime($_POST['return']));
        $dbtfrom =  date("H:i", strtotime($wrktimein));
        $dbtto =  date("H:i", strtotime($wrktimeout));

        if ($filetimefrom<$dbtfrom or $filetimefrom>$dbtto or  $filetimeto>$dbtto or $filetimeto<$dbtfrom){
            echo 3;
            return;
        }
    }

    //validate duplicate filing 
    {
        $today =date("Y-m-d"); 
        $today2 =date("Y-m-d H:i:s"); 
        $dteStart = new DateTime($_POST['obdf']. ' ' .$_POST['depart']); 
        $dteEnd   = new DateTime($_POST['obdt']. ' ' .$_POST['return']); 
        $dteDiff  = $dteStart->diff($dteEnd); 
        $interval = $dteDiff->format("%H");

        $stmtcheckob = $pdo->prepare("select * from obs where EmpID=:id and (OBDateFrom=:odfrom or OBDateTo=:odto or OBDateFrom=:odto or OBDateTo=:odfrom or (OBDateFrom<:odfrom and OBDateTo>:odfrom) or 
                                    (OBDateFrom<:odto and OBDateTo>:odto))  and (OBStatus=1 or OBStatus=2 or OBStatus=4)");
        $stmtcheckob->bindParam(':id' , $ESID);
        $stmtcheckob->bindParam(':odfrom' ,$_POST['obdf']);
        $stmtcheckob->bindParam(':odto' ,$_POST['obdt']);
        $stmtcheckob->execute();
        $rwCount=$stmtcheckob->rowCount();
        if ($rwCount>0){
            while($rwftch=$stmtcheckob->fetch()){
                $dtfromob=$rwftch['OBTimeFrom'];
                $dttoob=$rwftch['OBTimeTo'];
                if ($dtfromob<=$_POST['depart'] && $dttoob>=$_POST['depart']){
                    echo 2;
                    return;
                }elseif($dtfromob<=$_POST['return'] && $dttoob>=$_POST['return']){
                    echo 2;
                    return;
                }elseif($_POST['depart']<=$dtfromob && $_POST['return']>=$dtfromob){
                    echo 2;
                    return;
                }elseif($_POST['depart']<=$dttoob && $_POST['return']>=$dttoob){
                    echo 2;
                    return;
                }
            }
        }
    }

    // insert into ob summary table
    {
        $sql = "INSERT INTO obs (EmpID,EmpSID,OBFD,OBDateFrom,OBDateTo,OBIFrom,OBDuration,OBITo,OBPurpose,OBTimeFrom,OBTimeTo,OBCAAmt,OBCAPurpose,OBStatus,OBUpdated,OBType,OBInputDate) 
            VALUES (:id,:is,:fd,:odfrom,:odto,:oifrom,:oduration,:oito,:opurpose,:otfrom,:otto,:ocamt,:obcp,:obs,:obup,:obtt,:ipd)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id' , $ESID);
        $stmt->bindParam(':is', $id);
        $stmt->bindParam(':fd' ,$today);
        $stmt->bindParam(':odfrom' ,$_POST['obdf']);
        $stmt->bindParam(':odto' ,$_POST['obdt']);
        $stmt->bindParam(':oifrom' ,$_POST['itfrom']);
        $stmt->bindParam(':oduration' ,$interval);
        $stmt->bindParam(':oito' ,$_POST['itto']);
        $stmt->bindParam(':opurpose' ,$_POST['emppurpose']);
        $stmt->bindParam(':otfrom' ,$_POST['depart']);
        $stmt->bindParam(':otto' ,$_POST['return']);
        $stmt->bindParam(':ocamt' ,$_POST['ca']);
        $stmt->bindParam(':obcp' ,$_POST['capurpose']);
        $stmt->bindParam(':obs' ,$statid);
        $stmt->bindParam(':obup' ,$today2);
        $stmt->bindParam(':obtt' ,$obtype);
        $stmt->bindParam(':ipd' ,$today2);
        $stmt->execute(); 
    }

    //insert dar
    {
        $statementGet = $pdo->prepare("SELECT * FROM employees where EmpID =  :dd");
        $statementGet->bindParam(':dd' ,$ESID);
        $statementGet->execute();
        $rowGet=$statementGet->fetch();

        if ($statementGet->rowCount()>0){
            $dd= $rowGet['EmpLN'] . ' ' . $rowGet['EmpFN'];
        }

        $ch="Applied Send to OB " . $dd;
        $sqls = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
        $stmt23 = $pdo->prepare($sqls);
        $stmt23->bindParam(':id' , $id);
        $stmt23->bindParam(':empact', $ch);
        $stmt23->bindParam(':ddt', $todaydar);
        $stmt23->execute();
    }
    
    //insert to obs itemize
    {
        $statement = $pdo->prepare("SELECT max(OBID) FROM obs ");
        $statement->execute();
        $roww=$statement->fetch();
        if ($statement->rowCount()>0){
            $maxid= $roww[0];
        }else{
            $maxid= 1;
        }
        $fkeyid=$maxid;
        $obdf = $_POST['obdf'];
        $obdt = $_POST['obdt'];

        // if ($obdf==$obdt){
        //     // insert into dars
        //     $sql = "INSERT INTO obshbd (OBID,EmpID,EmpSID,OBFD,OBDateFrom,OBDateTo,OBIFrom,OBDuration,OBITo,OBPurpose,OBTimeFrom,OBTimeTo,OBCAAmt,OBCAPurpose,OBStatus,OBUpdated,OBType,OBInputDate) 
        //     VALUES (:oid,:id,:is,:fd,:odfrom,:odto,:oifrom,:oduration,:oito,:opurpose,:otfrom,:otto,:ocamt,:obcp,:obs,:obup,:obtt,:ipd)";
        //     $stmt = $pdo->prepare($sql);
        //     $stmt->bindParam(':oid' , $fkeyid);
        //     $stmt->bindParam(':id' , $ESID);
        //     $stmt->bindParam(':is', $id);
        //     $stmt->bindParam(':fd' ,$today);
        //     $stmt->bindParam(':odfrom' ,$_POST['obdf']);
        //     $stmt->bindParam(':odto' ,$_POST['obdt']);
        //     $stmt->bindParam(':oifrom' ,$_POST['itfrom']);
        //     $stmt->bindParam(':oduration' ,$interval);
        //     $stmt->bindParam(':oito' ,$_POST['itto']);
        //     $stmt->bindParam(':opurpose' ,$_POST['emppurpose']);
        //     $stmt->bindParam(':otfrom' ,$_POST['depart']);
        //     $stmt->bindParam(':otto' ,$_POST['return']);
        //     $stmt->bindParam(':ocamt' ,$_POST['ca']);
        //     $stmt->bindParam(':obcp' ,$_POST['capurpose']);
        //     $stmt->bindParam(':obs' ,$statid);
        //     $stmt->bindParam(':obup' ,$today2);
        //     $stmt->bindParam(':obtt' ,$obtype);
        //     $stmt->bindParam(':ipd' ,$today2);
        //     // $stmt->execute(); 
        // }else

        // echo 0;
        // return;
        
        {
            $dtstartob=$obdf;
            $dtendob=$obdt;

            while ($dtstartob<=$dtendob) {
                $day_desc = date ("l", strtotime($dtstartob));
                $dtstartob1 = date ("Y-m-d", strtotime($dtstartob));
                $statementleaveadd  = $pdo->prepare("Select * from workdays INNER JOIN 
                workschedule ON workdays.SchedTime=workschedule.WorkSchedID 
                inner join schedeffectivity as c on workdays.EFID=c.efids
                where (workdays.empid='$ESID') and (workdays.Day_s='$day_desc') and ('$dtstartob1' >= dfrom) and ('$dtstartob1 ' <= dto)  and (SchedTime > 0)");
                $statementleaveadd->execute();
                // if have schedule
                if ($statementleaveadd->rowCount()>0){  

                    $mtnh = date("Y-m-d", strtotime($dtstartob));
                    $statementhol = $pdo->prepare("select * from holidays where Hdate=:mnth order by sid desc");
                    $statementhol->bindParam(':mnth', $mtnh);
                    $statementhol->execute();
                    $counthol = $statementhol->rowCount();
               

                    //validate if not holiday
                    if($counthol == 0){
                        $sql = "INSERT INTO obshbd (OBID,EmpID,EmpSID,OBFD,OBDateFrom,OBDateTo,OBIFrom,OBDuration,OBITo,OBPurpose,OBTimeFrom,OBTimeTo,OBCAAmt,OBCAPurpose,OBStatus,OBUpdated,OBType,OBInputDate) 
                        VALUES (:oid,:id,:is,:fd,:odfrom,:odto,:oifrom,:oduration,:oito,:opurpose,:otfrom,:otto,:ocamt,:obcp,:obs,:obup,:obtt,:ipd)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':oid' , $fkeyid);
                        $stmt->bindParam(':id' , $ESID);
                        $stmt->bindParam(':is', $id);
                        $stmt->bindParam(':fd' ,$today);
                        $stmt->bindParam(':odfrom' ,$dtstartob);
                        $stmt->bindParam(':odto' ,$dtstartob);
                        $stmt->bindParam(':oifrom' ,$_POST['itfrom']);
                        $stmt->bindParam(':oduration' ,$interval);
                        $stmt->bindParam(':oito' ,$_POST['itto']);
                        $stmt->bindParam(':opurpose' ,$_POST['emppurpose']);
                        $stmt->bindParam(':otfrom' ,$_POST['depart']);
                        $stmt->bindParam(':otto' ,$_POST['return']);
                        $stmt->bindParam(':ocamt' ,$_POST['ca']);
                        $stmt->bindParam(':obcp' ,$_POST['capurpose']);
                        $stmt->bindParam(':obs' ,$statid);
                        $stmt->bindParam(':obup' ,$today2);
                        $stmt->bindParam(':obtt' ,$obtype);
                        $stmt->bindParam(':ipd' ,$today2);
                        $stmt->execute();

                        //SOB-02
                        {  
                            $ida = $pdo->lastInsertId();
                            $sqlst = "SELECT * FROM payrol WHERE (:dts BETWEEN PYDateFrom AND PYDateTo) ";
                            $state = $pdo->prepare($sqlst);
                            $state->bindParam(':dts', $dtstartob);
                            $state->execute();
                
                            while ($checkerstate = $state->fetch()) {
                                $pdateData = $checkerstate['PYDate'];//aug 5
                            }
                
                            if($state->rowCount()>=1){
                                //ramon
                                if(date_format(date_create($pdateData),"d")==5){
                                     $pdateData=date_format(date_create($pdateData),"Y-m"."-20");//aug 20
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
                                    $maxdate = $checkerstatemaxdate['PYDate']; //aug 20
                                }
                                
                             
                                if($statemaxid->rowCount()>=1){

                                    if($maxdate >= $pdateData ){ //auug 20 > aug 20
                                        if(date_format(date_create($maxdate),"d")==5){ 
                                        $pdateData=date_format(date_create($maxdate),"Y-m"."-20");
                                        }else{
                                        $pdateData=date_format(date_create($maxdate),"Y-m"."-5");
                                        $pdateData = date ("Y-m-d", strtotime($pdateData. "+ 1 month")); // 
                                        // print $pdateData;
                                        // return;
                                        }
                                    }
                                }
                
                                $tagSQL="INSERT INTO ob_look_back (refID,refEmpID,refPayDate,inputdate) VALUES(:id,:empid,:pdate,:inputdate)";
                                $tagSQL=$pdo->prepare($tagSQL);
                                $tagSQL->bindParam(':id',$ida);
                                $tagSQL->bindParam(':empid',$ESID);
                                $tagSQL->bindParam(':pdate', $pdateData); //aug 20
                                $tagSQL->bindParam(':inputdate',$today2);

                                $tagSQL->execute(); 
                            }
                        }
                    }    
                }

                $dtstartob = date ("Y-m-d", strtotime($dtstartob. "+1 day"));
            }

            print 100;
            return;
        } 
    }    
 ?>