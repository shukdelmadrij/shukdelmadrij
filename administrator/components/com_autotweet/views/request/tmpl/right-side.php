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

$author = $this->item->xtform->get('author');
$title = $this->item->xtform->get('title');

if (($isManualMsg) && (empty($title)))
{
	$this->item->xtform->set('title', '');
}

$article_text = $this->item->xtform->get('article_text');

if (($isManualMsg) && (empty($article_text)))
{
	$this->item->xtform->set('article_text', '');
}

$allow_new_reqpost = EParameter::getComponentParam(CAUTOTWEETNG, 'allow_new_reqpost', 0);
$create_event = $this->item->xtform->get('create_event', 0);

?>

<div class="span6">
	<div class="row-fluid">
		<div class="span12">

			<ul class="nav nav-tabs" id="qTypeTabs">

				<li id="auditinfo-tab"><a data-toggle="tab" href="#auditinfo">
					<i class="xticon xticon-user"></i>
					 <?php echo JText::_('COM_AUTOTWEET_AUDIT_INFORMATION'); ?>
				</a></li>

				<li id="overrideconditions-tab"><a data-toggle="tab" href="#override-conditions">
					<i class="xticon xticon-file-text-o"></i>
					 <?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_MESSAGE_OPTIONS'); ?>
				</a></li>

				<li id="filterconditions-tab"><a data-toggle="tab" href="#filterconditions">
					<i class="xticon xticon-filter"></i>
					 <?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_FILTERS_OPTIONS'); ?>
				</a></li>
				<?php

/* Facebook Events Deprecated
				if (($allow_new_reqpost) || ($create_event))
				{
				?>
				<li id="createevent-tab"><a data-toggle="tab" href="#createevent">
					<i class="xticon xticon-calendar-o"></i>
					 <?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_CREATE_EVENT'); ?>
				</a></li>
				<?php
				}
*/
				?>
			</ul>

			<div class="tab-content" id="qContent">

				<div id="auditinfo" class="tab-pane fade">
					<dl class="dl-horizontal">
						<dt>
							<?php
							echo JText::_('COM_AUTOTWEET_CREATED_DATE');
							?>
						</dt>
						<dd>
							<?php
							echo $this->item->get('created');
							?>

							<?php
							$created = $this->item->get('created_by');

							if ($created)
							{
								echo JFactory::getUser($created)->get('username');
							}
							else
							{
								echo '-';
							}
							?>
						</dd>

						<dt>
							<?php
							echo JText::_('COM_AUTOTWEET_MODIFIED_DATE');
							?>
						</dt>
						<dd>
							<?php
							$modified = $this->item->get('modified');

							if ((int) $modified)
							{
								echo $modified;
							}
							?>

							<?php
							$modified_by = $this->item->get('modified_by');

							if ($modified_by)
							{
								echo JFactory::getUser($modified_by)->get('username');
							}
							else
							{
								echo '-';
							}
							?>
						</dd>

						<dt>
							<?php
							echo JText::_('COM_AUTOTWEET_RESULT_MESSAGE');
							?>
						</dt>
						<dd>
							<?php
							echo $alert_message ? $alert_message : '-';
							?>
						</dd>
					</dl>
				</div>

				<div id="override-conditions" class="tab-pane fade">
					<div class="control-group">
						<label for="title" class="control-label <?php echo $labelDisabledNotManual; ?>" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_MESSAGE_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_MESSAGE'); ?> </label>
						<div class="controls">
							<textarea name="xtform[title]" id="title" rows="5" cols="80" maxlength="512" <?php echo $readonlyNotManual; ?>><?php
								echo $this->item->xtform->get('title');
								?></textarea>
						</div>
					</div>

					<div class="control-group">
						<label for="article_text" class="control-label <?php echo $labelDisabledNotManual; ?>" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_FULL_TEXT_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_FULL_TEXT'); ?> </label>
						<div class="controls">
							<textarea name="xtform[article_text]" id="article_text" rows="5" cols="40" maxlength="512" <?php echo $readonlyNotManual; ?>><?php
								echo $this->item->xtform->get('article_text');
								?></textarea>
						</div>
					</div>

					<div class="control-group">
						<label for="hashtags" class="control-label <?php echo $labelDisabledNotManual; ?>" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_HASHTAGS_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_HASHTAGS'); ?> </label>
						<div class="controls">
							<input type="text" name="xtform[hashtags]" id="hashtags" value="<?php echo $this->item->xtform->get('hashtags'); ?>" maxlength="64" <?php
							echo $readonlyNotManual;
							?> />
						</div>
					</div>
				</div>

				<div id="filterconditions" class="tab-pane fade">
					<div class="control-group">
						<label for="catid" class="control-label <?php echo $labelDisabledNotManual; ?>" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_CATEGORY_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_CATEGORY'); ?> </label>
						<div class="controls">
							<?php echo SelectControlHelper::category('xtform[catid]', 'com_content', $this->item->xtform->get('catid'), null, null, 1, 1, !$isManualMsg); ?>
						</div>
					</div>

					<div class="control-group">
						<label for="author" class="control-label <?php echo $labelDisabledNotManual; ?>" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_AUTHOR_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_AUTHOR'); ?> <span class="star">&#160;*</span> </label>
						<div class="controls">

