<?php
/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * AutotweetViewComposer
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewComposer extends F0FViewHtml
{
	/**
	 * onAdd.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 *
	 * @since	1.5
	 */
	public function onAdd($tpl = null)
	{
		$result = parent::onAdd($tpl);

		Extly::loadAwesome();
		JHtml::stylesheet('lib_extly/ng-table.min.css', false, true);

		$file = EHtml::getRelativeFile('js', 'com_autotweet/composer/app.min.js');

		if ($file)
		{
			$this->assign('extensionmainjs', $file);
			$dependencies = array();

			$paths = array();

			// Libraries
			$paths['angular'] = 'media/lib_extly/js/angular/angular.min';

			$paths['angular-animate'] = 'media/lib_extly/js/angular/angular-animate.min';
			$dependencies['angular-animate'] = array('angular');

			$paths['angular-resource'] = 'media/lib_extly/js/angular/angular-resource.min';
			$dependencies['angular-resource'] = array('angular');

			$paths['ng-table'] = 'media/lib_extly/js/angular/ng-table-noamd.min';
			$dependencies['ng-table'] = array('angular');

			// App
			$paths['requests-service'] = 'media/com_autotweet/js/composer/requests-service.min';
			$dependencies['requests-service'] = array('angular-resource', 'extlycore');

			$paths['agendas-service'] = 'media/com_autotweet/js/composer/agendas-service.min';
			$dependencies['agendas-service'] = array('angular');

			$paths['directives'] = 'media/com_autotweet/js/composer/directives.min';
			$dependencies['directives'] = array('angular');

			$paths['editor-controller'] = 'media/com_autotweet/js/composer/editor-controller.min';
			$dependencies['editor-controller'] = array('angular');

			$paths['requests-controller'] = 'media/com_autotweet/js/composer/requests-controller.min';
			$dependencies['requests-controller'] = array('ng-table', 'requests-service');

			$paths['agendas-controller'] = 'media/com_autotweet/js/composer/agendas-controller.min';
			$dependencies['agendas-controller'] = array('agendas-service');

			$scriptManager = Extly::getScriptManager();
			$scriptManager->setFramework(DependencyManager::JS_ANGULAR);
			$scriptManager->initApp(CAUTOTWEETNG_VERSION, $file, $dependencies, $paths);

			$list_limit = JFactory::getConfig()->get('list_limit');
			$scriptManager->addPostRequireScript(
					"angular.bootstrap(document, ['composerApp']);
					window.xtListLimit = {$list_limit};
					jQuery('.post-attrs-group a').click(function(e) {
						var btn = jQuery(e.target), v;

						if (btn.hasClass('xticon')) {
						 	btn = btn.parent('a');
						 }

						 v = btn.attr('data-value');

						jQuery('.xt-subform').hide();
						jQuery('.xt-subform-' + v).show();
					});"
			);
		}

		$document = JFactory::getDocument();
		$document->addScript('//cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js');

		$platform = F0FPlatform::getInstance();
		$this->assign('editown', $platform->authorise('core.edit.own', $this->input->getCmd('option', 'com_foobar')));
		$this->assign('editstate', $platform->authorise('core.edit.state', $this->input->getCmd('option', 'com_foobar')));

		return $result;
	}
}
