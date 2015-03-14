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

$this->loadHelper('select');

$urlBase = JUri::root();
$isBackend = F0FPlatform::getInstance()->isBackend();

$postsLink = JRoute::_('index.php?option=com_autotweet&view=posts');
$requestsLink = JRoute::_('index.php?option=com_autotweet&view=requests');
$channelsLink = JRoute::_('index.php?option=com_autotweet&view=channels');
$rulesLink = JRoute::_('index.php?option=com_autotweet&view=rules');
$feedsLink = JRoute::_('index.php?option=com_autotweet&view=feeds');

$freeFlavour = VersionHelper::isFreeFlavour();
$update_dlid = EParameter::getComponentParam(CAUTOTWEETNG, 'update_dlid');
$needsdlid = ((!$freeFlavour) && (empty($update_dlid)));

?>
<div class="extly dashboard">
	<div class="extly-body">

			<div class="row-fluid">
				<div class="span8">

<?php
				if ($needsdlid)
				{
?>
				<div class="alert">
<?php
					echo JText::sprintf(
							'COM_AUTOTWEET_LBL_CPANEL_NEEDSDLID',
							VersionHelper::getFlavourName(),
							'http://www.extly.com/live-update-your-download-id.html'
					);
?>
				</div>
				<?php
				}

				if ($this->get('version_check'))
				{
?>

					<form name="adminForm" id="adminForm" action="index.php" method="post">
						<input type="hidden" name="option" id="option" value="com_autotweet" />
						<input type="hidden" name="view" id="view" value="cpanels" />
						<input type="hidden" name="task" id="task" value="no-task" />
						<?php

							echo EHtml::renderRoutingTags();

						?>

						<span class="loaderspinner72">
							<?php echo JText::_('COM_AUTOTWEET_LOADING'); ?>
						</span>
						<div id="updateNotice">
						</div>

					</form>
<?php
				}
