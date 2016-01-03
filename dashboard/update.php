<?php
session_start();
if (!isset($_SESSION['authed'])) {
	header("Location: login.html");
	exit();
}
else {
	if (empty($_POST['name']) || empty($_POST['html']) || empty($_POST['id']) || empty($_POST['site'])) { header('Location: .'); exit(); }

	include_once('settings.php');
	$con = new mysqli($fancyVars['dbaddr'], $fancyVars['dbuser'], $fancyVars['dbpass'], $fancyVars['dbname']);
	mysqli_set_charset($con, "utf8");
	$name = urldecode($_POST['name']);
	$html = urldecode($_POST['html']);
	$site = $_POST['site'];
	$id = $_POST['id'];

	if ($id == 0) {
		//New section
		$sql = "INSERT INTO `{$con->real_escape_string($site)}` (`name`, `html`) VALUES ('{$con->real_escape_string($name)}', '{$con->real_escape_string($html)}');";
	}
	else {
		//Updating
		$sql = "UPDATE `{$con->real_escape_string($site)}` SET `name`='{$con->real_escape_string($name)}', `html`='{$con->real_escape_string($html)}' WHERE `id` = '{$con->real_escape_string($id)}';";
	}
	$con->query($sql);

	header('Location: index.php?site='.$site);
	exit();
}
?>