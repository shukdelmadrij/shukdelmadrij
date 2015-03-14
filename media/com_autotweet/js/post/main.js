/**
 * @package Extly.Components
 * @subpackage com_autotweet - AutoTweet posts content to social channels
 *             (Twitter, Facebook, LinkedIn, etc).
 *
 * @author Prieco S.A.
 * @copyright Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link http://www.extly.com http://support.extly.com
 */

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

define('post', [], function() {
	"use strict";

	/* BEGIN - variables to be inserted here */

	/* END - variables to be inserted here */

	// Image
	window.jInsertFieldValue = function jInsertFieldValue(value, id) {
		jQuery('#' + id).val(value);
	};

	// User
	window.jSelectUser_author = function jSelectUser_author(id, username) {
		jQuery('#author').val(id);
		jQuery('#author_username').val(username);
		try {
			jQuery('#sbox-window').close();
		} catch (err) {
			SqueezeBox.close();
		}
	};

	var postView = new PostView({
		el : jQuery('#adminForm')
	});

	return postView;

});