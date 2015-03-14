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
$this->loadHelper('grid');

JHTML::_('behavior.calendar');

?>
<div class="extly">
	<div class="extly-body">

		<form name="adminForm" id="adminForm" action="index.php" method="post" class="form-horizontal">

			<div class="row-fluid">
				<div class="span12">

					<input type="hidden" name="option" id="option" value="com_autotweet" />
					<input type="hidden" name="view" id="view" value="posts" />
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
								<?php
								if (!$this->isModule)
								{
									?>
								<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
								</th>
								<?php
								}
								?>

								<th><?php echo JHTML::_('grid.sort', 'COM_AUTOTWEET_VIEW_MSGLOG_POSTDATE_TITLE', 'postdate', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<th><?php echo JHTML::_('grid.sort', 'COM_AUTOTWEET_POSTS_FIELD_MESSAGE', 'name', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<th width="160"><?php echo JHTML::_('grid.sort', 'LBL_POSTS_CHANNEL', 'channel_id', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<th width="80"><?php echo JHTML::_('grid.sort', 'COM_AUTOTWEET_VIEW_POSTS_PUBSTATES_SELECT', 'pubstate', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<?php
								if (!$this->isModule)
								{
									?>

								<th width="80"><?php echo JHTML::_('grid.sort', 'COM_AUTOTWEET_VIEW_SOURCE_TITLE', 'plugin', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>

								<th width="80"><?php echo JHTML::_('grid.sort', 'JGLOBAL_FIELD_ID_LABEL', 'id', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
								</th>
								<?php
								}
								?>
							</tr>
							<tr style="<?php
							if ($this->isModule)
							{
								echo 'display:none;';
							}
							?>">
								<td></td>

								<td class="form-inline"><?php echo JHTML::_('calendar', $this->getModel()->getState('postdate'), 'postdate', 'postdate', '%Y-%m-%d', array('class' => 'input-small')); ?>
								</td>

								<td class="form-inline" style="white-space: nowrap">
									<div class="input-append">
										<input type="text" name="search" id="search" value="<?php echo $this->escape($this->getModel()->getState('search'));?>" class="input-medium" onchange="document.adminForm.submit();"
											placeholder="<?php echo JText::_('COM_AUTOTWEET_POSTS_FIELD_MESSAGE') ?>" />
										<button class="btn" onclick="this.form.submit();">
											<?php echo JText::_('COM_AUTOTWEET_FILTER_SUBMIT'); ?>
											<img src="<?php echo $this->get('blankImage'); ?>" height="20">
										</button>
									</div>

									<a class="xtd-btn-reset"><small><?php echo JText::_('COM_AUTOTWEET_RESET'); ?></small></a>
								</td>
								<td><?php echo SelectControlHelper::channels($this->getModel()->getState('channel'), 'channel', array('onchange' => 'this.form.submit();', 'class' => 'input-medium')); ?>
								</td>
								<td><?php echo SelectControlHelper::pubstates($this->getModel()->getState('pubstate'), 'pubstate', array('onchange' => 'this.form.submit();', 'class' => 'input-small'), null, true); ?>
								</td>

								<td><?php echo SelectControlHelper::plugins($this->getModel()->getState('plugin'), 'plugin', array('onchange' => 'this.form.submit();', 'class' => 'input-small')) ?>
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
								$link = JRoute::_('index.php?option=com_autotweet&view=posts&task=edit&id=' . (int) $item->id);
								$checkedout = ($item->checked_out != 0);

								?>
							<tr class="row<?php echo $m?> <?php
								if ($item->pubstate == 'error')
								{
									echo $item->pubstate;
								}
							?>">
								<?php
								if (!$this->isModule)
								{
									?>
								<td><?php echo JHTML::_('grid.id', $i, $item->id, $checkedout); ?>
								</td>
								<?php
								}
								?>

								<td><a href="<?php

								echo $link;

								?>" class="nobr"> <?php
								echo JHtml::_('date', $item->postdate, JText::_('COM_AUTOTWEET_DATE_FORMAT'));
								?>
								</a>
								</td>

								<td><?php

								echo EHtmlGrid::lockedWithIcons($checkedout);

								?> <a href="<?php

								echo $link;

								?>"> <?php

								$message = $item->message;

								if ($this->isModule)
								{
									$message = TextUtil::truncString($message, SharingHelper::MAX_CHARS_TITLE_SHORT_SCREEN);
								}
								else
								{
									$message = TextUtil::truncString($message, SharingHelper::MAX_CHARS_TITLE_SCREEN);
								}

								echo htmlentities($message, ENT_COMPAT, 'UTF-8');
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

								<td><span class="channel-<?php echo $item->channel_id ?>"></span> <?php echo $item->channel_id ? SelectControlHelper::getChannelName($item->channel_id, $this->isModule) : '&mdash;'; ?>
								</td>

								<td>
									<div rel="tooltip" data-original-title="<?php

									$result = htmlentities(JText::_($item->resultmsg), ENT_COMPAT, 'UTF-8');
									echo $result;

									?>">
										<?php echo GridHelper::pubstates($item, $i, $this->isModule); ?>
									</div>
								</td>

								<?php
								if (!$this->isModule)
								{
									?>

								<td><?php
								echo AutoTweetModelPlugins::getSimpleName($item->plugin);
								?>
								</td>

								<td><?php
								echo $item->id;
								?>
								</td>

								<?php
								}
								?>
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
							<?php
							if (!$this->isModule)
							{
							?>
			<div class="row-fluid">
				<div class="span6">
<?php

	if ((isset($this->perms->editstate)) && ($this->perms->editstate))
	{
?>
					<div class="well">
						<fieldset class="extly-batch">
							<h4><?php echo JText::_('COM_AUTOTWEET_BATCH_POSTS_TITLE'); ?></h4>
							<p><?php echo JText::_('COM_AUTOTWEET_BATCH_POSTS_DESC'); ?></p>

							<div class="control-group">
								<label for="pubstate" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_STATE_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_STATE'); ?></label>
								<div class="controls">
									<?php echo SelectControlHelper::pubstates(null, 'batch_pubstate', array('class' => 'input')); ?>
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
							<?php
							}
							?>

		</form>

	</div>
</div>
