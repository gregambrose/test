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

	function _updateusers() {
		$q = 'UPDATE users SET usFirstName = \'Fred\', usLastName=\'Smith\',	usInitials = \'FJS\' WHERE usCode = 1';
		_doquery( 'update user 1', $q );
		$q = 'UPDATE users SET usFirstName = \'Mary\', usLastName=\'Jones\',	usInitials = \'MFJ\' WHERE usCode = 4';
		_doquery( 'update user 4', $q );
		$q = 'UPDATE users SET usFirstName = \'Frank\', usLastName=\'Wilson\',	usInitials = \'FRW\' WHERE usCode = 5';
		_doquery( 'update user 5', $q );
		$q = 'UPDATE users SET usFirstName = \'Brain\', usLastName=\'Appleby\',	usInitials = \'BA\' WHERE usCode = 6';
		_doquery( 'update user 6', $q );
		$q = 'UPDATE users SET usFirstName = \'Henry\', usLastName=\'Nicholson\', usInitials = \'HGN\' WHERE usCode = 7';
		_doquery( 'update user 7', $q );
		$q = 'UPDATE users SET usFirstName = \'Peter\', usLastName=\'Smith\',	usInitials = \'PJS\' WHERE usCode = 8';
		_doquery( 'update user 8', $q );
		$q = 'UPDATE users SET usFirstName = \'Amanda\', usLastName=\'Jollie\',	usInitials = \'AMJ\' WHERE usCode = 9';
		_doquery( 'update user 9', $q );
		$q = 'UPDATE users SET usFirstName = \'William\', usLastName=\'Jessop\',	usInitials = \'WJS\' WHERE usCode = 10';
		_doquery( 'update user 10', $q );
		$q = 'UPDATE users SET usFirstName = \'Martin\', usLastName=\'Devonan\',	usInitials = \'MKD\' WHERE usCode = 11';
		_doquery( 'update user 11', $q );
		$q = 'UPDATE users SET usFirstName = \'Sam\', usLastName=\'Stevens\',	usInitials = \'SPS\' WHERE usCode = 12';
		_doquery( 'update user 12', $q );
	}

	function _processclients() {
		$q = 'SELECT clCode FROM clients WHERE clType = 1';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( 'cant do query' . $q, E_USER_ERROR );
		}

		$clients = array(  );
		$elems = 0;

		while ($row = udbgetrow( $result )) {
			$clCode = $row['clCode'];
			$clients[$elems++] = $clCode;
		}

		$elem = 0;

		while ($elem < $elems) {
			$clCode = $clients[$elem];
			$client = new Client( $clCode );

			while (true) {
				$otherElem = rand( 0, $elems - 1 );

				if ($otherElem != $elem) {
					break;
					continue;
				}
			}

			$clCode2 = $clients[$otherElem];
			$other = new Client( $clCode2 );
			_copyclient( false, $other, $client );
			$client->update(  );
			++$elem;
		}

		$q = 'SELECT clCode FROM clients WHERE clType != 1';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( 'cant do query' . $q, E_USER_ERROR );
		}

		$clients = array(  );
		$elems = 0;

		while ($row = udbgetrow( $result )) {
			$clCode = $row['clCode'];
			$clients[$elems++] = $clCode;
		}

		$elem = 0;

		while ($elem < $elems) {
			$clCode = $clients[$elem];
			$client = new Client( $clCode );

			while (true) {
				$otherElem = rand( 0, $elems - 1 );

				if ($otherElem != $elem) {
					break;
					continue;
				}
			}

			$clCode2 = $clients[$otherElem];
			$other = new Client( $clCode2 );
			_copyclient( true, $other, $client );
			$client->update(  );
			++$elem;
		}

	}

	function _processpolicies() {
		$q = 'SELECT plCode FROM policies ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( 'cant do query' . $q, E_USER_ERROR );
		}

		$policies = array(  );
		$elems = 0;

		while ($row = udbgetrow( $result )) {
			$plCode = $row['plCode'];
			$policies[$elems++] = $plCode;
		}

		$elem = 0;

		while ($elem < $elems) {
			$plCode = $policies[$elem];
			$policy = new Policy( $plCode );
			$policy->set( 'plPolicyHolder', '' );
			$policy->set( 'plSourceDocm', '' );
			$policy->set( 'plCoverDescription', _changenumerals( $policy->get( 'plCoverDescription' ) ) );
			$policy->set( 'plPolicyNumber', _changenumerals( $policy->get( 'plPolicyNumber' ) ) );
			$policy->set( 'plPrevCoverDesc', _changenumerals( $policy->get( 'plPrevCoverDesc' ) ) );
			$policy->update(  );
			++$elem;
		}

	}

	function _copyclient(&$retail, &$other, $client) {
		$client->set( 'clFirstName', $other->get( 'clFirstName' ) );
		$client->set( 'clLastName', $other->get( 'clLastName' ) );
		$client->set( 'clName', $other->get( 'clName' ) );
		$client->set( 'clSortName', $other->get( 'clSortName' ) );
		$client->set( 'clInvAddFirstName', $other->get( 'clInvAddFirstName' ) );
		$client->set( 'clInvAddLastName', $other->get( 'clInvAddLastName' ) );

		if ($retail == false) {
			$name = _makerandomcompanyname(  );
			$client->set( 'clFirstName', '' );
			$client->set( 'clLastName', '' );
			$client->set( 'clName', $name );
			$client->set( 'clSortName', $name );
			$client->set( 'clInvAddFirstName', '' );
			$client->set( 'clInvAddLastName', '' );
			$client->set( 'clBusinessTrade', '' );
			$client->set( 'clBusinessDesc', '' );
		} 
else {
			_makerandomretailname( $client );
			$client->set( 'clName', '' );
			$client->set( 'clSortName', '' );
			$client->set( 'clInvAddFirstName', '' );
			$client->set( 'clInvAddLastName', '' );
		}

		$client->set( 'clAddress', _changenumerals( $client->get( 'clAddress' ) ) );
		$client->set( 'clInvAddress', _changenumerals( $client->get( 'clInvAddress' ) ) );
		$client->set( 'clPostcode', _changenumerals( $client->get( 'clPostcode' ) ) );
		$client->set( 'clInvAddPostcode', _changenumerals( $client->get( 'clInvAddPostcode' ) ) );
		$client->set( 'clHomePhone', _changenumerals( $client->get( 'clHomePhone' ) ) );
		$client->set( 'clMobile', _changenumerals( $client->get( 'clMobile' ) ) );
		$client->set( 'clFax', _changenumerals( $client->get( 'clFax' ) ) );
		$client->set( 'clInvAddress', _changenumerals( $client->get( 'clInvAddress' ) ) );
		$client->set( 'clInvAddPostcode', _changenumerals( $client->get( 'clInvAddPostcode' ) ) );
		$client->set( 'clInvAddWorkPhone', _changenumerals( $client->get( 'clInvAddWorkPhone' ) ) );
		$client->set( 'clInvAddMobile', _changenumerals( $client->get( 'clInvAddMobile' ) ) );
		$client->set( 'clInvAddFax', _changenumerals( $client->get( 'clInvAddFax' ) ) );
		$x = _correctaddress( $client->get( 'clAddress' ), $client->get( 'clPostcode' ) );
		$name = $x[0];
		$pc = $x[1];
		$client->set( 'clAddress', $name );
		$client->set( 'clPostcode', $pc );
		$x = _correctaddress( $client->get( 'clInvAddAddress' ), $client->get( 'clInvAddPostcode' ) );
		$name = $x[0];
		$pc = $x[1];
		$client->set( 'clInvAddAddress', $name );
		$client->set( 'clInvAddPostcode', $pc );
	}

	function _makerandomcompanyname() {
		global $firsts;
		global $seconds;

		$numFirsts = sizeof( $firsts );
		$numSeconds = sizeof( $seconds );
		$loops = 0;

		while (true) {
			if (100 < ++$loops) {
				trigger_error( 'cant create', E_USER_ERROR );
			}

			$elem = rand( 0, $numFirsts - 1 );
			$n1 = $firsts[$elem];
			$elem = rand( 0, $numSeconds - 1 );
			$n2 = $seconds[$elem];
			$ltd = '';

			if (15 <= rand( 1, 100 )) {
				$ltd = 'Ltd.';
			}

			$name = '' . $n1 . ' ' . $n2 . ' ' . $ltd;
			$q = '' . 'SELECT clCode FROM clients WHERE clName = \'' . $name . '\'';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if ($num == 0) {
				break;
				continue;
			}
		}

		return $name;
	}

	function _makerandomretailname($client) {
		global $retailFirst;
		global $retailLast;

		$loops = 0;

		while (true) {
			if (100 < ++$loops) {
				trigger_error( 'cant create', E_USER_ERROR );
			}

			$first = array_rand( $retailFirst );
			$sex = $retailFirst[$first];
			$elem = array_rand( $retailLast );
			$last = $retailLast[$elem];
			$initials = substr( $first, 0, 1 ) . substr( $last, 0, 1 );

			if ($sex == 'F') {
				$title = 2;
			} 
else {
				$title = 1;
			}

			$q = '' . 'SELECT clCode FROM clients WHERE clFirstName = \'' . $first . '\' AND clLastName = \'' . $last . '\'';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if ($num == 0) {
				break;
				continue;
			}
		}

		$client->set( 'clFirstName', $first );
		$client->set( 'clLastName', $last );
		$client->set( 'clName', '' );
		$client->set( 'clInitials', $initials );
		$client->set( 'clTitle', $title );
	}

	function _correctaddress($add, $postCode) {
		global $towns;
		global $counties;

		$y = $postCode;
		$x = $add;

		if (stristr( $postCode, 'ha' ) !== false) {
			$pc = array_rand( $towns );
			$town = $towns[$pc];
			$x = str_replace( 'harrow', $town, $add );
			$x = str_replace( 'Harrow', $town, $x );
			$x = str_replace( 'pinner', $town, $x );
			$x = str_replace( 'Pinner', $town, $x );
			$y = str_replace( 'ha', strtolower( $pc ), $postCode );
			$y = str_replace( 'HA', $pc, $postCode );
			$cn = $counties[$pc];
			$x = str_replace( 'middx', $cn, $x );
			$x = str_replace( 'Middx', $cn, $x );
		} 
else {
			$x = str_replace( 'middx', 'hants', $x );
			$x = str_replace( 'Middx', 'Hants', $x );
		}

		$out = array( 0 => $x, 1 => $y );
		return $out;
	}

	function _correctpostcode($pc) {
		$pcs = array( 'SU', 'GU', 'ME', 'CM', 'PO', 'LU' );
		$elem = array_rand( $pcs );
		$pcUpper = $pcs[$elem];
		$pcLower = strtolower( $pcUpper );
		$x = str_replace( 'ha', $pcLower, $pc );
		$x = str_replace( 'HA', $pcUpper, $pc );
		return $x;
	}

	function _changenumerals($in) {
		$len = strlen( $in );

		if (0 < $len) {
			$a = 1;
		}

		$out = '';
		$elem = 0;

		while ($elem < $len) {
			$char = substr( $in, $elem, 1 );

			if (( '1' <= $char && $char <= '9' )) {
				while (true) {
					$new = rand( 1, 9 );

					if ($new != $char) {
						break;
						continue;
					}
				}

				$char = $new;
			}

			$out .= $char;
			++$elem;
		}

		return $out;
	}

	function _doquery($text, $q) {
		$result = mysql_query( $q );

		if ($result === true) {
			print '' . 'OK ' . $text . '  <br>';

			if (substr( $q, 0, 6 ) == 'INSERT') {
				echo 'insert value was ' . mysql_insert_id(  );
				return null;
			}
		} 
else {
			$err = mysql_error(  );
			print '' . 'FAILED  ' . $text . ' : error was ' . $err . ' <br>';
		}

	}

	function _makecommnames() {
		global $firsts;
		global $seconds;

		$seconds = array( 'Partners', '& Crosby', '& Slingsby', 'Exports', 'Promotions', 'Marketing', 'Enterprises', 'Ventures', 'Estates', 'Industries', 'Development', 'Services', 'Offices', 'Distribution', 'Logistics', '& Capper', 'Consultants', 'Systems', 'Controls', '& Mitford', '&  Masters', '& Stockton', 'Communications', 'Associates', '& Crawley', '& Simmons', '& Crenshaw', '& Smith', '& Renshaw', '& Paterson', '& Hobley', '& Stratford', '& Roberts', '& Rankin', '& Potter', '& Stansfield', '& Trescott' );
		$firsts = array( 'Abbot', 'Abingdon', 'Ashford', 'Beckwith', 'Belwood', 'Bettercare', 'Bradley', 'Central', 'Clovelly', 'Crick', 'Defoe', 'Denton', 'Denwood', 'Devereux', 'Eagleton', 'Ellswood', 'Escombe', 'Everett', 'Fanshawe', 'Feltham', 'Fletcher', 'Framlington', 'Fricker', 'Frimley', 'Gelhorn', 'Gentry', 'Goring', 'Gosford', 'Griffin', 'Hadley', 'Halcombe', 'Hinchcombe', 'Hotchkiss', 'Insight', 'Intrepid', 'Ionian', 'Ivor', 'Jarvis', 'Jedburgh', 'Jenkins', 'Jensen', 'Johnson', 'Kenwood', 'Kilmartin', 'Kingfisher', 'Kirkpatrick', 'Lamartine', 'Lascombe', 'Leet', 'Letwin', 'Mansfield', 'Melbury', 'Mercantile', 'Merton', 'Montgomery ', 'Oakley', 'Orlando', 'Ortago', 'Orville', 'Osterley', 'Petersfield', 'Pinkerton', 'Preston', 'Presbury', 'Quantocks', 'Quartermaine', 'Queensway', 'Quinton', 'Radlett', 'Redway', 'Relton', 'Richards', 'Russell', 'Saunders', 'Shenton', 'Sherwood', 'Singleton', 'Skeffington', 'Stockley', 'Templeton', 'Thorpe', 'Travis', 'Trimble', 'Underwood', 'Unicorn', 'Unique', 'Unwin', 'Village', 'Vincent', 'Vitali', 'Vosper', 'Welldon', 'Welstead', 'Winscombe', 'Winterton', 'Xavier', 'York', 'Youens', 'Young', 'Zander', 'Zentrum', 'Zephyr', 'Zoller', 'Simpson', 'Lassiter', 'Lomax', 'Stansfield', 'Letterman', 'Rosewood', 'Rafferty', 'Moss', 'Ancaster', 'Hardcastle', 'Swift', 'Eversholt', 'Edwardsv', 'Scrope', 'Tenby', 'Wells', 'Wriston', 'Anscombe', 'Horndean', 'Farringdon', 'Updike', 'Melchett', 'Myers', 'Minter', 'Vassell', 'Vickery', 'Naseby', 'Niven', 'Nixon', 'Holbrook', 'Norrington', 'Nash', 'Torrance', 'Plath' );
	}

	function _makeretailnames() {
		global $retailFirst;
		global $retailLast;

		$retailFirst = array( 'Andrew' => 'M', 'Mark' => 'M', 'John' => 'M', 'Arthur' => 'M', 'James' => 'M', 'Michael' => 'M', 'David' => 'M', 'Duncan' => 'M', 'Daniel' => 'M', 'Peter' => 'M', 'Matthew' => 'M', 'Ian' => 'M', 'Edward' => 'M', 'Harold' => 'M', 'Patrick' => 'M', 'Steven' => 'M', 'Toby' => 'M', 'Lester' => 'M', 'Leonard' => 'M', 'Paul' => 'M', 'Victor' => 'M', 'Robert' => 'M', 'Vincent' => 'M', 'Graham' => 'M', 'Alistair' => 'M', 'Charles' => 'M', 'Francis' => 'M', 'Neil' => 'M', 'Sophie' => 'F', 'Tanya' => 'F', 'Mary' => 'F', 'Elizabeth' => 'F', 'Helen' => 'F', 'Anne' => 'F', 'Jane' => 'F', 'Alice' => 'F', 'Lucy' => 'F', 'Emma' => 'F', 'Annabel' => 'F', 'Esther' => 'F', 'Susan' => 'F', 'June' => 'F', 'Joanne' => 'F', 'Rachel' => 'F', 'Bridget' => 'F', 'Nicole' => 'F', 'Catherine' => 'F', 'Natasha' => 'F', 'Nerys' => 'F' );
		$retailLast = array( 'Ashton', 'Adams', 'Appleby', 'Andrews', 'Astor', 'Atkinson', 'Anscombe', 'Ash', 'Ahmed', 'Attwood', 'Austin', 'Arnold', 'Arnott', 'Banham', 'Balcombe', 'Bentley', 'Barnwell', 'Barr', 'Bartlett', 'Biddle', 'Black', 'Birk', 'Brown', 'Bryan', 'Buckle', 'Burnell', 'Brent', 'Cater', 'Cosworth', 'Carter', 'Catt', 'Castleman', 'Corbin', 'Cripps', 'Capel', 'Caplan', 'Clough', 'Clifton', 'Cobb', 'Coleman', 'Crowe', 'Dempsey', 'Dent', 'Dewhirst', 'Drew', 'Duncan', 'Dingwall', 'Dodd', 'Donnelly', 'Dresser', 'Ducreux', 'Diplock', 'Davies', 'Dart', 'Daniels', 'Eversholt', 'Edwards', 'Enscombe', 'Eastwood', 'Egerton', 'Eccles', 'Eddington', 'Elsworthy', 'Edmonds', 'Everett', 'Everard', 'Emmerson', 'Enderby', 'Fowler', 'Finch', 'Forster', 'Fenwick', 'Flynn', 'Francis', 'Forth', 'Ferdinand', 'Ferguson', 'Ford', 'Fox', 'Frith', 'Fuller', 'Fry', 'Fulton', 'Fredricks', 'Frew', 'Gerard', 'Gidman', 'Gibson', 'Gilbert', 'Geffen', 'Geddes', 'Gates', 'Gleeson', 'Golding', 'Grantham', 'Graves', 'Green', 'Grey', 'Greer', 'Greville', 'Groves', 'Gulliver', 'Gunner', 'Gormley', 'Hardcastle', 'Hanson', 'Hoover', 'Hardy', 'Harkness', 'Hoskins', 'Hayes', 'Hepburn', 'Hindle', 'Hewitt', 'Healey', 'Hayward', 'Heller', 'Hine', 'Hodges', 'Hogan', 'Hollis', 'Howlett', 'Holmes', 'Irwin', 'Isherwood', 'Ivanov', 'Ironside', 'Ince', 'Ingram', 'Inglis', 'Innes', 'Irons', 'Ingham', 'Ingoldsby', 'Jeffrey', 'Jonas', 'Jackman', 'Jerome', 'Jennings', 'Jobson', 'James', 'Jessup', 'Jenson', 'Jewell', 'Jebb', 'Janner', 'Johnson', 'Joens', 'Jordan', 'Joseph', 'Kirk', 'Kenton', 'Keith', 'Kay', 'Kerr', 'Kerry', 'Keyes', 'Kirby', 'Khan', 'Kilmer', 'Kilroy', 'Knight', 'Knowles', 'Knightley', 'Kidder', 'Kwok', 'Kumari', 'Lightfoot', 'Luscombe', 'Langford', 'Langtry', 'Leech', 'Lee', 'Leslie', 'Lerner', 'Leonard', 'Lewin', 'Lawrence', 'Lewis', 'Langley', 'Larkin', 'Lamont', 'Larner', 'Langdon', 'Lancaster', 'Lampert', 'Lau', 'Mansfield', 'Macdonald', 'Macbeth', 'Mutley', 'Macleod', 'Morpeth', 'Mann', 'Manners', 'Meeks', 'Mayhew', 'Marsh', 'Marks', 'Marshall', 'Martell', 'Midgley', 'Merton', 'Motram', 'Middleton', 'Michaels', 'Messiter', 'Meredith', 'Miller', 'Norton', 'Nesbitt', 'Nichols', 'Newell', 'Newman', 'Nolan', 'Nightingale', 'Nielsen', 'Norris', 'North', 'Nottage', 'Niles', 'Nimmo', 'Nixon', 'Noble', 'Noakes', 'Overhill', 'Oakes', 'Oswald', 'Oliver', 'Omar', 'Orridge', 'Osbourne', 'Osgood', 'Olivier', 'Overton', 'Owen', 'Oulton', 'Osman', 'Ovendon', 'Osterley', 'Ottinger', 'Oxley', 'Peters', 'Page', 'Padman', 'Pakham', 'Painter', 'Pacey', 'Parker', 'Parnell', 'Parr', 'Partridge', 'Partington', 'Peyton', 'Paterson', 'Peake', 'Paine', 'Penfold', 'Pemberton', 'Peppitt', 'Perry', 'Plany', 'Platt', 'Packard', 'Porter', 'Pond', 'Poole', 'Pope', 'Quinton', 'Quigley', 'Quaid', 'Quilley', 'Quartey', 'Quinn', 'Quell', 'Quimper', 'Redgrave', 'Ricketts', 'Robinson', 'Ratcliffe', 'Richards', 'Rice', 'Reece', 'Rawlings', 'Ray', 'Reid', 'Regan', 'Rees', 'Redman', 'Rochester', 'Rodgers', 'Rodman', 'Roy', 'Rowley', 'Ruddock', 'Rudge', 'Ross', 'Rosen', 'Rooney', 'Rodwell', 'Rook', 'Rushton', 'Ruffle', 'Sandman', 'Said', 'Sacks', 'Salter', 'Schroder', 'Savage', 'Sanderson', 'Sellwood', 'Self', 'Selway', 'Seymour', 'Shah', 'Sheldon', 'Sopwith', 'Shepherd', 'Sheriff', 'Silver', 'Simpson', 'Sinclair', 'Singer', 'Stack', 'Stanford', 'Skinner', 'Sloane', 'Smith', 'Speller', 'Spence', 'Squire', 'Southgate', 'Southwood', 'Stanton', 'Standish', 'Staples', 'Stewart', 'Stephens', 'Taylor', 'Tang', 'Tanner', 'Tait', 'Tapster', 'Tasker', 'Todd', 'Tobin', 'Tomkinson', 'Thomas', 'Teves', 'Tetley', 'Tell', 'Temple', 'Thorpe', 'Torrance', 'Turnbull', 'Thomson', 'Tyrrell', 'Trenton', 'Tyson', 'Tunstall', 'Turton', 'Thursley', 'Twiss', 'Underwood', 'Ulla', 'Usher', 'Urquhart', 'Uppingham', 'Ulrich', 'Urban', 'Uddin', 'Unwin', 'Uppard', 'Unscombe', 'Usner', 'Urse', 'Vincent', 'Vermont', 'Vishram', 'Vine', 'Virani', 'Vernal', 'Vasile', 'Varsani', 'Valentine', 'Vaney', 'Vyas', 'Vong', 'Vidot', 'Vardy', 'Vibaud', 'Vickery', 'Venn', 'Walter', 'Wickes', 'Wendell', 'Wagoner', 'White', 'Welstead', 'Weir', 'Webb', 'Webster', 'Watson', 'Wang', 'Ward', 'Walsh', 'Wells', 'Whitaker', 'Wheeler', 'Wickham', 'Whitfield', 'West', 'Weston', 'Wilcox', 'Wynn', 'Wright', 'Williams', 'Wilson', 'Woods', 'Wren', 'Woodrow', 'Winters', 'Wolf', 'Xavier', 'Xia', 'Xhaferi', 'Yates', 'Yamachi', 'Yusuf', 'Young', 'Yorke', 'Yong', 'Yallop', 'Yardley', 'Yarkhill', 'Yang', 'Yoffey', 'Yeates', 'Yassin', 'Zacharias', 'Zafar', 'Zander', 'Zadran', 'Zalgat', 'Zazau', 'Zhang', 'Zergat', 'Zuman', 'Zarrins', 'Ziderman', 'Zinia', 'Zaman', 'Zamir' );
	}

	function _maketowns() {
		global $towns;
		global $counties;

		$towns = array( 'SN' => 'Swindon', 'GU' => 'Guildford', 'ME' => 'Maidstone', 'CM' => 'Chelmsford', 'PO' => 'Portsmouth', 'LU' => 'Luton' );
		$counties = array( 'SN' => 'Wilts', 'GU' => 'Surrey', 'ME' => 'Kent', 'CM' => 'Essex', 'PO' => 'Hants.', 'LU' => 'Beds.' );
	}

	require_once( '../include/startup.php' );
	_makecommnames(  );
	_makeretailnames(  );
	_maketowns(  );
	_updateusers(  );
	_processclients(  );
	_processpolicies(  );
	echo 'DONE';
?>