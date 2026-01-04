<?php
		include 'w_conn.php';
      session_start();
      if(isset($_COOKIE["WeDoID"])) {
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
  if (isset($_SESSION['id']) && $_SESSION['id']!="0"){
      
       header ('location: index.php');
  }
  else{ 
    

  }
    if(isset($_GET['logout'])){
      session_start();
      $_SESSION['id']="0";
      session_destroy();
      unset($_COOKIE['WeDoID']); 
        setcookie('WeDoID', null, -1, '/'); 
      header ('location: login.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login Page</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	 <link rel="icon" href="assets/images/logos/favicon.ico" type="image/x-icon"> 
  <link rel="shortcut icon" href="assets/images/logos/favicon.ico">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  	<script src='https://www.google.com/recaptcha/api.js'></script>
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  	<link rel="stylesheet" type="text/css" href="assets/css/loginstyle.css">
 	<script  src="assets/js/logintime.js"></script>
  	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>  <!-- capture  -->
    <script type="text/javascript" src="assets/js/github_sha256.js"></script>    <!-- capture  -->


  <!-- Capture start -->
    <script type="text/javascript">
      $(document).ready(function () {

        var dataHash = 0;
        captchaLoad();
        function captchaLoad(){
            fetch("https://captchagenerator.pythonanywhere.com/api", {
                method: "POST"
            })
                .then(resp => resp.json())
                .then(data => {
                    if (data.status === "ok") {
                        dataHash=data.captcha_hash;
                        document.getElementById("captcha-image").setAttribute("src", `data:image/png;base64,${data.image}`)
                        // document.getElementById("captcha-form").addEventListener("submit", e => {
                    }
                })
                .catch(error => {
                    alert("Could not fetch the data, refresh the page!")
            })
        }

        $(document).on('click', '.btnsubmit', function (e) {

          if($("#uname").val()=="" || $("#uname").val()==null  ){
            swal("Message!", "Please fill-up all fields.", "error");
            return false;
          }
          if($("#pass").val()=="" || $("#pass").val()==null  ){
              swal("Message!", "Please fill-up all fields.", "error");
              return false;
          }
            
            captchaTrigger = 0;

            var getSHA256 = function sha256(ascii) {
                function rightRotate(value, amount) {
                    return (value >>> amount) | (value << (32 - amount));
                };

                var mathPow = Math.pow;
                var maxWord = mathPow(2, 32);
                var lengthProperty = 'length'
                var i, j; // Used as a counter across the whole file
                var result = ''

                var words = [];
                var asciiBitLength = ascii[lengthProperty] * 8;

                //* caching results is optional - remove/add slash from front of this line to toggle
                // Initial hash value: first 32 bits of the fractional parts of the square roots of the first 8 primes
                // (we actually calculate the first 64, but extra values are just ignored)
                var hash = sha256.h = sha256.h || [];
                // Round constants: first 32 bits of the fractional parts of the cube roots of the first 64 primes
                var k = sha256.k = sha256.k || [];
                var primeCounter = k[lengthProperty];
                /*/
                var hash = [], k = [];
                var primeCounter = 0;
                //*/

                var isComposite = {};
                for (var candidate = 2; primeCounter < 64; candidate++) {
                    if (!isComposite[candidate]) {
                        for (i = 0; i < 313; i += candidate) {
                            isComposite[i] = candidate;
                        }
                        hash[primeCounter] = (mathPow(candidate, .5) * maxWord) | 0;
                        k[primeCounter++] = (mathPow(candidate, 1 / 3) * maxWord) | 0;
                    }
                }

                ascii += '\x80' // Append Ƈ' bit (plus zero padding)
                while (ascii[lengthProperty] % 64 - 56) ascii += '\x00' // More zero padding
                for (i = 0; i < ascii[lengthProperty]; i++) {
                    j = ascii.charCodeAt(i);
                    if (j >> 8) return; // ASCII check: only accept characters in range 0-255
                    words[i >> 2] |= j << ((3 - i) % 4) * 8;
                }
                words[words[lengthProperty]] = ((asciiBitLength / maxWord) | 0);
                words[words[lengthProperty]] = (asciiBitLength)

                // process each chunk
                for (j = 0; j < words[lengthProperty];) {
                    var w = words.slice(j, j += 16); // The message is expanded into 64 words as part of the iteration
                    var oldHash = hash;
                    // This is now the undefinedworking hash", often labelled as variables a...g
                    // (we have to truncate as well, otherwise extra entries at the end accumulate
                    hash = hash.slice(0, 8);

                    for (i = 0; i < 64; i++) {
                        var i2 = i + j;
                        // Expand the message into 64 words
                        // Used below if 
                        var w15 = w[i - 15], w2 = w[i - 2];

                        // Iterate
                        var a = hash[0], e = hash[4];
                        var temp1 = hash[7]
                            + (rightRotate(e, 6) ^ rightRotate(e, 11) ^ rightRotate(e, 25)) // S1
                            + ((e & hash[5]) ^ ((~e) & hash[6])) // ch
                            + k[i]
                            // Expand the message schedule if needed
                            + (w[i] = (i < 16) ? w[i] : (
                                w[i - 16]
                                + (rightRotate(w15, 7) ^ rightRotate(w15, 18) ^ (w15 >>> 3)) // s0
                                + w[i - 7]
                                + (rightRotate(w2, 17) ^ rightRotate(w2, 19) ^ (w2 >>> 10)) // s1
                            ) | 0
                            );
                        // This is only used once, so could be moved below, but it only saves 4 bytes and makes things unreadble
                        var temp2 = (rightRotate(a, 2) ^ rightRotate(a, 13) ^ rightRotate(a, 22)) // S0
                            + ((a & hash[1]) ^ (a & hash[2]) ^ (hash[1] & hash[2])); // maj

                        hash = [(temp1 + temp2) | 0].concat(hash); // We don't bother trimming off the extra ones, they're harmless as long as we're truncating when we do the slice()
                        hash[4] = (hash[4] + temp1) | 0;
                    }

                    for (i = 0; i < 8; i++) {
                        hash[i] = (hash[i] + oldHash[i]) | 0;
                    }
                }

                for (i = 0; i < 8; i++) {
                    for (j = 3; j + 1; j--) {
                        var b = (hash[i] >> (j * 8)) & 255;
                        result += ((b < 16) ? 0 : '') + b.toString(16);
                    }
                }
                return result;
            };

                const captchaCodeElement = document.getElementById("captcha-code");
                    // getSHA256(captchaCodeElement.value)
                    var ee = getSHA256($("#captcha-code").val())
                    // .then((hashedCaptchaCode) => {
                        // if(captchaCodeElement){
                            // alert(ee + " - " + dataHash);
                            // return false;
                            // if(ee === dataHash){
                                // captchaTrigger=1;
                                // if(captchaTrigger==1){

                                  var uname = $("#uname").val();
                                  var pass = $("#pass").val();
                                  if (uname == "" || pass == "") {
                                    $(".lg-warning").text("Unknown Username or Password !");
                                    $(".lg-warning").css("display", "block");
                                    return false;
                                  } else {
                                    var data = $(".loginform").serialize();
                                    $.ajax({
                                      url: "query/query-login.php",
                                      data: data,
                                      type: "POST",

                                      success: function (data) {
                                        if (data == 0) {
                                          $(".lg-warning").text("Incorrect Username !");
                                          $(".lg-warning").css("display", "block");
                                        } else if (data == 1) {
                                          $(".lg-warning").text("Incorrect Password !");
                                          $(".lg-warning").css("display", "block");
                                        } else if (data == 7) {
                                          location.replace("http://dashboard.wedoinc.ph/questionnaire.php");
                                      
                                        } else {
                                          swal("Message!", "Challenge Verification Successful", "success");
                                          location.reload(true);
                                        }
                                      },
                                    });
                                  }
                                  
                                // }
                            // }else{
                            //     swal("Message!","Challenge Verification Unsuccessful", "error");
                            //     return;
                            // }
                        // }
                    // }) 
        });

      })
    </script>
    <!--capture end -->

    <style type="text/css">
      .modal-body{
        text-align: center;

      }
      .modal-body img{
      height: 150px; 
      }
      .lg-warning{
        padding: 10px;
        background-color: #dc3545;
        color: #fff;
        border-radius:  10px;
        display: none;
      }

  	</style>
    <script type="text/javascript">
      function myFunction() {
        var x = document.getElementById("pass");
        if (x.type === "password") {
          x.type = "text";
        } else {
          x.type = "password";
        }
      }
    </script>
</head>
<body onload="startTime()">
	<!-- content -->
	<div class="container main-login">
		<div class="row">
			<div class="col-lg-4 l-side-login">
				<img src="assets/images/logos/wedo-logo.png" class="bn-logo">
				<div class="lg-form">
					 <h6 class="lg-warning">Username is incorrect ! </h6>
					<h4>Welcome to</h4> <h4>WeDo Dashboard Login</h4>
					<hr style="border-top: 1px solid transparent;">
					<form  method="post" class="loginform">
  						<div class="form-group">
      						<input type="text" class="form-control" name="uname" placeholder="Username" id="uname" >
  						</div>
  						<div class="form-group">
      						<input type="password" id="pass" class="form-control" name="pass" placeholder="Password" id="pass">
  						</div>
              <div class="form-group">
                 <input type="checkbox" onclick="myFunction()"><label style="color:#fff;">Show Password</label>
              </div>

              <!-- capture start -->
                <div class="class">
                      <img src="" alt="captcha image" id="captcha-image" border="1">
                </div>
                  
                <p>
                  <label for="captcha-code px-2 " style="color:white; padding-top:15px"> Enter letters displayed in above image
                    <br>
                    <span class="wpcf7-form-control-wrap your-subject">
                        <input
                            type="text"
                            name="captcha-code"
                            value=""
                            id="captcha-code"
  
                            class="form-control"
              
                        >
                    </span>
                  </label>
                </p>
              <!-- capture end  -->
                
  						<!-- <div class="g-recaptcha" data-sitekey="6LcxV-kUAAAAAG23FJoXF1UYmy7OedMtc-GGXWUN"></div> -->
  						<button type="button" class="btn btn-danger btn-block lg-button btnsubmit">Login</button>
  				</form>
				
				</div>
				<label class="wtrmrk">Powered by WeDo BPO</label>
			</div>
			<div class="col-lg-8 banner-login">
				<div class="tmdate-display">
					<h5 id="dtnow">OCT 2, 2020</h5>
					<h1 id="hr-mn">00:00</h1><h6 id="sec">:00 AM</h6>
				</div>
			</div>
		</div>
	</div>
</body>
</html>