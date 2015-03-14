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

var Update = Core.ExtlyModel.extend({
	url : function() {
		return Core.SefHelper.route('index.php?option=com_autotweet&view=cpanels&task=getUpdateInfo');
	}
});/**
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

var Updates = Backbone.Collection.extend({
	model : Update
});
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

var LiveUpdateView = Backbone.View.extend({

	initialize : function() {
		var view = this;

		this.$updateNotice = this.$("#updateNotice");
		this.collection.on('add', this.loadLiveUpdates, this);

		this.$el.ajaxStart(function() {
			view.$(".loaderspinner72").addClass('loading72');
		}).ajaxStop(function() {
			view.$(".loaderspinner72").removeClass('loading72');
		});

		this.getLiveUpdates();
	},

	getLiveUpdates : function() {
		var view = this;

		this.collection.create(this.collection.model, {
			attrs : {
				'token' : view.$('#XTtoken').attr('name')
			},

			wait : true,
			dataType: 'text',
			error : function(model, fail, xhr) {
				view.$updateNotice.html(fail.responseText);
			}
		});
	},

	loadLiveUpdates : function(resp) {
		var hasUpdate = resp.get('hasUpdate');

		if (hasUpdate) {
			this.$updateNotice.html(resp.get('result'));
		}
	}

});
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