<?php
						echo EHtmlSelect::userSelect($author, 'xtform[author]', 'author');
?>

						</div>
					</div>

					<div class="control-group">
						<label for="language" class="control-label <?php echo $labelDisabledNotManual; ?>" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_LANGUAGE_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_LANGUAGE'); ?> </label>
						<div class="controls">
							<?php echo SelectControlHelper::languages($this->item->xtform->get('language'), 'xtform[language]') ?>
						</div>
					</div>

					<?php
					if (EXTLY_J3)
					{
						?>
					<div class="control-group">
						<label for="language" class="control-label <?php echo $labelDisabledNotManual; ?>" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_ACCESS_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_ACCESS'); ?> </label>
						<div class="controls">
							<?php
							echo JHTML::_('access.level', 'xtform[access]', $this->item->xtform->get('access', 1));
							?>
						</div>
					</div>

					<?php
					}
					/*
					 else
					{
					echo JHTML::_('list.accesslevel', $this->item->xtform->get('access', 1));
					}
					*/
					?>

					<?php
					if ((AUTOTWEETNG_JOOCIAL) && (EParameter::getComponentParam(CAUTOTWEETNG, 'targeting', false)))
					{
					?>
		            <hr/>

					<div class="control-group">
	            		<label class="control-label" for="xtformtarget_id" id="target_id-lbl"><?php
						echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_TARGETING_TITLE');
						?></label>
	            		<div class="controls">
	              			<?php echo SelectControlHelper::targets($this->item->xtform->get('target_id'), 'xtform[target_id]', null); ?>
	            		</div>
	          		</div>

					<?php
					}
					?>
				</div>

				<?php
/* Facebook Events Deprecated
				if (($allow_new_reqpost) || ($create_event))
				{
				?>
				<div id="createevent" class="tab-pane fade">
					<div class="control-group">
						<label class="control-label <?php echo $labelDisabledNotManual; ?>" for="create_event" id="create_event-lbl" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_CREATEEVENT_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_CREATEEVENT_TITLE'); ?> </label>
						<div class="controls inline">
							<?php echo EHtmlSelect::yesNo($create_event, 'xtform[create_event]', array(), 'create_event'); ?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label <?php echo $labelDisabledNotManual; ?>" for="location" id="location-lbl" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTLOCATION_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTLOCATION_TITLE'); ?> </label>
						<div class="controls">
							<input type="text" name="xtform[location]" id="location" value="<?php echo $this->item->xtform->get('location'); ?>" maxlength="64" <?php
							echo $readonlyNotManual;
							?> />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label <?php echo $labelDisabledNotManual; ?>" for="street" id="street_token-lbl" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTSTREET_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTSTREET_TITLE'); ?> </label>
						<div class="controls">
							<input type="text" name="xtform[street]" id="street" value="<?php echo $this->item->xtform->get('street'); ?>" maxlength="64" <?php
							echo $readonlyNotManual;
							?> />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label <?php echo $labelDisabledNotManual; ?>" for="city" id="city_token-lbl" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTCITY_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTCITY_TITLE'); ?> </label>
						<div class="controls">
							<input type="text" name="xtform[city]" id="city" value="<?php echo $this->item->xtform->get('city'); ?>" maxlength="64" <?php
							echo $readonlyNotManual;
							?> />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label <?php echo $labelDisabledNotManual; ?>" for="start_time" id="start_time-lbl" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTSTARTTIME_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTSTARTTIME_TITLE'); ?> </label>
						<div class="controls">
							<?php echo JHTML::_('calendar', $this->item->xtform->get('start_time', JFactory::getDate()->toSql()), 'xtform[start_time]', 'start_time', '%Y-%m-%d', array('class' => 'input')); ?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label <?php echo $labelDisabledNotManual; ?>" for="end_time" id="end_time-lbl" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTENDTIME_DESC');
			?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_MANUALMSG_EVENTENDTIME_TITLE'); ?> </label>
						<div class="controls">
							<?php echo JHTML::_('calendar', $this->item->xtform->get('end_time'), 'xtform[end_time]', 'end_time', '%Y-%m-%d', array('class' => 'input')); ?>
						</div>
					</div>
				</div>
				<?php
				}
*/
				?>

			</div>

		</div>
	</div>
</div>

