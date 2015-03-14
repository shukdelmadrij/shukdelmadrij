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

var VkGroupView = Backbone.View
		.extend({

			events : {
				'click #vkgrouploadbutton' : 'ongroupsReq',
				'change #xtformvkgroup_id' : 'onChangeGroup'
			},

			initialize : function() {
				this.collection.on('add', this.loadVkGroup, this);
				this.vkgrouplist = '#xtformvkgroup_id';
			},

			ongroupsReq : function () {
				var thisView = this,
					list = thisView.$(this.vkgrouplist),

					channelId = thisView.$('#channel_id').val().trim(),
					channelToken = thisView.$('#access_token').val().trim(),

					token = thisView.$('#XTtoken').attr('name');

				thisView.$('#channel_id').val(channelId);
				thisView.$('#access_token').val(channelToken);

				Core.UiHelper.listReset(list);

				this.collection.create(this.collection.model, {
					attrs : {
						channel_id : channelId,
						access_token : channelToken,
						token : token
					},

					wait : true,
					dataType:     'text',
					error : function(model, fail, xhr) {
						validationHelper.showError(thisView,
								fail.responseText);
					}
				});
			},

			loadVkGroup : function (message) {
				var vkgrouplist = this.$(this.vkgrouplist), groups, socialIcon, first = true;

				vkgrouplist.empty();
				if (message.get('status')) {
					groups = message.get('groups');
					socialIcon = message.get('social_icon');

					_.each(groups, function(group) {
						var opt = new Option();

						opt.value = group.id;
						opt.text = group.name;

						jQuery(opt)
							.attr('social_icon', socialIcon)
							.attr('social_url', group.url);

						if (first) {
							first = false;
							opt.selected = 'selected';
						}

						vkgrouplist.append(opt);
					});

					this.onChangeGroup();

					vkgrouplist.trigger('liszt:updated');
				} else {
					validationHelper.showError(this, message.get('error_message'));
				}

			},

			onChangeGroup : function() {
				var oselected = this.$('#xtformvkgroup_id option:selected'),
					socialIcon = oselected.attr('social_icon'),
					socialUrl = oselected.attr('social_url');

				validationHelper.assignSocialUrl(this, 'social_url_vkgroup', socialIcon, socialUrl);
			}

		});