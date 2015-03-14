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

var PostView = Backbone.View.extend({

	events : {
		'change #plugin' : 'onChangePlugin'
	},

	initialize : function() {
		this.overrideConditionsTab = this.$('#overrideconditions-tab');
		this.overrideconditions = this.$('#override-conditions');
		this.filterConditionsTab = this.$('#filterconditions-tab');
		this.auditInfoTab = this.$('#auditinfo-tab');

		/*
		this.createEventTab = this.$('#createevent-tab');
		this.createEvent = this.$('#createevent');
		*/

		// Activate Tabs
		this.$('#qTypeTabs a[data-toggle=tab]').first().tab();

		this.onChangePlugin();
		// this.onChangeCreateEvent();
	},

	onChangePlugin: function() {
		var plugin = this.$('#plugin').val();

		if (plugin == 'autotweetpost')
		{
			this.overrideConditionsTab.fadeIn(0);
			this.overrideConditionsTab.find('a').tab('show');
		}
		else
		{
			this.overrideConditionsTab.fadeOut(0);
			this.auditInfoTab.find('a').tab('show');
		}
	}

	/*
	onChangeCreateEvent: function() {
		var create_event = this.$('#create_event').val();

		if (create_event == '1')
		{
			this.createEventTab.fadeIn(0);
			this.createEvent.fadeIn(0);
		}
		else
		{
			this.createEventTab.fadeOut(0);
			this.createEvent.fadeOut(0);
		}
	}
	*/

});
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