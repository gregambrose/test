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

	class document {
		var $table = null;
		var $keyField = null;

		function document($code) {
			$this->keyField = 'doCode';
			$this->table = 'documents';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['doClient'] = 'INT';
			$this->fieldTypes['doPolicy'] = 'INT';
			$this->fieldTypes['doInsco'] = 'INT';
			$this->fieldTypes['doIntroducer'] = 'INT';
			$this->fieldTypes['doTrans'] = 'INT';
			$this->fieldTypes['doSysTran'] = 'INT';
			$this->fieldTypes['doClientSequence'] = 'INT';
			$this->fieldTypes['doPolicySequence'] = 'INT';
			$this->fieldTypes['doIntroducerSequence'] = 'INT';
			$this->fieldTypes['doTransSequence'] = 'INT';
			$this->fieldTypes['doDocmType'] = 'INT';
			$this->fieldTypes['doUploadType'] = 'INT';
			$this->fieldTypes['doFileSize'] = 'INT';
			$this->fieldTypes['doOriginator'] = 'INT';
			$this->fieldTypes['doEnteredBy'] = 'INT';
			$this->fieldTypes['doClientSentHow'] = 'INT';
			$this->fieldTypes['doClientSentBy'] = 'INT';
			$this->fieldTypes['doClientSentWhen'] = 'DATE';
			$this->fieldTypes['doClientAttachedTo'] = 'INT';
			$this->fieldTypes['doInscoSentHow'] = 'INT';
			$this->fieldTypes['doInscoSentBy'] = 'INT';
			$this->fieldTypes['doInscoSentWhen'] = 'DATE';
			$this->fieldTypes['doInscoAttachedTo'] = 'INT';
			$this->fieldTypes['doIntroducerSentHow'] = 'INT';
			$this->fieldTypes['doIntroducerSentBy'] = 'INT';
			$this->fieldTypes['doIntroducerSentWhen'] = 'DATE';
			$this->fieldTypes['doIntroducerAttachedTo'] = 'INT';
			$this->fieldTypes['doNextActionBy'] = 'INT';
			$this->fieldTypes['doBinaryDetail'] = 'BLOB';
			$this->_setUpdatedByField( 'doLastUpdateBy' );
			$this->_setUpdatedWhenField( 'doLastUpdateOn' );
			$this->handleConcurrency( true );
			$q = 'SELECT dtName, icName, usFirstName as nextByFirst, usLastName as nextByLast  FROM documents
				LEFT JOIN documentTypes on doDocmType = dtCode
				LEFT JOIN insuranceCompanies on doInsco = icCode
				LEFT JOIN users on doNextActionBy = usCode
				where doCode = CODE';
			$this->setExtraSql( $q );
			$q = 'SELECT hsName   FROM documents
				LEFT JOIN howSent on doClientSentBy = hsCode
				where doCode = CODE';
			$this->setExtraSql( $q );
		}

		function getdocumentcontents() {
			return base64_decode( $this->get( 'doBinaryDetail' ) );
		}

		function getdocumenttype() {
			return $this->get( 'doDocmType' );
		}

		function setdocumenttype($type) {
			$this->set( 'doDocmType', $type );
		}

		function setpolicy($plCode) {
			$this->set( 'doPolicy', $plCode );
		}

		function getpolicycode() {
			return $this->get( 'doPolicy' );
		}

		function setclient($clCode) {
			$this->set( 'doClient', $clCode );
		}

		function getclientcode() {
			return $this->get( 'doClient' );
		}

		function getuploadeddocument($docmName) {
			if (!isset( $_FILES[$docmName] )) {
				if (0 < $this->get( 'doFileSize' )) {
					return 1;
				}

				return 2;
			}

			$error = $_FILES[$docmName]['error'];
			$name = $_FILES[$docmName]['name'];
			$size = $_FILES[$docmName]['size'];
			$tmpName = $_FILES[$docmName]['tmp_name'];
			$type = $_FILES[$docmName]['type'];

			if ($error != 0) {
				return 3;
			}

			$this->addDocument( $name, $size, $type, $tmpName );
			return 0;
		}

		function adddocument($name, $size, $type, $tmpName) {
			$this->set( 'doFileName', $name );
			$this->set( 'doFileSize', $size );
			$this->set( 'doFileType', $type );
			$this->_setDocumentContents( $tmpName );
		}

		function adddocumentusingtext($name, $type, $text) {
			$size = strlen( $text );
			$this->set( 'doFileName', $name );
			$this->set( 'doFileSize', $size );
			$this->set( 'doFileType', $type );
			$this->_setDocumentContentsUsingText( $text );
		}

		function correctdocumentname() {
			$doCode = $this->getKeyValue(  );
			$doCode = sprintf( '%07d', $doCode );
			$name = $this->get( 'doFileName' );

			if (strcmp( $doCode, substr( $name, 0, 7 ) ) != 0) {
				$name = $doCode . '-' . $name;
				$this->set( 'doFileName', $name );
			}

		}

		function viewdocument() {
			$name = $this->get( 'doFileName' );
			$size = $this->get( 'doFileSize' );
			$type = $this->get( 'doFileType' );
			$data = $this->getDocumentContents(  );

			if (strlen( $data ) == 0) {
				return false;
			}


			if ($size == 0) {
				return false;
			}

			$name = str_replace( ' ', '_', $name );
			header( '' . 'Content-type: ' . $type );
			header( '' . 'Content-length: ' . $size );
			header( '' . 'Content-Disposition: attachment; filename=' . $name );
			header( 'Content-Description: E-NetBroker File' );
			header( 'Content-Type: application/force-download' );
			echo $data;
			exit(  );
		}

		function viewdocumentinline() {
			$name = $this->get( 'doFileName' );
			$size = $this->get( 'doFileSize' );
			$type = $this->get( 'doFileType' );
			$data = $this->getDocumentContents(  );

			if (strlen( $data ) == 0) {
				return false;
			}


			if ($size == 0) {
				return false;
			}

			header( '' . 'Content-type: ' . $type );
			header( '' . 'Content-length: ' . $size );
			header( '' . 'Content-Disposition: inline; filename=' . $name );
			header( 'Content-Description: E-NetBroker File' );
			echo $data;
			exit(  );
		}

		function setclientsequence() {
			$clCode = $this->get( 'doClient' );

			if ($clCode <= 0) {
				trigger_error( 'cant create sequence without client', E_USER_ERROR );
			}

			$doClientSequence = fsetsequence( 'CLD', $clCode );
			$this->set( 'doClientSequence', $doClientSequence );
		}

		function setpolicysequence() {
			$plCode = $this->get( 'doPolicy' );

			if ($plCode <= 0) {
				trigger_error( 'cant create sequence without policy', E_USER_ERROR );
			}

			$doPolicySequence = fsetsequence( 'PLD', $plCode );
			$this->set( 'doPolicySequence', $doPolicySequence );
		}

		function settranssequence() {
			$ptCode = $this->get( 'doTrans' );

			if ($ptCode <= 0) {
				trigger_error( 'cant create sequence without trans', E_USER_ERROR );
			}

			$doTransSequence = fsetsequence( 'PTD', $ptCode );
			$this->set( 'doTransSequence', $doTransSequence );
		}

		function setinscosequence() {
			$icCode = $this->get( 'doInsco' );

			if ($icCode <= 0) {
				trigger_error( 'cant create sequence without ins co', E_USER_ERROR );
			}

			$doInscoSequence = fsetsequence( 'ICD', $icCode );
			$this->set( 'doInscoSequence', $doInscoSequence );
		}

		function setintroducersequence() {
			$inCode = $this->get( 'doIntroducer' );

			if ($inCode <= 0) {
				trigger_error( 'cant create sequence without introducer', E_USER_ERROR );
			}

			$doIntroducerSequence = fsetsequence( 'IND', $inCode );
			$this->set( 'doIntroducerSequence', $doIntroducerSequence );
		}

		function setsent($type, $doSentWhen, $doSentHow, $usCode) {
			if (( ( $type == 'CL' || $type == 'PL' ) || $type == 'PT' )) {
				$this->set( 'doClientSentWhen', $doSentWhen );
				$this->set( 'doClientSentHow', $doSentHow );
				$this->set( 'doClientSentBy', $usCode );
				return null;
			}


			if ($type == 'IC') {
				$this->set( 'doInscoSentWhen', $doSentWhen );
				$this->set( 'doInscoSentHow', $doSentHow );
				$this->set( 'doInscoSentBy', $usCode );
				return null;
			}


			if ($type == 'IN') {
				$this->set( 'doIntroducerSentWhen', $doSentWhen );
				$this->set( 'doIntroducerSentHow', $doSentHow );
				$this->set( 'doIntroducerSentBy', $usCode );
				return null;
			}

			trigger_error( '' . 'wrong type = ' . $type, E_USER_ERROR );
		}

		function _setdocumentcontents($fileName) {
			$this->set( 'doBinaryDetail', base64_encode( file_get_contents( $fileName ) ) );
		}

		function _setdocumentcontentsusingtext($text) {
			$this->set( 'doBinaryDetail', base64_encode( $text ) );
		}

		function _getdocumentcontents() {
			return base64_decode( $this->get( 'doBinaryDetail' ) );
		}
	}

?>