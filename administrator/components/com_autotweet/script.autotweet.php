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
defined('_JEXEC') or die();

// Load FOF if not already loaded
if (!defined('F0F_INCLUDED'))
{
	$paths = array(
					(defined('JPATH_LIBRARIES') ? JPATH_LIBRARIES : JPATH_ROOT . '/libraries') . '/f0f/include.php',
					__DIR__ . '/fof/include.php'
	);

	foreach ($paths as $filePath)
	{
		if ((!defined('F0F_INCLUDED')) && file_exists($filePath))
		{
			@include_once $filePath;
		}
	}
}

// Pre-load the installer script class from our own copy of FOF
if (!class_exists('F0FUtilsInstallscript', false))
{
	@include_once __DIR__ . '/fof/utils/installscript/installscript.php';
}

// Pre-load the database schema installer class from our own copy of FOF
if (!class_exists('F0FDatabaseInstaller', false))
{
	@include_once __DIR__ . '/fof/database/installer.php';
}

// Pre-load the update utility class from our own copy of FOF
if (!class_exists('F0FUtilsUpdate', false))
{
	@include_once __DIR__ . '/fof/utils/update/update.php';
}

/**
 * Com_AutoTweetInstallerScript
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class Com_AutoTweetInstallerScript extends F0FUtilsInstallscript
{
	/**
	 * The title of the component (printed on installation and uninstallation messages)
	 *
	 * @var string
	 */
	protected $componentTitle = 'AutoTweetNG';

	/**
	 * The component's name
	 *
	 * @var   string
	 */
	protected $componentName = 'com_autotweet';

	/**
	 * The list of extra modules and plugins to install on component installation / update and remove on component
	 * uninstallation.
	 *
	 * @var   array
	 */
	protected $installation_queue = array(

					// Extension: modules => { (folder) => { (module) => { (position), (published) } }* }*
					'modules' => array(
									'admin' => array(
													'autotweet_latest' => array(
																	'cpanel',
																	0
													)
									),
									'site' => array(
													'twfollow' => array(
																	'left',
																	0
													),
													'light_rss' => array(
																	'left',
																	0
													)
									)
					),

					// Extension: plugins => { (folder) => { (element) => (published) }* }*
					'plugins' => array(
									'system' => array(
													'autotweetautomator' => 1,
													'autotweetcontent' => 1
									),
									'installer' => array(
													'autotweet' => 1
									),
									'autotweet' => array(
													'autotweetpost' => 1
									)
					)
	);

	/**
	 * Obsolete files and folders to remove from the free version only. This is used when you move a feature from the
	 * free version of your extension to its paid version. If you don't have such a distinction you can ignore this.
	 *
	 * @var   array
	 */
	protected $removeFilesFree = array(
					'files' => array()

					,
					'folders' => array()
	);

	/**
	 * Obsolete files and folders to remove from both paid and free releases. This is used when you refactor code and
	 * some files inevitably become obsolete and need to be removed.
	 *
	 * @var   array
	 */
	protected $removeFilesAllVersions = array(
					'files' => array(
									// AutoTweetNG 6.3, and before
									'administrator/components/com_autotweet/autotweet.xml',
									'administrator/components/com_autotweet/controller.php',
									'administrator/components/com_autotweet/controllers/account.json.php',
									'administrator/components/com_autotweet/controllers/account.php',
									'administrator/components/com_autotweet/controllers/account.raw.php',
									'administrator/components/com_autotweet/controllers/accounts.php',
									'administrator/components/com_autotweet/controllers/accounts.raw.php',
									'administrator/components/com_autotweet/controllers/autotweetentries.php',
									'administrator/components/com_autotweet/controllers/autotweetentry.php',
									'administrator/components/com_autotweet/controllers/autotweet.php',
									'administrator/components/com_autotweet/controllers/autotweet.raw.php',
									'administrator/components/com_autotweet/controllers/dashboard.php',
									'administrator/components/com_autotweet/controllers/fbwizzardbaseaccount.php',
									'administrator/components/com_autotweet/controllers/fbwizzardbaseaccounts.php',
									'administrator/components/com_autotweet/controllers/fbwizzardbaseaccounts.raw.php',
									'administrator/components/com_autotweet/controllers/manualmessage.php',
									'administrator/components/com_autotweet/controllers/queue.php',
									'administrator/components/com_autotweet/controllers/rule.php',
									'administrator/components/com_autotweet/controllers/rules.php',
									'administrator/components/com_autotweet/helpers/autotweetcronjob.php',
									'administrator/components/com_autotweet/helpers/autotweetextaccesshelper.php',
									'administrator/components/com_autotweet/helpers/autotweetguihelper.php',
									'administrator/components/com_autotweet/helpers/autotweetinfohelper.php',
									'administrator/components/com_autotweet/helpers/autotweetng16-test.ini',
									'administrator/components/com_autotweet/helpers/autotweetng.ini',
									'administrator/components/com_autotweet/helpers/autotweetpluginfactory.php',
									'administrator/components/com_autotweet/helpers/autotweetroutehelper.php',
									'administrator/components/com_autotweet/helpers/autotweetruleengine.php',
									'administrator/components/com_autotweet/helpers/autotweetutil.php',
									'administrator/components/com_autotweet/helpers/autotweetview.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetaccountfactory.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetchannelfactoryhelper.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetchannel.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetchannelfactory',
									'administrator/components/com_autotweet/helpers/channels/autotweetfacebookaccount.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetfacebookbase.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetfacebookevent.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetfacebooklink.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetfacebook.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetlinkedinbase.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetlinkedingroup.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetlinkedin.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetmail.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetpingfm.php',
									'administrator/components/com_autotweet/helpers/channels/autotweetseesmicping.php',
									'administrator/components/com_autotweet/helpers/channels/autotweettwitter.php',
									'administrator/components/com_autotweet/helpers/channels/fmhttpcodehelper.php',
									'administrator/components/com_autotweet/helpers/fmregistryhelper.php',
									'administrator/components/com_autotweet/helpers/urlshortservices/autotweetshortservicefactory.php',
									'administrator/components/com_autotweet/install.autotweet.php',
									'administrator/components/com_autotweet/installer.php',
									'administrator/components/com_autotweet/models/account.php',
									'administrator/components/com_autotweet/models/accounts.php',
									'administrator/components/com_autotweet/models/autotweetentries.php',
									'administrator/components/com_autotweet/models/autotweetentry.php',
									'administrator/components/com_autotweet/models/autotweet.php',
									'administrator/components/com_autotweet/models/fbwizzardbaseaccount.php',
									'administrator/components/com_autotweet/models/fbwizzardbaseaccounts.php',
									'administrator/components/com_autotweet/models/info.php',
									'administrator/components/com_autotweet/models/manualmessage.php',
									'administrator/components/com_autotweet/models/queueentry.php',
									'administrator/components/com_autotweet/models/queue.php',
									'administrator/components/com_autotweet/models/rule.php',
									'administrator/components/com_autotweet/sql/install.sql',
									'administrator/components/com_autotweet/sql/uninstall.sql',
									'administrator/components/com_autotweet/tables/autotweetaccount.php',
									'administrator/components/com_autotweet/tables/autotweetautomator.php',
									'administrator/components/com_autotweet/tables/autotweetchannel.php',
									'administrator/components/com_autotweet/tables/autotweetchanneltype.php',
									'administrator/components/com_autotweet/tables/autotweetmsglog.php',
									'administrator/components/com_autotweet/tables/autotweetqueue.php',
									'administrator/components/com_autotweet/tables/autotweetrule.php',
									'administrator/components/com_autotweet/tables/autotweetruletype.php',
									'administrator/components/com_autotweet/uninstall.autotweet.php',
									'administrator/components/com_autotweet/views/rule/tmpl/default.php',
									'administrator/components/com_autotweet/views/rule/tmpl/edit.php',

									'administrator/components/com_autotweet/views/target/tmpl/criteria_template.php',

									// Extly Lib - Old CronParser
									'libraries/extly/helpers/CronParser.php',

									// German removal
									'administrator/language/de-DE/de-DE.com_autotweet.ini',
									'administrator/language/de-DE/de-DE.com_autotweet.sys.ini',
									'language/de-DE/de-DE.com_autotweet.ini',
									'language/de-DE/de-DE.com_autotweet.sys.ini',

									// Extly Lib - Old Css
									'media/lib_extly/css/extly-base.css',

									// Extly Lib - IE Shim
									'media/lib_extly/js/ie.js',
									'media/lib_extly/js/utils/ie.js',
									'media/lib_extly/js/utils/ie.min.js',

									// Utils
									'media/lib_extly/js/utils/tourist.js',
									'media/lib_extly/js/utils/tourist.min.js',

									// Utils
									'media/lib_extly/js/jquery/jquery.min.js',
									'media/lib_extly/js/jquery/jquery-migrate.min.js',
									'media/lib_extly/js/jquery/jquery-noconflict.js',

									// Images
									'media/lib_extly/images/a-pizza.jpg',

									// Extly Lib - defaultmain.js
									'media/lib_extly/js/defaultmain.js',
									'media/lib_extly/js/defaultmain.min.js',

									// Extly Lib - jquery.disabled.js
									'media/lib_extly/js/jquery/jquery.disabled.js',

									// Extly Lib - lodash.min.js new version
									'media/lib_extly/js/lodash.min.js',
									'media/lib_extly/js/backbone/lodash.min.js',
									'media/lib_extly/js/backbone/lodash.underscore.min.js',

									// Extlycorefront no more
									'media/lib_extly/js/extlycorefront.js',
									'media/lib_extly/js/extlycorefront.min.js',

									// No social 64 icons
									'media/com_autotweet/images/mail-forward.png',
									'media/com_autotweet/images/social_facebook_box_blue_64.png',
									'media/com_autotweet/images/social_linkedin_box_blue_64.png',
									'media/com_autotweet/images/social_twitter_box_blue_64.png',

									// - browseview
									'administrator/components/com_autotweet/helpers/browseview.php',

									// Twitter Lib 0.8.3
									'administrator/components/com_autotweet/helpers/channels/tmhOAuth/tmhUtilities.php',

									// Extly Lib - Styles
									'media/lib_extly/css/extly-base660.css',
									'media/lib_extly/css/extly-base-2_5_12.css',
									'media/lib_extly/css/extly-base-2_5_16.css',
									'media/lib_extly/css/extly-base-2_5_17.css',
									'media/lib_extly/css/extly-base-2_5_19.css',
									'media/lib_extly/css/extly-font-awesome.min.css',
									'media/lib_extly/css/extly-font-awesome-2_5_19.min.css',

									// Deprecated v7.5.0
									'administrator/components/com_autotweet/helpers/channels/facebookevent.php',
									'administrator/components/com_autotweet/helpers/channels/facebookvideo.php',

									// --- Joocial Reset --- BEGIN ---
									// Target reset
									'media/com_autotweet/js/target.js',
									'media/com_autotweet/js/target.min.js',
									'administrator/components/com_autotweet/controllers/targets.php',
									'administrator/components/com_autotweet/models/targets.php',
									'administrator/components/com_autotweet/tables/target.php',
									// ItemEditor reset
									'media/com_autotweet/js/itemeditor.js',
									'media/com_autotweet/js/itemeditor.min.js',
									'media/com_autotweet/js/itemeditor.helper.min.js',
									'media/com_autotweet/js/shortcuts.js',
									'media/com_autotweet/js/shortcuts.min.js',
									'administrator/components/com_autotweet/controllers/itemeditors.php',
									// Jootool reset
									'media/com_autotweet/js/jootool.js',
									'media/com_autotweet/js/jootool.min.js',
									// Virtual Manager reset
									'media/com_autotweet/js/manager.js',
									'media/com_autotweet/js/manager.min.js',
									'administrator/components/com_autotweet/controllers/managers.php',
									'administrator/components/com_autotweet/models/extensions.php',
									'administrator/components/com_autotweet/tables/extension.php',
									// Composer reset
									'administrator/components/com_autotweet/views/composer/tmpl/1-2-scheduler.php'
									// --- Joocial Reset --- END ---
										),

					'folders' => array(
									// Legacy Reset
									'administrator/components/com_autotweet/assets',
									'administrator/components/com_autotweet/cronjob',
									'administrator/components/com_autotweet/help',
									'administrator/components/com_autotweet/helpers/channels/atfacebook',
									'administrator/components/com_autotweet/helpers/channels/atlinkedin',
									'administrator/components/com_autotweet/helpers/channels/atmailer',
									'administrator/components/com_autotweet/helpers/channels/atpingfm',
									'administrator/components/com_autotweet/helpers/channels/attwitter',
									'administrator/components/com_autotweet/helpers/jsonwrapper',
									'administrator/components/com_autotweet/language',
									'administrator/components/com_autotweet/models/fields',
									'administrator/components/com_autotweet/models/forms',
									'administrator/components/com_autotweet/views/account',
									'administrator/components/com_autotweet/views/accounts',
									'administrator/components/com_autotweet/views/autotweet',
									'administrator/components/com_autotweet/views/autotweetentries',
									'administrator/components/com_autotweet/views/autotweetentry',
									'administrator/components/com_autotweet/views/dashboard',
									'administrator/components/com_autotweet/views/fbwizzardbaseaccount',
									'administrator/components/com_autotweet/views/fbwizzardbaseaccounts',
									'administrator/components/com_autotweet/views/info',
									'administrator/components/com_autotweet/views/manualmessage',
									'administrator/components/com_autotweet/views/queue',

									// Extly Lib - Scheduler backend
									'libraries/extly/scheduler/backend',
									'media/lib_extly/js/scheduler',

									// FontAwesome png 16x16 icons
									'media/lib_extly/images/icons',

									// --- Joocial Reset --- BEGIN ---
									// Target reset
									'media/com_autotweet/js/target',
									'administrator/components/com_autotweet/views/target',
									'administrator/components/com_autotweet/views/targets',
									// ItemEditor reset
									'media/com_autotweet/js/itemeditor',
									'administrator/components/com_autotweet/views/itemeditor',
									'components/com_autotweet/views/itemeditor',
									// Jootool reset
									'components/com_autotweet/views/jootool',
									'components/com_autotweet/views/cpanels',
									'components/com_autotweet/views/post',
									'components/com_autotweet/views/posts',
									'components/com_autotweet/views/channel',
									'components/com_autotweet/views/channels',
									'components/com_autotweet/views/fbchannel',
									'components/com_autotweet/views/gpluschannel',
									'components/com_autotweet/views/lichannel',
									'components/com_autotweet/views/licompanychannel',
									'components/com_autotweet/views/ligroupchannel',
									'components/com_autotweet/views/mailchannel',
									'components/com_autotweet/views/nochannel',
									'components/com_autotweet/views/twchannel',
									'components/com_autotweet/views/vkchannel',
									// Virtual Manager reset
									'administrator/components/com_autotweet/views/manager'
					// --- Joocial Reset --- END ---
										)
	);

	/**
	 * A list of scripts to be copied to the "cli" directory of the site
	 *
	 * @var   array
	 */
	protected $cliScriptFiles = array(
					'autotweetstartcronjob.php'
	);

	/**
	 * Joomla! pre-flight event. This runs before Joomla! installs or updates the component. This is our last chance to
	 * tell Joomla! if it should abort the installation.
	 *
	 * @param   string      $type    Installation type (install, update, discover_install)
	 * @param   JInstaller  $parent  Parent object
	 *
	 * @return  boolean  True to let the installation proceed, false to halt the installation
	 */
	public function preflight($type, $parent)
	{
		$result = parent::preflight($type, $parent);

		// PHP 5.2, Thou shall not pass | Anonymous function variable assignment example
		if ($result)
		{
			try
			{
				$date = new DateTime;
				$now = $date->getTimestamp();
				$greet = function ($name)
				{
					return sprintf("Hello %s\r\n", $name);
				};

				$test = $greet('Extly');
			}
			catch (Exception $e)
			{
				$msg = "<p>You need PHP $this->minimumPHPVersion or later to install this component</p>";

				if (version_compare(JVERSION, '3.0', 'gt'))
				{
					JLog::add($msg, JLog::WARNING, 'jerror');
				}
				else
				{
					JError::raiseWarning(100, $msg);
				}

				return false;
			}
		}

		return $result;
	}

	/**
	 * Installs Extly Library if necessary
	 *
	 * @param   JInstaller  $parent  The parent object
	 *
	 * @return  array  The installation status
	 */
	protected function installStrapper($parent)
	{
		$src = $parent->getParent()->getPath('source');
		$source = $src . '/' . $this->strapperSourcePath;
		$source_file = $source . '/lib_extly/version.txt';

		$target = JPATH_ROOT . '/libraries/extly';
		$target_file = $target . '/version.txt';

		if (!JFolder::exists($source))
		{
			return array(
							'required' => false,
							'installed' => false,
							'version' => '0.0.0',
							'date' => '2011-01-01'
			);
		}

		$haveToInstallStrapper = false;

		if (!JFolder::exists($target))
		{
			$haveToInstallStrapper = true;
		}
		else
		{
			$strapperVersion = array();

			if (JFile::exists($target_file))
			{
				$rawData = JFile::read($target_file);
				$rawData = ($rawData === false) ? "0.0.0\n2011-01-01\n" : $rawData;
				$info = explode("\n", $rawData);
				$strapperVersion['installed'] = array(
								'version' => trim($info[0]),
								'date' => new JDate(trim($info[1]))
				);
			}
			else
			{
				$strapperVersion['installed'] = array(
								'version' => '0.0',
								'date' => new JDate('2011-01-01')
				);
			}

			$rawData = JFile::read($source_file);
			$rawData = ($rawData === false) ? "0.0.0\n2011-01-01\n" : $rawData;
			$info = explode("\n", $rawData);
			$strapperVersion['package'] = array(
							'version' => trim($info[0]),
							'date' => new JDate(trim($info[1]))
			);

			$haveToInstallStrapper = $strapperVersion['package']['date']->toUNIX() > $strapperVersion['installed']['date']->toUNIX();
		}

		$installedStraper = false;

		if ($haveToInstallStrapper)
		{
			$versionSource = 'package';
			$installer = new JInstaller;
			$installedStraper = $installer->install($source . '/lib_extly');
		}
		else
		{
			$versionSource = 'installed';
		}

		if (!isset($strapperVersion))
		{
			$strapperVersion = array();

			if (JFile::exists($target_file))
			{
				$rawData = JFile::read($target_file);
				$rawData = ($rawData === false) ? "0.0.0\n2011-01-01\n" : $rawData;
				$info = explode("\n", $rawData);
				$strapperVersion['installed'] = array(
								'version' => trim($info[0]),
								'date' => new JDate(trim($info[1]))
				);
			}
			else
			{
				$strapperVersion['installed'] = array(
								'version' => '0.0',
								'date' => new JDate('2011-01-01')
				);
			}

			$rawData = JFile::read($source_file);
			$rawData = ($rawData === false) ? "0.0.0\n2011-01-01\n" : $rawData;
			$info = explode("\n", $rawData);

			$strapperVersion['package'] = array(
							'version' => trim($info[0]),
							'date' => new JDate(trim($info[1]))
			);

			$versionSource = 'installed';
		}

		if (!($strapperVersion[$versionSource]['date'] instanceof JDate))
		{
			$strapperVersion[$versionSource]['date'] = new JDate;
		}

		return array(
						'required' => $haveToInstallStrapper,
						'installed' => $installedStraper,
						'version' => $strapperVersion[$versionSource]['version'],
						'date' => $strapperVersion[$versionSource]['date']->format('Y-m-d')
		);
	}

	/**
	 * Runs after install, update or discover_update. In other words, it executes after Joomla! has finished installing
	 * or updating your component. This is the last chance you've got to perform any additional installations, clean-up,
	 * database updates and similar housekeeping functions.
	 *
	 * @param   string      $type    install, update or discover_update
	 * @param   JInstaller  $parent  Parent object
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		// Do not process $removeFilesFree
		$this->isPaid = true;
		$this->renderType = $type;
		parent::postflight($type, $parent);
	}

	/**
	 * Renders the post-installation message
	 *
	 * @param   bool        $status                      Param
	 * @param   bool        $fofInstallationStatus       Param
	 * @param   bool        $strapperInstallationStatus  Param
	 * @param   JInstaller  $parent                      Parent object
	 *
	 * @return void
	 */
	protected function renderPostInstallation($status, $fofInstallationStatus, $strapperInstallationStatus, $parent)
	{
		$rows = 0;

		if ($this->renderType == 'update')
		{
			echo '<div class="alert">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Warning! If you have manually installed optional plugins, you must also manually update them. Please, review the latest versions, <a href="http://www.extly.com/autotweet-ng-pro.html#extensions" target="_blank">here <i class="icon-link"></i></a>.</strong>
  </div>';
		}
		?>
<img src="../media/com_autotweet/images/autotweet-logo.png" width="57"
	height="57" alt="Extly - Joomla Extensions" align="right" />

<h1>Welcome to <?php echo $this->componentTitle; ?>!</h1>

<p>
	<strong>Enhance your social media management!</strong>
</p>

<table class="adminlist table table-striped" width="100%">
	<thead>
		<tr>
			<th class="title" colspan="2">Extension</th>
			<th width="30%">Status</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row<?php

		$rows++;
		echo $rows % 2;

		?>">
			<td class="key" colspan="2"><?php echo $this->componentTitle ?></td>
			<td><strong style="color: green">Installed</strong></td>
		</tr>
		<?php

		if ($fofInstallationStatus['required'])
		{
			?>
		<tr class="row<?php

			$rows++;
			echo $rows % 2;

			?>">
			<td class="key" colspan="2"><strong>Framework on Framework (FOF) <?php echo $fofInstallationStatus['version'] ?></strong>
				[<?php echo $fofInstallationStatus['date'] ?>]
			</td>
			<td><strong> <span
							style="color: <?php echo $fofInstallationStatus['required'] ? ($fofInstallationStatus['installed'] ? 'green' : 'red') : '#660' ?>; font-weight: bold;">
	<?php echo $fofInstallationStatus['required'] ? ($fofInstallationStatus['installed'] ? 'Installed' : 'Not Installed') : 'Already up-to-date'; ?>
						</span>
			</strong></td>
		</tr>
		<?php
		}

		if ($strapperInstallationStatus['required'])
		{
			?>
		<tr class="row<?php

			$rows++;
			echo $rows % 2;

			?>">
			<td class="key" colspan="2"><strong>Extly Library <?php echo $strapperInstallationStatus['version'] ?></strong>
				[<?php echo $strapperInstallationStatus['date'] ?>]
			</td>
			<td><strong> <span
							style="color: <?php echo $strapperInstallationStatus['required'] ? ($strapperInstallationStatus['installed'] ? 'green' : 'red') : '#660' ?>; font-weight: bold;">
			<?php echo $strapperInstallationStatus['required'] ? ($strapperInstallationStatus['installed'] ? 'Installed' : 'Not Installed') : 'Already up-to-date'; ?>
						</span>
			</strong></td>
		</tr>
		<?php
		}
		?>
		<?php

		if (count($status->modules))
		{
			?>
			<tr>
			<th>Module</th>
			<th>Client</th>
			<th></th>
		</tr>
			<?php

			foreach ($status->modules as $module)
			{
				?>
				<tr class="row<?php

				$rows++;
				echo $rows % 2;

				?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong
							style="color: <?php
				echo $module['result'] ? "green" : "red";
				?>"><?php
				echo $module['result'] ? 'Installed' : 'Not installed';
				?></strong></td>
		</tr>
			<?php
			}
			?>
		<?php
		}
		?>
		<?php
		if (count($status->plugins))
		{
			?>
			<tr>
			<th>Plugin</th>
			<th>Group</th>
			<th></th>
		</tr>
			<?php

			foreach ($status->plugins as $plugin)
			{
				?>
				<tr class="row<?php

				$rows++;
				echo $rows % 2;

				?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong
							style="color: <?php
				echo $plugin['result'] ? "green" : "red";
				?>"><?php
				echo $plugin['result'] ? 'Installed' : 'Not installed';
				?></strong></td>
		</tr>
			<?php
			}
		}
		?>
		</tbody>
</table>

<fieldset>

	<h2>Tutorials</h2>

	<h3>Tutorial: How to AutoTweet from Joomla in 5 minutes</h3>
	<p>
		<a target="_blank"
			href="http://www.extly.com/how-to-autotweet-in-5-minutes-from-joomla.html"><img
			alt="How to AutoTweet from Joomla in 5 minutes"
			src="http://www.extly.com/images/autotweet-documentation/How_to_AutoTweet_from_Joomla_in_5_minutes.jpg"
			height="300" width="480" /></a>
	</p>

	<h3>Tutorial: How to AutoTweet from Your Own Facebook App</h3>
	<p>
		<a target="_blank"
			href="http://www.extly.com/how-to-autotweet-from-your-own-facebook-app.html"><img
			alt="How to AutoTweet from Your Own Facebook App"
			src="http://cdn.extly.com/images/autotweet-documentation/How-to-AutoTweet-from-Your-Own-Facebook-App-Joomla-New-Facebook-Developer-Site.jpg"
			width="480" /></a>
	</p>

	<hr />

	<p>
		If you have any question, please, don't hesitate to contact us.<br />
		Technical Support: <a href="https://support.extly.com" target="_blank">https://support.extly.com</a>
	</p>

	<p>
		We are passionately committed to your success.<br /> Support Team<br />
		<strong>Extly.com - Extensions</strong><br /> <a
			href="https://support.extly.com" target="_blank">https://support.extly.com</a>
		| <a href="http://twitter.com/extly" target="_blank">@extly</a> | <a
			href="http://www.facebook.com/extly" target="_blank">facebook.com/extly</a>
	</p>

</fieldset>
<?php
	}

	/**
	 * Uninstalls subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param   JInstaller  $parent  The parent object
	 *
	 * @return  stdClass  The subextension uninstallation status
	 */
	protected function uninstallSubextensions($parent)
	{
		$status = parent::uninstallSubextensions($parent);

		if ($status)
		{
			$this->_uninstallAutotweetPlugins($status, $parent);
		}

		return $status;
	}

	/**
	 * _uninstallAutotweetPlugins.
	 *
	 * @param   object      &$status  Params.
	 * @param   JInstaller  $parent   Params.
	 *
	 * @return	boolean.
	 */
	public function _uninstallAutotweetPlugins(&$status, $parent)
	{
		$db = JFactory::getDBO();

		$query = 'SELECT * FROM ' . $db->quoteName('#__extensions') . ' WHERE (' . $db->quoteName('type') . ' = ' . $db->Quote('plugin') . ') AND (' . $db->quoteName('element') . ' like ' . $db->Quote('%autotweet%') . ' OR ' . $db->quoteName('name') . ' like ' . $db->Quote('%AutoTweet%') . ' OR ' . $db->quoteName('element') . ' like ' . $db->Quote('%joocial%') . ' OR ' . $db->quoteName('name') . ' like ' . $db->Quote('%Joocial%') . ')' . ' ORDER BY ' . $db->quoteName('extension_id');

		$db->setQuery($query);
		$extensions = $db->loadAssocList();

		foreach ($extensions as $ext)
		{
			$installer = new JInstaller;
			$result = $installer->uninstall($ext['type'], $ext['extension_id']);

			$status->plugins[] = array(
							'name' => 'plg_' . $ext['element'],
							'group' => $ext['folder'],
							'result' => $result
			);
		}
	}

	/**
	 * renderPostUninstallation
	 *
	 * @param   bool        $status  Param
	 * @param   JInstaller  $parent  Parent object
	 *
	 * @return void
	 */
	protected function renderPostUninstallation($status, $parent)
	{
		?>
<h2><?php echo $this->componentTitle; ?> Uninstallation Status</h2>
<?php
		parent::renderPostUninstallation($status, $parent);
	}
}

