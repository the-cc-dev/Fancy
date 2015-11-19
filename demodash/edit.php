<?php
if (!isset($_GET['id']) || !isset($_GET['site'])) { header('Location: .'); }
$id = $_GET['id'];
$site = $_GET['site'];
include_once('settings.php');
$con = new mysqli($fancyVars['dbaddr'], $fancyVars['dbuser'], $fancyVars['dbpass'], $fancyVars['dbname']);
mysqli_set_charset($con, "utf8");

if ($id == 0) {
	$html = '';
	$name = 'Enter name here';
}
else {
	$section = $con->query("SELECT * FROM `{$con->real_escape_string($site)}` WHERE `id` = '{$con->real_escape_string($id)}';");
	$row = $section->fetch_array(MYSQLI_ASSOC);
	$html = $row['html'];
	$name = $row['name'];
}

$site = $_GET['site'];

//Get elements
$sql = $con->query("SELECT `id`, `name` FROM `{$con->real_escape_string($site)}` WHERE 1;");
$elements = array();
foreach ($sql as $row) {
	$elements[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title><?php echo $site; ?> - Fancy Dashboard</title>

	<!-- Bootstrap Core CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="css/sb-admin.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	<!-- My CSS -->
	<link href="css/style.css" rel="stylesheet">

	<script src="js/jquery.js"></script>
	<script src="js/markdown.min.js"></script>
	<script src="js/mammoth.browser.min.js"></script>
	<script src="js/index.js"></script>

	<script>
		$(document).ready(function() {
			var result = {
				value: <?php echo json_encode($html); ?>
			}
			displayResult(result);
		});

		function del(name) {
			return;
		}
	</script>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>

<body>

	<div id="wrapper">

		<!-- Navigation -->
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo 'index.php?site='.$site ?>"><?php echo $site; ?> - Fancy Dashboard</a>
			</div>
			<!-- Top Menu Items -->
			<ul class="nav navbar-right top-nav">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Demo Dashboard <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li>
							<a href="javascript:void(0);"><i class="fa fa-fw fa-power-off"></i> Logout</a>
						</li>
					</ul>
				</li>
			</ul>
			<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav side-nav">
					<?php
					echo '<li><a href="edit.php?site='.$site.'&id=0"><i class="fa fa-fw fa-plus"></i> New Element</a></li>';
					foreach ($elements as $element) {

						$code = '<li>';
						if ($name == $element['name']) {
							$code = '<li class="active">';
						}
						$code .= '<a href="edit.php?site='.$site.'&id='.$element['id'].'">'.$element['name'].'</a></li>';
						echo $code;
					}
					?>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</nav>

		<div id="page-wrapper">

			<div class="container-fluid">

				<!-- Page Heading -->
				<div class="row">
					<div class="col-lg-12">
						<h1 id="name" class="page-header" contenteditable="true"><?php echo $name; ?></h1>

						<ol class="breadcrumb">
							<li>
								<i class="fa fa-file"></i>  <a href="<?php echo 'index.php?site='.$site ?>">Dashboard</a>
							</li>
							<li class="active">
								<i class="fa fa-pencil-square-o"></i> Element Editor
							</li>
						</ol>
						
						<!-- Page content -->
						<b>Upload a Microsoft Word (docx) file:</b>
						<input id="docxUpload" type="file"><br />
						<div id="siteDash" class="row">
							<div id="markdown" class="col-sm-4">
								<div class="panel panel-yellow">
									<div class="panel-heading">
										<h3 class="panel-title">Markdown</h3>
									</div>
									<div class="panel-body">
										<textarea id="md" class="form-control" style="min-height: 310px;" autocomplete="off"></textarea>
									</div>
								</div>
							</div>
							
							<div id="preview"  class="col-sm-4">
								<div class="panel panel-yellow" style="height: auto; min-height: 400px;">
									<div class="panel-heading">
										<h3 class="panel-title">Preview</h3>
									</div>
									<div class="panel-body">
										<iframe id="page" style="margin: 0px;"></iframe>
									</div>
								</div>
							</div>
							
								<div id="html"     class="col-sm-4">
									<form action="javascript:void(0);" method="post">
										<div class="panel panel-yellow">
											<div class="panel-heading">
												<h3 class="panel-title" seamless='seamless'>HTML</h3>
											</div>
											<div class="panel-body">
												<textarea id="htmleditor" name="html" class="form-control" style="min-height: 310px;" autocomplete="off"></textarea>
											</div>
										</div>
									<input id="nameInput" name="name" type="hidden" value="<?php echo $name; ?>" />
									<input name="id" type="hidden" value="<?php echo $id; ?>" />
									<input name="site" type="hidden" value="<?php echo $site; ?>" />
									<input type="submit" class="btn btn-success" style="margin-left: 10px; float: right;" />
									<a href="javascript:void(0);" class="btn btn-danger" style="float: right;" onclick="del(<?php echo "'".$name."'"; ?>);">Delete Element</a>
									</div>
								</form>
							</div>
						<p><em>Note: &lt;script&gt; tags are not ran in the preview but will remain live in the actual page<br />
						If you want to use advanced HTML it is advised you do not use the Markdown editor as it can break non-formatting HTML.</em></p>

						<p><h3>How to add this element to your site:</h3>
							<code>
								<?php
									echo htmlspecialchars('<?php');
									echo ' fancy(\'<span id="nameCode">'.addslashes($name).'</span>\');';
									echo ' ?>';
								?>
							</code>
						</p>

					</div>
				</div>
				<!-- /.row -->

			</div>
			<!-- /.container-fluid -->

		</div>
		<!-- /#page-wrapper -->

	</div>
	<!-- /#wrapper -->

	<!-- jQuery -->
	<script src="js/jquery.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.min.js"></script>

</body>

</html>