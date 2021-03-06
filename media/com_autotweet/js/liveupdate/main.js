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

define('liveupdate', [ 'extlycore' ],
		function(Core) {
	"use strict";

	var liveUpdateView = null, updateNotice;

	/* BEGIN - variables to be inserted here */

	/* END - variables to be inserted here */

	updateNotice = jQuery('#updateNotice');

	if (updateNotice.size())
	{
		liveUpdateView = new LiveUpdateView({
			el : jQuery('#adminForm'),
			collection : new Updates()
		});
	}
	else
	{
		updateNotice = jQuery('#fullUpdateNotice');

		if (updateNotice.size())
		{
			liveUpdateView = new FullLiveUpdateView({
				el : jQuery('#adminForm'),
				collection : new Updates()
			});
		}
	}

	return liveUpdateView;

});
