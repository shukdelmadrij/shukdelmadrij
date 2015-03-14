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

var FbAlbumView = Backbone.View
		.extend({

			events : {
				'click #fbalbumloadbutton' : 'onAlbumsReq'
			},

			initialize : function() {
				this.collection.on('add', this.loadFbAlbum, this);
				this.fbalbumlist = '#xtformfbalbum_id';
			},

			onAlbumsReq : function onAlbumsReq() {
				var thisView = this,
					params = appParamsHelper.get(thisView),
					list = thisView.$(this.fbalbumlist),
					fbChannelView = this.options.fbChannelView,
					channelId = fbChannelView.getFbChannelId(),
					channelToken = fbChannelView.getFbChannelAccessToken();

				Core.UiHelper.listReset(list);

				this.collection.create(this.collection.model, {
					attrs : {
						own_app : params.p_own_app,
						app_id : params.p_app_id,
						secret : params.p_secret,
						access_token : params.p_access_token,
						channel_id : channelId,
						channel_access_token : channelToken,
						token : params.p_token
					},

					wait : true,
					dataType:     'text',
					error : function(model, fail, xhr) {
						validationHelper.showError(messagesview,
								fail.responseText);
					}
				});
			},

			loadFbAlbum : function loadFbAlbum(message) {
				var fbalbumlist = this.$(this.fbalbumlist), albums;

				fbalbumlist.empty();
				if (message.get('status')) {
					albums = message.get('albums');
					_.each(albums, function(album) {
						var opt = new Option();
						opt.value = album.id;
						opt.text = album.name;
						fbalbumlist.append(opt);
					});
					fbalbumlist.trigger('liszt:updated');
				} else {
					validationHelper.showError(this.attributes.messagesview,
							message.get('error_message'));
				}

			}

		});
