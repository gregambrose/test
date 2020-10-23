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

	class xmltag {
		var $name = null;
		var $attributes = null;
		var $data = null;

		function xmltag($name) {
			$this->name = $name;
			$attributes = array(  );
		}

		function setattributes($attributes) {
			$this->attributes = $attributes;
		}

		function getattributes() {
			return $this->attributes;
		}

		function setdata($data) {
			$this->data = $data;
		}

		function getdata() {
			return $this->data;
		}

		function getname() {
			return $this->name;
		}
	}

?>