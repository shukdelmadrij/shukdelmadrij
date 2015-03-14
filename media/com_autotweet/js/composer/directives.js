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

define('directives', [], function() {
	var loadingContainerApp = function() {
		return {
			restrict : 'A',
			scope : false,
			link : function(scope, element, attrs) {
				var loadingLayer = angular
						.element('<span class="loaderspinner72">loading...</span>');
				element.append(loadingLayer);
				element.addClass('loading-container');
				scope.$watch(attrs.loadingContainer, function(value) {
					loadingLayer.toggleClass('ng-hide', !value);
				});
			}
		};
	};

	return {
		loadingContainer : function () {
			return loadingContainerApp;
		}
	};

});
