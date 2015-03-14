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

var appParamsHelper = {
	get : function(scope) {
		var canvas_page = autotweet_canvas_app_url,
			app_id = 'My-App-ID',
			secret = 'My-App-Secret',
			access_token = 'My-Access-Token',
			ownApp = scope.$('#use_own_api').val(),
			token = scope.$('#XTtoken').attr('name');

		// Yes, with Canvas Page
		if (ownApp === '1') {
			canvas_page = scope.$('#canvas_page').val().trim();
			app_id = scope.$('#app_id').val().trim();
			secret = scope.$('#secret').val().trim();

			scope.$('#canvas_page').val(canvas_page);
			scope.$('#app_id').val(app_id);
			scope.$('#secret').val(secret);

		// Yes (no Canvas Page)
		} else if (ownApp === '2') {
			app_id = scope.$('#app_id').val().trim();
			secret = scope.$('#secret').val().trim();

			scope.$('#app_id').val(app_id);
			scope.$('#secret').val(secret);
		}

		access_token = scope.$('#access_token').val().trim();
		scope.$('#access_token').val(access_token);

		var params = {
			p_own_app : ownApp,
			p_canvas_page : canvas_page,
			p_encoded_canvas_page : encodeURIComponent(canvas_page),
			p_app_id : encodeURIComponent(app_id),
			p_secret : encodeURIComponent(secret),
			p_access_token : encodeURIComponent(access_token),
			p_token : encodeURIComponent(token)
		};

		var url_params =
			      'app_id=' + params.p_app_id
			    + '&secret=' + params.p_secret
			    + '&access_token=' + params.p_access_token
				+ '&ownapp=' + params.p_own_app
				+ '&canvas_page=' + params.p_encoded_canvas_page
				+ '&token=' + params.p_token;

		params.p_url_params = url_params;

		return params;
	},

	getLi : function(scope) {
		var api_key = scope.$('#api_key').val(),
			secret_key = scope.$('#secret_key').val(),
			oauth_user_token = scope.$('#oauth_user_token').val(),
			oauth_user_secret = scope.$('#oauth_user_secret').val(),
			token = scope.$('#XTtoken').attr('name');

		var params = {
			p_api_key : api_key,
			p_secret_key : encodeURIComponent(secret_key),
			p_oauth_user_token : encodeURIComponent(oauth_user_token),
			p_oauth_user_secret : encodeURIComponent(oauth_user_secret),
			p_token : encodeURIComponent(token)
		};

		var url_params =
				  'api_key=' + params.p_api_key
				+ '&secret_key=' + params.p_secret_key
				+ '&oauth_user_token=' + params.p_oauth_user_token
				+ '&oauth_user_secret=' + params.p_oauth_user_secret
				+ '&token=' + params.p_token;

		params.p_url_params = url_params;

		return params;
	}
};

var validationHelper = {
	showSuccess : function(scope, userId, socialIcon, socialUrl) {
		scope.$('#user_id').val(userId);

		this.assignSocialUrl(scope, 'social_url', socialIcon, socialUrl);

		scope.$('#validation-notchecked').hide();
		scope.$('#validation-error').hide();
		scope.$('#validation-errormsg').hide();

		scope.$('#validation-success').show();
	},

	assignSocialUrl : function(scope, target, socialIcon, socialUrl) {
		if (socialUrl)
		{
			scope.$('#' + target).val(socialUrl);
			scope.$('.' + target).html(this.formatUrl(socialIcon, socialUrl));
		}
	},

	showError : function(scope, msg) {
		scope.$('#user_id').val('');

		scope.$('#validation-notchecked').hide();
		scope.$('#validation-success').hide();

		scope.$('#validation-theerrormsg').html(msg);
		scope.$('#validation-error').show();
		scope.$('#validation-errormsg').show();
	},

	formatUrl : function(socialIcon, socialUrl) {
		return '<p><a href="' + socialUrl + '" target="_blank">' + socialIcon + ' ' + socialUrl + '</a></p>';
	}
};

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

