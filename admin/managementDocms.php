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

	require( '../include/startup.php' );
	$managementDocmsTemplate = &$session->get( 'managementDocmsTemplate' );

	if ($managementDocmsTemplate == null) {
		$managementDocmsTemplate = new ManagementDocmsTemplate( 'managementDocms.html' );
	}

	$session->set( 'managementDocmsTemplate', $managementDocmsTemplate );
	$managementDocmsTemplate->process(  );
	$session->set( 'managementDocmsTemplate', $managementDocmsTemplate );
?>