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
 * Autotweet Model for info dialog.
 **/

jimport('joomla.application.component.model');

/**
 * AutotweetModelUpdate
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelUpdate extends F0FModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		require_once dirname(__FILE__) . '/../helpers/update.php';
		parent::__construct($config);
	}

	/**
	 * getComponentInfo
	 *
	 * @return	array
	 */
	public function getComponentInfo()
	{
		static $compdata = null;

		if (!$compdata)
		{
			$compdata = UpdateNgHelper::getComponentInfo();

			if (!$compdata)
			{
				$this->setError(JText::sprintf('COM_AUTOTWEET_MSG_ERROR_FILENOTFOUND', 'version information'));

				return null;
			}
		}

		return $compdata;
	}

	/**
	 * getPluginInfo
	 *
	 * @return	array
	 */
	public function getPluginInfo()
	{
		static $plugdata = null;

		if (!$plugdata)
		{
			$plugdata = UpdateNgHelper::getPluginInfo();

			if (!$plugdata)
			{
				$this->setError(JText::sprintf('COM_AUTOTWEET_MSG_ERROR_FILENOTFOUND', 'version information'));

				return null;
			}
		}

		return $plugdata;
	}

	/**
	 * getThirdpartyInfo
	 *
	 * @return	array
	 */
	public function getThirdpartyInfo()
	{
		static $thirdparty = null;

		if (!$thirdparty)
		{
			$thirdparty = UpdateNgHelper::getThirdpartyInfo();

			if (!$thirdparty)
			{
				$this->setError(JText::sprintf('COM_AUTOTWEET_MSG_ERROR_FILENOTFOUND', 'version information'));

				return null;
			}

			// Load language to get the name for unknown plugins with language support
			$jlang = JFactory::getLanguage();

			foreach ($thirdparty as $plugin)
			{
				$jlang->load($plugin['name']);
			}
		}

		return $thirdparty;
	}

	/**
	 * getSystemInfo
	 *
	 * @return	array
	 */
	public function getSystemInfo()
	{
		static $sysdata = null;

		if (!$sysdata)
		{
			$sysdata = UpdateNgHelper::getSystemInfo();

			if (!$sysdata)
			{
				$this->setError('No system info available!');

				return null;
			}
		}

		return $sysdata;
	}
}
