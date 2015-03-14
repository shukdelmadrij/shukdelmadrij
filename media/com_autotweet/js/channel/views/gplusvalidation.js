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

var GplusValidationView = Backbone.View.extend({
	events : {
		'click #gplusvalidationbutton' : 'onValidationReq'
	},

	initialize : function() {
		this.collection.on('add', this.loadvalidation, this);
	},

	onValidationReq : function() {
		var view = this,

			channel_id = view.$('#channel_id').val().trim(),
			client_secret = view.$('#client_secret').val().trim(),
			developer_key = view.$('#developer_key').val().trim(),

			token = view.$('#XTtoken').attr('name');

		view.$('#channel_id').val(channel_id);
		view.$('#client_secret').val(client_secret);
		view.$('#developer_key').val(developer_key);

		view.$(".loaderspinner").addClass('loading');

		this.collection.create(this.collection.model, {
			attrs : {
				channel_id : channel_id,
				token : token
			},

			wait : true,
			dataType:     'text',
			error : function(model, fail, xhr) {
				view.$(".loaderspinner").removeClass('loading');
				validationHelper.showError(view, fail.responseText);
			}
		});
	},

	loadvalidation : function loadvalidation(resp) {
		var status = resp.get('status'),
			error_message = resp.get('error_message'),
			user = resp.get('user'),
			socialIcon = resp.get('social_icon'),
			socialUrl = resp.get('social_url');

		this.$(".loaderspinner").removeClass('loading');

		if (status) {
			validationHelper.showSuccess(this, user.id, socialIcon, socialUrl);
		} else {
			validationHelper.showError(this, error_message);
		}
	}

});
