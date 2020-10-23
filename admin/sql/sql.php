<?php
/**
* @ iDezender 8.0
* @ Developed by Qarizma
*
* @    Visit our website:
* @    www.iRadikal.com
* @    For cheap decoding service :)
* @    And for the ionCube Decoder!
*/          

	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>';
	echo SITE_NAME;
	echo '</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
	';

	if (!isset( $_GET['q'] )) {
		exit(  );
	}

	$select = $_GET['q'];
	echo '' . 'Request is ' . $select . '<br>';
	include_once( '../../config/config.inc.php' );
	include_once( 'phpReportGen.php' );
	$prg = new phpReportGenerator(  );
	$prg->width = '100%';
	$prg->cellpad = '0';
	$prg->cellspace = '0';
	$prg->border = '1';
	$prg->header_color = '#666666';
	$prg->header_textcolor = '#FFFFFF';
	$prg->body_alignment = 'left';
	$prg->body_color = '#CCCCCC';
	$prg->body_textcolor = '#800022';
	$prg->surrounded = '1';
	mysql_connect( DBHOST, DBUSER, DBPASSWORD );
	mysql_select_db( DBDATABASE );
	$res = mysql_query( $select );

	if ($res == false) {
		echo 'invalid sql<br>';
		exit(  );
	}

	$prg->mysql_resource = $res;
	$prg->title = 'Test Table';
	$prg->generateReport(  );
	echo '</body>
</html>
';
?>