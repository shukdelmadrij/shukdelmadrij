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

?>
<!-- com_autotweet_OUTPUT_START -->
<p style="text-align:center;">
	<span class="loaderspinner">&nbsp;</span>
</p>

<legend><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_ACCOUNTDATA_TITLE'); ?></legend>

<?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTER_DESC'); ?>

<div class="control-group">
	<label class="required control-label" for="consumer_key" id="consumer_key-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTER_FIELD_CONSUMER_KEY'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('consumer_key'); ?>" id="consumer_key" name="xtform[consumer_key]" class="required validate-token" required="required">
	</div>
</div>
<div class="control-group">
	<label class="required control-label" for="consumer_secret" id="consumer_secret-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTER_FIELD_CONSUMER_SECRET'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="password" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('consumer_secret'); ?>" id="consumer_secret" name="xtform[consumer_secret]" class="required validate-token" required="required">
	</div>
</div>
<div class="control-group">
	<label class="required control-label" for="access_token" id="access_token-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTER_ACCESS_TOKEN'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('access_token'); ?>" id="access_token" name="xtform[access_token]" class="required validate-token" required="required">
	</div>
</div>
<div class="control-group">
	<label class="required control-label" for="access_token_secret" id="access_token_secret-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTER_FIELD_ACCESS_TOKEN_SECRET'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="password" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('access_token_secret'); ?>" id="access_token_secret" name="xtform[access_token_secret]" class="required validate-token" required="required">
	</div>
</div>

<div class="control-group">
	<label class="control-label"> <a class="btn btn-info" id="twvalidationbutton"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTON'); ?></a>
	</label>

	<div id="validation-notchecked" class="controls">
		<span class="lead"><i class="xticon xticon-question"></i> </span><span class="loaderspinner">&nbsp;</span>
	</div>

	<div id="validation-success" class="controls" style="display: none">
		<span class="lead"><i class="xticon xticon-check"></i> <?php echo JText::_('COM_AUTOTWEET_STATE_PUBSTATE_SUCCESS'); ?></span><span class="loaderspinner">&nbsp;</span>
	</div>

	<div id="validation-error" class="controls" style="display: none">
		<span class="lead"><i class="xticon xticon-exclamation"></i> <?php echo JText::_('COM_AUTOTWEET_STATE_PUBSTATE_ERROR'); ?></span><span class="loaderspinner">&nbsp;</span>
	</div>

</div>

<div id="validation-errormsg" class="alert alert-block alert-error" style="display: none">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<div id="validation-theerrormsg">
		<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTH_MSG'); ?>
	</div>
</div>

<div class="control-group">
	<label class=" required control-label" for="user_id" id="user_id-lbl"><?php echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_ACCOUNTID_TITLE'); ?><span class="star">&nbsp;*</span>
	</label>
	<div class="controls">
		<input type="text" maxlength="255" size="64" value="<?php echo $this->item->xtform->get('user_id'); ?>" id="user_id" name="xtform[user_id]" class="required validate-numeric" required="required" readonly="readonly">
<?php

		require dirname(__FILE__) . '/../../channel/tmpl/social_url.php';

?>
	</div>
</div>

<div class="alert">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="xticon xticon-thumbs-up"></i> <a href="http://www.extly.com/how-to-autotweet-in-5-minutes-from-joomla.html" target="_blank">
	Tutorial: How to AutoTweet from Joomla in 5 minutes</a>
</div>
<!-- com_autotweet_OUTPUT_START -->
