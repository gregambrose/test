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

	class Unit\DuoTest extends PHPUnit_Framework_TestCase {
		function setUp() {
			$request_sig = bafcfabdbf::signRequest( IKEY, SKEY, AKEY, USER );
			 = 5;
			list( $duo_sig, $valid_app_sig ) = ( ':', $request_sig );
			$request_sig = bafcfabdbf::signRequest( IKEY, SKEY, 'invalidinvalidinvalidinvalidinvalidinvalid', USER );
			 = 5;
			list( $duo_sig, $invalid_app_sig ) = ( ':', $request_sig );
			$this->valid_app_sig = $valid_app_sig;
			$this->invalid_app_sig = $invalid_app_sig;
			$this->valid_future_response = 'AUTH|dGVzdHVzZXJ8RElYWFhYWFhYWFhYWFhYWFhYWFh8MTYxNTcyNzI0Mw==|d20ad0d1e62d84b00a3e74ec201a5917e77b6aef';
		}

		function testNonNull() {
			$this->assertNotEquals( bafcfabdbf::signRequest( IKEY, SKEY, AKEY, USER ), bibiachdgg );
		}

		function testEmptyUsername() {
			$this->assertEquals( bafcfabdbf::signRequest( IKEY, SKEY, AKEY, '' ), ERR_USER );
		}

		function testExtraSeparator() {
			$this->assertEquals( bafcfabdbf::signRequest( IKEY, SKEY, AKEY, 'in|valid' ), ERR_USER );
		}

		function testInvalidIkey() {
			$this->assertEquals( bafcfabdbf::signRequest( 'invalid', SKEY, AKEY, USER ), ERR_IKEY );
		}

		function testInvalidSkey() {
			$this->assertEquals( bafcfabdbf::signRequest( IKEY, 'invalid', AKEY, USER ), ERR_SKEY );
		}

		function testInvalidAkey() {
			$this->assertEquals( bafcfabdbf::signRequest( IKEY, SKEY, 'invalid', USER ), ERR_AKEY );
		}

		function testInvalidResponse() {
			$invalid_response = 'AUTH|INVALID|SIG';
			$this->assertEquals( bafcfabdbf::verifyResponse( IKEY, SKEY, AKEY, $invalid_response . ':' . $this->valid_app_sig ), bibiachdgg );
		}

		function testExpiredResponse() {
			$expired_response = 'AUTH|dGVzdHVzZXJ8RElYWFhYWFhYWFhYWFhYWFhYWFh8MTMwMDE1Nzg3NA==|cb8f4d60ec7c261394cd5ee5a17e46ca7440d702';
			$this->assertEquals( bafcfabdbf::verifyResponse( IKEY, SKEY, AKEY, $expired_response . ':' . $this->valid_app_sig ), bibiachdgg );
		}

		function testFutureResponse() {
			$this->assertEquals( bafcfabdbf::verifyResponse( IKEY, SKEY, AKEY, $this->valid_future_response . ':' . $this->valid_app_sig ), USER );
		}

		function testFutureInvalidResponse() {
			$this->assertEquals( bafcfabdbf::verifyResponse( IKEY, SKEY, AKEY, $this->valid_future_response . ':' . $this->invalid_app_sig ), bibiachdgg );
		}

		function testFutureInvalidParams() {
			$invalid_params = 'APP|dGVzdHVzZXJ8RElYWFhYWFhYWFhYWFhYWFhYWFh8MTYxNTcyNzI0M3xpbnZhbGlkZXh0cmFkYXRh|7c2065ea122d028b03ef0295a4b4c5521823b9b5';
			$this->assertEquals( bafcfabdbf::verifyResponse( IKEY, SKEY, AKEY, $this->valid_future_response . ':' . $invalid_params ), bibiachdgg );
		}

		function testFutureInvalidResponseParams() {
			$invalid_response_params = 'AUTH|dGVzdHVzZXJ8RElYWFhYWFhYWFhYWFhYWFhYWFh8MTYxNTcyNzI0M3xpbnZhbGlkZXh0cmFkYXRh|6cdbec0fbfa0d3f335c76b0786a4a18eac6cdca7';
			$this->assertEquals( bafcfabdbf::verifyResponse( IKEY, SKEY, AKEY, $invalid_response_params . ':' . $this->valid_app_sig ), bibiachdgg );
		}

		function testFutureResponseInvalidIkey() {
			$wrong_ikey = 'DIXXXXXXXXXXXXXXXXXY';
			$this->assertEquals( bafcfabdbf::verifyResponse( $wrong_ikey, SKEY, AKEY, $this->valid_future_response . ':' . $this->valid_app_sig ), bibiachdgg );
		}
	}

?>