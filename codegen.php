<?php

/*** PHP Code Generator v0.1.0 (08/10/2017) by Szilárd Barta (Free Software, GNU GPLv3 License) - bartaszili (at) gmail (dot) com ***/
/*** License: https://www.gnu.org/licenses/gpl.txt ***/
/*** Includes: jQuery v3.2.1, Bootstrap v3.3.7  ***/
/*** Description: PHP Code Generator, mainly for secure passwords. ***/
/*** Project home: https://github.com/bartaszili/PHP-Code-Generator ***/

$version = 'v0.1.0 (08/10/2017)';

/*********************/
/*** 01: Functions ***/
/*********************/

function genPassword($length = 8, $available_sets = 'luds', $exclude = '', $unique = true) {

	$sets = array();
	if(strpos($available_sets, 'l') !== false) {
    $sets[] = 'abcdefghjkmnpqrstuvwxyz';
  }
	if(strpos($available_sets, 'u') !== false) {
    $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
  }
	if(strpos($available_sets, 'd') !== false) {
    $sets[] = '23456789';
  }
	if(strpos($available_sets, 's') !== false) {
    $sets[] = '!@#$%&*?';
  }

  $exclude = trim(preg_replace('/\s+/', '', $exclude)); //remove any whitespace
  $excl_sets = array(); //build excluded characters array
  if(isset($exclude) && !empty($exclude)) {
    $excl_count = substr_count($exclude, ',');
    if($excl_count >= 1) {
      $excl_sets = explode(',', $exclude);
    }
    else {
      $excl_sets[] = $exclude;
    }
    foreach($sets as $key => $set) { // remove excluded characters from sets
      $sets[$key] = str_replace($excl_sets, '', $set);
    }
  }

	$all = '';
	$oneFromEachSet = '';
	foreach($sets as $set) {
		$oneFromEachSet .= $set[array_rand(str_split($set))];
		$all .= $set;
	}

	if($unique == true) { // unique or not
		foreach($sets as $key => $set) { // remove characters from sets
      $sets[$key] = str_replace(str_split($oneFromEachSet), '', $set);
    }
	}
	$all = str_shuffle($all);
	if($length > strlen($all)) {
		return false;
	}
	else {
		$password = substr($all, 0, $length - strlen($oneFromEachSet));
		$password = str_shuffle($oneFromEachSet . $password);

		return $password;
	}
}

function multiPasswords($leading, $num, $length = 8, $available_sets = 'luds', $exclude = '', $unique = true) {
	$a = strlen($leading);
  $arr = array();
  for($i = 0; $i < $num; $i++) {
    $arr[] = $leading . genPassword($length - $a, $available_sets, $exclude, $unique);
  }
  $x = count($arr);
  $y = count(array_unique($arr));
  if($x == $y) {
    return $arr;
  }
  else {
    return false;
  }
}

