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

var FbValidationView = Backbone.View.extend({
	events : {
		'click #authextendbutton' : 'onExtendReq',
		'click #authbutton' : 'onAuthorizationReq',
		'click #fbvalidationbutton' : 'onValidationReq'
	},

	initialize : function() {
		var view = this;

		this.attributes.dispatcher.on('change:use_own_api',
				this.onChangeOwnApi, this);
		this.attributes.dispatcher.on('change:og_features',
				this.onChangeOgFeatures, this);

		this.collection.on('add', this.loadvalidation, this);

		this.$el.ajaxStart(function() {
			view.$(".loaderspinner72").addClass('loading72');
		}).ajaxStop(function() {
			view.$(".loaderspinner72").removeClass('loading72');
		});

		this.onChangeOwnApi();
	},

	onChangeOwnApi : function(e) {
		var ownApp = this.$('#use_own_api').val(),

			// No or Yes, with Canvas Page
			authorizeCanvas = (ownApp != '2');

		if (authorizeCanvas)
		{
			this.$('#canvas_page').addClass('required').addClass('validate-facebookapp');

			this.$('#authextendbutton').fadeOut(0);
			this.$('#authbutton').fadeIn(0);

			this.$('#fbextendbutton').fadeOut(0);
			this.$('#fbvalidationbutton').fadeIn(0);
		}
		else
		{
			this.$('#canvas_page').removeClass('required').removeClass('validate-facebookapp');

			this.$('#authextendbutton').fadeIn(0);
			this.$('#authbutton').fadeOut(0);

			this.$('#fbextendbutton').fadeIn(0);
			this.$('#fbvalidationbutton').fadeOut(0);
		}

		// No
		if (ownApp === '0') {
			this.$('#own-app-testing').fadeIn();
			this.$('#own-app-details').fadeOut();

			this.$('#app_id').removeClass('required');
			this.$('#secret').removeClass('required');
			this.$('#canvas_page').removeClass('required');

		// Yes, with Canvas Page
		} else if (ownApp === '1') {
			this.$('#own-app-testing').fadeOut();
			this.$('#own-app-details-canvas-page').fadeIn(0);
			this.$('#own-app-details').fadeIn();

			this.$('#app_id').addClass('required');
			this.$('#secret').addClass('required');
			this.$('#canvas_page').addClass('required');
		// Yes (no Canvas Page)
		} else {
			this.$('#own-app-testing').fadeOut();
			this.$('#own-app-details-canvas-page').fadeOut(0);
			this.$('#own-app-details').fadeIn();

			this.$('#app_id').addClass('required');
			this.$('#secret').addClass('required');
			this.$('#canvas_page').removeClass('required');
		}
	},

	onChangeOgFeatures: function() {
		var og_features = this.$('#og_features').val();

		if (og_features === '1') {
			this.$('#og-fields').fadeIn();
		} else {
			this.$('#og-fields').fadeOut();
		}
	},

	onAuthorizationReq : function(e) {
		var params = appParamsHelper.get(this);

		e.preventDefault();

		var canvas_page = params.p_canvas_page + '?' + params.p_url_params;
		if (canvas_page.match(/apps.facebook.com/)) {
			window.open(canvas_page);
		} else {
			alert('Invalid Canvas Page. It must be http://apps.facebook.com/...');
		}
	},

	onExtendReq : function(e) {
		if (e) {
			e.preventDefault();
		}

		if (window.fbAsyncInit) {
			this.fbAssignToken();
		} else {
			this.fbInit();
		}
	},

	fbInit: function() {
		var view = this,
			params = appParamsHelper.get(this);

		window.fbAsyncInit = function() {

			// init the FB JS SDK
			FB.init({
				appId : params.p_app_id,
				version : 'v2.0',
				status : true,
				xfbml : false
			});

			view.fbAssignToken();
		};

		// Load the SDK asynchronously
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {
				return;
			}
			js = d.createElement(s);
			js.id = id;

			js.src = "//connect.facebook.com/en_US/sdk.js";

			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	},

	fbAssignToken: function() {
		var view = this;

		FB.login(
				function(response) {
					if (response.authResponse) {
						FB.getLoginStatus(
							function(response) {
								var accessToken;

								if (response.status === 'connected') {
									accessToken = response.authResponse.accessToken;
									view.$('#access_token').val(accessToken);
								} else {
									view.onExtendReq();
								}
							}
						);
					} else {
						alert('User cancelled login or did not fully authorize.');
					}
				},
			{
				scope : 'public_profile,manage_pages,publish_actions,user_events,user_groups,user_photos,user_videos'
			});
	},

	onValidationReq : function() {
		var view = this, params = appParamsHelper.get(view);

		this.collection.create(this.collection.model, {
			attrs : {
				own_app : params.p_own_app,
				app_id : params.p_app_id,
				secret : params.p_secret,
				access_token : params.p_access_token,
				token : params.p_token
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
			user,
			tokenInfo,
			issued_at,
			expires_at;

		if (status) {
			user = resp.get('user');
			tokenInfo = resp.get('tokenInfo');
			issued_at = tokenInfo.issued_at;
			expires_at = tokenInfo.expires_at;

			validationHelper.showSuccess(this, user.id);

			this.$('#issued_at').val(issued_at);
			this.$('#expires_at').val(expires_at);

			this.attributes.dispatcher.trigger("fbapp:channelschanged");
		} else {
			validationHelper.showError(this, error_message);
		}
	}

});
