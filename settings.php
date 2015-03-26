<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    header("Location: install");
    exit;
}

require_once("config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit;
} 

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

$getusersettings = mysqli_query($con, "SELECT `user`, `password`, `email`, `salt`, `api_key` FROM `Users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysqli_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysqli_fetch_assoc($getusersettings);

//Get current settings
$currentwebsite = WEBSITE;
$currentpathtoscript = PATH_TO_SCRIPT;
$currentadcode = htmlspecialchars_decode(AD_CODE);
$currentcountuniqueonlystate = COUNT_UNIQUE_ONLY_STATE;
$currentcountuniqueonlytime = COUNT_UNIQUE_ONLY_TIME;
$currentignoreadminstate = IGNORE_ADMIN_STATE;
$currentcustomurlstate = CUSTOM_URL_STATE;
$currentcustomurl = CUSTOM_URL; 


if (!empty($_POST)) {
    //Get new settings from POST
    $user = $_POST["user"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $salt = $resultgetusersettings["salt"];
    if ($password != $resultgetusersettings["password"]) {
        //Salt and hash passwords
        $randsalt = md5(uniqid(rand(), true));
        $salt = substr($randsalt, 0, 3);
        $hashedpassword = hash("sha256", $password);
        $password = hash("sha256", $salt . $hashedpassword);
    }
    $website = $_POST["website"];
    $pathtoscript = rtrim($_POST["pathtoscript"], "/");	
    
    if (isset($_POST["advertcode"])) {
        if (get_magic_quotes_gpc()) {
            $adcode = stripslashes(htmlspecialchars($_POST["advertcode"]));
        } else {
            $adcode = htmlspecialchars($_POST["advertcode"]);
        }
    }
    $countuniqueonlystate = $_POST["countuniqueonlystate"];
    $countuniqueonlytime = $_POST["countuniqueonlytime"];
    $ignoreadminstate = $_POST["ignoreadminstate"];
    $customurlstate = $_POST["customurlstate"];
    $customurl = $_POST["customurl"];

    //Remember previous settings
    if (empty($adcode)) {
        $adcode = $currentadcode;
    }
    if (empty($countuniqueonlytime)) {
        $adcode = $currentcountuniqueonlytime;
    }
    if (empty($customurl)) {
        $customurl = $currentcustomurl;
    }

    $settingsstring = "<?php\n\n//Database Settings\ndefine('DB_HOST', '" . DB_HOST . "');\ndefine('DB_USER', '" . DB_USER . "');\ndefine('DB_PASSWORD', '" . DB_PASSWORD . "');\ndefine('DB_NAME', '" . DB_NAME . "');\n\n//Other Settings\ndefine('SALT', '" . SALT . "');\ndefine('WEBSITE', " . var_export($website, true) . ");\ndefine('PATH_TO_SCRIPT', " . var_export($pathtoscript, true) . ");\ndefine('AD_CODE', " . var_export($adcode, true) . ");\ndefine('COUNT_UNIQUE_ONLY_STATE', " . var_export($countuniqueonlystate, true) . ");\ndefine('COUNT_UNIQUE_ONLY_TIME', " . var_export($countuniqueonlytime, true) . ");\ndefine('IGNORE_ADMIN_STATE', " . var_export($ignoreadminstate, true) . ");\ndefine('CUSTOM_URL_STATE', " . var_export($customurlstate, true) . ");\ndefine('CUSTOM_URL', " . var_export($customurl, true) . ");\ndefine('VERSION', '" . VERSION . "');\n\n?>";

    //Update Settings
    mysqli_query($con, "UPDATE Users SET `user` = \"$user\", `password` = \"$password\", `email` = \"$email\", `salt` = \"$salt\" WHERE `user` = \"" . $resultgetusersettings["user"] . "\"");
    
    //Write config
    $configfile = fopen("config.php", "w");
    fwrite($configfile, $settingsstring);
    fclose($configfile);

    //Show updated values
    header("Location: settings.php");
    exit;
}

mysqli_close($con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Indication &middot; Settings</title>
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/bootstrap-notify/css/bootstrap-notify.min.css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 30px;
    padding-bottom: 30px;
}
/* Fix weird notification appearance */
a.close.pull-right {
    padding-left: 10px;
}
</style>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="index.php">Indication</a>
</div>
<div class="navbar-collapse collapse" id="navbar-collapse">
<ul class="nav navbar-nav navbar-right">
<li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $resultgetusersettings["user"]; ?> <span class="caret"></span></a>
<ul class="dropdown-menu" role="menu">
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</li>
</ul>
</div>
</div>
</nav>
<div class="container">
<div class="page-header">
<h1>Settings</h1>
</div>
<div class="notifications top-right"></div>
<form role="form" method="post" autocomplete="off">
<h4>User Details</h4>
<div class="form-group">
<label class="control-label" for="user">User</label>
<input type="text" class="form-control" id="user" name="user" value="<?php echo $resultgetusersettings["user"]; ?>" placeholder="Enter a username..." required>
</div>
<div class="form-group">
<label class="control-label" for="email">Email</label>
<input type="email" class="form-control" id="email" name="email" value="<?php echo $resultgetusersettings["email"]; ?>" placeholder="Type an email..." required>
</div>
<div class="form-group">
<label class="control-label" for="password">Password</label>
<input type="password" class="form-control" id="password" name="password" value="<?php echo $resultgetusersettings["password"]; ?>" placeholder="Enter a password..." required>
</div>
<h4>Site Settings</h4>
<div class="form-group">
<label for="website">Website</label>
<input type="text" class="form-control" id="website" name="website" value="<?php echo $currentwebsite; ?>" placeholder="Enter your websites name..." required>
</div>
<div class="form-group">
<label for="pathtoscript">Path to Script</label>
<input type="text" class="form-control" id="pathtoscript" name="pathtoscript" value="<?php echo $currentpathtoscript; ?>" placeholder="Type the path to Indication..." pattern="(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-?]*)*\/?" data-validation-pattern-message="Please enter a valid URL" required>
</div>
<h4>Ad Code</h4>
<p>Show an advert before user can continue to their link. This can be changed on a per link basis.</p>
<div class="alert alert-warning">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<b>Warning:</b> On some server configurations using HTML code here may produce errors.</div>
<div class="form-group">
<textarea class="form-control" id="advertcode" name="advertcode" placeholder="Enter a ad code..."><?php echo $currentadcode; ?></textarea>
</div>
<h4>Count Unique Visitors Only</h4>
<p>This settings allows you to make sure an individual user's clicks are only counted once.</p>
<div class="radio">
<?php
if ($currentcountuniqueonlystate == "Enabled" ) {
    echo "<label><input type=\"radio\" id=\"countuniqueonlystateenable\" name=\"countuniqueonlystate\" value=\"Enabled\" checked=\"checked\"> Enabled</label></div>
        <div class=\"radio\"><label><input type=\"radio\" id=\"countuniqueonlystatedisable\" name=\"countuniqueonlystate\" value=\"Disabled\"> Disabled</label>";    
} else {
    echo "<label><input type=\"radio\" id=\"countuniqueonlystateenable\" name=\"countuniqueonlystate\" value=\"Enabled\"> Enabled</label></div>
     <div class=\"radio\"><label><input type=\"radio\" id=\"countuniqueonlystatedisable\" name=\"countuniqueonlystate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
</div>
<?php
if ($currentcountuniqueonlystate == "Enabled" ) {
?>
<div class="form-group">
<label for="countuniqueonlytime">Time to consider a user unique (hours)</label>
<input type="number" class="form-control" id="countuniqueonlytime" name="countuniqueonlytime" value="<?php echo $currentcountuniqueonlytime; ?>" placeholder="Enter a time..." required>
</div>
<?php
}
?>
<h4>Ignore Admin</h4>
<p>This settings prevents links being counted when you are logged in to Indication.</p>
<div class="radio">
<?php
if ($currentignoreadminstate == "Enabled" ) {
    echo "<label><input type=\"radio\" id=\"ignoreadminstateenable\" name=\"ignoreadminstate\" value=\"Enabled\" checked=\"checked\"> Enabled</label></div>
    <div class=\"radio\"><label><input type=\"radio\" id=\"ignoreadminstatedisable\" name=\"ignoreadminstate\" value=\"Disabled\"> Disabled</label>";    
} else {
    echo "<label><input type=\"radio\" id=\"ignoreadminstateenable\" name=\"ignoreadminstate\" value=\"Enabled\"> Enabled</label></div>
    <div class=\"radio\"><label><input type=\"radio\" id=\"ignoreadminstatedisable\" name=\"ignoreadminstate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
</div>
<h4>Custom URL</h4>
<p>Allows you to use a custom get url (for clean urls).</p>
<div class="radio">
<?php
if ($currentcustomurlstate == "Enabled" ) {
    echo "<label><input type=\"radio\" id=\"customurlstateenable\" name=\"customurlstate\" value=\"Enabled\" checked=\"checked\"> Enabled</label></div>
        <div class=\"radio\"><label><input type=\"radio\" id=\"customurlstatedisable\" name=\"customurlstate\" value=\"Disabled\"> Disabled</label>";    
} else {
    echo "<label><input type=\"radio\" id=\"customurlstateenable\" name=\"customurlstate\" value=\"Enabled\"> Enabled</label></div>
     <div class=\"radio\"><label><input type=\"radio\" id=\"customurlstatedisable\" name=\"customurlstate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
</div>
<?php
if ($currentcustomurlstate == "Enabled" ) {
?>
<div class="form-group">
<label for="customurl">Custom URL</label>
<input type="text" class="form-control" id="customurl" name="customurl" value="<?php echo $currentcustomurl; ?>" placeholder="Enter a custom URL..." required>
</div>
<?php
}
?>
<button type="submit" class="btn btn-default">Save</button>
</form>
<br>
<h5>API key</h5>
<p>Your API key is: <div id="api_key"><b><?php echo $resultgetusersettings["api_key"]; ?></b></div></p>
<button id="generateapikey" class="btn btn-default">Generate New Key</button>
</div>
<script src="assets/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/bootstrap-notify/js/bootstrap-notify.min.js"></script>
<script src="assets/jquery.cookie.min.js"></script>
<script src="assets/nod.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    if ($.cookie("settings_updated")) {
        $(".top-right").notify({
            type: "info",
            transition: "fade",
            icon: "info-sign",
            message: {
                text: "Settings saved!"
            }
        }).show();
        $.removeCookie("settings_updated");
    }
    $("form").submit(function() {
        $.cookie("settings_updated", "true");
    });
    var metrics = [
        ["#user", "presence", "User name cannot be empty!"],
        ["#email", "email", "Enter a valid email address"],
        ["#password", "presence", "Passwords should be more than 6 characters"],
        ["#website", "presence", "Website cannot be empty!"],
        ["#pathtoscript", "presence", "Path to script cannot be empty!"],
        ["#countuniqueonlytime", "min-num:1", "Time must be higer than 1"]
    ];
    $("form").nod(metrics);
    $("#generateapikey").click(function() {
        $.ajax({
            type: "POST",
            url: "worker.php",
            data: "action=generateapikey",
            error: function() {
                $("#api_key").html("<b>Could not generate key. Failed to connect to worker.</b>");
            },
            success: function(api_key) {
                $("#api_key").html("<b>"  + api_key +  "</b>");
            }
        });
    });
});
</script>
</body>
</html>