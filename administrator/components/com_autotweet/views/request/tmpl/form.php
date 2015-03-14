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

if (!class_exists('JFormFieldImagelist'))
{
	require_once JPATH_LIBRARIES . '/joomla/form/fields/imagelist.php';
}

$alert_style = 'alert-info';
$alert_message = '';

$native_object = json_decode($this->item->native_object);

if ((isset($native_object->error)) && ($native_object->error))
{
	$alert_style = 'alert-error';
	$alert_message = JText::_($native_object->error_message);
}

$isManualMsg = ($this->item->plugin === 'autotweetpost');
$readonlyNotManual = (!$isManualMsg ? 'readonly="readonly"' : '');
$labelDisabledNotManual = (!$isManualMsg ? 'disabled' : '');

// New Manual, by default not processed
if (($isManualMsg) && ($this->item->get('id') == 0))
{
	$this->item->set('published', false);
}

?>

<div class="extly request-edit">
	<div class="extly-body">

		<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal form-validate">
			<input type="hidden" name="option" value="com_autotweet" />
			<input type="hidden" name="view" value="requests" />
			<input type="hidden" name="task" value="" />
			<?php

				echo EHtml::renderRoutingTags();

			?>

			<div class="row-fluid">

				<div class="span6">

					<fieldset class="details">

						<div class="control-group">
							<label class="required control-label" for="publish_up_time" id="publish_up-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_REQ_SCHEDULED_DATE_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_REQ_SCHEDULED_DATE'); ?> <span class="star">&#160;*</span>
							</label>
							<div class="controls">
								<?php

								$publish_up = JHtml::_('date', $this->item->publish_up, JText::_('COM_AUTOTWEET_DATE_FORMAT'));
								echo JHTML::_('calendar', $publish_up, 'publish_up', 'publish_up', '%Y-%m-%d', array('class' => 'input required'));

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
							<label for="plugin required" class="control-label" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_REQ_PLUGIN_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_REQ_PLUGIN'); ?> <span class="star">&#160;*</span>
							</label>
							<div class="controls">
								<?php echo SelectControlHelper::plugins($this->item->plugin, 'plugin', array('class' => 'input required')); ?>
							</div>
						</div>

						<div class="control-group">
							<label for="ref_id" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_REQ_REFERENCE_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_REQ_REFERENCE'); ?> <span class="star">&#160;*</span>
							</label>
							<div class="controls">
								<input type="text" name="ref_id" id="ref_id" value="<?php echo empty($this->item->ref_id) ? JFactory::getDate()->toUnix() : $this->item->ref_id; ?>" class="required" maxlength="64" />
							</div>
						</div>

						<div class="control-group">
							<label for="name" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_REQ_TITLE_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_REQ_TITLE'); ?> <span class="star">&#160;*</span>
							</label>
							<div class="controls">
								<input type="text" name="description" id="description" value="<?php echo htmlentities($this->item->description, ENT_COMPAT, 'UTF-8'); ?>" class="required" maxlength="512" required="required" />
							</div>
						</div>

						<div class="control-group">
							<label for="text" class="control-label" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_REQ_LINK_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_REQ_LINK'); ?>
							</label>
							<div class="controls">
								<input type="text" name="url" id="url" value="<?php echo TextUtil::renderUrl($this->item->url); ?>" maxlength="512"/>
							</div>
						</div>

						<?php

						echo EHtml::imageControl(
							TextUtil::renderUrl($this->item->image_url),
							'image_url',
							'COM_AUTOTWEET_REQ_IMAGE',
							'COM_AUTOTWEET_REQ_IMAGE_DESC',
							null,
							null,
							true
						);

						?>

						<div class="control-group">
							<label class="control-label <?php echo $labelDisabledNotManual; ?>" for="published" id="published-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_REQ_PUBLISHED_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_REQ_PUBLISHED_TITLE'); ?> </label>
							<div class="controls inline">
								<?php

								echo EHtmlSelect::published($this->item->get('published'), 'published', array(), 'JYES', 'JNO');

								?>
							</div>
						</div>

						<div class="control-group">
							<label for="request_id" class="control-label" rel="tooltip" data-original-title="<?php echo JText::_('JGLOBAL_FIELD_ID_DESC'); ?>"><?php
							echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?> </label>
							<div class="controls">
								<input type="text" name="id" id="request_id" value="<?php echo $this->item->id; ?>" class="uneditable-input" readonly="readonly">
							</div>
						</div>

					</fieldset>

				</div>

				<?php

				require dirname(__FILE__) . '/right-side.php';

				?>

			</div>
		</form>
	</div>
</div>

