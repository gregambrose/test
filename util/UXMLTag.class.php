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

	class uxmltag {
		var $name = null;
		var $attributes = null;
		var $data = null;

		function uxmltag($name) {
			$this->name = $name;
			$attributes = array(  );
		}

		function setattributes($attributes) {
			$this->attributes = $attributes;
		}

		function getattribute($att) {
			if (!isset( $this->attributes[$att] )) {
				trigger_error( '' . 'cant get attribute ' . $att, E_USER_ERROR );
			}

			$att = $this->attributes[$att];
			return $att;
		}

		function getattributes() {
			return $this->attributes;
		}

		function setdata($data) {
			$this->data .= $data;
		}

		function cleardata() {
			$this->data = '';
		}

		function getdata() {
			return html_entity_decode( $this->data );
		}

		function getname() {
			return $this->name;
		}

		function getcopy() {
			$tag = new UXMLtag( $this->name );
			$tag->attributes = $this->getAttributes(  );
			$tag->data = $this->getData(  );
			return $tag;
		}
	}

?>