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

var LiGroupView = Backbone.View
		.extend({

			events : {
				'click #ligrouploadbutton' : 'onChangeChannel',
				'change #xtformgroup_id' : 'onChangeGroup'
			},

			initialize : function() {
				this.collection.on('add', this.loadLiGroup, this);
				this.ligrouplist = '#xtformgroup_id';
				this.$('.group-warn').fadeOut();
			},

			onChangeChannel : function() {
				var thisView = this,
					params = appParamsHelper.getLi(thisView);

				Core.UiHelper.listReset(thisView.$(this.ligrouplist));

				this.collection.create(this.collection.model, {
					attrs : {
						api_key : params.p_api_key,
						secret_key : params.p_secret_key,
						oauth_user_token : params.p_oauth_user_token,
						oauth_user_secret : params.p_oauth_user_secret,
						token : params.p_token
					},

					wait : true,
					dataType:     'text',
					error : function(model, fail, xhr) {
						validationHelper.showError(this, fail.responseText);
					}
				});
			},

			loadLiGroup : function(message) {
				var ligrouplist = this.$(this.ligrouplist), channels, socialIcon, first = true;

				ligrouplist.empty();
				if (message.get('status')) {
					channels = message.get('channels');
					socialIcon = message.get('icon');

					_.each(channels, function(channel) {
						var opt = new Option();
						opt.value = channel.id;
						opt.text = channel.name;

						jQuery(opt)
							.attr('social_icon', socialIcon)
							.attr('social_url', channel.url);

						if (first) {
							first = false;
							opt.selected = 'selected';
						}

						ligrouplist.append(opt);
					});

					this.onChangeGroup();
					validationHelper.showSuccess(this, '');

					ligrouplist.trigger('liszt:updated');
				} else {
					validationHelper.showError(this, message.get('error_message'));
				}

			},

			onChangeGroup : function() {
				var oselected = this.$('#xtformgroup_id option:selected'),
					socialIcon = oselected.attr('social_icon'),
					socialUrl = oselected.attr('social_url');

				validationHelper.assignSocialUrl(this, 'social_url_ligroup', socialIcon, socialUrl);
			}
		});
