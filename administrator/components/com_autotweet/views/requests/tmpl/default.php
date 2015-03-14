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

F0FModel::getTmpInstance('Plugins', 'AutoTweetModel');

$this->loadHelper('select');
$this->loadHelper('grid');

JHTML::_('behavior.calendar');

?>
<div class="extly">
	<div class="extly-body">

		<form name="adminForm" id="adminForm" action="index.php" method="post" class="form-horizontal">

			<div class="row-fluid">
				<div class="span12">

					<input type="hidden" name="option" id="option" value="com_autotweet" />
					<input type="hidden" name="view" id="view" value="request" />
					<input type="hidden" name="task" id="task" value="browse" />
					<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
					<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
					<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
					<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
					<?php

						echo EHtml::renderRoutingTags();

					?>
					<table class="adminlist table table-striped" id="itemsList">
						<thead>
							<tr>

								<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
								</th>

								<th><?php echo JHTML::_('grid.sort', 'COM_AUTOTWEET_VIEW_MSGLOG_POSTDATE_TITLE', 'publish_up', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<th><?php echo JHTML::_('grid.sort', 'COM_AUTOTWEET_REQUESTS_FIELD_MESSAGE', 'name', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<th width="80"><?php echo JHTML::_('grid.sort', 'COM_AUTOTWEET_VIEW_SOURCE_TITLE', 'plugin', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<th width="80"><?php echo JHTML::_('grid.sort', 'COM_AUTOTWEET_REQ_PUBLISHED_TITLE', 'published', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<th width="80"><?php echo JHTML::_('grid.sort', 'JGLOBAL_FIELD_ID_LABEL', 'id', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>
							</tr>
							<tr>

								<td></td>

								<td class="form-inline"><?php echo JHTML::_('calendar', $this->getModel()->getState('publish_up'), 'publish_up', 'publish_up', '%Y-%m-%d', array('class' => 'input-small')); ?>
								</td>

								<td class="form-inline" style="white-space: nowrap">
									<div class="input-append">
										<input type="text" name="search" id="search" value="<?php echo $this->escape($this->getModel()->getState('search'));?>" class="input-medium" onchange="document.adminForm.submit();"
											placeholder="<?php echo JText::_('COM_AUTOTWEET_REQUESTS_FIELD_MESSAGE') ?>" />
										<button class="btn" onclick="this.form.submit();">
											<?php echo JText::_('COM_AUTOTWEET_FILTER_SUBMIT'); ?>
											<img src="<?php echo $this->get('blankImage'); ?>" height="20">
										</button>
									</div>

									<a class="xtd-btn-reset"><small><?php echo JText::_('COM_AUTOTWEET_RESET'); ?></small></a>
								</td>

								<td><?php echo SelectControlHelper::plugins($this->getModel()->getState('plugin'), 'plugin', array('onchange' => 'this.form.submit();', 'class' => 'input-small')) ?>
								</td>

								<td><?php echo EHtmlSelect::yesNo(
										$this->getModel()->getState('published', 0),
										'published',
										array('onchange-submit' => 'true', 'class' => 'btn-mini')
										); ?>
								</td>

								<td></td>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="20"><?php
								EHtml::renderPagination($this);
								?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							if ($count = count($this->items))
							{
								?>
							<?php
							$i = 0;
							$m = 1;

							foreach ($this->items as $item)
							{
								$m = 1 - $m;
								$checkedout = ($item->checked_out != 0);
								$ordering = $this->lists->order == 'ordering';
								$native_object = json_decode($item->native_object);
								$has_error = false;
								$link = JRoute::_('index.php?option=com_autotweet&view=request&task=edit&id=' . (int) $item->id);

								if ((isset($native_object->error)) && ($native_object->error))
								{
									$has_error = true;
									$alert_style = 'alert-error';
									$alert_message = JText::_($native_object->error_message);
								}

								?>
							<tr class="row<?php echo $m?> <?php
								if ($has_error)
								{
									echo 'error';
								}
							?>">

								<td><?php echo JHTML::_('grid.id', $i, $item->id, $checkedout); ?>
								</td>

								<td><a href="<?php echo $link; ?>" class="nobr"> <?php

								if (empty($item->publish_up))
								{
									echo '<span class="alert-error error"><i class="xticon xticon-exclamation-circle"></i></span>';
								}
								else
								{
									echo JHtml::_('date', $item->publish_up, JText::_('COM_AUTOTWEET_DATE_FORMAT'));
								}

								?>
								</a>
								</td>

								<td><?php

								echo EHtmlGrid::lockedWithIcons($checkedout);

								?> <a href="<?php echo $link; ?>"> <?php

								$description = TextUtil::truncString($item->description, SharingHelper::MAX_CHARS_TITLE_SCREEN);
								echo htmlentities($description, ENT_COMPAT, 'UTF-8');

								?>
								</a>
								<?php

								if (!empty($item->url))
								{
									echo ' <a href="' . TextUtil::renderUrl($item->url) . '" target="_blank"><i class="xticon xticon-globe"></i></a>';
								}

								if (!empty($item->image_url))
								{
									echo ' <a href="' . TextUtil::renderUrl($item->image_url) . '" target="_blank"><i class="xticon xticon-image"></i></a>';
								}

								?>
								</td>

								<td><?php  echo AutoTweetModelPlugins::getSimpleName($item->plugin); ?>
								</td>

								<td><?php
								if ($has_error)
								{
									$alert_message = htmlentities($alert_message, ENT_COMPAT, 'UTF-8');
									echo '<div rel="tooltip" data-original-title="' . $alert_message . '">';
									echo EHtmlGrid::publishedWithIcons($item, $i, $this->perms->editstate);
									echo ' <a class="xticon xticon-thumbs-down"></a>';
									echo ' - ' . JText::_('COM_AUTOTWEET_STATE_PUBSTATE_ERROR') . '</div>';
								}
								else
								{
									echo SelectControlHelper::processedWithIcons($item, $i, $this->perms->editstate) . ' - ' .
											($item->published ? JText::_('JYES') : JText::_('JNO'));
								}
								?>
								</td>

								<td><?php
								echo $item->id;
								?>
								</td>
							</tr>
							<?php
								$i++;
							}

							?>
							<?php
							}
							else
							{
								?>
							<tr>
								<td colspan="10" align="center"><?php echo JText::_('AUTOTWEET_COMMON_NOITEMS_LABEL') ?></td>
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>

				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
<?php

	if ((isset($this->perms->editstate)) && ($this->perms->editstate))
	{
?>
					<div class="well">
						<fieldset class="extly-batch">
							<h4>
								<?php echo JText::_('COM_AUTOTWEET_BATCH_REQS_TITLE'); ?>
							</h4>
							<p>
								<?php echo JText::_('COM_AUTOTWEET_BATCH_REQS_DESC'); ?>
							</p>

							<div class="control-group">
								<label class="control-label" for="create_event" id="create_event-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_REQ_PUBLISHED_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_REQ_PUBLISHED_TITLE'); ?> </label>
								<div class="controls inline">
									<?php echo EHtmlSelect::yesNo(0, 'batch_published'); ?>
								</div>
							</div>

							<a class="btn" onclick="Joomla.submitbutton('batch');" type="submit"><?php echo JText::_('COM_AUTOTWEET_BATCH_MOVE_BUTTON'); ?></a>
						</fieldset>
					</div>
<?php
	}
?>
				</div>
				<div class="span6"><div class="alert alert-info">
				<?php
							echo JText::_('COM_AUTOTWEET_PROCESSING_MODES_INFO');
				?>
				<a target="_blank" href="http://www.extly.com/autotweetng-documentation-faq/271-autotweet-documentation-cronjob-mode.html"><i class="xticon xticon-link"></i></a>
				</div></div>
			</div>

		</form>
	</div>
</div>
