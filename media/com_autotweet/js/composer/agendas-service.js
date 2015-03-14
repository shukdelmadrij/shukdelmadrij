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

/*global define*/
'use strict';

/**
 * Services that persists and retrieves Agendas from localStorage
 */
define('agendas-service', [], function () {
	var agendasService = window.angular.module('agendasService', []);

	agendasService.factory('Agenda',
		function() {

			var STORAGE_ID = 'agendas-autotweet';

			return {
				get: function () {
					return JSON.parse(localStorage.getItem(STORAGE_ID) || '[]');
				},

				put: function (todos) {
					localStorage.setItem(STORAGE_ID, JSON.stringify(todos));
				}
			};

		});

	return agendasService;
});
