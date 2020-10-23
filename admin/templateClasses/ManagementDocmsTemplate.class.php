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

	class managementdocmstemplate {
		function managementdocmstemplate($html) {
			documentstemplate::documentstemplate( $html );
			$this->type = 'MR';
		}

		function showdocuments($text) {
			$doType = MANAGEMENT_DOCM_TYPE;
			$q = 'SELECT doCode, doSubject, doWhenEntered, doEnteredBy, doWhenOriginated, doOriginator, doDocmType, doNextActionBy,
			  doLocked, doClientAttachedTo, doInscoAttachedTo, doIntroducerAttachedTo, 
			  doClientSentWhen, doInscoSentWhen, doIntroducerSentWhen,
			  doFileSize,
			  doClientSequence, doPolicySequence, doTransSequence, doInscoSequence, doIntroducerSequence,
			  doPolicy, doTrans, doSysTran
			  FROM documents WHERE doDeleted != 1 ';
			$q .= '' . ' AND doDocmType=' . $doType;
			$q .= ' ORDER BY  doUpdateorCreate DESC,doWhenEntered DESC, doCode DESC';
			$out = $this->_displayDocumentsUsingSelect( $q, $text );
			return $out;
		}
	}

?>