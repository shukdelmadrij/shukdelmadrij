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

define('requests-service', ['extlycore'], function (Core) {
	var requestsService = window.angular.module('requestsService', ['ngResource']);

	requestsService.factory('Request', [ '$resource',
		function($resource) {
			var url = Core.SefHelper.route('index.php?option=com_autotweet&view=requests&format=json');
			var xTtoken = jQuery('#XTtoken').attr('name');

			var jsonParse = function(data) {
				var body = data.split(/@EXTLYSTART@|@EXTLYEND@/);

				if (body.length === 3) {
					return JSON.parse(body[1]);
				} else {
					return {
						status : false,
						message : data
					};
				}
			};

			return $resource(url, {}, {
				query : {
					method : 'POST',
					params : {
						task: '@taskCommand',
						_token : xTtoken
					},
					isArray : true,
					transformResponse : function(data, headersGetter) {
						return jsonParse(data);
					}
				},
				save : {
					method : 'POST',
					params : {
						task: '@taskCommand',
						_token : xTtoken,
						ref_id: '@ref_id'
					},
					transformResponse : function(data, headersGetter) {
						return jsonParse(data);
					}
				},
				get : {
					method : 'POST',
					params : {
						task: '@taskCommand',
						_token : xTtoken,
						id: '@request_id'
					},
					transformResponse : function(data, headersGetter) {
						return jsonParse(data);
					}
				}
			});
		} ]);

	return requestsService;
});
