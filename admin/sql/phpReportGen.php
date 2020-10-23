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

	class phpreportgenerator {
		var $mysql_resource = null;
		var $header = null;
		var $foolter = null;
		var $fields = array(  );
		var $cellpad = null;
		var $cellspace = null;
		var $border = null;
		var $width = null;
		var $modified_width = null;
		var $header_color = null;
		var $header_textcolor = null;
		var $header_alignment = null;
		var $body_color = null;
		var $body_textcolor = null;
		var $body_alignment = null;
		var $surrounded = null;

		function generatereport() {
			$this->border = (empty( $this->border ) ? '0' : $this->border);
			$this->cellpad = (empty( $this->cellpad ) ? '1' : $this->cellpad);
			$this->cellspace = (empty( $this->cellspace ) ? '0' : $this->cellspace);
			$this->width = (empty( $this->width ) ? '100%' : $this->width);
			$this->header_color = (empty( $this->header_color ) ? '#FFFFFF' : $this->header_color);
			$this->header_textcolor = (empty( $this->header_textcolor ) ? '#000000' : $this->header_textcolor);
			$this->header_alignment = (empty( $this->header_alignment ) ? 'left' : $this->header_alignment);
			$this->body_color = (empty( $this->body_color ) ? '#FFFFFF' : $this->body_color);
			$this->body_textcolor = (empty( $this->body_textcolor ) ? '#000000' : $this->body_textcolor);
			$this->body_alignment = (empty( $this->body_alignment ) ? 'left' : $this->body_alignment);
			$this->surrounded = (empty( $this->surrounded ) ? false : true);
			$this->modified_width = ($this->surrounded == true ? '100%' : $this->width);

			if (!is_resource( $this->mysql_resource )) {
				exit( 'User doesn\'t supply any valid mysql resource after executing query result' );
			}

			$field_count = mysql_num_fields( $this->mysql_resource );
			$i = 0;

			while ($i < $field_count) {
				$field = mysql_fetch_field( $this->mysql_resource );
				$this->fields[$i] = $field->name;
				$this->fields[$i][0] = strtoupper( $this->fields[$i][0] );
				++$i;
			}

			echo '<b><i>' . $this->header . '</i></b>';
			echo '<P></P>';

			if ($this->surrounded == true) {
				echo '' . '<table width=\'' . $this->width . '\'  border=\'1\' cellspacing=\'0\' cellpadding=\'0\'><tr><td>';
			}

			echo '' . '<table width=\'' . $this->modified_width . '\'  border=\'' . $this->border . '\' cellspacing=\'' . $this->cellspace . '\' cellpadding=\'' . $this->cellpad . '\'>';
			echo '' . '<tr bgcolor = \'' . $this->header_color . '\'>';
			$i = 0;

			while ($i < $field_count) {
				echo '' . '<th align = \'' . $this->header_alignment . '\'><font color = \'' . $this->header_textcolor . '\'>&nbsp;' . $this->fields[$i] . '</font></th>';
				++$i;
			}

			echo '</tr>';

			while ($rows = mysql_fetch_row( $this->mysql_resource )) {
				echo '' . '<tr align = \'' . $this->body_alignment . '\' bgcolor = \'' . $this->body_color . '\'>';
				$i = 0;

				while ($i < $field_count) {
					echo '' . '<td><font color = \'' . $this->body_textcolor . '\'>&nbsp;' . $rows[$i] . '</font></td>';
					++$i;
				}

				echo '</tr>';
			}

			echo '</table>';

			if ($this->surrounded == true) {
				echo '</td></tr></table>';
			}

		}
	}

?>