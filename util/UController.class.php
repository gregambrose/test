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

	class ucontroller {
		var $view = null;
		var $model = null;
		var $processesToCall = null;

		function ucontroller() {
			$this->processesToCall = array(  );
		}

		function setmodel($model) {
			$this->model = &$model;

		}

		function setprocess($function, $input) {
			$this->processesToCall[$input] = $function;
		}

		function process() {
			global $session;

			$in = $session->getInput(  );
			$input = array_merge( $_GET, $_POST, $in );
			$this->model->setInput( $input );
			$done = $this->doBeforeProcessingActions(  );

			if ($done == false) {
				$this->_processActions( $input );
			}

			$this->model->displayPage(  );
		}

		function dobeforeprocessingactions() {
			return false;
		}

		function _processactions($input) {
			$done = 0;
			$wasDone = false;
			foreach ($this->processesToCall as $inType => $function) {
				if (isset( $input[$inType] ) == true) {
					if (method_exists( $this, $function ) == true) {
						$wasDone = $this->$function( $this, $input );

						if ($wasDone == true) {
							++$done;
							continue;
						}

						continue;
					}


					if (method_exists( $this->model, $function ) == true) {
						$wasDone = $this->model->$function( $input );

						if ($wasDone == true) {
							++$done;
							continue;
						}

						continue;
					}

					trigger_error( '' . 'no action ' . $function, E_USER_ERROR );
					continue;
				}
			}


			if ($done == 0) {
				$function = '_default';

				if (method_exists( $this, $function ) == true) {
					$wasDone = $this->$function( $this, $input );
				} 
else {
					if (method_exists( $this->model, $function ) == true) {
						$wasDone = $this->model->$function( $this, $input );
					}
				}


				if ($wasDone == true) {
					++$done;
				}
			}

			return $done;
		}
	}

?>