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

$isFrontendEnabled = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')->isFrontendEnabled($this->item->channeltype_id);

?>

<div class="extly">
	<div class="extly-body">

		<?php echo Extly::showInvalidFormAlert(); ?>

		<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal form-validate">
			<input type="hidden" name="option" value="com_autotweet" />
			<input type="hidden" name="view" value="channels" />
			<input type="hidden" name="task" value="" />
			<?php

				echo EHtml::renderRoutingTags();

			?>

			<div class="row-fluid">

				<div class="span6">

					<fieldset class="basic">

						<legend>
							<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_SELECTCHANNEL_TITLE'); ?>
						</legend>

						<div class="control-group">
							<label for="channeltype_id" class="control-label required" rel="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_SELECTCHANNEL_DESC'); ?>"> <?php echo
							JText::_('COM_AUTOTWEET_VIEW_TYPE_TITLE');
							?> <span class="star">&#160;*</span>
							</label>
							<div class="controls">
								<?php echo SelectControlHelper::channeltypes($this->item->channeltype_id, 'channeltype_id', array('class' => 'required')); ?>
							</div>
						</div>

					</fieldset>

					<fieldset class="details">

						<legend>
							<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_CHANNELDATA_TITLE'); ?>
						</legend>
<?php
						echo EHtml::requiredTextControl($this->item->get('name'), 'name', 'COM_AUTOTWEET_VIEW_CHANNEL_NAME_TITLE', 'COM_AUTOTWEET_VIEW_CHANNEL_NAME_DESC', null, 64);

						echo EHtml::textareaControl($this->item->get('description'), 'description', 'COM_AUTOTWEET_VIEW_DESCRIPTION_TITLE', 'COM_AUTOTWEET_VIEW_DESCRIPTION_DESC');

						echo EHtmlSelect::publishedControl($this->item->get('published'), 'published');

						if ((AUTOTWEETNG_JOOCIAL) && ($isFrontendEnabled))
						{
							echo SelectControlHelper::scopeControl($this->item->get('scope', 'S'), $this->item->get('xtform')->get('frontendchannel'));
						}

						echo EHtmlSelect::yesNoControl($this->item->get('autopublish'), 'autopublish', 'COM_AUTOTWEET_VIEW_AUTOPUBLISH_TITLE', 'COM_AUTOTWEET_VIEW_AUTOPUBLISH_DESC');

						echo EHtmlSelect::yesNoControl($this->item->get('xtform')->get('hashtags', true), 'xtform[hashtags]', 'COM_AUTOTWEET_VIEW_HASHTAGS_TITLE', 'COM_AUTOTWEET_VIEW_HASHTAGS_DESC');
?>

						<div class="control-group">
							<label for="media_mode" class="control-label" rel="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_MEDIAMODE_DESC'); ?>"><?php
							echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_MEDIAMODE_TITLE'); ?> </label>
							<div class="controls">
								<?php echo SelectControlHelper::mediamodes($this->item->media_mode, 'media_mode', null); ?>
							</div>
						</div>

						<?php
						if ((AUTOTWEETNG_JOOCIAL) && (EParameter::getComponentParam(CAUTOTWEETNG, 'targeting', false)))
						{
						?>
			            <hr/>

			            <div class="control-group">
			              <label class="control-label" for="xtformtarget_id" id="target_id-lbl"><?php echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_TARGETING_TITLE'); ?></label>
			              <div class="controls">
			                <?php echo SelectControlHelper::targets($this->item->xtform->get('target_id'), 'xtform[target_id]', null); ?>
			              </div>
			            </div>

						<?php
						}

						echo EHtml::idControl($this->item->get('id'), 'id', 'channel_id');

						?>

					</fieldset>

				</div>

				<div class="span6">

					<div class="row-fluid">
						<div class="span12">
							<div id="channel_data">
								<fieldset class="channel_data">
									<p class="text-center">
										<span class="loaderspinner">&nbsp;</span>
									</p>
									<legend>
										<?php echo JText::_('...requesting channel data...'); ?>
									</legend>
								</fieldset>
							</div>
						</div>
					</div>

					<?php

					$alert_message = $this->get('alert_message');

					if ($this->item->id)
					{
						include_once 'audit.php';
					}

					?>

				</div>

			</div>
		</form>
	</div>
</div>
<script type="text/javascript">

window.addEvent('domready', function() {

	document.formvalidator.setHandler('token',
			function (value) {
				regex=/^[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]$/;
				return regex.test(value);
			}
	);

	document.formvalidator.setHandler('facebookapp',
			function (value) {
				regex=/^http(s)?\:\/\/apps\.facebook\.com\/[a-zA-Z0-9-_]+$/;
				return regex.test(value);
			}
	);

});

</script>
