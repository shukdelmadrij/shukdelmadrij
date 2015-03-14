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

if (EXTLY_J3)
{
?>

<script>
require(['punycode'], function(punycode) {
	if (!window.punycode) {
		window.punycode = punycode;
	}
});
</script>

<?php
}
?>

<!-- com_autotweet_OUTPUT_START -->
<p style="text-align:center;">
	<span class="loaderspinner">&nbsp;</span>
</p>

<legend><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_ACCOUNTDATA_TITLE'); ?></legend>
<p>
	<?php echo JText::_('COM_AUTOTWEET_CHANNEL_MAIL_DESC'); ?>
</p>

<div class="control-group">
	<label class="control-label required" for="mail_sender_email" id="mail_sender_email-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_MAIL_SENDER_MAIL'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('mail_sender_email'); ?>" id="mail_sender_email" name="xtform[mail_sender_email]" class="required validate-email" required="required">
	</div>
</div>
<div class="control-group required">
	<label class=" control-label" for="mail_sender_name" id="mail_sender_name-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_MAIL_SENDER_NAME'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('mail_sender_name'); ?>" id="mail_sender_name" name="xtform[mail_sender_name]" class="required" required="required">
	</div>
</div>
<div class="control-group required">
	<label class=" control-label" for="mail_recipient_email" id="mail_recipient_email-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_MAIL_RECIPIENT_MAIL'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('mail_recipient_email'); ?>" id="mail_recipient_email" name="xtform[mail_recipient_email]" class="required validate-email" required="required">
	</div>
</div>
<!-- com_autotweet_OUTPUT_START -->
