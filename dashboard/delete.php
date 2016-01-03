<?php
session_start();
if (!isset($_SESSION['authed'])) {
	header("Location: login.html");
	exit();
}
else {
	include_once('settings.php');
	$con = new mysqli($fancyVars['dbaddr'], $fancyVars['dbuser'], $fancyVars['dbpass'], $fancyVars['dbname']);
	mysqli_set_charset($con, "utf8");
	$type = $_GET['type'];

	if ($type == 'site' && !empty($_GET['name'])) {
		$name = $_GET['name'];
		$sql = "DROP TABLE IF EXISTS `{$con->real_escape_string($name)}`;";
	}
	elseif ($type == 'element' && !empty($_GET['name']) && !empty($_GET['site'])) {
		$site = $_GET['site'];
		$name = $_GET['name'];
		$sql = "DELETE FROM `{$con->real_escape_string($site)}` WHERE `name` = '{$con->real_escape_string($name)}';";
	}

	$con->query($sql);

	header('Location: .');
	exit();
}
?>