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
 * AutotweetModelLiveUpdates - The updates provisioning Model
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelLiveUpdates extends F0FUtilsUpdate
{
	const CONFIG_AUTOUPDATE_NOTIFY_UPDATE = 1;
	const CONFIG_AUTOUPDATE_NOTIFY = 2;
	const CONFIG_AUTOUPDATE_UPDATE = 3;

	const CONFIG_AUTOUPDATE_STABLE = 0;
	const CONFIG_AUTOUPDATE_BETA = 1;

	/**
	 * Public constructor. Initialises the protected members as well.
	 *
	 * @param   array  $config  Param
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$dlid = EParameter::getComponentParam(CAUTOTWEETNG, 'update_dlid', '');
		$this->extraQuery = '';

		// If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			$this->extraQuery = 'dlid=' . $dlid;
		}

		$this->updateSiteName = VersionHelper::getFlavourName();
		$this->updateSite = VersionHelper::getUpdatesSite();

		$minstability = EParameter::getComponentParam(CAUTOTWEETNG, 'minstability');

		if ($minstability == self::CONFIG_AUTOUPDATE_BETA)
		{
			// Live site
			$this->updateSite .= '-beta';
		}

		// $this->updateSite = 'http://www.extly.com/update-autotweetng-free-test.xml';
		// $this->updateSite = 'http://www.extly.com/update-autotweetng-joocial-test.xml';
	}

	/**
	 * autoupdate
	 *
	 * @return	array
	 */
	/*
	public function autoupdate()
	{
		$return = array(
						'message' => ''
		);

		^^ First of all let's check if there are any updates
		$updateInfo = (object) $this->getUpdates(true);

		^^ There are no updates, there's no point in continuing
		if (!$updateInfo->hasUpdate)
		{
			return array(
				'message' => array(
					"No available updates found"
				)
			);
		}

		$return['message'][] = "Update detected, version: " . $updateInfo->version;

		^^ Ok, an update is found, what should I do?
		$autoupdate = EParameter::getComponentParam(CAUTOTWEETNG, 'autoupdateCli', self::CONFIG_AUTOUPDATE_NOTIFY);

		^^ Let's notifiy the user
		if ($autoupdate == self::CONFIG_AUTOUPDATE_NOTIFY_UPDATE || $autoupdate == self::CONFIG_AUTOUPDATE_NOTIFY)
		{
			$email = $params->get('notificationEmail');

			if (!$email)
			{
				$return['message'][] = "There isn't an email for notifications, no notification will be sent.";
			}
			else
			{
				^^ Ok, I can send it out, but before let's check if the user set any frequency limit
				$numfreq = $params->get('notificationFreq', 1);
				$freqtime = $params->get('notificationTime', 'day');
				$lastSend = $this->getLastSend();
				$shouldSend = false;

				if (!$numfreq)
				{
					$shouldSend = true;
				}
				else
				{
					$check = strtotime('-' . $numfreq . ' ' . $freqtime);

					if ($lastSend < $check)
					{
						$shouldSend = true;
					}
					else
					{
						$return['message'][] = "Frequency limit hit, I won't send any email";
					}
				}

				if ($shouldSend)
				{
					if ($this->sendNotificationEmail($updateInfo->version, $email))
					{
						$return['message'][] = "E-mail(s) correctly sent";
					}
					else
					{
						$return['message'][] = "An error occurred while sending e-mail(s). Please double check your settings";
					}

					$this->setLastSend();
				}
			}
		}

		^^ Let's download and install the latest version
		if ($autoupdate == self::CONFIG_AUTOUPDATE_NOTIFY_UPDATE || $autoupdate == self::CONFIG_AUTOUPDATE_UPDATE)
		{
			if (EParameter::getComponentParam(CAUTOTWEETNG, 'update_dlid'))
			{
				$return['message'][] = "You have to enter the DownloadID in order to update your pro version";
			}
			else
			{
				$return['message'][] = $this->updateComponent();
			}
		}

		return $return;
	}
*/
	/**
	 * sendNotificationEmail
	 *
	 * @param   string  $version  Param
	 * @param   string  $email    Param
	 *
	 * @return	bool
	 */
/*
	private function sendNotificationEmail($version, $email)
	{
		$email_subject = <<<ENDSUBJECT
THIS EMAIL IS SENT FROM YOUR SITE "[SITENAME]" - Update available
ENDSUBJECT;

		$email_body = <<<ENDBODY
This email IS NOT sent by the authors of AutoTweetNG/ Joocial. It is sent automatically
by your own site, [SITENAME]

================================================================================
UPDATE INFORMATION
================================================================================

Your site has determined that there is an updated version of AutoTweetNG/ Joocial
available for download.

New version number: [VERSION]

This email is sent to you by your site to remind you of this fact. The authors
of the software will never contact you about available updates.

================================================================================
WHY AM I RECEIVING THIS EMAIL?
================================================================================

This email has been automatically sent by a CLI script you, or the person who built
or manages your site, has installed and explicitly activated. This script looks
for updated versions of the software and sends an email notification to all
Super Users. You will receive several similar emails from your site, up to 6
times per day, until you either update the software or disable these emails.

To disable these emails, please contact your site administrator.

If you do not understand what this means, please do not contact the authors of
the software. They are NOT sending you this email and they cannot help you.
Instead, please contact the person who built or manages your site.

================================================================================
WHO SENT ME THIS EMAIL?
================================================================================

This email is sent to you by your own site, [SITENAME]

ENDBODY;

		$jconfig = JFactory::getConfig();
		$sitename = $jconfig->get('sitename');

		$substitutions = array(
						'[VERSION]' => $version,
						'[SITENAME]' => $sitename
		);

		$email_subject = str_replace(array_keys($substitutions), array_values($substitutions), $email_subject);
		$email_body = str_replace(array_keys($substitutions), array_values($substitutions), $email_body);

		$mailer = JFactory::getMailer();

		$mailfrom = $jconfig->get('mailfrom');
		$fromname = $jconfig->get('fromname');

		$mailer->setSender(
				array(
						$mailfrom,
						$fromname
			)
		);

		$mailer->addRecipient($email);
		$mailer->setSubject($email_subject);
		$mailer->setBody($email_body);

		return $mailer->Send();
	}
	*/

	/**
	 * updateComponent
	 *
	 * @return	string
	 */
	/*
	private function updateComponent()
	{
		JLoader::import('joomla.updater.update');

		$db = JFactory::getDbo();

		$update_site = array_shift($this->getUpdateSiteIds());

		$query = $db->getQuery(true)->select($db->qn('update_id'))->from($db->qn('#__updates'))->where($db->qn('update_site_id') . ' = ' . $update_site);

		$uid = $db->setQuery($query)->loadResult();

		$update = new JUpdate;
		$instance = JTable::getInstance('update');
		$instance->load($uid);
		$update->loadFromXML($instance->detailsurl);

		if (isset($update->get('downloadurl')->_data))
		{
			$url = trim($update->downloadurl->_data);
		}
		else
		{
			return "No download URL found inside XML manifest";
		}

		$config = JFactory::getConfig();
		$tmp_dest = $config->get('tmp_path');

		if (!$tmp_dest)
		{
			return "Joomla temp directory is empty, please set it before continuing";
		}
		elseif (!JFolder::exists($tmp_dest))
		{
			return "Joomla temp directory does not exists, please set the correct path before continuing";
		}

		$p_file = JInstallerHelper::downloadPackage($url);

		if (!$p_file)
		{
			return "An error occurred while trying to download the latest version, double check your Download ID";
		}

		^^ Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

		if (!$package)
		{
			return "An error occurred while unpacking the file, please double check your Joomla temp directory";
		}

		$installer = new JInstaller;
		$installed = $installer->install($package['extractdir']);

		^^ Let's cleanup the downloaded archive and the temp folder
		if (JFolder::exists($package['extractdir']))
		{
			JFolder::delete($package['extractdir']);
		}

		if (JFile::exists($package['packagefile']))
		{
			JFile::delete($package['packagefile']);
		}

		if ($installed)
		{
			return "Component successfully updated";
		}
		else
		{
			return "An error occurred while trying to update the component";
		}
	}
	*/

	/**
	 * getLastSend
	 *
	 * @return	int
	 */
	/*
	private function getLastSend()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)->select($db->qn('lastexec'))->from($db->qn('#__autotweet_automator'))->where($db->qn('plugin') . ' = ' . $db->q('autoupdate_lastsend'));
		$result = $db->setQuery($query)->loadResult();

		if (!$result)
		{
			return 0;
		}
		else
		{
			$date = new JDate($result);

			return $date->toUnix();
		}
	}
	*/

	/**
	 * setLastSend
	 *
	 * @return	int
	 */
	/*
	private function setLastSend()
	{
		$db = JFactory::getDbo();

		$now = new JDate;

		$query = $db->getQuery(true)->select('COUNT(*)')->from($db->qn('#__autotweet_automator'))->where($db->qn('plugin') . ' = ' . $db->q('autoupdate_lastsend'));
		$count = $db->setQuery($query)->loadResult();

		if ($count)
		{
			$query = $db->getQuery(true)->update($db->qn('#__autotweet_automator'))->set($db->qn('lastexec') . ' = ' . $db->q($now->toSql()))->where($db->qn('plugin') . ' = ' . $db->q('autoupdate_lastsend'));
			$db->setQuery($query)->execute();
		}
		else
		{
			$data = (object) array(
				'plugin' => 'autoupdate_lastsend',
				'lastexec' => $now->toSql()
			);

			$db->insertObject('#__autotweet_automator', $data);
		}
	}
	*/
}
