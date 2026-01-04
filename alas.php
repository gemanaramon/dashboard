<?php session_start();
    if (isset($_SESSION['id']) && $_SESSION['id'] != "0") {

    } else {
        if (! isset($_COOKIE["WeDoID"])) {

            header('location: login');
        } else {
            if (! isset($_COOKIE["WeDoID"])) {
                session_destroy();
                header('location: login');
            } else {
                try {
                    include 'w_conn.php';
                    $pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die("ERROR: Could not connect. " . $e->getMessage());
                }
                $statement = $pdo->prepare("select * from empdetails");
                $statement->execute();

                while ($row = $statement->fetch()) {
                    if (password_verify($row['EmpID'], $_COOKIE["WeDoID"])) {
                        $_SESSION['id'] = $row['EmpID'];

                        $statement = $pdo->prepare("select * from empdetails where EmpID = :un");
                        $statement->bindParam(':un', $_SESSION['id']);
                        $statement->execute();
                        $count                = $statement->rowCount();
                        $row                  = $statement->fetch();
                        $hash                 = $row['EmpPW'];
                        $_SESSION['UserType'] = $row['EmpRoleID'];
                        $cid                  = $row['EmpCompID'];
                        $_SESSION['CompID']   = $row['EmpCompID'];
                        $_SESSION['EmpISID']  = $row['EmpISID'];
                        $statement            = $pdo->prepare("select * from companies where CompanyID = :pw");
                        $statement->bindParam(':pw', $cid);
                        $statement->execute();
                        $comcount = $statement->rowCount();
                        $row      = $statement->fetch();
                        if ($comcount > 0) {
                            $_SESSION['CompanyName']  = $row['CompanyDesc'];
                            $_SESSION['CompanyLogo']  = $row['logopath'];
                            $_SESSION['CompanyColor'] = $row['comcolor'];
                        } else {
                            $_SESSION['CompanyName']  = "ADMIN";
                            $_SESSION['CompanyLogo']  = "";
                            $_SESSION['CompanyColor'] = "red";
                        }
                        $_SESSION['PassHash'] = $hash;

                    } else {

                    }
                }
            }
        }

    }
?>
<?php
    date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Automated Leave Application System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript" src="assets/js/script.js"></script>
    <script src="assets/js/script-reports.js"></script>
    <script type="text/javascript" src="assets/js/script-modules.js"></script>
        <script type="text/javascript" src="assets/js/administrative.js"></script>

    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
    <script>
    function FunctionChangedate() {
        var d = new Date();
        var n = d.getFullYear();
        var x = document.getElementById("lstarts").max = n + "-12-31";
        var x = document.getElementById("lenddate").max = n + "-12-31";
        var x = document.getElementById("lstarts").min = n + "-01-01";
        var x = document.getElementById("lenddate").min = n + "-01-01";
    }
    </script>
</head>

<style type="text/css">
html body {
    font-family: Tahoma !important;
}

.modal-backdrop {
    background-color: transparent;
}

.ihd-dis {
    /*display:  none;*/
}
</style>