var Channel = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getParamsForm&toolbar=none');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbAlbum = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getFbAlbums');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbChannel = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getFbChannels');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbChValidation = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getFbChValidation');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbExtend = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getFbExtend');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbValidation = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getFbValidation');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var GplusValidation = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getGplusValidation');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var LiGroup = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getLiGroups');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var LiCompany = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getLiCompanies');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var LiValidation = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getLiValidation');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var TwValidation = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getTwValidation');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var VkGroup = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getVkGroups');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var VkValidation = Core.ExtlyModel.extend({
			url : function() {
				return Core.SefHelper.route('index.php?option=com_autotweet&view=channels&task=getVkValidation');
			}
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var Channels = Backbone.Collection.extend({
	model : Channel
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbAlbums = Backbone.Collection.extend({
	model : FbAlbum
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbChannels = Backbone.Collection.extend({
	model : FbChannel
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbChValidations = Backbone.Collection.extend({
	model : FbChValidation
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbExtends = Backbone.Collection.extend({
	model : FbExtend
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var FbValidations = Backbone.Collection.extend({
	model : FbValidation
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var GplusValidations = Backbone.Collection.extend({
	model : GplusValidation
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var LiCompanies = Backbone.Collection.extend({
	model : LiCompany
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var LiGroups = Backbone.Collection.extend({
	model : LiGroup
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var LiValidations = Backbone.Collection.extend({
	model : LiValidation
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var TwValidations = Backbone.Collection.extend({
	model : TwValidation
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var VkGroups = Backbone.Collection.extend({
	model : VkGroup
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

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

var VkValidations = Backbone.Collection.extend({
	model : VkValidation
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

var FbChannelView = Backbone.View
		.extend({

			events : {
				'change #xtformfbchannel_id' : 'onChangeChannel'
			},

			initialize : function() {
				this.attributes.dispatcher.on('fbapp:channelschanged',
						this.onAccessTokenChanged, this);
				this.collection.on('add', this.loadFbChannel, this);

				this.fbchannellist = '#xtformfbchannel_id';
				this.fbChannelSelected = null;

				this.$('.group-warn').fadeOut();
			},

			onAccessTokenChanged : function() {
				var thisView = this, messagesview = this.attributes.messagesview, params = appParamsHelper
						.get(thisView);

				Core.UiHelper.listReset(thisView.$(this.fbchannellist));

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
						validationHelper.showError(messagesview,
								fail.responseText);
					}
				});
			},

			onChangeChannel : function() {
				var accessToken,
					channelType,
					oselected,
					socialIcon,
					socialUrl;

				this.fbChannelSelected = null;
				accessToken = this.getFbChannelAccessToken();
				channelType = this.getFbChannelType();
				oselected = this.getFbChannelSelected();
				socialIcon = oselected.attr('social_icon');
				socialUrl = oselected.attr('social_url');

				this.$('#fbchannel_access_token').val(accessToken);
				validationHelper.assignSocialUrl(this, 'social_url', socialIcon, socialUrl);

				if (channelType === 'Group') {
					this.$('.group-warn').fadeIn();
				} else {
					this.$('.group-warn').fadeOut();
				}

				if (channelType === 'User') {
					this.$('.open_graph_features').fadeIn();
				} else {
					this.$('.open_graph_features').fadeOut();
				}

				this.$('.channel-type').val(channelType);
			},

			getFbChannelSelected : function() {
				if (!this.fbChannelSelected) {
					this.fbChannelSelected = this.$(this.fbchannellist + ' option:selected');
				}

				return this.fbChannelSelected;
			},

			getFbChannelAccessToken : function() {
				var oselected = this.getFbChannelSelected(),
					access_token = 'INVALID';
				if (oselected) {
					access_token = oselected.attr('access_token');
				}
				return access_token;
			},

			getFbChannelType : function() {
				var oselected = this.getFbChannelSelected(),
					channelType = 'INVALID';

				if (oselected) {
					channelType = oselected.attr('data_type');
				}

				return channelType;
			},

			getFbChannelId : function() {
				return this.getFbChannelSelected().val();
			},

			loadFbChannel : function(message) {
				var fbchannellist = this.$(this.fbchannellist), channels, socialIcon, first = true;

				fbchannellist.empty();
				this.fbChannelSelected = null;

				if (message.get('status')) {
					channels = message.get('channels');
					socialIcon = message.get('icon');

					_.each(channels, function(channel) {
						var opt = new Option();
						opt.value = channel.id;
						opt.text = channel.type + ': ' + channel.name;

						jQuery(opt)
							.attr('access_token', channel.access_token)
							.attr('data_type', channel.type)
							.attr('social_icon', socialIcon)
							.attr('social_url', channel.url);

						if (first) {
							first = false;
							opt.selected = 'selected';
						}

						fbchannellist.append(opt);
					});

					this.onChangeChannel();

					fbchannellist.trigger('liszt:updated');
				} else {
					validationHelper.showError(this.attributes.messagesview,
							message.get('error_message'));
				}

			}

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

var FbExtendView = Backbone.View.extend({
	events : {
		'click #fbextendbutton' : 'onExtendReq'
	},

	initialize : function() {
		var view = this;

		this.collection.on('add', this.loadExtend, this);

		this.$el.ajaxStart(function() {
			view.$(".loaderspinner72").addClass('loading72');
		}).ajaxStop(function() {
			view.$(".loaderspinner72").removeClass('loading72');
		});
	},

	onExtendReq : function () {
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

	loadExtend : function (resp) {
		var status = resp.get('status'),
			error_message = resp.get('error_message'),
			user,
			extended_token,
			tokenInfo,
			issued_at,
			expires_at;

		if (status) {
			user = resp.get('user');
			extended_token = resp.get('extended_token');
			tokenInfo = resp.get('tokenInfo');
			issued_at = tokenInfo.issued_at;
			expires_at = tokenInfo.expires_at;

			if (user) {
				validationHelper.showSuccess(this, user.id);
			} else {
				validationHelper.showSuccess(this, tokenInfo.data.user_id);
			}

			this.$('#access_token').val(extended_token);
			this.$('#issued_at').val(issued_at);
			this.$('#expires_at').val(expires_at);

			this.attributes.dispatcher.trigger("fbapp:channelschanged");
		} else {
			validationHelper.showError(this, error_message);
		}
	}

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

var LiCompanyView = Backbone.View
		.extend({

			events : {
				'click #licompanyloadbutton' : 'onChangeChannel',
				'change #xtformcompany_id' : 'onChangeCompany'
			},

			initialize : function() {
				this.collection.on('add', this.loadLiCompany, this);
				this.licompanylist = '#xtformcompany_id';
				this.$('.group-warn').fadeOut();
			},

			onChangeChannel : function() {
				var thisView = this,
					params = appParamsHelper.getLi(thisView);

				Core.UiHelper.listReset(thisView.$(this.licompanylist));

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

			loadLiCompany : function(message) {
				var licompanylist = this.$(this.licompanylist), channels, socialIcon, first = true;

				licompanylist.empty();
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

						licompanylist.append(opt);
					});

					this.onChangeCompany();
					validationHelper.showSuccess(this, '');

					licompanylist.trigger('liszt:updated');
				} else {
					validationHelper.showError(this, message.get('error_message'));
				}

			},

			onChangeCompany : function() {
				var oselected = this.$('#xtformcompany_id option:selected'),
					socialIcon = oselected.attr('social_icon'),
					socialUrl = oselected.attr('social_url');

				validationHelper.assignSocialUrl(this, 'social_url_licompany', socialIcon, socialUrl);
			}

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

var TwValidationView = Backbone.View.extend({
	events : {
		'click #twvalidationbutton' : 'onValidationReq'
	},

	initialize : function() {
		this.collection.on('add', this.loadvalidation, this);
	},

	onValidationReq : function onValidationReq() {
		var view = this,

			consumer_key = view.$('#consumer_key').val().trim(),
			consumer_secret = view.$('#consumer_secret').val().trim(),
			access_token = view.$('#access_token').val().trim(),
			access_token_secret = view.$('#access_token_secret').val().trim(),

			token = view.$('#XTtoken').attr('name');

		view.$('#consumer_key').val(consumer_key);
		view.$('#consumer_secret').val(consumer_secret);
		view.$('#access_token').val(access_token);
		view.$('#access_token_secret').val(access_token_secret);

		view.$(".loaderspinner").addClass('loading');

		this.collection.create(this.collection.model, {
			attrs : {
				consumer_key : consumer_key,
				consumer_secret : consumer_secret,
				access_token : access_token,
				access_token_secret : access_token_secret,
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
			validationHelper.showSuccess(this, user.id_str, icon, url);
		} else {
			validationHelper.showError(this, error_message);
		}
	}

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

var VkValidationView = Backbone.View.extend({
	events : {
		'click #authorizeButton' : 'onAuthorization',
		'click #vkvalidationbutton' : 'onValidationReq'
	},

	initialize : function() {
		this.collection.on('add', this.loadvalidation, this);
	},

	onAuthorization : function onAuthorization() {
		this.$('#authorizeGroup').addClass('hide');
		this.$('#validationGroup').removeClass('hide');
	},

	processTokenUrl :  function processTokenUrl(view) {
		var hash, params, access_token = {};

		// Access token is coming

		hash = view.$('#token_url').val().trim();
		params = hash.split('#');

		if (_.size(params) == 2) {
			hash = params[1];
		} else {
			return false;
		}

		if (!_.isEmpty(hash))
		{
			params = hash.split('&');
			_.each(params, function(param) {
					var kv = param.split('='), k, v;

					if (_.size(kv) == 2) {
						k = kv[0];
						v = kv[1];

						jQuery('#raw_' + k).val(v);

						access_token[k] = v;
					}
				}
			);

			jQuery('#access_token').val(JSON.stringify(access_token));

			return true;
		};

		return false;
	},

	onValidationReq : function onValidationReq() {
		var view = this,
			channel_id = view.$('#channel_id').val(),
			access_token,
			token = view.$('#XTtoken').attr('name');

		if (!this.processTokenUrl(view)) {
			validationHelper.showError(view, 'Invalid Token Url');
		}

		access_token = view.$('#access_token').val();

		view.$(".loaderspinner").addClass('loading');

		this.collection.create(this.collection.model, {
			attrs : {
				channel_id : channel_id,
				access_token : access_token,
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
			socialIcon = resp.get('social_icon'),
			socialUrl = resp.get('social_url');

		this.$(".loaderspinner").removeClass('loading');

		if (status) {
			validationHelper.showSuccess(this, error_message, socialIcon, socialUrl);
		} else {
			validationHelper.showError(this, error_message);
		}
	}

});
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
