<?php

/**
 * @package     Extly.Components
 * @subpackage  autotweet - Plugin AutoTweetNG Installer-Extension
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Handle commercial extension update authorization
 *
 * @package     Joomla.Plugin
 * @subpackage  Installer.AutoTweetNG
 * @since       2.5
 */
class PlgInstallerAutoTweet extends JPlugin
{
	/**
	 * @var    String  your extension identifier, to retrieve its params
	 * @since  2.5
	 */
	private $extension = 'com_autotweet';

	/**
	 * Handle adding credentials to package download request
	 *
	 * @param   string  &$url      Url from which package is going to be downloaded
	 * @param   array   &$headers  Headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean true if credentials have been added to request or not our business, false otherwise (credentials not set by user)
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri = JUri::getInstance($url);

		/*
		  I don't care about download URLs not coming from our site
		  Note: as the Download ID is common for all extensions, this plugin will be triggered for all
		  extensions with a download URL on our site
		 */
		$host = $uri->getHost();

		if ($host == 'xtl-repo.s3.amazonaws.com')
		{
			$url .= '&dummy=my.zip';

			return true;
		}

		if (!in_array(
				$host,
				array(
						'www.extly.com',
						'cdn.extly.com'
				)
			))
		{
			return true;
		}

		// Get the download ID
		JLoader::import('joomla.application.component.helper');
		$component = JComponentHelper::getComponent($this->extension);

		$dlid = $component->params->get('update_dlid', '');

		// If the download ID is invalid, return without any further action
		if (!preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			return true;
		}

		// Appent the Download ID to the download URL
		if (!empty($dlid))
		{
			$uri->setVar('dlid', $dlid);
			$url = $uri->toString();
		}

		return true;
	}
}
