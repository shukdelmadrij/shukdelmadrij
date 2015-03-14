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

var LiValidationView = Backbone.View.extend({
	events : {
		'click #livalidationbutton' : 'onValidationReq'
	},

	initialize : function() {
		this.collection.on('add', this.loadvalidation, this);
	},

	onValidationReq : function onValidationReq() {
		var view = this,

			api_key = view.$('#api_key').val().trim(),
			secret_key = view.$('#secret_key').val().trim(),
			oauth_user_token = view.$('#oauth_user_token').val().trim(),
			oauth_user_secret = view.$('#oauth_user_secret').val().trim(),

			token = view.$('#XTtoken').attr('name');

		view.$('#api_key').val(api_key);
		view.$('#secret_key').val(secret_key);
		view.$('#oauth_user_token').val(oauth_user_token);
		view.$('#oauth_user_secret').val(oauth_user_secret);

		view.$(".loaderspinner").addClass('loading');

		this.collection.create(this.collection.model, {
			attrs : {
				api_key : api_key,
				secret_key : secret_key,
				oauth_user_token : oauth_user_token,
				oauth_user_secret : oauth_user_secret,
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
			icon = resp.get('icon'),
			url = resp.get('url');

		this.$(".loaderspinner").removeClass('loading');

		if (status) {
			validationHelper.showSuccess(this, user.id, icon, url);
		} else {
			validationHelper.showError(this, error_message);
		}
	}

});
