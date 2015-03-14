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

var FbChValidationView = Backbone.View.extend({
	events : {
		'click #fbchvalidationbutton' : 'onValidationReq'
	},

	initialize : function() {
		var view = this;

		this.collection.on('add', this.loadvalidation, this);

		this.$el.ajaxStart(function() {
			view.$(".loaderspinner72").addClass('loading72');
		}).ajaxStop(function() {
			view.$(".loaderspinner72").removeClass('loading72');
		});
	},

	onValidationReq : function() {
		var view = this,
			params = appParamsHelper.get(view),
			fbchannel_access_token = this.$('#fbchannel_access_token').val();

		this.collection.create(this.collection.model, {
			attrs : {
				own_app : params.p_own_app,
				app_id : params.p_app_id,
				secret : params.p_secret,
				access_token : params.p_access_token,
				token : params.p_token,
				fbchannel_access_token : fbchannel_access_token
			},

			wait : true,
			dataType:     'text',
			error : function(model, fail, xhr) {
				validationHelper.showError(view, fail.responseText);
			}
		});
	},

	loadvalidation : function(resp) {
		var status = resp.get('status'),
			error_message = resp.get('error_message'),
			tokenInfo = resp.get('tokenInfo'),
			issued_at = tokenInfo.issued_at,
			expires_at = tokenInfo.expires_at;

		if (status) {
			this.$('#channel_issued_at').val(issued_at);
			this.$('#channel_expires_at').val(expires_at);
		} else {
			this.$('#channel_issued_at').val(error_message);
			this.$('#channel_expires_at').val('');
		}
	}

});
