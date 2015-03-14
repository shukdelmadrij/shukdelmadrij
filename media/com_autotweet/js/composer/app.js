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

define('app', ['editor-controller', 'requests-controller', 'agendas-controller', 'directives', 'ng-table'], function (editorController, requestsController, agendasController, directives) {
	var app =  window.angular.module('composerApp', ['requestsService', 'agendasService', 'ngTable']);

	// Disabling Debug Data
	app.config(['$compileProvider', function ($compileProvider) {
		$compileProvider.debugInfoEnabled(false);
	}]);

	app.controller('EditorController', editorController);
	app.controller('RequestsController', requestsController);
	app.controller('AgendasController', agendasController);
	app.directive('loadingContainer', directives.loadingContainer());

	return app;
});
