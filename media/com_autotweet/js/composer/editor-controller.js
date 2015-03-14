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

/* global define */
'use strict';

define('editor-controller', [ 'extlycore' ], function(Core) {

	// Do this instead
	var controller = function($scope, $rootScope, $sce, RequestService) {
		var _this = this;
		var local = $scope.editorCtrl;
		var Request = RequestService;
		var theForm = jQuery('.extly-body form');
		var initial_form = theForm.serializeArray();

		var redirectOnSuccess = false;

		local.remainingCount = 0;
		local.showDialog = false;

		local.addRequest = function(e) {
			var descr = (local.description || ''), request;
			var form, params = {}, attrs = {}, agenda = [], channels = [];

			e.preventDefault();
			descr = descr.trim();

			// No mensage
			if (descr.length == 0) {
				local.showDialog = true;
				local.messageResult = false;
				local.messageText = $sce.trustAsHtml('Invalid field: Message');

				return;
			}

			params['description'] = descr;
			params['url'] = local.url;

			form = theForm.serializeArray();
			_.each(form, function(item) {

				// console.log('name: ' + item.name);
				// console.log('  >>> ' + item.value);

				if (item.name == 'postThis') {
					attrs['postthis'] = item.value;
				} else if (item.name == 'everGreen') {
					attrs['evergreen'] = item.value;
				} else if (item.name == 'channelchooser[]') {
					channels.push(item.value);
				} else if (item.name == 'unix_mhdmd') {
					attrs[item.name] = item.value;
				} else if (item.name == 'ref_id') {
					params[item.name] = item.value;
					attrs[item.name] = item.value;
				} else if (item.name == 'agenda[]') {
					agenda.push(item.value);
				} else if (item.name.match(/unix_mhdmd_/)
						|| (item.name == 'scheduling_date')
						|| (item.name == 'scheduling_time')
						|| (item.name  == 'postAttrs')
						|| (item.name  == 'selectedMenuItem')) {
					// Skip
				} else {
					/*
					 * option
					 * view
					 * task
					 * returnurl
					 * b29aa44adfb8707f3abd0222bfb52329
					 * lang
					 * published
					 * id
					 * image_url
					 * unix_mhdmd
					 */
					params[item.name] = item.value;
				}
			});

			// autotweet_advanced_attrs
			attrs['agenda'] = agenda;
			attrs['channels'] = channels;
			attrs['channels_text'] = _this.getChannelsText();
			attrs['image'] = '';
			params['autotweet_advanced_attrs'] = JSON.stringify(attrs);

			if (local.plugin == 'autotweetpost') {
				params['taskCommand'] = 'applyAjaxOwnAction';
			} else {
				params['taskCommand'] = 'applyAjaxPluginAction';
			}

			params['ajax'] = 1;

			// console.log(params);

			if ((attrs['unix_mhdmd']) && (attrs['unix_mhdmd'].length > 0) && (agenda.length > 1)) {
				_this.error({
					status : '',
					statusText : 'Repeat Expression not allowed for more than one Agenda date.'
				});

				return false;
			}

			local.waiting = true;
			request = new Request(params);
			request.$save(null, _this.success, _this.error);
		};

		_this.jQueryAddRequest = function(event) {
			_this.addRequest(event);
			$scope.$digest();
		};

		_this.jQueryAddRequestAndRedirect = function(event) {
			_this.redirectOnSuccess = true;
			_this.jQueryAddRequest(event);
		};

		_this.getChannelsText = function() {
			var options = jQuery('#channelchooser option:selected'), channels_text;

			channels_text = _.reduce(options, function(memo, option) {
				var txt = jQuery(option).text();

				if (memo == '') {
					return txt;
				} else {
					return memo + ', ' + txt;
				}
			}, '');

			return channels_text;
		};

		local.countRemaining = function() {
			local.remainingCount = 140 - local.description.length;
		};

		_this.success = function(response) {
			_this.reset();

			local.showDialog = true;
			local.messageResult = response.status;
			local.messageText = $sce.trustAsHtml(response.message);

			local.request_id = 0;
			local.ref_id = response.hash;

			local.waiting = false;
			$rootScope.$emit('newRequest');

			if ((response.status) && (_this.redirectOnSuccess)) {
				Joomla.submitbutton('cancel');
			};

			_this.redirectOnSuccess = false;
		};

		_this.load = function(request) {
			var nativeObject = JSON.parse(request.native_object);
			_this.reset();
			local.waiting = false;

			local.showDialog = false;
			local.messageResult = 'success';
			local.messageText = $sce.trustAsHtml('-Loaded-');

			// console.log(request);
			// console.log(nativeObject);

			// Ng-model
			local.description = request.description;
			local.url = request.url;

			// Ng-value
			local.request_id = request.id;
			local.ref_id = request.ref_id;
			local.plugin = request.plugin;

			// Ng-model - agendasCtrl // agendasCtrl.scheduling_date / agendasCtrl.scheduling_time
			if (request.autotweet_advanced_attrs) {
				$rootScope.$emit('loadAgenda', request.autotweet_advanced_attrs.agenda);
			}

			// Rest of the fields
			jQuery('#image_url').val(request.image_url);
			jRefreshPreview('', 'image_url')

			if (request.autotweet_advanced_attrs) {
				jQuery('#itemeditor_postthis').val(request.autotweet_advanced_attrs.postthis);
				jQuery('#itemeditor_evergreen').val(request.autotweet_advanced_attrs.evergreen);
				jQuery('#channelchooser').val(request.autotweet_advanced_attrs.channels);
				jQuery('#unix_mhdmd').val(request.autotweet_advanced_attrs.unix_mhdmd);

				Core.UiHelper.resetBtnGroup('#itemeditor_postthis');
				Core.UiHelper.resetBtnGroup('#itemeditor_evergreen');
			}
		};

		_this.getHash = function() {
			var now = new Date(), hash;

			hash = CryptoJS.MD5('' + now.getTime() + _.random(0, 9007199254740992));
			hash = CryptoJS.MD5(hash + _.random(0, 9007199254740992));
			hash = CryptoJS.MD5(hash + _.random(0, 9007199254740992));

			return hash;
		};

		_this.reset = function() {
			_.each(initial_form, function(item) {
				jQuery('.extly-body form [name=\'' + item.name + '\']').val(item.value);
			});

			local.ref_id = _this.getHash();
			local.plugin = 'autotweetpost';
			local.description = '';
			local.url = '';
			local.selectedMenuItem = '';
			jQuery('#channelchooser').val([]);
			jQuery('#image_url-image').html('');
			$rootScope.$emit('loadAgenda', []);

			Core.UiHelper.resetBtnGroup('#itemeditor_postthis');
			Core.UiHelper.resetBtnGroup('#itemeditor_evergreen');
		};

		_this.jQueryReset = function(event) {
			event.preventDefault();
			_this.reset();
			$scope.$digest();
		};

		_this.error = function(httpResponse) {
			local.showDialog = true;
			local.messageResult = false;
			local.messageText = $sce.trustAsHtml('Error: ' + httpResponse.status + ' ' + httpResponse.statusText);

			_this.redirectOnSuccess = false;
			local.waiting = false;
		};

		_this.editRequest = function(e, request_id) {
			var request, params = {};

			e.preventDefault();

			if (!request_id) {
				return;
			}

			params['id'] = request_id;
			params['request_id'] = request_id;
			params['taskCommand'] = 'readAjaxAction';
			params['ajax'] = 1;

			local.waiting = true;
			request = new Request(params);
			request.$get(null, _this.load, _this.error);
		};

		_this.publishRequest = function(e, request_id) {
			var request, params = {};

			e.preventDefault();

			if (!request_id) {
				return;
			}

			params['id'] = request_id;
			params['request_id'] = request_id;
			params['taskCommand'] = 'publishAjaxAction';
			params['ajax'] = 1;

			local.waiting = true;
			request = new Request(params);
			request.$save(null, _this.success, _this.error);
		};

		_this.cancelRequest = function(e, request_id) {
			var request, params = {};

			e.preventDefault();

			if (!request_id) {
				return;
			}

			params['id'] = request_id;
			params['request_id'] = request_id;
			params['taskCommand'] = 'cancelAjaxAction';
			params['ajax'] = 1;

			local.waiting = true;
			request = new Request(params);
			request.$save(null, _this.success, _this.error);
		};

		_this.backtoQueueRequest = function(event, request_id) {
			var request, params = {};

			e.preventDefault();

			if (!request_id) {
				return;
			}

			params['id'] = request_id;
			params['request_id'] = request_id;
			params['taskCommand'] = 'backtoQueueAjaxAction';
			params['ajax'] = 1;

			local.waiting = true;
			request = new Request(params);
			request.$save(null, _this.success, _this.error);
		};

		$rootScope.$on('editRequest', _this.editRequest);
		$rootScope.$on('publishRequest', _this.publishRequest);
		$rootScope.$on('cancelRequest', _this.cancelRequest);
		$rootScope.$on('backtoQueueRequest', _this.backtoQueueRequest);

		// Joomla 3 - Back-end Site
		jQuery('#toolbar-apply button').attr('onclick', null).click(_this.jQueryAddRequest);
		jQuery('#toolbar-save button').attr('onclick', null).click(_this.jQueryAddRequestAndRedirect);
		jQuery('#toolbar-save-new button').attr('onclick', null).click(_this.jQueryAddRequest);

		// Joomla 3 - Front-end Site
		// apply
		jQuery('#F0FHeaderHolder button:nth-of-type(1)').attr('onclick', null).click(_this.jQueryAddRequest);

		// save
		jQuery('#F0FHeaderHolder button:nth-of-type(2)').attr('onclick', null).click(_this.jQueryAddRequestAndRedirect);

		// savenew
		jQuery('#F0FHeaderHolder button:nth-of-type(3)').attr('onclick', null).click(_this.jQueryAddRequest);

		// Joomla 2.5 - Back-end Site and Front-end Site
		jQuery('#toolbar-apply a').attr('onclick', null).click(_this.jQueryAddRequest);
		jQuery('#toolbar-save a').attr('onclick', null).click(_this.jQueryAddRequestAndRedirect);
		jQuery('#toolbar-save-new a').attr('onclick', null).click(_this.jQueryAddRequest);
	};

	controller.$inject = ['$scope', '$rootScope', '$sce', 'Request'];

	return controller;

});
