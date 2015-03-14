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

var ChannelView = Backbone.View.extend({
	events : {
		'change #channeltype_id' : 'onChangeChannelType'
	},

	initialize : function() {
		this.collection.on('add', this.loadchannel, this);
	},

	onChangeChannelType : function onChangeChannelType() {
		var view = this;
		view.$(".loaderspinner").addClass('loading');

		this.collection.create(this.collection.model, {
			attrs : {
				channelId : this.$('#channel_id').val(),
				channelTypeId : this.$('#channeltype_id').val(),
				token : this.$('#XTtoken').attr('name')
			},

			wait : true,
			dataType:     'text',
			success : function(model, resp, options) {
				view.$('#channel_data').html(model.get('message'));
				view.refresh();
			},
			error : function(model, fail, xhr) {
				view.$('#channel_data').html(fail.responseText);
			}
		});
	},

	loadchannel : function loadchannel(paramsform) {
		var msg = paramsform.get('message');
		this.$('#channel_data').html(msg);
		this.refresh();
	},

	refresh: function refresh() {
		// Enable Chosen in selects
		this.$('#channel_data select').chosen({
			disable_search_threshold : 10,
			allow_single_deselect : true
		});

		// Activate Tabs
		this.$('#channel_data .nav-tabs a').tab();
		this.$('#channel_data .nav-tabs a').click(function(e) {
			e.preventDefault();
		});
		this.$('#channel_data .nav-tabs a:first').tab('show');
	},

	submitbutton : function submitbutton(task) {
		var is_valid, domform = this.el;
		if (task === 'channel.cancel') {
			Joomla.submitform(task, domform);
		}
		is_valid = document.formvalidator.isValid(domform);
		if (is_valid) {
			Joomla.submitform(task, domform);
		}
	}

});
