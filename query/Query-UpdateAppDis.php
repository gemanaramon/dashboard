<?php

//disapprove application
include 'w_conn.php';
session_start();
if (isset($_SESSION['id']) && $_SESSION['id'] != "0") {} else {header('location: login.php');}
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
date_default_timezone_set("Asia/Manila");
$tdy = date("Y-m-d H:i:s");
$rss = $_POST['reas'];
if ($_SESSION['UserType'] == 3) {
    $stat = 3;

} else if ($_SESSION['UserType'] == 2) {
    $stat = 3;

} else {
    $stat = 5;
}


if (isset($_GET['ntype'])) {
    if ($_GET['ntype'] == "EO") {

        if ($_SESSION['UserType'] == 1) {
            $sql = "UPDATE earlyout SET Status=:st,HR_remark=:rs,DateTimeUpdated=:dtu where SID=" . $_GET['id'];
        } else {
            $sql = "UPDATE earlyout SET Status=:st,IS_remark=:rs,DateTimeUpdated=:dtu where SID=" . $_GET['id'];
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':st', $stat);
        $stmt->bindParam(':rs', $rss);
        $stmt->bindParam(':dtu', $tdy);
        $stmt->execute();

        $sql2 = "select employees.EmpLN as LN,employees.EmpFN as FN from employees inner join earlyout on employees.EmpID=earlyout.EMPID where SID=" . $_GET['id'];
        $stmt = $pdo->prepare($sql2);
        $stmt->execute();
        $row = $stmt->fetch();
        $nameE = $row['FN'] . " " . $row['LN'];

        $id = $_SESSION['id'];
        $ch = "DisApproved EO of " . $nameE;
        // insert into dars
        $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':empact', $ch);
        $stmt->bindParam(':ddt', $tdy);
        $stmt->execute();

    } else if ($_GET['ntype'] == "OB") {
        if ($_SESSION['UserType'] == 1) {

            $sql2 = "UPDATE obshbd SET OBStatus=:st,OBHRReason=:rs,OBUpdated=:dtu where OBID=" . $_GET['id'];
            $sql = "UPDATE obs SET OBStatus=:st,OBHRReason=:rs,OBUpdated=:dtu where OBID=" . $_GET['id'];
        } else {

            $sql2 = "UPDATE obshbd SET OBStatus=:st,OBISReason=:rs,OBUpdated=:dtu where OBID=" . $_GET['id'];
            $sql = "UPDATE obs SET OBStatus=:st,OBISReason=:rs,OBUpdated=:dtu where OBID=" . $_GET['id'];
        }
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindParam(':st', $stat);
        $stmt2->bindParam(':rs', $rss);
        $stmt2->bindParam(':dtu', $tdy);
        $stmt2->execute();

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':st', $stat);
        $stmt->bindParam(':rs', $rss);
        $stmt->bindParam(':dtu', $tdy);
        $stmt->execute();

        $sql2 = "select employees.EmpLN as LN,employees.EmpFN as FN from employees inner join obs on employees.EmpID=obs.EmpID where OBID=" . $_GET['id'];
        $stmt = $pdo->prepare($sql2);
        $stmt->execute();
        $row = $stmt->fetch();
        $nameE = $row['FN'] . " " . $row['LN'];

        $id = $_SESSION['id'];
        $ch = "DisApproved OB of " . $nameE;
        // insert into dars
        $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':empact', $ch);
        $stmt->bindParam(':ddt', $tdy);
        $stmt->execute();
    } else if ($_GET['ntype'] == "HL") {
        if ($_SESSION['UserType'] == 1) {
            $sql = "UPDATE hleaves SET LStatus=:st,LHRReason=:rs,LDateTimeUpdated=:dtu where LeaveID=" . $_GET['id'];
        } else {
            $sql = "UPDATE hleaves SET LStatus=:st,LISReason=:rs,LDateTimeUpdated=:dtu where LeaveID=" . $_GET['id'];
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':st', $stat);
        $stmt->bindParam(':rs', $rss);
        $stmt->bindParam(':dtu', $tdy);
        $stmt->execute();

        $sql = "UPDATE hleavesbd set LStatus=:st,LISReason=:rs,LDateTimeUpdated=:dtu where FID=:lid";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':st', $stat);
        $stmt->bindParam(':rs', $rss);
        $stmt->bindParam(':dtu', $tdy);
        $stmt->bindParam(':lid', $_GET['id']);
        $stmt->execute();

        $sql2 = "select employees.EmpLN as LN,employees.EmpFN as FN from employees inner join hleaves on employees.EmpID=hleaves.EmpID where LeaveID=" . $_GET['id'];
        $stmt = $pdo->prepare($sql2);
        $stmt->execute();
        $row = $stmt->fetch();
        $nameE = $row['FN'] . " " . $row['LN'];

        $id = $_SESSION['id'];
        $ch = "Disapproved Leave of " . $nameE;
        // insert into dars
        $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':empact', $ch);
        $stmt->bindParam(':ddt', $tdy);
        $stmt->execute();

    } else if ($_GET['ntype'] == "OT") {
        if ($_SESSION['UserType'] == 1) {

            $sql = "UPDATE otattendancelog SET Status=:st,HRReason=:rs,DateTimeUpdate=:dtu where OTLOGID=" . $_GET['id'];
        } else {

            $sql = "UPDATE otattendancelog SET Status=:st,ISReason=:rs,DateTimeUpdate=:dtu where OTLOGID=" . $_GET['id'];
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':st', $stat);
        $stmt->bindParam(':rs', $rss);
        $stmt->bindParam(':dtu', $tdy);
        $stmt->execute();

        $sql2 = "select employees.EmpLN as LN,employees.EmpFN as FN from employees inner join otattendancelog on employees.EmpID=otattendancelog.EmpID where OTLOGID=" . $_GET['id'];
        $stmt = $pdo->prepare($sql2);
        $stmt->execute();
        $row = $stmt->fetch();
        $nameE = $row['FN'] . " " . $row['LN'];

        $id = $_SESSION['id'];
        $ch = "Disapproved OT of " . $nameE;
        // insert into dars
        $sql = "INSERT INTO dars (EmpID,EmpActivity,DarDateTime) VALUES (:id,:empact,:ddt)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':empact', $ch);
        $stmt->bindParam(':ddt', $tdy);
        $stmt->execute();
    }
}