?>

					<div class="well">

						<h2>
							<?php echo JText::_('COM_AUTOTWEET_ICON_CPANELS')?>
							<?php echo JText::_('COM_AUTOTWEET_JOOCIAL_METER')?>
						</h2>

						<h3>
							<?php echo JText::_('COM_AUTOTWEET_ICON_REQUESTS')?>
							<?php echo JText::_('COM_AUTOTWEET_TITLE_REQUESTS')?>
						</h3>

						<div class="progress">
							<?php
							if ($this->posts)
							{
								?>
							<div class="bar bar-success" style="width: <?php echo $this->p_posts; ?>%;">
								<a href="<?php

								echo $requestsLink;

								?>">
								<?php echo JText::_('COM_AUTOTWEET_ICON_POSTS')?>
								<?php echo JText::_('COM_AUTOTWEET_TITLE_POSTS'); ?>
								(<?php

									echo number_format($this->posts);

								?>)
								</a>
							</div>
							<?php
							}

							if ($this->requests)
							{
								?>
							<div class="bar bar-info" style="width: <?php echo $this->p_requests; ?>%;">
								<a href="<?php

								echo $requestsLink;

								?>">
								<?php echo JText::_('COM_AUTOTWEET_ICON_REQUESTS')?>
								<?php echo JText::_('COM_AUTOTWEET_TITLE_REQUESTS'); ?>
								(<?php

									echo number_format($this->requests);

								?>)
								</a>
							</div>
							<?php
							}
							?>
						</div>

						<h3>
							<?php echo JText::_('COM_AUTOTWEET_ICON_POSTS')?>
							<?php echo JText::_('COM_AUTOTWEET_TITLE_POSTS')?>
						</h3>

						<div class="progress">
							<?php
							if ($this->error)
							{
								?>
							<div class="bar bar-danger" style="width: <?php echo $this->p_error; ?>%;">
								<?php echo SelectControlHelper::getTextForEnum('error', true); ?>
								<a href="<?php

								echo $postsLink;

								?>">
								(<?php

								echo number_format($this->error);

								?>)
								</a>
							</div>
							<?php
							}

							if ($this->approve)
							{
								?>
							<div class="bar bar-warning" style="width: <?php echo $this->p_approve; ?>%;">
								<?php echo SelectControlHelper::getTextForEnum('approve', true); ?>
								<a href="<?php

								echo $postsLink;

								?>">
								(<?php

								echo number_format($this->approve);

								?>)
								</a>
							</div>
							<?php
							}

							if ($this->cronjob)
							{
								?>
							<div class="bar bar-info" style="width: <?php echo $this->p_cronjob; ?>%;">
								<?php echo SelectControlHelper::getTextForEnum('cronjob', true); ?>
								<a href="<?php

								echo $postsLink;

								?>">
								(<?php

								echo number_format($this->cronjob);

								?>)
								</a>
							</div>
							<?php
							}

							if ($this->success)
							{
								?>
							<div class="bar bar-success" style="width: <?php echo $this->p_success; ?>%;">
								<?php echo SelectControlHelper::getTextForEnum('success', true); ?>
								<a href="<?php

								echo $postsLink;

								?>">
								(<?php

								echo number_format($this->success);

								?>)
								</a>
							</div>
							<?php
							}
							?>
						</div>

					</div>

					<?php

					if ($isBackend)
					{
					?>

					<h2>
						<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNELS_TITLE')?>
					</h2>
					<p>
						<a href="<?php echo $channelsLink; ?>" class="btn btn-large">
							<i class="xticon xticon-facebook"></i>
						</a>
						<a href="<?php echo $channelsLink; ?>" class="btn btn-large">
							<i class="xticon xticon-google-plus"></i>
						</a>
						<a href="<?php echo $channelsLink; ?>" class="btn btn-large">
							<i class="xticon xticon-linkedin"></i>
						</a>
						<a href="<?php echo $channelsLink; ?>" class="btn btn-large">
							<i class="xticon xticon-envelope-o"></i>
						</a>
						<a href="<?php echo $channelsLink; ?>" class="btn btn-large">
							<i class="xticon xticon-twitter"></i>
						</a>
						<a href="<?php echo $channelsLink; ?>" class="btn btn-large">
							<i class="xticon xticon-vk"></i>
						</a>
					</p>

					<h2>
						<?php echo JText::_('COM_AUTOTWEET_SHORTCUTS')?>
					</h2>
					<p>
						<a href="<?php echo $postsLink; ?>" class="btn btn-large">
							<?php echo JText::_('COM_AUTOTWEET_ICON_POSTS')?>
							<?php echo JText::_('COM_AUTOTWEET_TITLE_POSTS')?>
						</a>
						<a href="<?php echo $requestsLink; ?>" class="btn btn-large">
							<?php echo JText::_('COM_AUTOTWEET_ICON_REQUESTS')?>
							<?php echo JText::_('COM_AUTOTWEET_TITLE_REQUESTS')?>
						</a>
						<a href="<?php echo $channelsLink; ?>" class="btn btn-large">
							<?php echo JText::_('COM_AUTOTWEET_ICON_CHANNELS')?>
							<?php echo JText::_('COM_AUTOTWEET_TITLE_CHANNELS')?>
						</a>
						<a href="<?php echo $rulesLink; ?>" class="btn btn-large">
							<?php echo JText::_('COM_AUTOTWEET_ICON_RULES')?>
							<?php echo JText::_('COM_AUTOTWEET_TITLE_RULES')?>
						</a>
						<a href="<?php echo $feedsLink; ?>" class="btn btn-large">
							<?php echo JText::_('COM_AUTOTWEET_ICON_FEEDS')?>
							<?php echo JText::_('COM_AUTOTWEET_TITLE_FEEDS')?>
						</a>
					</p>

					<?php
					}

					?>
				</div>
				<div class="span4">

					<?php

					if ($isBackend)
					{
						if (AUTOTWEETNG_JOOCIAL)
						{
							$manager = EExtensionHelper::getExtensionId('system', 'autotweetautomator');

							$url = 'index.php?option=com_autotweet&view=managers&task=edit&id=' . $manager;
							$url = JRoute::_($url);

							echo '<p class="text-right lead"><i class="xticon xticon-user"></i> <a class="btn btn-primary span10" href="' . $url . '">' .
								JText::_('COM_AUTOTWEET_VIEW_ABOUT_VIRTUALMANAGER_TITLE')
								. '</a></p><p class="text-right">';

							if (VirtualManager::getInstance()->isWorking())
							{
								echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_VIRTUALMANAGER_WORKING');
								echo ' <i class="xticon xticon-sun-o"></i>';
							}
							else
							{
								echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_VIRTUALMANAGER_RESTING');
								echo ' <i class="xticon xticon-moon-o"></i>';
							}

							echo '</p>';
						}
					?>

					<h3>
						<a href="https://support.extly.com" target="_blank">
						<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SUPPORT_TITLE'); ?>
						<i class="xticon xticon-link"></i>
						</a>
					</h3>
					<p>
						<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SUPPORT_TWITTERFOLLOW'); ?>

					<h3 class="customsocialicons">
						<a target="_blank" href="http://twitter.com/extly"> <i
							class="xticon xticon-twitter"> </i>
						</a> <a target="_blank" href="http://www.facebook.com/extly"> <i
							class="xticon xticon-facebook-sign"></i>
						</a> <a target="_blank"
							href="http://www.linkedin.com/company/extly-com---joomla-extensions?trk=hb_tab_compy_id_2890809">
							<i class="xticon xticon-linkedin"></i>
						</a> <a target="_blank"
							href="https://plus.google.com/108048364795063174131"> <i
							class="xticon xticon-google-plus"> </i>
						</a> <a target="_blank" href="http://pinterest.com/extly/"> <i
							class="xticon xticon-pinterest"> </i>
						</a> <a target="_blank" href="https://github.com/anibalsanchez"> <i
							class="xticon xticon-github"> </i>
						</a>
					</h3>

					<p>
						<?php

echo JText::sprintf('COM_AUTOTWEET_VIEW_ABOUT_SUPPORT_JEDREVIEW', '<a href="' . JED_ID . '" target="_blank">Joomla! Extensions Directory: ');
						echo '</a>.';
						?>
					</p>

					<p>
						For more information: <a
							href="http://documentation.extly.com/autotweetng_joocial/faq.html"
							target="_blank"><?php echo VersionHelper::getFlavourName(); ?> Documentation</a>
					</p>
					<p>
						Support: <a href="http://support.extly.com" target="_blank">http://support.extly.com</a>
					</p>
					<ul class="footer-links">
						<li><a href="http://www.extly.com/blog.html" target="_blank">Read
								the Extly.com blog</a></li>
						<li><a href="http://support.extly.com" target="_blank">Submit
								issues</a></li>
						<li><a
							href="http://www.extly.com/autotweet-ng-pro.html#changelog"
							target="_blank">Roadmap and changelog</a></li>
					</ul>

					<?php
					}

					?>
				</div>
			</div>

	</div>
</div>
