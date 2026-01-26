<?php
    session_start();
    include 'w_conn.php';

    // 1. Handle Logout immediately
    if (isset($_GET['logout'])) {
    $_SESSION = [];
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
                $_SESSION['id']       = $row['EmpID'];
                $_SESSION['UserType'] = $row['EmpRoleID'];
                $_SESSION['CompID']   = $row['EmpCompID'];
                $_SESSION['EmpISID']  = $row['EmpISID'];
                $_SESSION['PassHash'] = $row['EmpPW'];

                // Handle Company details or Admin defaults
                if (! empty($row['EmpCompID'])) {
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
    } catch (PDOException $e) {
        error_log("Connection Error: " . $e->getMessage());
    }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeDo | Unified Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="assets/images/logos/wedo-favicon.png">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="assets/js/logintime.js"></script>

    <style>
        :root {
            --primary-red: #ff4d4d;
            --accent-glow: rgba(255, 77, 77, 0.3);
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
        }

        /* Animated Mesh Gradient Background */
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            background-image:
                radial-gradient(at 0% 0%, rgba(220, 53, 69, 0.15) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(22, 33, 62, 1) 0, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin: 0;
            overflow: hidden;
        }

        /* Glassmorphism Container with Micro-interaction */
        .login-container {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            padding: 50px 40px;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
            border-color: rgba(255, 255, 255, 0.25);
        }

        .bn-logo {
            width: 120px;
            margin-bottom: 35px;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.1));
        }

        h4 { font-weight: 600; font-size: 1.5rem; letter-spacing: -1px; }
        .subtitle { color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-bottom: 35px; }

        /* Floating Input Style */
        .form-control {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            color: #fff !important;
            padding: 25px 18px;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control::placeholder { color: rgba(255, 255, 255, 0.3); }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.4);
            border-color: var(--primary-red);
            box-shadow: 0 0 15px var(--accent-glow);
            transform: scale(1.02);
        }

        .btnsubmit {
            background: var(--primary-red);
            border: none;
            border-radius: 14px;
            padding: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .btnsubmit:hover {
            background: #ff3333;
            box-shadow: 0 15px 30px rgba(220, 53, 69, 0.5);
            transform: translateY(-2px);
        }

        /* Clock Styling */
        .tmdate-display {
            position: absolute;
            bottom: 40px;
            right: 50px;
            text-align: right;
            border-left: 2px solid var(--primary-red);
            padding-left: 20px;
        }

        #hr-mn { font-size: 3rem; font-weight: 600; line-height: 1; margin: 0; }
        #dtnow { font-size: 0.9rem; color: var(--primary-red); text-transform: uppercase; letter-spacing: 2px; }
        #sec { font-size: 1rem; opacity: 0.6; }

        .lg-warning {
            background: #dc3545;
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-bottom: 20px;
            display: none;
            animation: shake 0.4s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body onload="startTime()">

    <div class="login-container text-center">
        <img src="assets/images/logos/wedo-logo.png" class="bn-logo" alt="Logo">

        <div class="lg-form text-left">
            <h6 class="lg-warning" id="error-msg">Incorrect credentials</h6>
            <h4>Sign In</h4>
             <p class="subtitle">Secure access for WeDo BPO Personnel</p>

            <form class="loginform">
                <div class="form-group">
                    <input type="text" class="form-control" name="uname" id="uname" placeholder="Username" autocomplete="off">
                </div>

                <div class="form-group mb-4">
                    <input type="password" id="pass" class="form-control" name="pass" placeholder="Password">
                    <div class="custom-control custom-checkbox mt-3">
                        <input type="checkbox" class="custom-control-input" id="showPass" onclick="togglePass()">
                        <label class="custom-control-label text-white-50 small" for="showPass" style="cursor:pointer">Show Password</label>
                    </div>
                </div>

                <button type="button" class="btn btn-danger btn-block btnsubmit">Authenticate</button>
            </form>
        </div>
    </div>

    <div class="tmdate-display d-none d-lg-block">
        <h5 id="dtnow">Loading...</h5>
        <h2 id="hr-mn">00:00</h2>
        <span id="sec">00</span>
    </div>

  
    <script>
        // Real-time Clock Logic
        function startTime() {
            const today = new Date();
            let h = today.getHours();
            let m = today.getMinutes();
            let s = today.getSeconds();
            const ampm = h >= 12 ? 'PM' : 'AM';

            h = h % 12 || 12;
            m = m < 10 ? "0" + m : m;
            s = s < 10 ? "0" + s : s;

            document.getElementById('hr-mn').innerHTML = h + ":" + m;
            document.getElementById('sec').innerHTML = ":" + s + " " + ampm;

            const options = { month: 'short', day: 'numeric', year: 'numeric' };
            document.getElementById('dtnow').innerHTML = today.toLocaleDateString('en-US', options);

            setTimeout(startTime, 1000);
        }

        // Toggle Password
        function togglePass() {
            const x = document.getElementById("pass");
            x.type = x.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
