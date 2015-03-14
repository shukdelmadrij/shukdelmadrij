<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * DependencyManager
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class DependencyManager
{
	protected $version = '1.0.0';

	protected $appAvailable = false;

	protected $isRendered = false;

	protected $postRequireStatements = array();

	private $loadExtlyAdminMode = false;

	private $ownJqueryDisabled = false;

	private $loadBootstrap = false;

	const EXTLY_J25_JQUERY = 'jquery1102';

	const JS_BODY = '</body>';
	const JS_HTML = '</html>';

	const JS_SCRIPT_BEGIN = "\n\n<script type='text/javascript'>/*\n<![CDATA[*/\n\n";
	const JS_SCRIPT_END = "\n/*]]>*/</script>\n\n";

	const JS_BACKBONE = 0;

	const JS_ANGULAR = 1;

	private $framework = 0;

	/**
	 * __construct
	 *
	 * @param   bool  $loadExtlyAdminMode  Param
	 * @param   bool  $ownJqueryDisabled   Param
	 * @param   bool  $loadBootstrap       Param
	 */
	public function __construct($loadExtlyAdminMode = null, $ownJqueryDisabled = false, $loadBootstrap = false)
	{
		if ($loadExtlyAdminMode === null)
		{
			$this->loadExtlyAdminMode = JFactory::getApplication()->isAdmin();
		}
		else
		{
			$this->loadExtlyAdminMode = $loadExtlyAdminMode;
		}

		$this->ownJqueryDisabled = $ownJqueryDisabled;
		$this->loadBootstrap = $loadBootstrap;

		$this->framework = self::JS_BACKBONE;
	}

	/**
	 * initApp.
	 *
	 * @param   string  $version          Param
	 * @param   string  $extensionmainjs  Param
	 * @param   array   &$dependencies    {key2 => {key1, keyi}}
	 * @param   array   &$paths           {key1 => pathjs1, key2 => pathjs2}
	 *
	 * @return	void
	 */
	protected function _initApp($version = null, $extensionmainjs = null, &$dependencies = array(), &$paths = array())
	{
		$this->appAvailable = true;
		$this->version = $version;

		$appName = 'extlycore';

		// Module dependencies must be added
		if ($extensionmainjs)
		{
			$host = Extly::getHost();

			$appName = $this->getAppName($extensionmainjs);

			// App conditional to all dependencies
			if ((!array_key_exists($appName, $dependencies)) && (!empty($paths)))
			{
				$dependencies[$appName] = array_keys($paths);
			}

			// $extensionmainjs = str_replace('.js', '', $extensionmainjs);
			$extensionmainjs = preg_replace('/\.js$/', '', $extensionmainjs);

			$paths[$appName] = $this->addAppPath($extensionmainjs);
		}

		static $initialized = false;

		if (!$initialized)
		{
			$initialized = true;

			if ($this->framework == self::JS_ANGULAR)
			{
				$this->initPlatformNg($dependencies, $paths);
			}
			else
			{
				$this->initPlatform($dependencies, $paths);
			}
		}
	}

	/**
	 * setFramework
	 *
	 * @param   int  $selected  Param
	 *
	 * @return	void
	 */
	public function setFramework($selected)
	{
		$this->framework = $selected;
	}

	/**
	 * hasApp
	 *
	 * @return	bool
	 */
	public function hasApp()
	{
		$key = $this->getAppKey();
		$hasApp = F0FPlatform::getInstance()->getCache($key);

		// It has an App (or a cached script), and it's not rendered
		return ( (($this->appAvailable) || ($hasApp)) && (!$this->isRendered) );
	}

	/**
	 * getAppName
	 *
	 * @param   string  $file  Param
	 *
	 * @return	string
	 */
	public function getAppName($file)
	{
		$file = basename($file);

		// $file = str_replace('.min.js', '', $file);
		// $file = str_replace('.min.js', '', $file);

		$file = preg_replace('/(\.min)?\.js$/', '', $file);

		return $file;
	}

	/**
	 * addAppDependency.
	 *
	 * @param   string  $extensionmainjs  Param
	 * @param   array   &$dependencies    {key2 => {key1, keyi}}
	 * @param   array   &$paths           {key1 => pathjs1, key2 => pathjs2}
	 *
	 * @return	string
	 */
	public function addAppDependency($extensionmainjs, &$dependencies = array(), &$paths = array())
	{
		$appName = 'extlycore';

		// Module dependencies must be added
		if ($extensionmainjs)
		{
			$appName = $this->getAppName($extensionmainjs);
		}

		// App conditional to all dependencies
		if ((!array_key_exists($appName, $dependencies)) && (!empty($paths)))
		{
			$dependencies[$appName] = array_keys($paths);
		}

		return $appName;
	}

	/**
	 * _getAppKey
	 *
	 * @return	string
	 */
	protected function getAppKey()
	{
		return md5((string) JFactory::getUri());
	}

	/**
	 * initPlatform
	 *
	 * @param   array  &$dependencies  Param
	 * @param   array  &$paths         Param
	 *
	 * @return	void
	 */
	protected function initPlatform(&$dependencies, &$paths)
	{
		// Dependencies and Paths => Extlycore
		$paths['underscore'] = Extly::JS_LIB . 'backbone/underscore.min';
		$paths['backbone'] = Extly::JS_LIB . 'backbone/backbone.min';
		$paths['extlycore'] = Extly::JS_LIB . 'extlycore.min';

		$dependencies['backbone'] = array('underscore');

		if (EXTLY_J25)
		{
			// Joomla 2.5
			JHTML::_('behavior.mootools');

			if (!$this->ownJqueryDisabled)
			{
				$paths[self::EXTLY_J25_JQUERY] = Extly::JS_LIB . 'jquery/jquery-extly.min';

				$dependencies['underscore'] = array(self::EXTLY_J25_JQUERY);

				if (($this->loadExtlyAdminMode) || ($this->loadBootstrap))
				{
					$dependencies['bootstrap'] = array(self::EXTLY_J25_JQUERY);
				}
			}

			if (($this->loadExtlyAdminMode) || ($this->loadBootstrap))
			{
				$paths['bootstrap'] = Extly::JS_LIB . 'bootstrap/bootstrap.min';
			}

			if ($this->loadExtlyAdminMode)
			{
				$paths['j3compat'] = Extly::JS_LIB . 'j3compat.min';

				$isAdmin = JFactory::getApplication()->isAdmin();

				if ($isAdmin)
				{
					$paths['chosen'] = Extly::JS_LIB . 'j3/chosen.jquery.min';

					if (!$this->ownJqueryDisabled)
					{
						$dependencies['chosen'] = array(self::EXTLY_J25_JQUERY);
					}

					$dependencies['j3compat'] = array('bootstrap', 'chosen');
				}
				else
				{
					$dependencies['j3compat'] = array('bootstrap');
				}

				$dependencies['extlycore'] = array('backbone', 'j3compat');
			}
			else
			{
				// Site
				$dependencies['extlycore'] = array('backbone');
			}
		}
		else
		{
			// Joomla 3.0 or superior
			// JQuery - Bootstrap
			JHtml::_('jquery.framework');
			JHtml::_('bootstrap.framework');

			// Chosen - tooltip
			if ($this->loadExtlyAdminMode)
			{
				JHtml::_('formbehavior.chosen', 'select');
				JHtml::_('behavior.tooltip');
			}

			$dependencies['extlycore'] = array('backbone');
		}
	}

	/**
	 * initPlatform
	 *
	 * @param   array  &$dependencies  Param
	 * @param   array  &$paths         Param
	 *
	 * @return	void
	 */
	protected function initPlatformNg(&$dependencies, &$paths)
	{
		// Dependencies and Paths => Extlycore
		$paths['underscore'] = Extly::JS_LIB . 'backbone/underscore.min';
		$paths['angular'] = Extly::JS_LIB . 'angular/angular.min';
		$paths['extlycore'] = Extly::JS_LIB . 'extlycoreng.min';

		if (EXTLY_J25)
		{
			// Joomla 2.5
			JHTML::_('behavior.mootools');

			if (!$this->ownJqueryDisabled)
			{
				$paths[self::EXTLY_J25_JQUERY] = Extly::JS_LIB . 'jquery/jquery-extly.min';

				if (($this->loadExtlyAdminMode) || ($this->loadBootstrap))
				{
					$dependencies['bootstrap'] = array(self::EXTLY_J25_JQUERY);
				}
			}

			if (($this->loadExtlyAdminMode) || ($this->loadBootstrap))
			{
				$paths['bootstrap'] = Extly::JS_LIB . 'bootstrap/bootstrap.min';
			}

			if ($this->loadExtlyAdminMode)
			{
				$paths['j3compat'] = Extly::JS_LIB . 'j3compat.min';

				$isAdmin = JFactory::getApplication()->isAdmin();

				if ($isAdmin)
				{
					// $paths['chosen'] = Extly::JS_LIB . 'j3/chosen.jquery.min';

					/*
					if (!$this->ownJqueryDisabled)
					{
						$dependencies['chosen'] = array(self::EXTLY_J25_JQUERY);
					}
					*/

					// $dependencies['j3compat'] = array('bootstrap', 'chosen');
					$dependencies['j3compat'] = array('bootstrap');
				}
				else
				{
					$dependencies['j3compat'] = array('bootstrap');
				}

				// $dependencies['extlycore'] = array('angular', 'j3compat');
				$dependencies['extlycore'] = array('j3compat');
			}
			else
			{
				// Site
				// $dependencies['extlycore'] = array('angular');
			}
		}
		else
		{
			// Joomla 3.0 or superior
			// JQuery - Bootstrap
			JHtml::_('jquery.framework');
			JHtml::_('bootstrap.framework');

			// Chosen - tooltip
			if ($this->loadExtlyAdminMode)
			{
				// JHtml::_('formbehavior.chosen', 'select');
				JHtml::_('behavior.tooltip');
			}

			// $dependencies['extlycore'] = array('angular');
		}
	}

	/**
	 * insertApp.
	 *
	 * @param   string  &$body  Param
	 *
	 * @return	void
	 */
	public function insertApp(&$body)
	{
		if ($this->hasApp())
		{
			$this->isRendered = true;

			$jsapp = $this->getApp();

			$this->injectScript($body, $jsapp);
		}
	}

	/**
	 * injectScript
	 *
	 * @param   string  &$body  Param
	 * @param   string  $jsapp  Param
	 *
	 * @return	void
	 */
	protected function injectScript(&$body, $jsapp)
	{
		$pos = strrpos($body, self::JS_BODY);

		if ($pos !== false)
		{
			$body = substr($body, 0, $pos) . $jsapp . substr($body, $pos);
		}
		else
		{
			$pos = strrpos($body, self::JS_HTML);

			if ($pos !== false)
			{
				$body = substr($body, 0, $pos) . $jsapp . substr($body, $pos);
			}
		}
	}

	/**
	 * addPostRequireScript.
	 *
	 * @param   string  $script  Param
	 *
	 * @return	void
	 */
	public function addPostRequireScript($script)
	{
		$this->postRequireStatements[] = $script;
	}

	/**
	 * addAppPath
	 *
	 * @param   string  $appPath  Param
	 *
	 * @return	string
	 */
	protected function addAppPath($appPath)
	{
		return $appPath;
	}

	/**
	 * addPostRequireHook
	 *
	 * @param   string  &$app  Param
	 *
	 * @return	void
	 */
	protected function addPostRequireHook(&$app)
	{
		if (!empty($this->postRequireStatements))
		{
			$app .= self::JS_SCRIPT_BEGIN . 'function postRequireHook() {'
				. implode('', $this->postRequireStatements)
				. '};' . self::JS_SCRIPT_END;
		}
	}

	/**
	 * getFinalFilename
	 *
	 * @param   string  $filename  Param
	 *
	 * @return	void
	 */
	protected function getFinalFilename($filename)
	{
		if ((JFactory::getConfig()->get('debug')) && (strpos($filename, 'media/lib_extly') === false))
		{
			$filename = str_replace('.min', '', $filename);
		}

		return $filename;
	}
}
