<?php
defined( '_JEXEC' ) or die( 'Restricted access' ); //Check if we are being called from within a Joomla! session.  Die if not.
/* 
* Utility function to recursively work through a vertically indented
* hierarchial menu
*/
function mosRecurseListMenu( $id, $level, &$children, $open, &$indents, $class_sfx, $highlight ) {
	global $Itemid;
	global $HTTP_SERVER_VARS;
	if (@$children[$id]) { //@ supresses any error messages associated with processing array $children
		$n = min( $level, count( $indents )-1 );
		if ($level==0) echo '<ul class="nav" id="horiznav">';
		else echo $indents[$n][0];
		foreach ($children[$id] as $row) {
		    switch ($row->type) {
				case 'separator':
					// do nothing
					$row->link = "seperator";
					break;
				case 'url':
					//JJY replaced eregi = depricated case insensitive regular expression match
					//with preg_match
					if ( preg_match( '/index.php\?/i', $row->link ) ) {
						if ( !preg_match( '/Itemid=/i', $row->link ) ) {
							$row->link .= '&Itemid='. $row->id;
						}
					}
					break;
				default:
					$row->link .= "&Itemid=$row->id";
				break;
          	}
			
            $li =  "\n".$indents[$n][1] ;
            $current_itemid = trim( JRequest::getVar( 'Itemid', 0 ) );
            if ($row->link != "seperator" &&
				$current_itemid == $row->id || 
            	$row->id == $highlight || 
                (JRoute::_( substr($_SERVER['PHP_SELF'],0,-9) . $row->link)) == $_SERVER['REQUEST_URI'] ||
                (JRoute::_( substr($_SERVER['PHP_SELF'],0,-9) . $row->link)) == $HTTP_SERVER_VARS['REQUEST_URI']) {
					$li = "<li class=\"active\">";
			}
	        echo $li;
								
            echo mosGetLink( $row, $level, $class_sfx );
						mosRecurseListMenu( $row->id, $level+1, $children, $open, $indents, $class_sfx, "" );
            echo $indents[$n][2];

        }//end foreach
		echo "\n".$indents[$n][3];

	}
}


/*
* Utility function to return parent row if it exists.  Returns -1 otherwise.
*/
function getTheParentRow($rows, $id) {
		if (isset($rows[$id]) && $rows[$id]) {
			if($rows[$id]->parent > 0) {
				return $rows[$id]->parent;
			}	
		}
		return -1;
}


/*
* Utility function for writing a menu link
*/
function mosGetLink( $mitem, $level, $class_sfx='' ) {
	global $Itemid;
	$txt = '';
	$menuclass = '';
	$topdaddy = 'top';
	JRoute::_('$mitem->link'); //Use JRoute to convert link to a Search Engine Friendly (SEF) link -- make the URL absolute
	//Case insensitive string compare.  If link does not have "http" as its first 4 characters then use JRoute to ensure it is SEF
	if (strcasecmp(substr($mitem->link,0,4),"http")) { 
		$mitem->link = JRoute::_($mitem->link);
	}
    switch ($mitem->browserNav) {
	    // cases are slightly different
		case 1:
			// open in a new window
			if ($mitem->cnt > 0) {
				if ($level == 0) {
					$txt = "<a class=\"topdaddy\" target=\"_window\"  href=\"$mitem->link\">$mitem->name</a>";
					$topdaddy = "topdaddy";
				} else {
					$txt = "<a class=\"daddy\" target=\"_window\"  href=\"$mitem->link\">$mitem->name</a>";
				}
			} else {
				$txt = "<a href=\"$mitem->link\" target=\"_window\" >$mitem->name</a>\n";
			}
			break;

		case 2:
			// open in a popup window
			if ($mitem->cnt > 0) {
				if ($level == 0) {
                $txt = "<a href=\"#\" class=\"topdaddy\" onClick=\"javascript: window.open('$mitem->link', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550');\" class=\"$menuclass\">$mitem->name</a>\n";
		   	    $topdaddy = "topdaddy";
		        } else {
                    $txt = "<a href=\"#\" class=\"daddy\" onClick=\"javascript: window.open('$mitem->link', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550');\" class=\"$menuclass\">$mitem->name</a>\n";
		        }
			} else {
				$txt = "<a href=\"#\" onClick=\"javascript: window.open('$mitem->link', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550');\" class=\"$menuclass\">$mitem->name</a>\n";
			}
			break;

		case 3:
			// don't link it
			if ($mitem->cnt > 0) {
				if ($level == 0) {
					$txt = "<a class=\"topdaddy\">$mitem->name</a>";
					$topdaddy = "topdaddy";
				} else {
					$txt = "<a class=\"daddy\">$mitem->name</a>";
				}
			} else {
				$txt = "<a>$mitem->name</a>\n";
			}
			break;
		
		default:
			// open in parent window
			if (isset($mitem->cnt) && $mitem->cnt > 0) {
				if ($level == 0) {
					$txt = "<a class=\"topdaddy\" href=\"$mitem->link\">$mitem->name</a>";
					$topdaddy = "topdaddy";
				} else {
					$txt = "<a class=\"daddy\" href=\"$mitem->link\"> $mitem->name</a>";
				}
			} else {
				$txt = "<a href=\"$mitem->link\">$mitem->name</a>";
			}
			break;
	}
    return "<span class=\"" . $topdaddy . "\">" . $txt . "</span>";
}


