<?php
session_start();
include 'w_conn.php';

// 1. Handle Logout immediately
if (isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    setcookie('WeDoID', '', time() - 3600, '/'); 
    header('location: login.php');
    exit();
}

// 2. If session already exists, redirect to index
if (isset($_SESSION['id']) && $_SESSION['id'] != "0") {
    header('location: index.php');
    exit();
}

if (isset($_COOKIE["WeDoID"])) {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch everything in ONE query using a LEFT JOIN
        $sql = "SELECT e.*, c.CompanyDesc, c.logopath, c.comcolor 
                FROM empdetails e 
                LEFT JOIN companies c ON e.EmpCompID = c.CompanyID";
        
        $stmt = $pdo->query($sql);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Check if this row's EmpID matches the hashed cookie
            if (password_verify($row['EmpID'], $_COOKIE["WeDoID"])) {
                
                // Set all session variables at once
                $_SESSION['id']          = $row['EmpID'];
                $_SESSION['UserType']    = $row['EmpRoleID'];
                $_SESSION['CompID']      = $row['EmpCompID'];
                $_SESSION['EmpISID']     = $row['EmpISID'];
                $_SESSION['PassHash']    = $row['EmpPW'];
                
                // Handle Company details or Admin defaults
                if (!empty($row['EmpCompID'])) {
                    $_SESSION['CompanyName']  = $row['CompanyDesc'];
                    $_SESSION['CompanyLogo']  = $row['logopath'];
                    $_SESSION['CompanyColor'] = $row['comcolor'];
                } else {
                    $_SESSION['CompanyName']  = "ADMIN";
                    $_SESSION['CompanyLogo']  = "";
                    $_SESSION['CompanyColor'] = "red";
                }

                header('location: index.php');
                exit();
            }
        }
    } catch(PDOException $e) {
        error_log("Connection Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeDo | Dashboard Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="assets/js/logintime.js"></script>
    
    <style>
        :root {
            --primary-color: #dc3545;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin: 0;
            overflow: hidden;
        }

        .login-container {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bn-logo {
            width: 140px;
            margin-bottom: 30px;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.2));
        }

        h4 { font-weight: 600; margin-bottom: 8px; letter-spacing: -0.5px; }
        p.subtitle { color: rgba(255,255,255,0.6); font-size: 0.9rem; margin-bottom: 30px; }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: #fff;
            padding: 12px 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.2);
            color: #fff;
        }

        .captcha-box {
            background: #fff;
            border-radius: 12px;
            padding: 10px;
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }

        #captcha-image { border-radius: 8px; width: 100%; height: auto; }

        .lg-button {
            background: var(--primary-color);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            transition: transform 0.2s, background 0.2s;
            margin-top: 20px;
        }

        .lg-button:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .tmdate-display {
            position: absolute;
            bottom: 30px;
            right: 40px;
            text-align: right;
            opacity: 0.5;
        }

        .lg-warning {
            background: rgba(220, 53, 69, 0.9);
            font-size: 0.85rem;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: none;
        }
    </style>
</head>
<body onload="startTime()">

    <div class="login-container text-center">
        <img src="assets/images/logos/wedo-logo.png" class="bn-logo" alt="Logo">
        
        <div class="lg-form text-left">
            <h6 class="lg-warning" id="error-msg">Incorrect credentials</h6>
            <h4>Dashboard Login</h4>
            <p class="subtitle">Secure access for WeDo BPO Personnel</p>

            <form  class="loginform">
                <div class="form-group">
                    <input type="text" class="form-control" name="uname" id="uname" placeholder="Username">
                </div>
                
                <div class="form-group position-relative">
                    <input type="password" id="pass" class="form-control" name="pass" placeholder="Password">
                    <small class="form-text mt-2">
                        <input type="checkbox" onclick="myFunction()" id="showPass"> 
                        <label for="showPass" class="text-white-50 ml-1" style="cursor:pointer">Show Password</label>
                    </small>
                </div>

                <button type="button" class="btn btn-danger btn-block lg-button btnsubmit">Sign In</button>
            </form>
        </div>
        
        <p class="mt-4 text-white-50" style="font-size: 0.75rem;">Powered by WeDo BPO Inc. &copy; 2026</p>
    </div>

    <div class="tmdate-display d-none d-lg-block">
        <h5 id="dtnow">-- --, ----</h5>
        <h2 id="hr-mn" class="m-0">00:00</h2>
        <span id="sec">00</span>
    </div>

    </body>
</html>