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

include_once 'dependencymanager.php';

/**
 * SiteDependencyManager
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class SiteDependencyManager extends DependencyManager
{
	private $appPaths = array();

	private $appDependencies = array();

	private $appFiles = array();

	const JS_DIR_TMP = 'media/lib_extly/tmp/';

	const JS_HEAD = '</head>';

	const JS_REQ_INIT = <<<JS
	<!-- Extly Dependency Manager -->

		require.config({
		    baseUrl: "{URL_ROOT}"
		});

		require({ALL_KEYS}, function() {

			if (_.isFunction(window.postRequireHook))
			{
				postRequireHook();
			}

		});

JS;

	/**
	 * initApp.
	 *
	 * @param   string  $version          Param
	 * @param   string  $extensionmainjs  Param
	 * @param   array   $dependencies     {key2 => {key1, keyi}}
	 * @param   array   $paths            {key1 => pathjs1, key2 => pathjs2}
	 *
	 * @return	void
	 */
	public function initApp($version = null, $extensionmainjs = null, $dependencies = array(), $paths = array())
	{
		parent::_initApp($version, $extensionmainjs, $dependencies, $paths);

		$this->appPaths = array_merge($this->appPaths, $paths);
		$moreDependencies = $this->_discoverDependencies($paths);
		$this->appDependencies = array_merge($this->appDependencies, $dependencies, $moreDependencies);
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
		$appName = parent::addAppDependency($extensionmainjs, $dependencies, $paths);

		$this->appPaths = array_merge($this->appPaths, $paths);

		$this->_addDependencyArray($dependencies);

		$moreDependencies = $this->_discoverDependencies($paths);
		$this->_addDependencyArray($moreDependencies);
	}

	/**
	 * _addDependencyArray.
	 *
	 * @param   array  &$dependencies  {key2 => {key1, keyi}}
	 *
	 * @return	void
	 */
	private function _addDependencyArray(&$dependencies)
	{
		foreach ($dependencies as $appKey => $deps)
		{
			if (array_key_exists($appKey, $this->appDependencies))
			{
				$v = $this->appDependencies[$appKey];
				$this->appDependencies[$appKey] = array_merge($v, $deps);
			}
			else
			{
				$this->appDependencies[$appKey] = $deps;
			}
		}
	}

	/**
	 * _discoverDependencies.
	 *
	 * @param   array  $paths  Param
	 *
	 * @return	array
	 */
	private function _discoverDependencies($paths)
	{
		$dependencies = array();

		foreach ($paths as $key => $file)
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . $file . '.js';
			$file = $this->getFinalFilename($file);
			$js = JFile::read($file);

			$this->appFiles[$key] = $js;

			if (preg_match('/define\(\'\w\',\s*(\[[^]]*\])/', $js, $matches))
			{
				$m = json_decode($matches[1]);

				if ($m)
				{
					$dependencies[$key] = $m;
				}
			}
		}

		return $dependencies;
	}

	/**
	 * getApp
	 *
	 * @return	string
	 */
	protected function getApp()
	{
		$root = JFactory::getUri()->root();

		$platform = F0FPlatform::getInstance();
		$key = $this->getAppKey();
		$file = $platform->getCache($key);

		$cached = true;

		$expiration = EParameter::getExpiration();

		if ((empty($file)) || (!file_exists($file)) || (filectime($file) < $expiration))
		{
			$cached = false;
			$file = $this->_getOptimizedApp($this->appDependencies, $this->appPaths);
		}

		$url = $root . str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', $file);

		$jsapp = '<script src="' . $root . 'media/lib_extly/js/require/require.min.js"></script>' . "\n";
		$jsapp .= '<script src="' . $url . ($this->version? '?' . $this->version : '') . '" ></script>' . "\n";
		$jsapp .= $this->_generateRequire($key);

		$this->addPostRequireHook($jsapp);

		if (!$cached)
		{
			$platform->setCache($key, $file);
		}

		return $jsapp;
	}

	/**
	 * _generateRequire
	 *
	 * @param   string  $appkey  Param
	 *
	 * @return	string
	 */
	private function _generateRequire($appkey)
	{
		$requireKey = $appkey . '_req';

		$platform = F0FPlatform::getInstance();
		$req = $platform->getCache($requireKey);

		if ($req)
		{
			return $req;
		}

		// Not cached, go ahead

		$jsRequire = array();
		$jsRequire[] = self::JS_SCRIPT_BEGIN;

		$allKeys = array();

		foreach ($this->appPaths as $key => $file)
		{
			if ($this->_isLegacyPlugin($key))
			{
				continue;
			}

			$allKeys[] = $key;
		}

		$urlRoot = JUri::root();

		$initRequire = str_replace('{ALL_KEYS}', json_encode($allKeys), self::JS_REQ_INIT);
		$initRequire = str_replace('{URL_ROOT}', $urlRoot, $initRequire);

		$jsRequire[] = $initRequire;
		$jsRequire[] = self::JS_SCRIPT_END;

		$req = implode('', $jsRequire);

		// Caching
		$platform->setCache($requireKey, $req);

		return $req;
	}

	/**
	 * _isLegacyPlugin
	 *
	 * @param   string  $key  Param
	 *
	 * @return	bool
	 */
	private function _isLegacyPlugin($key)
	{
		return ($key == self::EXTLY_J25_JQUERY)
				|| ($key == 'ajaxbutton')
				|| ($key == 'angular')
				|| ($key == 'angular-animate')
				|| ($key == 'angular-route')
				|| ($key == 'angular-resource')
				|| ($key == 'ng-table')
				|| ($key == 'backbone')
				|| ($key == 'bootstrap')
				|| ($key == 'bootstrap-datepicker-nohide')
				|| ($key == 'bootstrap-timepicker')
				|| ($key == 'chained')
				|| ($key == 'chosen')
				|| ($key == 'image-picker')
				|| ($key == 'jstree')
				|| ($key == 'saveform')
				|| ($key == 'underscore');
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
		$pos = strpos($body, self::JS_HEAD);

		if ($pos !== false)
		{
			$body = substr($body, 0, $pos) . $jsapp . substr($body, $pos);
		}
	}

	/**
	 * _getOptimizedApp
	 *
	 * @param   array  $dependencies  Param
	 * @param   array  $paths         Param
	 *
	 * @return	void
	 */
	private function _getOptimizedApp($dependencies, $paths)
	{
		$levels = $this->_getModuleLevels($dependencies, $paths);
		$file = $this->_generateOptimizedApp($levels);

		return $file;
	}

	/**
	 * _getModuleLevels
	 *
	 * @param   array  $dependencies  Param
	 * @param   array  $paths         Param
	 *
	 * @return	void
	 */
	private function _getModuleLevels($dependencies, $paths)
	{
		$result = array();
		$linksIn = array();

		// Just for debugging
		$param_deps = $dependencies;
		$param_paths = $paths;

		foreach ($dependencies as $keyO => $deps)
		{
			if (!$deps)
			{
				continue;
			}

			if (!is_array($deps))
			{
				$deps = array($deps);
			}

			foreach ($deps as $keyI)
			{
				if (array_key_exists($keyI, $linksIn))
				{
					$linksIn[$keyI][$keyO] = $keyO;
				}
				else
				{
					$linksIn[$keyI] = array($keyO => $keyO);
				}
			}
		}

		while (!empty($paths))
		{
			$level = array();
			$next_paths = array();

			foreach ($paths as $key => $path)
			{
				if (!array_key_exists($key, $linksIn))
				{
					$level[$key] = $path;
				}
				else
				{
					$next_paths[$key] = $path;
				}
			}

			// Cleaning LinksIn
			$next_linksIn = array();

			foreach ($level as $key => $path)
			{
				foreach ($linksIn as $linksIn_key => $linksOuts)
				{
					foreach ($linksOuts as $linksOut_key)
					{
						if ($key == $linksOut_key)
						{
							unset($linksIn[$linksIn_key][$linksOut_key]);
						}
					}

					if (count($linksIn[$linksIn_key]) == 0)
					{
						unset($linksIn[$linksIn_key]);
					}
				}
			}

			// Deadlock! Dependencies can't be filled
			if ($paths == $next_paths)
			{
				$dbg = ' Deps: ' . print_r($param_deps, true);
				$dbg .= ' Paths: ' . print_r($param_paths, true);
				$dbg .= ' Result: ' . print_r($paths, true);

				throw new Exception('Deadlock! Javascript Dependencies can\'t be filled' . $dbg);
			}

			// Next paths
			$paths = $next_paths;

			// Output ready
			$result[] = $level;
		}

		$result = array_reverse($result);

		return $result;
	}

	/**
	 * _generateOptimizedModule
	 *
	 * @param   array  $levels  Param
	 *
	 * @return	string
	 */
	private function _generateOptimizedApp($levels)
	{
		$app = $this->getAppKey();
		$content = array();

		foreach ($levels as $level)
		{
			foreach ($level as $key => $file)
			{
				$c = $this->appFiles[$key];

				if ($this->_isLegacyPlugin($key))
				{
					$c .= "\ndefine('{$key}', function(){});\n";
				}

				$content[] = $c;
			}
		}

		$keyname = self::JS_DIR_TMP . $app . '.js';
		$file = JPATH_ROOT . DIRECTORY_SEPARATOR . $keyname;
		$final_content = implode("\n;\n", $content);
		JFile::write($file, $final_content);

		return $file;
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
		$host = Extly::getHost();
		$appPath = $host . $appPath;
		$site = JUri::root();

		return str_replace($site, '', $appPath);
	}
}