//Set the value of configuration option "arg_separator.output" to "&amp" for the rest of this PHP script
ini_set('arg_separator.output','&amp;');
function mosShowListMenu($menutype) {
	global $mainframe, $Itemid;
	$database = JFactory::getDBO();  //Get a  reference to the global JDatabase object -- only create it if it doesn't already exist  
    $user = JFactory::getUser();  //Get a reference to the global JUser object -- only create it if it doesn't already exist
	$class_sfx = null;
	error_reporting(E_ALL);  //Report ALL PHP errors
	$hilightid = null;
    if ($mainframe->getCfg('shownoauth')) { //If Joomla's "shownoauth" configuration value is set then...
     $sql = ("SELECT m.*, count(p.parent) as cnt" .
	"\nFROM #__menu AS m" .
	"\nLEFT JOIN #__menu AS p ON p.parent = m.id" .
    "\nWHERE m.menutype='$menutype' AND m.published='1'" .
	"\nGROUP BY m.id ORDER BY m.parent, m.ordering ");
    } else {
     $sql = ("SELECT m.*, sum(case when p.published=1 then 1 else 0 end) as cnt" .
	"\nFROM #__menu AS m" .
	"\nLEFT JOIN #__menu AS p ON p.parent = m.id" .
    "\nWHERE m.menutype='$menutype' AND m.published='1' AND m.access <= " . $user->get('gid') .      // Picks up the access-id
	"\nGROUP BY m.id ORDER BY m.parent, m.ordering ");
    }
    $database->setQuery($sql);  //Set the SQL query string
	$rows = $database->loadObjectList( 'id' );  //Load a list of database objects as per our query.  Returned array indexed by 'id'
	echo $database->getErrorMsg();  //Get the error message for the query
		$sql = "SELECT m.* FROM #__menu AS m"
		. "\nWHERE menutype='". $menutype ."' AND m.published='1'"; 
		$database->setQuery( $sql );  //Set the SQL query string
		$subrows = $database->loadObjectList( 'id' );  //Load a list of database objects as per our query.  Returned array indexed by 'id'
		$maxrecurse = 5;
		$parentid = $Itemid;
		
		//this makes sure toplevel stays hilighted when submenu active
		while ($maxrecurse-- > 0) {
			$parentid = getTheParentRow($subrows, $parentid);
			if (isset($parentid) && $parentid >= 0 && $subrows[$parentid]) {
				$hilightid = $parentid;
			} else {
				break;	
			}
		}	
	$indents = array(
	array( "<ul>", "<li>" , "</li>", "</ul>" ),
	);
	
	// establish the hierarchy of the menu
	$children = array();
	
	// first pass - collect children
    foreach ($rows as $v ) { //loop through the array $rows and assign $v the current value
		$pt = $v->parent; //Assign $v's parent value to $pt
		
		//If array member $children[$pt] exists then assign it to $list; otherwise create a new empty array called $list. 
		//Note: the @ supresses any error messages that may be generated with regard to the $children array.
		$list = @$children[$pt] ? $children[$pt] : array();  
		array_push( $list, $v ); //Push $v onto the end of array $list

        $children[$pt] = $list;
    }
	
	// second pass - collect 'open' menus
	$open = array( $Itemid );
	$count = 20; // maximum levels - to prevent runaway loop
	$id = $Itemid;
	while (--$count) {
		if (isset($rows[$id]) && $rows[$id]->parent > 0) {
			$id = $rows[$id]->parent;
			$open[] = $id;
		} else {
			break;
		}
	}

	$class_sfx = null;

    mosRecurseListMenu( 0, 0, $children, $open, $indents, $class_sfx, $hilightid );
}
?>



