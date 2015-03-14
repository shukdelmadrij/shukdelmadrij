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

define('channel', [ 'extlycore' ], function(Core) {
	"use strict";

	/* BEGIN - variables to be inserted here */


	/* END - variables to be inserted here */

	(new ChannelView({
		el : jQuery('#adminForm'),
		collection : new Channels()
	})).onChangeChannelType();

	var twValidationView = new TwValidationView({
		el : jQuery('#adminForm'),
		collection : new TwValidations()
	});

	var liValidationView = new LiValidationView({
		el : jQuery('#adminForm'),
		collection : new LiValidations()
	});

	var eventsDispatcher = _.clone(Backbone.Events);

	var fbValidationView = new FbValidationView({
		el : jQuery('#adminForm'),
		collection : new FbValidations(),
		attributes : {dispatcher : eventsDispatcher}
	});

	var fbChannelView = new FbChannelView({
		el : jQuery('#adminForm'),
		collection : new FbChannels(),
		attributes : {
			dispatcher : eventsDispatcher,
			messagesview : fbValidationView
		}
	});

	var fbAlbumView = new FbAlbumView({
		el : jQuery('#adminForm'),
		collection : new FbAlbums(),
		fbChannelView : fbChannelView
	});

	var fbChValidationView = new FbChValidationView({
		el : jQuery('#adminForm'),
		collection : new FbChValidations()
	});

	var fbExtendView = new FbExtendView({
		el : jQuery('#adminForm'),
		collection : new FbExtends(),
		attributes : {dispatcher : eventsDispatcher}
	});

	var gplusValidationView = new GplusValidationView({
		el : jQuery('#adminForm'),
		collection : new GplusValidations()
	});

	var liGroupView = new LiGroupView({
		el : jQuery('#adminForm'),
		collection : new LiGroups()
	});

	var liCompanyView = new LiCompanyView({
		el : jQuery('#adminForm'),
		collection : new LiCompanies()
	});

	var vkValidationView = new VkValidationView({
		el : jQuery('#adminForm'),
		collection : new VkValidations()
	});

	var vkGroupView = new VkGroupView({
		el : jQuery('#adminForm'),
		collection : new VkGroups()
	});

	window.xtAppDispatcher = eventsDispatcher;

});
