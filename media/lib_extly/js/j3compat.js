/**
 * @package Extly.Library
 * @subpackage lib_extly - Extly Framework
 * 
 * @author Prieco S.A. <support@extly.com>
 * @copyright Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link http://www.extly.com http://support.extly.com
 */

define('j3compat', [], function() {
	
	// Ported from Joomla 3
	var selects = jQuery('select');
	
	if (selects.chosen)
	{
		selects.chosen({
			disable_search_threshold : 10,
			allow_single_deselect : true
		});
	}

	// Ported from Joomla 3 . Isis
	jQuery('*[rel=tooltip]').tooltip();

	// Ported from Joomla 3 - Isis
	jQuery('.dropdown-toggle').dropdown();
	jQuery('.collapse').collapse('show');
	jQuery('#myModal').modal('hide');
	jQuery('.typeahead').typeahead();
	jQuery('.tabs').button();
	jQuery('.tip').tooltip();
	jQuery(".alert-message").alert();
	
});