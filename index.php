<?php     
  // // server should keep session data for AT LEAST 1 hour
  // ini_set('session.gc_maxlifetime', 3600);

  // // each client should remember their session id for EXACTLY 1 hour
  // session_set_cookie_params(3600);
  session_start();
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
  date_default_timezone_set("Asia/Manila"); 
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php  if ($_SESSION['CompanyName']==""){ echo "Dashboard"; } else{ echo $_SESSION['CompanyName']; } ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php if ($_SESSION['CompanyLogo']!=""){ echo $_SESSION['CompanyLogo']; } else{ echo "assets/images/logos/logo-2.png";}?>" type="image/x-icon"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="assets/js/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
    

    <script type="text/javascript" src="assets/js/script-home.js"></script> 
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
    <script type="text/javascript">

      document.onreadystatechange = function() { 
              document.getElementById("LoadingIndexViewer").style.display = "block";
        if (document.readyState !== "complete") { 
          document.getElementById("LoadingIndexViewer").style.display = "block";
        }else { 
          document.getElementById("LoadingIndexViewer").style.display = "none";
        } 
      }; 
    </script>
    <styLe>
      html body{
        font-family: Tahoma !important;
      }
      
      h1 {
        font-size: 26px;
      }

      .table-bordered .darth{
        color: black;
        text-align: center;
        padding: 10px;
        background-color: #faf5f5;
        }
        .table-bordered .col-darth{    
          width: 157px;
        }
        .td-dar{
        text-align: center;
        padding: 4px;
      }
        .table-bordered .td-dar{
            text-align: center;
              padding: 6px;
      }
      .btn-pos-right{
      /* padding-left: 0px; *//* position: absolute; */righ: 0px;position: absolute;right: 28px;top: 4p;/* padding: 12px; */
      padding-top: 6px;
      padding-bottom: 18px;
      }
      .btn-pos-right2{
      /* padding-left: 0px; *//* position: absolute; */righ: 0px;position: absolute;right: 28px;top: 4p;/* padding: 12px; */
      padding-top: 10px;
      padding-bottom: 18px;

      }
      .dtpar label {
        display: inline-block;
        padding: 0px 5px 0px 5px;
      }
      .dtpar input {
        display: inline-block;
        width: auto;
      }
      .title1{
          margin-left: 14px;
      }
      .home-title{
        display: inline-block;
        padding: 7px 40px;
        color: #fff;
        background-color: red;
        letter-spacing: 1px;
      }
      .dtpar{
        display: inline-block;
        float: right;
      }
      .dtpar p{
        display: inline-block;
        font-size: 13px;
      }
      .dtpar label{
        font-size: 13px;;
      }
      .dtpar input{
        font-size: 13px;
        width:149px;
      }
      .table-fixed{
        width: 100%;
      }
      .table-scroll{
        /*width:100%; */
        display: block;
        empty-cells: show;
        
        /* Decoration */
        border-spacing: 0;
        margin-top: 20px;
        border: 1px solid #d6d6d6;
        border-top-left-radius: 10px;
      }

      .table-scroll thead{
        background-color: #f1f1f1;  
        position:relative;
        display: block;
        width:100%;
        overflow-y: scroll;
        border-top-left-radius: 10px;
      }

      .table-scroll tbody{
        /* Position */
        display: block; position:relative;
        width:100%; overflow-y:scroll;
        font-size: 15px;
        /* Decoration */
      }

      .table-scroll tr{
        width: 100%;
        display:flex;
      }
      .dartable th,.dartable td{
          flex-grow:2;
        display: block;
        padding: 10px;
        text-align:center;
        white-space: nowrap;
      }
      .table-scroll .darth,.table-scroll .darth{
        flex-basis:100% !important;
        flex-grow:2;
        display: block;
        padding: 10px;
        text-align:center;
        white-space: nowrap;
      }
      .table-scroll .td-act{
      flex-basis:200%;
      text-align: left;
      }
      /* Other options */

      .table-scroll.small-first-col td:first-child,
      .table-scroll.small-first-col th:first-child{
        flex-basis:60%;
        flex-grow:1;
        white-space: nowrap;
      }

      .table-scroll tbody tr:nth-child(2n){
        border-bottom: 1px solid #ddd;
        border-top: 1px solid #ddd;
      }

      .body-half-screen{
        max-height: 25vh;
      }
      .body-half-screen tr td:last-child{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .small-col{flex-basis:10%;}
      .small-first-col tbody tr{/*
        border-bottom: 1px solid #000;*/
        border: none !important;
      }
      .btnref{
        background-color: transparent;
        border: none;
        color: green;

      }
      .btnref:hover{
          background-color: transparent;
        border: none;
        color: green;
      }
      .btnref i{
          font-size: 20px;
      }
      .btnref2{
        background-color: transparent;
        border: none;
        color: green;

      }
      .btnref2:hover{
          background-color: transparent;
        border: none;
        color: green;
      }
      .btnref2 i{
          font-size: 20px;
      }
      .LoginWarning .modal-body{
        padding: 0px;
      }
      .lg-buttons a{
        width: 50%;
        cursor: pointer;
        border-radius: 0px;
        color: #fff !important;
        font-size: 25px;
      }
      .lg-buttons{
        display: flex;
      }
      .lg-question{
        text-align: center;
        padding: 10px;
        padding-bottom: 50px;
      }
      .loadingarea img{
        width: 70px;
      }
      .loadingarea h2{
        display: inline-block;
      }
      #adddar::-webkit-scrollbar {
      width: 12px;
      background-color: #F5F5F5; }
      #adddar::-webkit-scrollbar-thumb {
      border-radius: 10px;
      -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
      <?php echo "background-color: " . $_SESSION['CompanyColor']; ?> }
      #addlilo::-webkit-scrollbar {
      width: 12px;
      background-color: #F5F5F5; }
      #addlilo::-webkit-scrollbar-thumb {
      border-radius: 10px;
      -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
      <?php echo "background-color: " . $_SESSION['CompanyColor']; ?>; }
      @media screen and (max-width: 768px){
        .outer-div-tblattendance .table-scroll{
          display: table;
        }
        .outer-div-tblattendance{
          overflow: auto;
        }
        .outer-div-tblattendance .table-scroll tbody{
          overflow: unset;
        }
        .outer-div-tblattendance .table-scroll thead{
          overflow: unset;
        }  
        .outer-div-tblattendance::-webkit-scrollbar {
          width: 12px;
          background-color: #F5F5F5; 
        }
        .outer-div-tblattendance::-webkit-scrollbar-thumb {
          border-radius: 10px;
          -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
          background-color: red; 
        }

        

      }
    </style>
  </head>
 
  <body style="background-image: none">
    <?php 
      include 'includes/header.php';
      ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-3 col-md-12"></div>
        <!-- website content -->
        <div class="col-lg-9 col-md-12 wd-login">

          <h1 style="font-family: 'Tahoma'">Shift Monitor</h1>

          <div class="row">
            <div class="home-container">
              <button type="button" data-toggle="modal" id="v_session" data-target="#newformd" class="btn home-title btn-success" style="box-shadow: 2px 3px 10px #534f4f;padding:7px 40px !important;<?php echo "background-color: " . $_SESSION['CompanyColor']; ?>"><i class="fa fa-plus-circle" aria-hidden="true"></i> UPDATE DAR </button>   
              <button class="btn btnref "  type="button"> <i class="fa fa-refresh" aria-hidden="true"></i> </button>
                <div class="dtpar hm-dtpar">
                    <label>Date Parameters From:</label>
                      <input type="date" id="dpfrom" value="<?php echo date('Y-m-d', strtotime('-7 days'));?>" class="form-control">
                    <label>To:</label>
                      <input type="date" id="dpto" value="<?php echo date('Y-m-d');?>" class="form-control">
                </div>

                <div class="container-format " style="height: 230px;">
                  <table class="table-scroll table dartable">
                    <thead>
                      <tr style="<?php echo "color:#000;background-color: #ebebeb";
                        ?>">
                        <th class="col-darth" width="40%">Date</th>
                        <th class="col-darth res-day"  width="40%">Day</th>
                        <th class="col-darth" width="40%">Time</th>
                        <th class="td-act" width="50%">Activity</th>
                      </tr>
                    </thead>
                    <tbody class="body-half-screen w-auto" id="adddar">         
                      <?php
                      try{
                          include 'w_conn.php';
                          $pdo = new PDO("mysql:host=$servername;dbname=$db", $username,$password);
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        }catch(PDOException $e){
                          die("ERROR: Could not connect. " . $e->getMessage());
                        }
                          $id=$_SESSION['id'];
                          $dt1=date('Y-m-d', strtotime('-7 days'));
                          $dt2=date('Y-m-d', strtotime('+1 days'));
                          $statement = $pdo->prepare("select * from dars where EmpID = :name and DarDateTime between :dt1 and :dt2  order by DarDateTime desc");
                          $statement->bindParam(':name' , $id);
                          $statement->bindParam(':dt1' , $dt1);
                          $statement->bindParam(':dt2' , $dt2);
                          $statement->execute();

                          while ($row = $statement->fetch()){
                          ?>
                            <tr>
                                <td class="td-dar" width="40%"><?php echo date("F j, Y", strtotime($row['DarDateTime'])); ?></td>
                                <td class="td-dar res-day" width="40%"><?php echo date("l", strtotime($row['DarDateTime'])); ?></td>
                                <td class="td-dar" width="40%"><?php echo date("h:i:s A", strtotime($row['DarDateTime'])); ?></td>            
                                <td class="td-act" width="50%"><?php echo $row['EmpActivity']; ?></td>
                            </tr>
                          <?php
                          }
                          ?>
                    </tbody>
                  </table>
                </div>
            </div>   
          </div> <br><br>

          <div class="home-container att-lilo">   
              <p style="font-family: 'Tahoma'; display: inline-block; font-size: 26px; margin: 0px;">Attendance Log</p>      
              <button class="btn btnref2"  type="button">
                <!-- <img src="assets/images/refreshicon.png" data-toggle="tooltip" data-placement="right" title="Refresh" width="25px"> -->
                <i class="fa fa-refresh" aria-hidden="true"></i>
              </button>         
              <div class="outer-div-tblattendance">
                <table class="table-scroll small-first-col">
                  <thead>
                    <tr style="<?php echo "color:#000;background-color: #ebebeb"; ?>">
                      <th class="darth">Date</th>
                      <th class="darth">Day</th>
                      <th class="darth">Schedule</th>
                      <th class="darth">Time In</th>
                      <th class="darth">Time Out</th>
                      <th class="darth">Type/Status</th>
                      <th class="darth">Duration</th>
                    </tr>
                  </thead>  
                  <tbody class="body-half-screen" id="addlilo">
                    <?php 
                        $dt1=date('Y-m-d', strtotime('-7 days'));
                        $dt2=date('Y-m-d');
                        include 'includes/home-attendancelog.php';
                    ?>
                  </tbody> 
                </table>
              </div>
              <div class="btn-pos-right">
                <button  class="btn btn-success lilosave" id="v_logins" style="padding: 10px 20px !important;box-shadow: 2px 3px 10px #534f4f;font-size: 15px !important;" data-toggle="modal" data-target="#LoginWarning"> 
                <label style="height: 3px;background-color: #fff;width: 50px;position: absolute;left: 100px;margin-top: 24px;"></label></button>           
              </div>
          </div>

        </div>
      </div>
    

    <!-- The Modal -->
    <div class="modal" id="LoginWarning">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
          <!-- Modal Header --> 
          <div class="modal-header" style="padding: 7px 8px;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          
          <!-- Modal body -->
          <div class="modal-body">
            <div class="dv-q">
                <div class="lg-question">
                    <h5 style="font-size: 20px;">You are about to login. Continue?</h5>
                </div>
                <div class="lg-buttons">
                  <a class="btn btn-success btn-login" id="lgyes">Yes</a>
                  <a class="btn btn-danger btn-cancel" id="lgno" data-dismiss="modal">No</a>
                </div>
                <h3 class="loadd" style="display: none;">Loading ...</h3>
            </div>
          </div>
          
          <!-- Modal footer -->
        
          
        </div>
      </div>
    </div>
    <!-- modal end --> 

    <!-- The Modal for Under Time -->
    <div class="modal" id="LoginEOUnder">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
          <!-- Modal Header --> 
          <div class="modal-header" style="padding: 7px 8px;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          
          <!-- Modal body -->
          <div class="modal-body">
            <div class="dv-q">
                <div class="lg-question">
                    <h3 style="color: red;">You are about to login. asdasdasd?</h3>
                    <h4><i class="fa fa-exclamation-triangle" style="color:#e3c80b;" aria-hidden="true"></i></h4>
                </div>
                <div class="lg-buttons">
                  <a class="btn btn-success" id="lgyesf">Confirm</a>
                  <a class="btn btn-danger btn-cancel" id="lgnof" data-dismiss="modal">No</a>
                </div>
                <h3 class="loadd" style="display: none;">Loading ...</h3>
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
            <div class="alert alert-success">
        
        </div>
          </div>
          
          <!-- Modal footer -->
        
          
        </div>
      </div>
    </div>
    <!-- modal end -->  

        <!-- The Modal --> 
    <div class="modal" id="LoadingIndexViewer">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
          <!-- Modal Header --> 
          <div class="modal-header" style="padding: 7px 8px;">
          </div>
          
          <!-- Modal body -->
          <div class="modal-body">
            <div class="loadingarea">
                <h2>Loading</h2>
                <img src="assets/images/load.gif">
                
            </div>
          </div>
          
          <!-- Modal footer -->
        
          
        </div>
      </div>
    </div>
    <!-- modal end -->  

    <div class="modal" id="newformd">
      <div class="modal-dialog">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header" style="padding: 0px;background-color: red;color:#fff;">
            <button type="button" class="close" data-dismiss="modal" style="color: #fff;opacity: 1;font-size: 30px !important;">&times;</button>
            <h4 class="modal-title"  style="padding: 10px;color:;">Activity Logger</h4>
          </div>

          <!-- Modal body -->
          <div class="modal-body ob-body">
            <form id="gdata" action="">                  
                  <div class="form-group darshow" style="display: block;">
                    <textarea id="daract" name="daract" class="form-control" placeholder="Input Activity Here..."></textarea>
                  </div>
                      <button type="button" id="obsave" class="btn btn-success ">UPDATE DAR</button>
                  </form>
          </div>

          <!-- Modal footer -->

        </div>
      </div>
    </div>

  </div>
</body> 
</html>