<body style="background-image: none">
    <?php
        include 'includes/header.php';
    ?>
    <div class="w-container">
        <div class="row">
            <div class="col-lg-3"></div>
            <!-- website content -->
            <div class="col-lg-9 module-content">
                <h4 class="page-title" style="<?php echo "color: " . $_SESSION['CompanyColor']; ?>">Automated Leave
                    Application</h4>

                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" id="eventListener" class="btn btn-primary" data-toggle="modal" data-target="#newform">+ Leave
                            Application Form</button>
                        <!-- The Modal -->
                        <div class="modal" id="newform">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Leave Application Form</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <?php
                                        include 'w_conn.php';
                                        try {
                                            $pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
                                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                        } catch (PDOException $e) {
                                            die("ERROR: Could not connect. " . $e->getMessage());
                                        }

                                        $id        = $_SESSION['id'];
                                        $isid      = $_SESSION['EmpISID'];
                                        $statement = $pdo->prepare("SELECT *
                              FROM employees
                              INNER JOIN empdetails ON employees.EmpID=empdetails.EmpID
                              INNER JOIN companies ON empdetails.EmpCompID=companies.CompanyID
                              INNER JOIN departments ON empdetails.EmpdepID=departments.DepartmentID
                              INNER JOIN positions ON positions.PSID=employees.PosID where employees.EmpID=:id order by employees.EmpLN ASC ");
                                        $statement->bindParam(':id', $id);
                                        $statement->execute();
                                        $row = $statement->fetch();
                                    ?>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <form id="alas_data" action="">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Personnel Name:</label>
                                                        <input type="text" disabled class="form-control"
                                                            value="<?php echo $row['EmpLN'] . ' ' . $row['EmpFN'] . ' ' . $row['EmpMN'] ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Company Name:</label>
                                                        <input type="text" disabled class="form-control"
                                                            value="<?php echo $row['CompanyDesc'] ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Department:</label>
                                                        <input type="text" disabled class="form-control"
                                                            value="<?php echo $row['DepartmentDesc'] ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Designation:</label>
                                                        <input type="text" disabled class="form-control"
                                                            value="<?php echo $row['PositionDesc'] ?>">
                                                    </div>
                                                    <div class="form-group d-none">
                                                        <label>Leave Kind:</label>
                                                        <select class="form-control" id="leavepay" required="required"
                                                            name="leavepay">
                                                            <option value="1">Paid</option>
                                                            <!--<option value="0">Unpaid</option>-->
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Leave Type:</label>
                                                        <select class="form-control" id="leavetype" required="required"
                                                            name="leavetype">

                                                            <?php
                                                                include 'w_conn.php';
                                                                #2024 new script

                                                                $id = $_SESSION['id'];

                                                                $sql25  = "select * from credit where EmpID = :id";
                                                                $stmt25 = $pdo->prepare($sql25);
                                                                $stmt25->bindParam(':id', $id);
                                                                $stmt25->execute();
                                                                $crdetail25 = $stmt25->fetch();
                                                                $crcnt25    = $stmt25->rowCount();

                                                                if ($crcnt25 > 0) {
                                                                    // if( $crdetail25['CTH']==15){
                                                                    //     if ($_SESSION['gender'] == "Male") {
                                                                    //         $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','35','33','30','22') order by LeaveDesc asc");

                                                                    //     } else if ($_SESSION['gender'] == "Female") {
                                                                    //         $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('35','27','33','30','22') order by LeaveDesc asc");

                                                                    //     } else {
                                                                    //         $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','35','27','36','37','33','30','22') order by LeaveDesc asc");
                                                                    //     }
                                                                    // }else{
                                                                    //     if ($_SESSION['gender'] == "Male") {
                                                                    //         $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','30','22','33') order by LeaveDesc asc");

                                                                    //     } else if ($_SESSION['gender'] == "Female") {
                                                                    //         $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('27','30','22','33') order by LeaveDesc asc");
                                                                    //     } else {
                                                                    //         $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','27','36','37','33','30','22') order by LeaveDesc asc");
                                                                    //     }
                                                                    // }
                                                                    if ($crdetail25['CTH'] == 0 && $crdetail25['CTH'] == 0) {
                                                                        if (($id == "WeDoinc-002") || ($id == "WeDoinc-006") || ($id == "WeDoinc-009") || ($id == "WeDoinc-0010") || ($id == "WeDoinc-012") || ($id == "WeDoinc-013")) {
                                                                            if ($_SESSION['gender'] == "Male") {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','35','33','30','22') order by LeaveDesc asc");

                                                                            } else if ($_SESSION['gender'] == "Female") {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('35','27','33','30','22') order by LeaveDesc asc");

                                                                            } else {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','35','27','36','37','33','30','22') order by LeaveDesc asc");
                                                                            }
                                                                        } else {
                                                                            if ($_SESSION['gender'] == "Male") {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','30','22','33') order by LeaveDesc asc");

                                                                            } else if ($_SESSION['gender'] == "Female") {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('27','30','22','33') order by LeaveDesc asc");

                                                                            } else {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','27','36','37','33','30','22') order by LeaveDesc asc");
                                                                            }
                                                                        }

                                                                    } else {
                                                                        if ($crdetail25['CTH'] == 15) {
                                                                            if ($_SESSION['gender'] == "Male") {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','35','33','30','22') order by LeaveDesc asc");

                                                                            } else if ($_SESSION['gender'] == "Female") {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('35','27','33','30','22') order by LeaveDesc asc");

                                                                            } else {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','35','27','36','37','33','30','22') order by LeaveDesc asc");
                                                                            }
                                                                        } else {
                                                                            if ($_SESSION['gender'] == "Male") {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','30','22','33') order by LeaveDesc asc");

                                                                            } else if ($_SESSION['gender'] == "Female") {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('27','30','22','33') order by LeaveDesc asc");

                                                                            } else {
                                                                                $sql = mysqli_query($con, "select * from  leaves where LeaveID IN('29','27','36','37','33','30','22') order by LeaveDesc asc");
                                                                            }

                                                                        }
                                                                    }

                                                                    while ($res = mysqli_fetch_array($sql)) {
                                                                        if ($_SESSION['id'] != "WeDoinc-003" and $res['LeaveID'] == 34) {
                                                                        } else {
                                                                        ?>
                <option value="<?php echo $res['LeaveID']; ?>">
                    <?php echo $res['LeaveDesc']; ?> </option>
                <?php
                    }
                        }

                    } else {
                    ?>
        <option value="33">Unpaid</option>
        <?php

            }
        ?>
                                                        </select>

                                                    </div>
                                                    <div class="form-group">
                                                        <label>Explanation/ Purpose of Leave:</label>
                                                        <textarea class="form-control" rows="4" id="purposeofleave"
                                                            name="reason"></textarea>
                                                    </div>

                                                </div>
                                                <div class="col-lg-6">

                                                    <?php
                                                        include 'w_conn.php';
                                                        //get date hired as regular
                                                        try {
                                                            $pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
                                                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                        } catch (PDOException $e) {
                                                            die("ERROR: Could not connect. " . $e->getMessage());
                                                        }
                                                        $id = $_SESSION['id'];
                                                        //parameter this value
                                                        $sql  = "select * from empdetails where EmpID = :id";
                                                        $stmt = $pdo->prepare($sql);
                                                        $stmt->bindParam(':id', $id);
                                                        $stmt->execute();
                                                        $details      = $stmt->fetch();
                                                        $detailscnt   = $stmt->rowCount();
                                                        $cdPerMonth   = 0;
                                                        $cdPerDay     = 0;
                                                        $creditEarned = 0;
                                                        if ($detailscnt == 1) {
                                                            if ($details['EmpStatID'] != 1) {
                                                                $creditEarned = "Non Regular employee don't have credits";
                                                            } else {

                                                                if (is_null($details['EmpDOR'])) {
                                                                    $creditEarned = "Missing Regularization Date";
                                                                } else {

                                                                    $dth = $details['EmpDOR'];
                                                                    $yr  = date("Y", strtotime($dth));
                                                                    $cyr = date("Y");
                                                                    ######################
                                                                    #2024 script for credit
                                                                    #get the details to validate 15 and 10 cridets
                                                                    $sql24  = "select * from credit where EmpID = :id";
                                                                    $stmt24 = $pdo->prepare($sql24);
                                                                    $stmt24->bindParam(':id', $id);
                                                                    $stmt24->execute();
                                                                    $crdetail24 = $stmt24->fetch();
                                                                    $crcnt24    = $stmt24->rowCount();

                                                                    if ($crcnt24 > 0) {
                                                                        #if credit is 15 set all credit without earning
                                                                        if ($crdetail24['CTH'] == 15) {
                                                                            $creditEarned = $crdetail24['CT'];
                                                                        } else {
                                                                            #if credit is 10 then set system to earning mode
                                                                            if ($yr < $cyr) {
                                                                                $sql  = "select * from credit where EmpID = :id";
                                                                                $stmt = $pdo->prepare($sql);
                                                                                $stmt->bindParam(':id', $id);
                                                                                $stmt->execute();
                                                                                $crdetail = $stmt->fetch();
                                                                                $crcnt    = $stmt->rowCount();
                                                                                if ($crcnt > 0) {
                                                                                    $crh  = $crdetail['CTH'];
                                                                                    $crth = $crdetail['CT'];

                                                                                    $tdy   = date("Y");
                                                                                    $tdy1  = date("Y", strtotime(date("Y") . "+1 years"));
                                                                                    $date1 = date_create("1/1/" . $tdy);
                                                                                    $date2 = date_create("1/1/" . $tdy1);
                                                                                    $diff  = date_diff($date1, $date2);
                                                                                    //output data
                                                                                    $noOfDays = $diff->format("%a") / 12;

                                                                                    //credit per month earning
                                                                                    $cdPerMonth = $crh / 12;

                                                                                    //credit per day earning
                                                                                    $cdPerDay = $cdPerMonth / $noOfDays;

                                                                                    //get no of days from jan to present
                                                                                    $todaydate  = date("Y");
                                                                                    $todaydate1 = date("m/d/Y");
                                                                                    $gnOfdJan   = date_create("01/01/" . $todaydate);
                                                                                    // $gnOfdCur = date_create("11/01/2024" );
                                                                                    $gnOfdCur = date_create($todaydate1);
                                                                                    //$gnOfdCur=date_create("01/01/2021");
                                                                                    $diff2 = date_diff($gnOfdJan, $gnOfdCur);
                                                                                    //output data
                                                                                    $gnOfdJanCur = $diff2->format("%r%a");
                                                                                    //get use credits and subtract to total earned credits ramon
                                                                                    $useCredit = $crh - $crth;
                                                                                    //get total earned creidit
                                                                                    $creditEarned = ($cdPerDay * ($gnOfdJanCur + 1)) - $useCredit;
                                                                                    // print $gnOfdJanCur + 1;

                                                                                } else {
                                                                                    $creditEarned = "Missing credit logs";
                                                                                    //return;
                                                                                }
                                                                            } else {

                                                                                $sql  = "select * from credit where EmpID = :id";
                                                                                $stmt = $pdo->prepare($sql);
                                                                                $stmt->bindParam(':id', $id);
                                                                                $stmt->execute();
                                                                                $crdetail = $stmt->fetch();
                                                                                $crcnt    = $stmt->rowCount();
                                                                                if ($crcnt > 0) {
                                                                                    $crh   = $crdetail['CTH'];
                                                                                    $crth  = $crdetail['CT'];
                                                                                    $tdy   = date("Y");
                                                                                    $tdy1  = date("Y", strtotime(date("Y") . "+1 years"));
                                                                                    $date1 = date_create("1/1/" . $tdy);
                                                                                    $date2 = date_create("1/1/" . $tdy1);
                                                                                    $diff  = date_diff($date1, $date2);
                                                                                    //output data
                                                                                    $noOfDays = $diff->format("%a") / 12;
                                                                                    //credit per month earning
                                                                                    $cdPerMonth = $crh / 12;
                                                                                    //credit per day earning
                                                                                    $cdPerDay = $cdPerMonth / $noOfDays;
                                                                                    //get no of days from jan to present
                                                                                    $todaydate  = date("Y");
                                                                                    $todaydate1 = date("m/d/Y");
                                                                                    $gnOfdJan   = date_create($dth);
                                                                                    $gnOfdCur   = date_create($todaydate1);
                                                                                    //$gnOfdCur=date_create("01/01/2021");
                                                                                    $diff2 = date_diff($gnOfdJan, $gnOfdCur);
                                                                                    //output data
                                                                                    $gnOfdJanCur = $diff2->format("%a");
                                                                                    //get use credits and subtract to total earned credits ramon
                                                                                    $useCredit = $crh - $crth;
                                                                                    //get total earned creidit

                                                                                    $creditEarned = ($cdPerDay * $gnOfdJanCur) - $useCredit;

                                                                                    //if greater than
                                                                                    //return;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            //return;
                                                    }?>
                                                    <div class="form-group"
                                                        style="border: 1px solid red;background-color: #bc0c0c;padding: 5px;border-radius: 10px;color: #fff;">
                                                        <label>Leave Credits:</label>
                                                        <input name="lcredit" type="text" id="lcredit"
                                                            style="color:red;" readonly="readonly"
                                                            value="<?php echo number_format((float) $creditEarned, 2, '.', ''); ?>" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Filing Date:</label>
                                                        <input name="lfdates" type="text" readonly="readonly"
                                                            value="<?php echo date('F d, Y'); ?>" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Leave Start Date:</label>
                                                        <input name="lstarts" max="<?php echo date('Y-12-31'); ?>"
                                                            min="<?php echo date('Y-01-01'); ?>" type="date"
                                                            id="lstarts" value="<?php echo date('Y-m-d'); ?>"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Leave End Date:</label>
                                                        <!--<input name="lenddate" max="<?php echo date('Y-12-31'); ?>" min="<?php echo date('Y-01-01'); ?>"  type="date" id="lenddate" value="<?php echo date('Y-m-d'); ?>"  class="form-control" >-->
                                                        <input name="lenddate" min="<?php echo date('Y-01-01'); ?>"
                                                            type="date" id="lenddate"
                                                            value="<?php echo date('Y-m-d'); ?>" class="form-control">
                                                    </div>
                                                    <!-- <div class="form-group row">

												<div class="col-lg-6" style="padding-left: 0px;"><label>Time From:</label><input readonly="readonly" class="form-control"  value="08:00"type="time" name="Ltimefrom" id="Ltimefrom"></div>


												<div class="col-lg-6" style="padding-right: 0px;"> <label>Time To:</label><input class="form-control" readonly="readonly" value="19:00" type="time" name="Ltimeto" id="Ltimeto"></div>
											</div> -->
                                                    <div class="form-group">
                                                        <!--   -->
                                                        <div class="ihd-dis" style="float: right;">
                                                            <input type="checkbox" class="form-check-input"
                                                                style="position: relative;" id="exampleCheck1">
                                                            <label class="form-check-label" for="exampleCheck1">If Half
                                                                Day?</label>
                                                        </div>
                                                        <label class="dur-text">Duration Days:</label>
                                                        <input type="text" readonly="readonly" value="1" id="leavedur"
                                                            name="leavedur" class="form-control dura">
                                                    </div>

                                                    <button type="button" id="alassave"
                                                        class="btn btn-success btn-block">Submit</button>
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



                        <br>
                        <div class="dtpar">
                            <h5>Date Parameters:</h5>
                            <label>From:</label>

                            <input type="date" class="form-control" id="dtp1"
                                value="<?php echo date('Y-m-d', strtotime(date("Y-m-d") . ' - 15 days')); ?>">
                            <label>To:</label>
                            <input type="date" class="form-control" id="dtp2" value="<?php echo date("Y-m-d"); ?>">
                            <button class="btn" id="alashistory" type="button"><img src="assets/images/refreshicon.png"
                                    data-toggle="tooltip" data-placement="right" title="Refresh" width="25px"></button>
                        </div>
                        <h5 class="module-title-history">Leave History</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Filling Date</th>
                                    <th>Date From</th>
                                    <th>Date To</th>
                                    <th>Duration</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody id="tbalas">
                                <?php
                                    try {
                                        include 'w_conn.php';
                                        $pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                    } catch (PDOException $e) {
                                        die("ERROR: Could not connect. " . $e->getMessage());
                                    }
                                    $id        = $_SESSION['id'];
                                    $dt1       = date('Y-m-d', strtotime(date("Y-m-d") . ' - 15 days'));
                                    $dt2       = date("Y-m-d", strtotime(date("Y-m-d") . ' + 1 days'));
                                    $statement = $pdo->prepare("SELECT a.LStatus as LST,a.FID as IDL,LeaveDesc,a.LFDate as FD,a.LStart as LS,a.LEnd as LE,a.LDuration as dur,a.LPurpose as LP,StatusDesc from hleavesbd as a
					    			                            INNER JOIN hleaves on a.FID=hleaves.LeaveID
															  	INNER JOIN status as b on a.LStatus=b.StatusID
															  	INNER JOIN leaves_validation c ON c.sid=a.LType
															  	INNER JOIN leaves as d on c.lid=d.LeaveID
															  	where a.EmpID=:id and hleaves.LStatus<>7 and a.LInputDate BETWEEN :dt1 AND :dt2 order by a.LStart asc");
                                    $statement->bindParam(':id', $id);
                                    $statement->bindParam(':dt1', $dt1);
                                    $statement->bindParam(':dt2', $dt2);
                                    $statement->execute();
                                    while ($row21 = $statement->fetch()) {
                                    ?>

                                <tr>
                                    <td><?php echo $row21['LeaveDesc']; ?></td>
                                    <td><?php echo date("F j, Y", strtotime($row21['FD'])); ?></td>
                                    <td><?php echo date("F j, Y", strtotime($row21['LS'])); ?></td>
                                    <td><?php echo date("F j, Y", strtotime($row21['LE'])); ?></td>
                                    <td><?php echo $row21['dur'] . " min"; ?></td>

                                    <td><?php echo $row21['LP']; ?></td>
                                    <td><?php echo $row21['StatusDesc']; ?></td>
                                    <?php
                                        if ($row21['LST'] == 1) {
                                            ?>
                                    <td><button type="button" class="btn btn-danger" data-toggle="modal"
                                            data-target="#myModalob<?php echo $row21['IDL']; ?>"><i class="fa fa-trash"
                                                aria-hidden="true"></i></button> </td>
                                    <?php
                                        } else {

                                            }
                                        ?>
                                </tr>

                                <!-- The Modal -->
                                <div class="modal ob-viewdel" id="myModalob<?php echo $row21['IDL']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">Are you sure you want to remove this ??</h4>
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                            </div>

                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <button type="button" id="<?php echo $row21['IDL']; ?>"
                                                    class="btn btn-success ys_leave">Yes</button>
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">No</button>
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
                <div class="modal" id="modalSuccess">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header" style="padding: 7px 8px;">
                                <h1 style="font-size: 25px; padding-left: 10px;color:green;"><i class="fa fa-check"
                                        aria-hidden="true"></i></h1>
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
                <div class="modal" id="modalWarning">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header" style="padding: 7px 8px;">
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


                <!-- end of website content -->
            </div>
        </div>
    </div>
</body>

</html>