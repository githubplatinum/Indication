<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$user = ADMIN_USER;
$password = ADMIN_PASSWORD;
$uniquekey = UNIQUE_KEY;

session_start();

//If cookie is set, skip login
if (isset($_COOKIE["shtrackerrememberme_" . $uniquekey . ""])) {
    $_SESSION["is_logged_in_" . $uniquekey . ""] = true;
}

if (isset($_POST["password"]) && isset($_POST["user"])) {
    if (sha1($_POST["password"]) == $password && $_POST["user"] == $user) {
        $_SESSION["is_logged_in_" . $uniquekey . ""] = true;
            if (isset($_POST["rememberme"])) {
                setcookie("shtrackerrememberme_" . $uniquekey . "", ADMIN_USER, time()+1209600);
            }
    } else {
        header("Location: login.php?login_error=true");
    }
} 

if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker: Login</title>
<meta name="robots" content="noindex, nofollow">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<style type="text/css">
    body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }
    .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
             border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
        -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
             box-shadow: 0 1px 2px rgba(0,0,0,.05);
    }
    .form-signin .form-signin-heading, .form-signin .checkbox {
        margin-bottom: 10px;
    }
    .form-signin input[type="text"], .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
    }
</style>
<link href="../resources/bootstrap/css/bootstrap-responsive.css" type="text/css" rel="stylesheet">
</head>
<body>
<!-- Content start -->
<div class="container">
<form class="form-signin" method="post">
<h2 class="form-signin-heading">SHTracker</h2>
<?php 

if (isset($_GET["login_error"])) {
    echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>Incorrect username or password.</div>";
} elseif (isset($_GET["logged_out"])) {
    echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>Successfully logged out.</div>";
}

?>
<input type="text" class="input-block-level" name="user" placeholder="Username">
<input type="password" class="input-block-level" name="password" placeholder="Password">
<label class="checkbox">
<input type="checkbox" value="rememberme"> Remember Me?
</label>
<button type="submit" class="btn btn-large btn-primary">Sign In</button>
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<!-- Javascript end -->
</body>
</html>
<?php
} else {
    header("Location: index.php");
    exit;
}
?>