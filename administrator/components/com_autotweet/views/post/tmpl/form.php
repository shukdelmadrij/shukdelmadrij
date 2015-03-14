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

JHTML::_('behavior.modal');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$isManualMsg = ($this->item->plugin === 'autotweetpost');
$readonlyNotManual = (!$isManualMsg ? 'readonly="readonly"' : '');
$labelDisabledNotManual = (!$isManualMsg ? 'disabled' : '');

if (($isManualMsg) && ($this->item->get('id') == 0))
{
	$this->item->set('pubstate', AutotweetPostHelper::POST_APPROVE);
}

$alert_style = 'alert-info';

if ($this->item->pubstate == 'error')
{
	$alert_style = 'alert-error';
}

$alert_message = JText::_($this->item->resultmsg);

?>

<div class="extly">
	<div class="extly-body">

		<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal form-validate">
			<input type="hidden" name="option" value="com_autotweet" />
			<input type="hidden" name="view" value="posts" />
			<input type="hidden" name="task" value="" />
			<?php

				echo EHtml::renderRoutingTags();

			?>
			<div class="row-fluid">

				<div class="span6">

					<fieldset class="details">

						<div class="control-group">
							<label class=" required control-label" for="postdate" id="postdate-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_PUBLICATION_DATE_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_PUBLICATION_DATE'); ?></label>
							<div class="controls">
								<?php

								$postdate = JHtml::_('date', $this->item->postdate, JText::_('COM_AUTOTWEET_DATE_FORMAT'));
								echo JHTML::_('calendar', $postdate, 'postdate', 'postdate', '%Y-%m-%d', array('class' => 'input', 'required' => 'required'));

								?>
							</div>
						</div>

						<div class="control-group">
							<label></label>
							<div class="controls">
								<?php

								echo $this->showWorldClockLink();

								?>
							</div>
						</div>

						<div class="control-group">
							<label for="channel_id" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_CHANNEL_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_CHANNEL'); ?> <span class="star">&#160;*</span></label>
							<div class="controls">
								<?php echo SelectControlHelper::channels($this->item->channel_id, 'channel_id', array('class' => 'input required', 'required' => 'required')) ?>
							</div>
						</div>

						<div class="control-group">
							<label for="plugin" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_PLUGIN_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_PLUGIN'); ?> <span class="star">&#160;*</span></label>
							<div class="controls">
								<?php echo SelectControlHelper::plugins($this->item->plugin, 'plugin', array('class' => 'input required', 'required' => 'required')) ?>
							</div>
						</div>

						<div class="control-group">
							<label for="ref_id" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_REFERENCE_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_REFERENCE'); ?> <span class="star">&#160;*</span>
							</label>
							<div class="controls">
								<input type="text" name="ref_id" id="ref_id" value="<?php echo $this->item->ref_id; ?>" class="input required" maxlength="64" required="required"/>
							</div>
						</div>

						<div class="control-group">
							<label for="title" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_TITLE_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_TITLE'); ?> <span class="star">&#160;*</span>
							</label>
							<div class="controls">
								<input type="text" name="title" id="title" value="<?php echo htmlentities($this->item->title, ENT_COMPAT, 'UTF-8'); ?>" class="input required" maxlength="512" required="required"/>
							</div>
						</div>

						<div class="control-group">
							<label for="message" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_MESSAGE_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_MESSAGE'); ?> <span class="star">&#160;*</span></label>
							<div class="controls">
								<textarea name="message" id="message" rows="5" cols="80" maxlength="512" class="input required"><?php
									echo $this->item->message;
									?></textarea>
							</div>
						</div>

						<div class="control-group">
							<label for="url" class="control-label" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_SHORT_URL_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_SHORT_URL'); ?></label>
							<div class="controls">
								<input type="text" name="url" id="url" value="<?php echo TextUtil::renderUrl($this->item->url); ?>" maxlength="512" />
							</div>
						</div>

						<div class="control-group">
							<label for="org_url" class="control-label" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_ORIGINAL_URL_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_ORIGINAL_URL'); ?></label>
							<div class="controls">
								<input type="text" name="org_url" id="org_url" value="<?php echo TextUtil::renderUrl($this->item->org_url); ?>" maxlength="512" />
							</div>
						</div>

						<?php

						echo EHtml::imageControl(
							TextUtil::renderUrl($this->item->image_url),
							'image_url',
							'COM_AUTOTWEET_POST_IMAGE_URL',
							'COM_AUTOTWEET_POST_IMAGE_URL_DESC',
							null,
							null,
							true
						);

						?>

						<div class="control-group">
							<label for="show_url" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_POST_SHOW_URL_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_POST_SHOW_URL'); ?> <span class="star">&#160;*</span></label>
							<div class="controls">
								<?php echo SelectControlHelper::showurl($this->item->show_url, 'show_url', array('class' => 'input required', 'required' => 'required')) ?>
							</div>
						</div>

<?php
						echo  SelectControlHelper::pubstatesControl(
							$this->item->pubstate,
							'pubstate',
							JText::_('COM_AUTOTWEET_POST_STATE') . ' <span class="star">&#160;*</span>',
							'COM_AUTOTWEET_POST_STATE_DESC',
							array('class' => 'input required', 'required' => 'required')
						);
?>

						<div class="control-group">
							<label for="post_id" class="control-label"
								rel="tooltip" data-original-title="<?php echo JText::_('JGLOBAL_FIELD_ID_DESC'); ?>"><?php
								echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?> </label>
							<div class="controls">
								<input type="text" name="id" id="post_id" value="<?php echo $this->item->id; ?>" class="uneditable-input" readonly="readonly">
							</div>
						</div>

					</fieldset>

				</div>

				<?php

				require dirname(__FILE__) . '/../../request/tmpl/right-side.php';

				?>

			</div>
		</form>
	</div>
</div>
