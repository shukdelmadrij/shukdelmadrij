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
 * UpdateNgHelper - Helper to get some infos about installed AutoTweet extensions.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class UpdateNgHelper
{
	const EXT_NOTINSTALLED = 'COM_AUTOTWEET_STATE_PLUGIN_NOTINSTALLED';
	const EXT_DISABLED = 'COM_AUTOTWEET_STATE_PLUGIN_DISABLED';
	const EXT_ENABLED = 'COM_AUTOTWEET_STATE_PLUGIN_ENABLED';
	const EXT_UNKNOWN = 'COM_AUTOTWEET_VIEW_ABOUT_VERSIONINFO_UNKNOWN';
	const SYSINFO_PHP = 1;
	const SYSINFO_MYSQL = 9;
	const SYSINFO_JOOMLA = 2;
	const SYSINFO_CURL = 3;
	const SYSINFO_SSL = 4;
	const SYSINFO_JSON = 5;
	const SYSINFO_TIMESTAMP = 6;
	const SYSINFO_NPECLOAUTH = 7;
	const SYSINFO_HMAC = 8;
	const SYSINFO_TIDY = 9;

	const SYSINFO_OK = '<span class="badge badge-success"><i class="xticon xticon-check"></i></span>';
	const SYSINFO_FAIL = '<span class="badge badge-important"><i class="xticon xticon-times-circle"></i></span>';

	/*
	 * const SERVER_INI_FILE = 'autotweetng16-test.ini';
	 */
	const SERVER_INI_FILE = 'autotweetng16.ini';
	const SERVER_INI_PATH = 'http://www.extly.com/download/';
	const COMP_INSTALL_FILE = 'a_autotweet.xml';
	const KEY_COMP = 'component';
	const FM_EXT_SOURCE = 'Extly.com';

	private static $_compinfo = null;

	private static $_pluginfo = null;

	private static $_thirdparty = null;

	// Seconds
	const CXN_TIMEOUT		= 3;

	// Seconds
	const EXEC_TIMEOUT		= 3;

	/**
	 * Helper to search an array recursive for a given value
	 *
	 * @param   string  $needle    Param
	 * @param   string  $haystack  Param
	 *
	 * @return	boolean
	 */
	private static function in_array_recursive($needle, $haystack)
	{
		$it = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));

		foreach ($it as $element)
		{
			if ($element == $needle)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * no public access (static class)
	 *
	 */
	private function __construct()
	{
		// Static class
	}

	/**
	 * getComponentInfo
	 *
	 * @return	array
	 */
	public static function getComponentInfo()
	{
		self::loadINI();

		return self::$_compinfo;
	}

	/**
	 * getPluginInfo
	 *
	 * @return	array
	 */
	public static function getPluginInfo()
	{
		self::loadINI();

		return self::$_pluginfo;
	}

	/**
	 * getThirdpartyInfo
	 *
	 * @return	array
	 */
	public static function getThirdpartyInfo()
	{
		self::loadINI();

		return self::$_thirdparty;
	}

	/**
	 * loadINI
	 *
	 * @return	bool
	 */
	protected static function loadINI()
	{
		if ((self::$_compinfo) && (self::$_pluginfo))
		{
			return true;
		}

		self::$_compinfo = array();
		self::$_pluginfo = array();
		self::$_thirdparty = array();

		// Get component parameter
		$version_check = EParameter::getComponentParam(CAUTOTWEETNG, 'version_check', 1);

		$remoteFile = self::SERVER_INI_PATH . self::SERVER_INI_FILE;
		$localFile = JPATH_AUTOTWEET_HELPERS . '/' . self::SERVER_INI_FILE;
		$file = $localFile;

		if ($version_check)
		{
			try
			{
				$ch = curl_init($remoteFile);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CXN_TIMEOUT);
				curl_setopt($ch, CURLOPT_TIMEOUT, self::EXEC_TIMEOUT);

				$data = curl_exec($ch);
				curl_close($ch);
				file_put_contents($localFile, $data);
			}
			catch (Exception $e)
			{
				$msg = $e->getMessage();
				$logger->log(JLog::ERROR, 'AutoTweetNG - ' . $msg);
			}
		}

		jimport('joomla.error.error');
		jimport('joomla.registry.registry');
		$registry = new JRegistry;

		if (!$registry->loadFile(
						$file,
						'INI',
					array(
						'processSections' => 'true'
					)
				))
		{
			$logger->log(JLog::ERROR, 'AutoTweetNG - error reading INI file ' . $file);

			return false;
		}

		// Init logging
		$logger = AutotweetLogger::getInstance();

		$db = JFactory::getDBO();

		// Get component info and remove from array
		$data = JApplicationHelper::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . self::COMP_INSTALL_FILE);
		self::$_compinfo = array(
						'id' => $registry->get('component.id'),
						'name' => $registry->get('component.name'),
						'server_version' => $registry->get('component.version'),
						'client_version' => $data['version'],
						'home' => $registry->get('component.home'),
						'faq' => $registry->get('component.faq'),
						'download' => $registry->get('component.download'),
						'support' => $registry->get('component.support'),
						'products' => $registry->get('component.products'),
						'twitter' => $registry->get('component.twitter'),
						'jed' => $registry->get('component.jed'),
						'message' => $registry->get('component.message'),
						'news' => $registry->get('component.news')
		);
		$extensions = TextUtil::listToArray($registry->get('component.extensions'));

		foreach ($extensions as $extension)
		{
			$state = self::EXT_NOTINSTALLED;
			$config = '';
			$client_version = '';
			$type = $registry->get($extension . '.type');
			$id = $registry->get($extension . '.id');
			$source = $registry->get($extension . '.source');

			if ('module' == $type)
			{
				$mod_filename = 'mod_' . $id;

				// Get the module id and set url for config
				$pluginsModel = F0FModel::getTmpInstance('Extensions', 'ExtlyModel');
				$pluginsModel->savestate(false)->setState('element', $mod_filename);
				$rows = $pluginsModel->getItemList();

				if (!empty($rows))
				{
					$row = $rows[0];

					if ($row->client_id)
					{
						$path = JPATH_ADMINISTRATOR . '/modules/' . $mod_filename . DIRECTORY_SEPARATOR . $mod_filename . '.xml';
					}
					else
					{
						$path = JPATH_ROOT . '/modules/' . $mod_filename . DIRECTORY_SEPARATOR . $mod_filename . '.xml';
					}

					$data = JApplicationHelper::parseXMLInstallFile($path);
					$client_version = $data['version'];

					if (self::_isEnabled($mod_filename))
					{
						$state = self::EXT_ENABLED;
					}
					else
					{
						$state = self::EXT_DISABLED;
					}

					// $config = 'index.php?option=com_modules&task=module.edit&id=' . $row->extension_id;
				}
			}
			else
			{
				// Get the plugin id and set url for config
				$pluginsModel = F0FModel::getTmpInstance('Plugins', 'AutoTweetModel');
				$pluginsModel->savestate(false)->set('element_id', $id);
				$rows = $pluginsModel->getItemList();

				if (!empty($rows))
				{
					$row = $rows[0];
					$path = JPATH_PLUGINS . DIRECTORY_SEPARATOR . $row->folder . DIRECTORY_SEPARATOR . $row->element . DIRECTORY_SEPARATOR . $row->element . '.xml';
					$data = JApplicationHelper::parseXMLInstallFile($path);

					$client_version = $data['version'];

					if (JPluginHelper::isEnabled($row->folder, $row->element))
					{
						$state = self::EXT_ENABLED;
					}
					else
					{
						$state = self::EXT_DISABLED;
					}

					$config = 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $row->id;
				}
			}

			// Append plugin state to result arrays
			if (self::FM_EXT_SOURCE == $source)
			{
				self::$_pluginfo[] = array(
								'id' => $id,
								'name' => $registry->get($extension . '.name'),
								'state' => $state,
								'client_version' => $client_version,
								'server_version' => $registry->get($extension . '.version'),
								'message' => $registry->get($extension . '.message'),
								'config' => $config
				);
			}
			else
			{
				self::$_thirdparty[] = array(
								'id' => $id,
								'name' => $registry->get($extension . '.name'),
								'state' => $state,
								'client_version' => $client_version,
								'message' => $registry->get($extension . '.message'),
								'config' => $config,
								'source' => $source,
								'download' => $registry->get($extension . '.download')
				);
			}
		}

		// Add installed plugins without entry in ini file to 3rd party list
		$pluginsModel = F0FModel::getTmpInstance('Plugins', 'AutoTweetModel');
		$pluginsModel->savestate(false);
		$plugins = $pluginsModel->getItemList();

		foreach ($plugins as $plugin)
		{
			$id = $plugin->element;
			$type = $plugin->folder;

			if (!self::in_array_recursive($id, self::$_pluginfo) && !self::in_array_recursive($id, self::$_thirdparty))
			{
				$path = JPATH_PLUGINS . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $id . '.xml';
				$data = JApplicationHelper::parseXMLInstallFile($path);

				$client_version = $data['version'];

				if (JPluginHelper::isEnabled($type, $id))
				{
					$state = self::EXT_ENABLED;
				}
				else
				{
					$state = self::EXT_DISABLED;
				}

				$config = 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $plugin->id;

				if (!empty($data['authorUrl']))
				{
					$source = $data['authorUrl'];
					$download = $data['authorUrl'];
				}
				else
				{
					$source = self::EXT_UNKNOWN;
					$download = '';
				}

				self::$_thirdparty[] = array(
								'id' => $id,
								'name' => $plugin->name,
								'state' => $state,
								'client_version' => $client_version,
								'message' => 'unknown extension plugin',
								'config' => $config,
								'source' => $source,
								'download' => $download
				);
			}
		}

		return true;
	}

	/**
	 * getSystemInfo
	 *
	 * @return	array
	 */
	public static function getSystemInfo()
	{
		try
		{
			$sysinfo = array();

			// PHP Version
			// Check for PHP4
			if (defined('PHP_VERSION'))
			{
				$version = PHP_VERSION;
			}
			elseif (function_exists('phpversion'))
			{
				$version = phpversion();
			}
			else
			{
				// No version info. I'll lie and hope for the best.
				$version = '5.0.0';
			}

			$db = JFactory::getDBO();
			$sysinfo[self::SYSINFO_PHP] = version_compare($version, '5.3.0', '>=');
			$sysinfo[self::SYSINFO_MYSQL] = version_compare($db->getVersion(), '5.5.0', '>=');
			$sysinfo[self::SYSINFO_CURL] = function_exists('curl_init');
			$sysinfo[self::SYSINFO_SSL] = function_exists('openssl_get_publickey');
			$sysinfo[self::SYSINFO_JSON] = function_exists('json_encode');
			$sysinfo[self::SYSINFO_NPECLOAUTH] = !class_exists('OAuthRequest');
			$sysinfo[self::SYSINFO_HMAC] = function_exists('hash_hmac');
			$sysinfo[self::SYSINFO_TIDY] = function_exists('tidy_parse_string');
			$sysinfo[self::SYSINFO_TIMESTAMP] = TwAppHelper::checkTimestamp();

			return $sysinfo;
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * _isEnabled
	 *
	 * @param   string  $module_name  Param
	 *
	 * @return	bool
	 */
	private static function _isEnabled($module_name)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid')
				->from('#__modules AS m')
				->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id')
				->where('m.published = 1')
				->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
				->where('e.enabled = 1')
				->where('m.module = ' . $db->q($module_name));
		$db->setQuery($query);
		$module = $db->loadObject();

		if ($module)
		{
			return true;
		}

		return false;
	}
}
