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

	require_once( './Web.php' );
	define( 'USERNAME', 'joed' );
	define( 'PASSWORD', 'letmein' );
	define( 'AKEY', 'ZpanH3bQPU2WfNZ5HuzMWY15HCm3IKUWq8eF1Nmu' );
	define( 'IKEY', 'DIXDORZXATXIXKD2ZALS' );
	define( 'SKEY', 'j1wOnMCIJkyp4ECglHmZD8iYjuffoURBXrSOwM9W' );
	define( 'HOST', 'api-db10c0b5.duosecurity.com' );
	echo '<html>';
	echo '<head>';
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
	echo '</head>';
	echo '<h1>Duo Security Web SDK Demo</h1>';
	echo sprintf( 'Username: %s <br>Password: %s <br><br><br><hr>', USERNAME, PASSWORD );

	if (isset( $_POST['sig_response'] )) {
		$resp = bafcfabdbf::verifyResponse( IKEY, SKEY, AKEY, $_POST['sig_response'] );

		if ($resp === USERNAME) {
			echo 'Hi, ' . $resp . '<br>';
			echo 'Your content here!';
		}
	} 
else {
		if (( isset( $_POST['user'] ) && isset( $_POST['pass'] ) )) {
			if (( $_POST['user'] === USERNAME && $_POST['pass'] === PASSWORD )) {
				$sig_request = bafcfabdbf::signRequest( IKEY, SKEY, AKEY, $_POST['user'] );
				echo '        <script type="text/javascript" src="Duo-Web-v2.js"></script>
        <link rel="stylesheet" type="text/css" href="Duo-Frame.css">
        <iframe id="duo_iframe"
            data-host="';
				echo HOST;
				echo '"
            data-sig-request="';
				echo $sig_request;
				echo '"
        ></iframe>
';
			}
		} 
else {
			echo '<form action=\'index.php\' method=\'post\'>';
			echo 'Username: <input type=\'text\' name=\'user\' /> <br />';
			echo 'Password: <input type=\'password\' name=\'pass\' /> <br />';
			echo '<input type=\'submit\' value=\'Submit\' />';
			echo '</form>';
		}
	}

	echo '</html>';
?>