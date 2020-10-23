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

	class classofbustemplate {
		var $cob = null;

		function classofbustemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'cbName' );
			$this->addField( 'cbIsTravel' );
			$this->addField( 'cbFeesVatable' );
			$this->addField( 'cbRMAR' );
			$this->addField( 'cbZeroIPT' );
			$this->addField( 'cbAllowIPTAmend' );
			$this->setFieldType( 'cbIsTravel', 'checked' );
			$this->setFieldType( 'cbFeesVatable', 'checked' );
			$this->setFieldType( 'cbRMAR', 'checked' );
			$this->setFieldType( 'cbZeroIPT', 'checked' );
			$this->setFieldType( 'cbAllowIPTAmend', 'checked' );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			$this->cob = null;
			$this->setUpdateFieldsWhenNoEdit( false );
		}

		function setcob($cbCode) {
			if ($cbCode < 1) {
				return null;
			}

			$this->cob = new Cob( $cbCode );
			$this->setAll( $this->cob->getAllForHTML(  ) );
		}

		function clearcob() {
			$this->cob = new Cob( null );
			$this->setAll( $this->cob->getAllForHTML(  ) );
			unset( $this[cob] );
		}

		function updatecob($input) {
			$cbCode = $input['cbCode'];
			$cob = new Cob( $cbCode );
			$cob->setAll( $input );
			$cob->update(  );
			$this->setMessage( 'updated' );
		}

		function wheneditrequested($template, $input) {
			if (!isset( $this->cob )) {
				$template->setMessage( 'no class of business selected to edit' );
				return false;
			}

			utemplate::wheneditrequested( $template, $input );
			$this->setMessage( 'class of business can be edited' );
			return false;
		}

		function listentries($text) {
			$q = 'SELECT * FROM classOfBus ORDER BY cbName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$this->set( 'code', $row['cbCode'] );
				$this->set( 'name', $row['cbName'] );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>