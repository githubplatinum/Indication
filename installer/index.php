<?php

// SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

//Security check, check if config exists
if (file_exists("../config.php")) {
    header("Location: ../admin");
    exit;
}

$version = "4.0beta";

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker: Installer</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<style>
    body {
        padding-top: 60px;
    }
    #footer {
        background-color: #f5f5f5;
    }
    @media (max-width: 767px) {
        #footer {
            margin-left: -20px;
            margin-right: -20px;
            padding-left: 20px;
            padding-right: 20px;
        }
    }
</style>
<link href="../resources/bootstrap/css/bootstrap-responsive.css" type="text/css" rel="stylesheet">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<!-- Nav start -->
<div class="navbar navbar-fixed-top">
<div class="navbar-inner">
<div class="container">
<a class="brand" href="#">SHTracker</a>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Installer</h1>
</div>
<?php

//Get path to script
$currenturl = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$pathtoscriptwithslash = "http://" . substr($currenturl, 0, strpos($currenturl, "installer"));
$pathtoscript = rtrim($pathtoscriptwithslash, "/");

?>	
<form action="install.php" method="post">
<h4>Database Settings</h4>
<div class="control-group">
<label class="control-label" for="dbhost">Database Host</label>
<div class="controls">
<input type="text" id="dbhost" name="dbhost" value="localhost" placeholder="Type a database host..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="dbuser">Database User</label>
<div class="controls">
<input type="text" id="dbuser" name="dbuser" placeholder="Type a database user..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="dbpassword">Database Password</label>
<div class="controls">
<input type="password" id="dbpassword" name="dbpassword" placeholder="Type a database password..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="dbname">Database Name</label>
<div class="controls">
<input type="text" id="dbname" name="dbname" placeholder="Type a database name..." required>
</div>
</div>
<h4>Admin Details</h4>
<div class="control-group">
<label class="control-label" for="adminuser">Admin User</label>
<div class="controls">
<input type="text" id="adminuser" name="adminuser" placeholder="Type a username..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="adminpassword">Password</label>
<div class="controls">
<input type="password" id="adminpassword" name="adminpassword" placeholder="Type a password..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="adminpasswordconfirm">Confirm Password</label>
<div class="controls">
<input type="password" id="adminpasswordconfirm" name="adminpasswordconfirm" placeholder="Type your again password..." data-validation-match-match="adminpassword" required>
</div>
</div>
<h4>Other Settings</h4>
<div class="control-group">
<label class="control-label" for="website">Website Name</label>
<div class="controls">
<input type="text" id="website" name="website" required placeholder="Type your websites name...">
</div>
</div>
<div class="control-group">
<label class="control-label" for="pathtoscript">Path to Script</label>
<div class="controls">
<input type="text" id="pathtoscript" name="pathtoscript" value="<? echo $pathtoscript; ?>" placeholder="Type where SHTracker is installed..." required>
</div>
</div>
<div class="form-actions">
<input type="hidden" name="doinstall">
<input type="submit" class="btn btn-primary" value="Install">
</div>
</form>
</div>
<!-- Content end -->
<!-- Footer start -->	
<div id="footer">
<div class="container">
<p class="muted credit">SHTracker <? echo $version; ?> Copyright <a href="http://github.com/joshf" target="_blank">Josh Fradley</a> <? echo date("Y"); ?>. Uses Twitter Bootstrap.</p>
</div>
</div>
<!-- Footer end -->
<!-- Javascript start -->	
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="//raw.github.com/ReactiveRaven/jqBootstrapValidation/1.3.4/jqBootstrapValidation.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(function() { 
        $("input").not("[type=submit]").jqBootstrapValidation();
    });
});
</script>
<!-- Javascript end -->
</body>
</html>