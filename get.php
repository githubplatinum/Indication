<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

ob_start();

?>
<html>
<head>
<title>SHTracker</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
</head>
<body>
<script type="text/javascript">
$(document).ready(function() {
    var count = 5;
    countdown = setInterval(function(){
        $("#counterplaceholder").html("<p><i>Your download will be ready in " + count + " second(s)</i></p>");
        if (count <= 0) {
            clearInterval(countdown);
            $("#counterplaceholder").fadeOut("fast");
            $("#downloadbutton").delay(300).fadeIn("fast");
        }
        count--;
    }, 1000);
});
</script>
<?php

//Connect to database
require_once("config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("<h1>SHTracker: Error</h1><p>Could not connect to database: " . mysql_error() . ". Check your database settings are correct.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("<h1>SHTracker: Error</h1><p>Could not connect to database: " . mysql_error() . ". Check your database settings are correct.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

mysql_select_db(DB_NAME, $con);

//Get the ID from $_GET OR $_POST
if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} elseif (isset($_POST["id"])) {
    $id = mysql_real_escape_string($_POST["id"]);
} else {
    die("<h1>SHTracker: Error</h1><p>ID cannot be blank.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

//Check if ID exists
$getinfo = mysql_query("SELECT name, url FROM Data WHERE id = \"$id\"");
$getinforesult = mysql_fetch_assoc($getinfo);
if ($getinforesult == 0) {
    die("<h1>SHTracker: Error</h1><p>ID does not exist.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

//Cookies don't like dots
$idclean = str_replace(".", "_", $id);

if (COUNT_UNIQUE_ONLY_STATE == "Enabled") {
    if (!isset($_COOKIE["shtrackerhasdownloaded_$idclean"])) {
        mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
        setcookie("shtrackerhasdownloaded_$idclean", "True", time()+3600*COUNT_UNIQUE_ONLY_TIME);
    }
} else {
    mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
}

//Check if download is password protected
$checkifprotected = mysql_query("SELECT protect, password FROM Data WHERE id = \"$id\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected);
if ($checkifprotectedresult["protect"] == "1") {
    if (isset($_POST["password"])) {
        if (sha1($_POST["password"]) != $checkifprotectedresult["password"]) {
            die("<h1>SHTracker: Error</h1><p>Incorrect password.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
        } else {
            setcookie("shtrackerhasauthed_$idclean", time()+900, time()+900);
        }
    } elseif (isset($_COOKIE["shtrackerhasauthed_$idclean"])) {
        $time = ($_COOKIE["shtrackerhasauthed_$idclean"]-time()) / 60;
        $timeleft = ceil($time);
        echo "<small><b>Notice:</b> your download session wll expire in $timeleft minutes...</small>";
    } else {
        die("<h1>Downloading " . $getinforesult["name"] . "</h1>
        <form method=\"post\">
        <p>To access this download please enter the password you were given.</p>
        <p>Password: <input type=\"password\" name=\"password\" /></p>
        <input type=\"submit\" value=\"Get Download\" /></form>
        <hr />
        <p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p>
        </body>
        </html>");
    }
}

//Check if we should show ads
$checkifadsshow = mysql_query("SELECT showads FROM Data WHERE id = \"$id\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow);
if ($checkifadsshowresult["showads"] == "1") {
    $adcode = htmlspecialchars_decode(AD_CODE);
    die("<h1>Downloading " . $getinforesult["name"] . "</h1><p>" . $adcode . "</p><p><div id=\"counterplaceholder\"></div></p><p><button id=\"downloadbutton\" style=\"display: none\" onClick=\"window.location = '" . $getinforesult["url"] . "'\">Get Download</button></p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

mysql_close($con);

//Redirect user to the download
header("Location: " . $getinforesult["url"] . "");
ob_end_flush();
exit;

?>
</body>
</html>