if (!class_exists('F0FUtilsCacheCleaner'))
{
	/**
	 * A utility class to help you quickly clean the Joomla! cache
	 *
	 * - Upgrading from
	 * 2.3.1
	 * 2014-06-10 09:42:10
	 *
	 * rev844F136-1410443902
	 * 2014-09-11 16:58:22
	 *
	 * @package     Extly.Components
	 * @subpackage  com_extly
	 * @since       1.0
	 */
	class F0FUtilsCacheCleaner
				{
		/**
		 * Clears the com_modules and com_plugins cache. You need to call this whenever you alter the publish state or
		 * parameters of a module or plugin from your code.
		 *
		 * @return  void
		 */
		public static function clearPluginsAndModulesCache()
		{
			self::clearPluginsCache();
			self::clearModulesCache();
		}

		/**
		 * Clears the com_plugins cache. You need to call this whenever you alter the publish state or parameters of a
		 * plugin from your code.
		 *
		 * @return  void
		 */
		public static function clearPluginsCache()
		{
			self::clearCacheGroups(
					array(
									'com_plugins'
					), array(
									0,
									1
					)
			);
		}

		/**
		 * Clears the com_modules cache. You need to call this whenever you alter the publish state or parameters of a
		 * module from your code.
		 *
		 * @return  void
		 */
		public static function clearModulesCache()
		{
			self::clearCacheGroups(
					array(
									'com_modules'
					), array(
									0,
									1
					)
			);
		}

		/**
		 * Clears the specified cache groups.
		 *
		 * @param   array  $clearGroups   Which cache groups to clear. Usually this is com_yourcomponent to clear your
		 *                                component's cache.
		 * @param   array  $cacheClients  Which cache clients to clear. 0 is the back-end, 1 is the front-end. If you do not
		 *                                specify anything, both cache clients will be cleared.
		 *
		 * @return  void
		 */
		public static function clearCacheGroups(array $clearGroups, array $cacheClients = array(0, 1))
		{
			$conf = JFactory::getConfig();

			foreach ($clearGroups as $group)
			{
				foreach ($cacheClients as $client_id)
				{
					try
					{
						$options = array(
										'defaultgroup' => $group,
										'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache')
						);

						$cache = JCache::getInstance('callback', $options);
						$cache->clean();
					}
					catch (Exception $e)
					{
						// Suck it up
					}
				}
			}
		}
	}
}
