<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html>
<head>
<title>SHTracker: Installer</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="//jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
</head>
<body>
<?php

//Security check
if (file_exists("../config.php")) {
    die("<h1>SHTracker: Error</h1><p>SHTracker has already been installed! If you wish to reinstall SHTracker, please delete config.php from your server and run this script again.</p><hr /><p><a href=\"../admin\">Go To Admin Home</a></p></body></html>"); 
}

//Get path to script
$currenturl = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$pathtoscriptwithslash = "http://" . substr($currenturl, 0, strpos($currenturl, "installer"));
$pathtoscript = rtrim($pathtoscriptwithslash, "/");

?>
<script type="text/javascript">
$(document).ready(function() {
    $("#installform").validate({
        rules: {
            adminpassword: {
                required: true,
            },
            dbhost: {
                required: true,
            },
            dbuser: {
                required: true,
            },
            dbpassword: {
                required: true,
            },
            dbname: {
                required: true,
            },
            adminuser: {
                required: true,
            },
            adminemail: {
                required: true,
                email: true
            },
            adminpassword: {
                required: true,
                minlength: 6
            },
            website: {
                required: true,
            },
            pathtoscript: {
                required: true,
                url: true
            },
        }
    });
});
</script>
<h1>SHTracker: Installer</h1>
<p><i>All fields are required!</i></p>
<form action="install.php" method="post" id="installform" >
<p><b>Database Settings:</b></p>
Host: <input type="text" name="dbhost" value="localhost" /><br />
User: <input type="text" name="dbuser" /><br />
Password: <input type="password" name="dbpassword" /><br />
Name: <input type="text" name="dbname" /><br />
<p><b>Admin Details:</b></p>
User: <input type="text" name="adminuser" /><br />
Email: <input type="text" name="adminemail" /><br />
Password: <input type="password" name="adminpassword" /><br />
<p><b>Other Settings:</b></p>
Website Name: <input type="text" name="website" /><br />
Path to Script: <input type="text" name="pathtoscript" value="<? echo $pathtoscript; ?>" /><br />
<p><input type="submit" value="Install" /></p>
</form>
<small>SHTracker 3.2 Copyright <a href="http://sidhosting.co.uk">Josh Fradley</a> <? echo date("Y"); ?></small>
</body>
</html>