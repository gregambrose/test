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

	class udateselector {
		var $lowYear = null;
		var $highYear = null;
		var $date = null;
		var $day = null;
		var $month = null;
		var $year = null;
		var $months = array( 0 => 'JANUARY', 1 => 'FEBRUARY', 2 => 'MARCH', 3 => 'APRIL', 4 => 'MAY', 5 => 'JUNE', 6 => 'JULY', 7 => 'AUGUST', 8 => 'SEPTEMBER', 9 => 'OCTOBER', 10 => 'NOVEMBER', 11 => 'DECEMBER' );

		function udateselector($date, $lowYear, $highYear) {
			$this->date = $date;
			$this->lowYear = $lowYear;
			$this->highYear = $highYear;
			$this->setDateByDate( $date );
		}

		function setdatebydaymonthyear($day, $month, $year) {
			$this->day = $day;
			$this->month = $month;
			$year = (int)$year;
			$this->year = $year;

			if (( ( $day == 0 && $month == 0 ) && $year == 0 )) {
				$this->date = null;
				return true;
			}

			$ok = checkdate( $month, $day, $year );

			if ($ok == false) {
				return false;
			}

			$this->date = mktime( 0, 0, 0, $month, $day, $year, 0 );
			return true;
		}

		function setdatebydate($date) {
			$this->date = $date;

			if ($date == null) {
				$this->day = null;
				$this->month = null;
				$this->year = null;
				return null;
			}

			$da = getdate( $date );
			$this->day = $da['mday'];
			$this->month = $da['mon'];
			$this->year = $da['year'];
		}

		function setday($day) {
			$this->day = $day;
		}

		function setmonth($month) {
			$this->month = $month;
		}

		function setyear($year) {
			$this->year = $year;
		}

		function recalculatedate() {
			$day = $this->day;
			$month = $this->month;
			$year = $this->year;
			$ok = checkdate( $month, $day, $year );

			if ($ok == false) {
				return false;
			}

			$this->date = mktime( 0, 0, 0, $month, $day, $year, 0 );
			return true;
		}

		function setasdaystart() {
			$this->date = mktime( 0, 0, 0, $this->month, $this->day, $this->year, 0 );
		}

		function setasdayend() {
			$this->date = mktime( 23, 59, 59, $this->month, $this->day, $this->year, 0 );
		}

		function getdate() {
			return $this->date;
		}

		function getdateassqldate() {
			$date = $this->date;

			if ($date == null) {
				return null;
			}

			$year = (int)date( 'Y', $date );
			$month = (int)date( 'm', $date );
			$day = (int)date( 'j', $date );
			$SQLDate = sprintf( '%04d-%02d-%02d', $year, $month, $day );
			return $SQLDate;
		}

		function getdateastimestamp() {
			$out = date( 'YmdHis', $this->date );
			return $out;
		}

		function getdayselector($template, $text) {
			$day = $this->day;

			if (( $day < 1 || 31 < $day )) {
				$day = 0;
			}

			$out = '';
			$x = 1;

			while ($x <= 31) {
				$template->set( 'day', $x );

				if ($day == $x) {
					$selected = 'selected';
				} 
else {
					$selected = '';
				}

				$template->set( 'daySelected', $selected );
				$out .= $template->parse( $text );
				++$x;
			}

			return $out;
		}

		function getmonthselector($template, $text) {
			$month = $this->month;
			$out = '';
			$x = 1;

			while ($x <= 12) {
				$template->set( 'month', $x );
				$desc = $this->months[$x - 1];
				$template->set( 'monthName', $desc );

				if ($month == $x) {
					$selected = 'selected';
				} 
else {
					$selected = '';
				}

				$template->set( 'monthSelected', $selected );
				$out .= $template->parse( $text );
				++$x;
			}

			return $out;
		}

		function getyearselector($template, $text) {
			$year = $this->year;
			$start = $this->lowYear;
			$end = $this->highYear;
			$out = '';
			$x = $start;

			while ($x <= $end) {
				if ($x == $year) {
					$selected = ' selected';
				} 
else {
					$selected = '';
				}

				$template->set( 'yearSelected', $selected );
				$template->set( 'year', $x );
				$out .= $template->parse( $text );
				++$x;
			}

			return $out;
		}
	}

?>