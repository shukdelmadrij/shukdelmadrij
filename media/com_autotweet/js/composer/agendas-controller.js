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

define('agendas-controller', [], function() {

	//Do this instead
	var controller = function($scope, $rootScope, Agenda) {
		var agendasCtrl = $scope.agendasCtrl,
		    agendas = agendasCtrl.agendas = Agenda.get();

		Agenda.put([]);

		agendasCtrl.add = function () {
			var scheduling_date = agendasCtrl.scheduling_date,
				scheduling_time = agendasCtrl.scheduling_time;

			if ((!scheduling_date) || (!scheduling_time)) {
				return;
			}

			scheduling_date = scheduling_date.trim();
			if (!scheduling_date.length) {
				return;
			}

			agendas.push({
				agendaDate: scheduling_date,
				agendaTime: scheduling_time
			});

			Agenda.put(agendas);

			agendasCtrl.scheduling_date = '';
		};

		agendasCtrl.remove = function (agenda) {
			agendas.splice(agendas.indexOf(agenda), 1);
		};

		$rootScope.$on('newRequest', function() {
			Agenda.put([]);
			agendas = agendasCtrl.agendas = Agenda.get();
		});

		$rootScope.$on('loadAgenda', function(event, param) {
			var output = [];

			_.each(param, function(item) {
				var scheduling_date, scheduling_time, parts = item.split(' ');

				scheduling_date = parts[0];
				scheduling_time = parts[1];

				// 14:45:00
				parts = scheduling_time.split(':');

				if (parts.length == 3) {
					parts.splice(2, 1);
					scheduling_time = parts.join(':');
				};

				output.push({
					agendaDate: scheduling_date,
					agendaTime: scheduling_time
				});
			});

			Agenda.put(output);
			agendas = agendasCtrl.agendas = Agenda.get();
		});

	};

	controller.$inject = ['$scope', '$rootScope', 'Agenda'];

	return controller;

});