function writeCsv($data, $csv) {
	reset($data);
	$i = array();
	foreach ($data as $row) {
		$i[] = '"' . $row . '"';
	}
	$fh = fopen($csv, 'w');
	fwrite($fh, implode('
', $i));
	fclose($fh);
}

function writeTxt($data, $txt) {
	reset($data);
	$fh = fopen($txt, 'w');
	fwrite($fh, implode('
', $data));
	fclose($fh);
}

/*************************************/
/*** 02: Get the data sent by form ***/
/*************************************/


if(isset($_REQUEST['quantity'])) { $quantity = $_REQUEST['quantity']; }
else { $quantity = 1; }
settype($quantity, 'string');

if(isset($_REQUEST['length'])) { $length = $_REQUEST['length']; }
else { $length = 8; }
settype($length, 'string');

if(isset($_REQUEST['exclude'])) { $exclude = $_REQUEST['exclude']; }
else { $exclude = ''; }
settype($exclude, 'string');

if(isset($_REQUEST['leading'])) { $leading = $_REQUEST['leading']; }
else { $leading = ''; }
settype($leading, 'string');

if(isset($_REQUEST['lowercase'])) { $lowercase = $_REQUEST['lowercase']; }
else { $lowercase = ''; }
settype($lowercase, 'string');
if($lowercase == 'l') { $l_checked = 'checked="checked"'; }
else { $l_checked = ''; }

if(isset($_REQUEST['uppercase'])) { $uppercase = $_REQUEST['uppercase']; }
else { $uppercase = ''; }
settype($uppercase, 'string');
if($uppercase == 'u') { $u_checked = 'checked="checked"'; }
else { $u_checked = ''; }

if(isset($_REQUEST['number'])) { $number = $_REQUEST['number']; }
else { $number = ''; }
settype($number, 'string');
if($number == 'd') { $d_checked = 'checked="checked"'; }
else { $d_checked = ''; }

if(isset($_REQUEST['special'])) { $special = $_REQUEST['special']; }
else { $special = ''; }
settype($special, 'string');
if($special == 's') { $s_checked = 'checked="checked"'; }
else { $s_checked = ''; }

if(isset($_REQUEST['unique'])) { $unique = $_REQUEST['unique']; }
else { $unique = ''; }
settype($unique, 'string');
if($unique == 'q') { $q_checked = 'checked="checked"'; }
else { $q_checked = ''; }

if(isset($_REQUEST['save_csv'])) { $save_csv = $_REQUEST['save_csv']; }
else { $save_csv = ''; }
settype($save_csv, 'string');
if($save_csv == 's1') { $s1_checked = 'checked="checked"'; }
else { $s1_checked = ''; }

if(isset($_REQUEST['save_txt'])) { $save_txt = $_REQUEST['save_txt']; }
else { $save_txt = ''; }
settype($save_txt, 'string');
if($save_txt == 's2') { $s2_checked = 'checked="checked"'; }
else { $s2_checked = ''; }

/*********************************/
/*** 03: Process recieved data ***/
/*********************************/

$success_msg = '';
$code_sets = $lowercase . $uppercase . $number . $special;
$csvFile = 'codegen_' . date('d-m-Y_H-i-s', time()) . '.csv';
$txtFile = 'codegen_' . date('d-m-Y_H-i-s', time()) . '.txt';

if($unique == 'q') { $q = true; }
else { $q = false; }

$results = multiPasswords($leading, $quantity, $length, $code_sets, $exclude, $q);
$result_list = implode('
', $results);


if($save_csv == 's1') {
	writeCsv($results, $csvFile);
	$success_msg .= '<li><a href="./' . $csvFile . '">' . $csvFile . '</a> saved</li>';
}


if($save_txt == 's2') {
	writeTxt($results, $txtFile);
	$success_msg .= '<li><a href="./' . $txtFile . '">' . $txtFile . '</a> saved</li>';
}

/*******************/
/*** 04: Display ***/
/*******************/

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="Code Generator" />
		<meta name="keywords" content="code, generator, password" />
		<meta name="author" content="Szilárd Barta, bartaszili@gmail.com" />
		<meta name="copyright" content="Free software" />
		<meta name="version" content="<?php echo $version; ?>" />
		<meta name="robots" content="noindex, nofollow" />
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<style type="text/css">
			body {
				padding-top: 70px;
			}
			footer p {
				margin: 15px 0px;
			}
			@-moz-document url-prefix() {
    			fieldset {
					display: table-cell;
				}
			}
			.position_footer {
				margin: 10px 0px;
			}
			.popover-content > p {
				text-align: justify;
			}
			.email_footer {
				cursor: pointer;
			}
		</style>
		<title>PHP Code Generator</title>
	</head>
	<body>
		<header>
			<nav class="navbar navbar-default navbar-fixed-top">
				<div class="container">
					<div class="navbar-header">
						<a class="navbar-brand" href="<?php echo $_SERVER['PHP_SELF']; ?>">PHP Code Generator</a>
					</div>
				</div>
			</nav>
		</header>
		<main>
			<div class="container theme-showcase">
<?php
if ($save_csv == 's1' || $save_txt == 's2') {
	echo '				<div class="alert alert-success">
					<span class="lead">Success</span>
					<hr />
					<ul>
						'.$success_msg.'
					</ul>
				</div>
';
}
?>
			<form id="mainForm" name="mainForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<div class="container-fluid">
					<div class="row">
						<div class="col-xs-5 col-sm-2 text-left"><label for="quantity">Quantity:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="quantity" class="form-control text-right" type="text" name="quantity" value="<?php echo $quantity; ?>" /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row">
						<div class="col-xs-5 col-sm-2 text-left"><label for="length">Length:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="length" class="form-control text-right" type="text" name="length" value="<?php echo $length; ?>" /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row">
						<div class="col-xs-5 col-sm-2 text-left"><label for="exclude">Exclude [0,O,i,1,l,|]:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="exclude" class="form-control text-right" type="text" name="exclude" value="<?php echo $exclude; ?>" /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row">
						<div class="col-xs-5 col-sm-2 text-left"><label for="leading">Leading characters:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="leading" class="form-control text-left" type="text" name="leading" value="<?php echo $leading; ?>" /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row form-check">
						<div class="col-xs-5 col-sm-2 text-left"><label for="lowercase" class="form-check-label">Lowercase [a-z]:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="lowercase" class="form-check-input pull-left" type="checkbox" name="lowercase" value="l" <?php echo $l_checked; ?> /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row form-check">
						<div class="col-xs-5 col-sm-2 text-left"><label for="uppercase" class="form-check-label">Uppercase [A-Z]:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="uppercase" class="form-check-input pull-left" type="checkbox" name="uppercase" value="u" <?php echo $u_checked; ?> /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row form-check">
						<div class="col-xs-5 col-sm-2 text-left"><label for="number" class="form-check-label">Number [0-9]:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="number" class="form-check-input pull-left" type="checkbox" name="number" value="d" <?php echo $d_checked; ?> /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row form-check">
						<div class="col-xs-5 col-sm-2 text-left"><label for="special" class="form-check-label">Special [$#*]:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="special" class="form-check-input pull-left" type="checkbox" name="special" value="s" <?php echo $s_checked; ?> /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row form-check">
						<div class="col-xs-5 col-sm-2 text-left"><label for="unique" class="form-check-label">Unique [1 char 1x]:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="unique" class="form-check-input pull-left" type="checkbox" name="unique" value="q" <?php echo $q_checked; ?> /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row form-check">
						<div class="col-xs-5 col-sm-2 text-left"><label for="save_csv" class="form-check-label">Save as CSV file:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="save_csv" class="form-check-input pull-left" type="checkbox" name="save_csv" value="s1" <?php echo $s1_checked; ?> /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
					<div class="row form-check">
						<div class="col-xs-5 col-sm-2 text-left"><label for="save_txt" class="form-check-label">Save as TXT file:</label></div>
						<div class="col-xs-7 col-sm-3 text-center"><input id="save_txt" class="form-check-input pull-left" type="checkbox" name="save_txt" value="s2" <?php echo $s2_checked; ?> /></div>
						<div class="clearfix visible-xs-block"></div>
						<div class="col-sm-7"></div>
					</div>
					<br />
				</div>
				<button type="submit" class="btn btn-default btn-block">GENERATE CODE</button>
				<br />
			</form>
<?php
if (!empty($result_list)) {
	echo '			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="lead">Generated Code(s)</span></h3>
				</div>
				<div class="panel-body"><pre class="pre-scrollable">
';
print($result_list);
echo '
				</pre></div></div>';
}
?>

		<?php clearstatcache(); ?>
			</div>
		</main>
		<footer>
			<div class="container theme-showcase">
				<nav class="navbar navbar-default navbar-static-bottom">
					<div class="container position_footer">
						<small><a href="https://github.com/bartaszili/PHP-Code-Generator" target="_blank">PHP Code Generator</a> &nbsp; <small class="text-muted"><?php echo $version; ?></small> &nbsp; by&nbsp;<span tabindex="0" class="email_footer" data-toggle="popover" data-trigger="focus" data-placement="top" data-html="true" data-container="body" data-content="<a href='mailto:bartaszili@gmail.com'>bartaszili@gmail.com</a>"><u>Szil&aacute;rd&nbsp;Barta</u></span> &nbsp; <small class="text-muted">(Free&nbsp;software, <a href="https://www.gnu.org/licenses/gpl.txt" target="_blank">License</a>)</small></small>
					</div>
				</nav>
			</div>
		</footer>
		<script id="jquery_min_js" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script>
			$(document).ready(function() {
				// BOOTSTRAP: CALL POPOVER FUNCTION
				$(function () {
					$('[data-toggle="popover"]').popover()
				});
			});
		</script>
	</body>
</html>
