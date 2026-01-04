<?php
		include 'w_conn.php';
      session_start();
      if (isset($_SESSION['id']) && $_SESSION['id']!="0"){
        header ('location: index');
      }

    if(isset($_GET['logout'])){
      session_start();
      $_SESSION['id']="0";
      session_destroy();
      header ('location: login');
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
   <link rel="icon" href="assets/images/logos/WeDo.png" type="image/x-icon"> 
	<link rel="stylesheet" href="assets/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
<!-- 	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

 -->
 <script src="assets/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

 <script src="assets/js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

  <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
	<title>Login Form</title>
  <script type="text/javascript">
      function fnct(){
            window.location.href = 'index.php';
          }
    $(document).ready(function(){
        
          $(".btnsubmit").click(function(){
            var uname = $("#uname").val();
            var pass = $("#pass").val();
            if (uname=="" || pass==""){
               $(".lg-warning").text("Unknown Username or Password !");
              $(".lg-warning").css("display", "block");
              return false;
            }
            else{
                var data=$(".loginform").serialize();
                        $.ajax({

                          url:'query/query-login.php', 
                          data:data,
                          type:'POST',
                                                               
                           success:function(data){
                              // if (data=="false"){
                              //   alert("IC");
                              // }
                              // alert(data);
                              if(data==0101)
                              {
                                    $(".lg-warning").text("Access restricted for Resigned Employee/s !");
                                    $(".lg-warning").css("display", "block");
                               
                                  return false;
                              }
                              
                              if (data==0){
                                $(".lg-warning").text("Incorrect Username !");
                                $(".lg-warning").css("display", "block");
                               
                              }
                              else if (data==1){
                                     $(".lg-warning").text("Incorrect Password !");
                                $(".lg-warning").css("display", "block");
                              }
                              else{
                                location.reload(true);
                              }
                           }
                        });
            }
          });
          $('#pass').keypress(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                var uname = $("#uname").val();
            var pass = $("#pass").val();
            if (uname=="" || pass==""){
                $(".lg-warning").text("Unknown Username or Password !");
              $(".lg-warning").css("display", "block");
              return false;
            }
            else{
                var data=$(".loginform").serialize();
                        $.ajax({

                          url:'query/query-login.php', 
                          data:data,
                          type:'POST',
                                                               
                           success:function(data){
                              // if (data=="false"){
                              //   alert("IC");
                              // }
                              // alert(data);
                              if (data==0){
                                $(".lg-warning").text("Incorrect Username !");
                                $(".lg-warning").css("display", "block");
                               
                              }
                              else if (data==1){
                                     $(".lg-warning").text("Incorrect Password !");
                                $(".lg-warning").css("display", "block");
                              }
                              else{
                                location.reload(true);
                              }
                           }
                        });
            }
            }
        });

           $('#uname').keypress(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                var uname = $("#uname").val();
            var pass = $("#pass").val();
            if (uname=="" || pass==""){
                $(".lg-warning").text("Unknown Username or Password !");
              $(".lg-warning").css("display", "block");
              return false;
            }
            else{
                var data=$(".loginform").serialize();
                        $.ajax({

                          url:'query/query-login.php', 
                          data:data,
                          type:'POST',
                                                               
                           success:function(data){
                              if (data==0){
                                  $(".lg-warning").text("Incorrect Username !");
                                  $(".lg-warning").css("display", "block");
                              }
                              else if (data==1){
                                  $(".lg-warning").text("Incorrect Password !");
                                  $(".lg-warning").css("display", "block");
                              }
                              else{
                                 location.reload(true);
                              }
                           }
                        });
            }
            }
        });

          $(".btn-success").click(function(){
               
          });
    });
  </script>
  <style type="text/css">
    .modal-body{
      text-align: center;

    }
    .modal-body img{
     height: 150px; 
    }
    .lg-warning{
      padding: 10px;
      background-color: #ff0000;
      color: #fff;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      display: none;
    }

  </style>
</head>
<body>
	<div class="container lg-wrp">
		<div class="row">
  <div class="col-lg-3">
  </div>
    <div class="col-sm-12 col-md-8 col-lg-6 lg-form-1">
      <div>
        <h6 class="lg-warning">Username is incorrect ! </h6>
      </div>
      <div class="lg-form">
      	  	<h5 class="wc">Welcome to your</h5>
      	   	<h1>Corporate Dashboard</h1>
      	   	<!-- <h6 class="lgin">Log in</h6> -->
      		<form  method="post" class="loginform">
      			<div class="form-group">
				    <input type="text" class="form-control" name="uname" placeholder="Username" id="uname" >
				</div>
				<div class="form-group">
				    <input type="password" class="form-control" name="pass" placeholder="Password" id="pass">
				</div>
				<button type="button" class="btn btn-block btnsubmit">LOGIN</button>
      		</form>
      	<!-- 	<h6>Don't have an account? <a href="#" class="link-su">Sign Up Now</a></h6>
      		<br>
      		<h6>Or</h6>
      		<br>
      		<h6>Continue with social media</h6> -->
      <!-- 		<div class="sc-media">
      			<a href="" class="fb"><i class="fab fa-facebook-f"></i></a>
      			<a href="" class="tw"><i class="fab fa-twitter"></i></a>
      			<a href="" class="gp"><i class="fab fa-google-plus-g"></i></a>
      			<a href="" class="li"><i class="fab fa-linkedin-in"></i></a>
      		</div> -->
      </div>
    </div>
     <div class="col-lg-3">
  </div>
  <!--   <div class="col-sm-12 col-md-4 col-lg-6 lg-form-2">
    	<div>
    		<h1><img src="assets/images/wedologo.png"></h1>
	        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	        consequat.</p>
    	</div>
        
    </div> -->
  	</div>
	</div>
   <div class="modal" id="newform">
            <div class="modal-dialog">
              <div class="modal-content">

                <!-- Modal Header -->
               

                <!-- Modal footer -->
               

              </div>
            </div>
          </div>
</body>
</html>