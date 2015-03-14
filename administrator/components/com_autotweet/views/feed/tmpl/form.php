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
JHtml::_('behavior.formvalidation');

$preview = null;

if ($this->item->id)
{
	$url = $this->item->xtform->get('url');

	if (filter_var($url, FILTER_VALIDATE_URL) !== false)
	{
		$feedLoaderHelper = FeedLoaderHelper::getInstance();
		$previewResult = $feedLoaderHelper->getPreview($this->item);

		if ( (isset($previewResult->preview)) && (count($previewResult->preview)) )
		{
			$preview = $previewResult->preview[0];
		}
		else
		{
			ELog::showMessage('COM_AUTOTWEET_VIEW_FEEDS_PREVIEW_UNAVAILABLE', JLog::ERROR);
		}
	}
	else
	{
		ELog::showMessage('COM_AUTOTWEET_FEED_INVALID_URL', JLog::ERROR);
	}
}

?>

<div class="extly">
	<div class="extly-body">

		<?php

			if ($this->get('ajax_import'))
			{
				include_once JPATH_AUTOTWEET . '/views/feeds/tmpl/import_progress.php';
			}

			echo Extly::showInvalidFormAlert();

		?>

		<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal form-validate">
			<input type="hidden" name="option" value="com_autotweet" />
			<input type="hidden" name="view" value="feeds" />
			<input type="hidden" name="task" value="" />
			<?php

				echo EHtml::renderRoutingTags();

			?>

			<div class="row-fluid">

				<div class="span6">

					<div id="feed_data">
						<fieldset class="feed_data">

							<legend>
								<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TITLE'); ?>
							</legend>

							<ul class="nav nav-tabs" id="feedTabs">
								<li><a data-toggle="tab" href="#feeddetails">
									<i class="xticon xticon-wrench"></i>
									<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_FEED_DETAILS'); ?>
								</a></li>
								<li><a data-toggle="tab" href="#publishing">
									<i class="xticon xticon-arrow-circle-o-up"></i>
									<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_PUBLISHING'); ?>
								</a></li>
								<li><a data-toggle="tab" href="#contentcreation">
									<i class="xticon xticon-file-text-o"></i>
									<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_TXT_HANDLING'); ?>
								</a></li>
								<li><a data-toggle="tab" href="#filters">
									<i class="xticon xticon-filter"></i>
									<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_FLTRS'); ?>
								</a></li>
<!--
								<li><a data-toggle="tab" href="#tagging"><?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_TAGGING'); ?> </a></li>
 -->
							</ul>

							<div class="tab-content" id="feedTabsContent">

								<?php

								include_once 'feed_feeddetails.php';
								include_once 'feed_publishing.php';
								include_once 'feed_contentcreation.php';
								include_once 'feed_filters.php';

								// Include_once 'feed_tagging.php';

								?>

							</div>

						</fieldset>
					</div>

				</div>

				<div class="span6">
					<?php

							if ($preview)
							{
								include_once 'feed_preview.php';
							}

					?>
				</div>

			</div>
		</form>
	</div>
</div>
