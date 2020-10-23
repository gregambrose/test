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

	require_once( '../include/startup.php' );
	echo 'Starting';
	udbsettablefortransactions( 'documents' );
	udbsettablefortransactions( 'policyTransactions' );
	udbsettablefortransactions( 'clientTransactions' );
	udbsettablefortransactions( 'clientTransAllocations' );
	udbsettablefortransactions( 'inscoTransactions' );
	udbsettablefortransactions( 'introducerTransactions' );
	udbsettablefortransactions( 'cashBatches' );
	udbsettablefortransactions( 'cashBatchItems' );
	echo 'DONE';
?>