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

<?php echo JText::_('COM_AUTOTWEET_CHANNEL_LINKEDIN_DESC'); ?>

<div class="control-group">
	<label class="required control-label" for="api_key" id="api_key-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_LINKEDIN_API_KEY'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('api_key'); ?>" id="api_key" name="xtform[api_key]" class="required validate-token" required="required">
	</div>
</div>
<div class="control-group">
	<label class="required control-label" for="secret_key" id="secret_key-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_LINKEDIN_SECRET_KEY'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="password" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('secret_key'); ?>" id="secret_key" name="xtform[secret_key]" class="required validate-token" required="required">
	</div>
</div>
<div class="control-group">
	<label class="required control-label" for="oauth_user_token" id="oauth_user_token-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_LINKEDIN_OAUTH_USER_TOKEN'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('oauth_user_token'); ?>" id="oauth_user_token" name="xtform[oauth_user_token]" class="required validate-token" required="required">
	</div>
</div>
<div class="control-group">
	<label class="required control-label" for="oauth_user_secret" id="oauth_user_secret-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_LINKEDIN_OAUTH_USER_SECRET'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="password" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('oauth_user_secret'); ?>" id="oauth_user_secret" name="xtform[oauth_user_secret]" class="required validate-token" required="required">
	</div>
</div>

<div class="control-group">
	<label class="control-label"> <a class="btn btn-info" id="livalidationbutton"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTON'); ?></a>
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
		<input type="text" maxlength="255" size="64" value="<?php echo $this->item->xtform->get('user_id'); ?>" id="user_id" name="xtform[user_id]" class="required" required="required" readonly="readonly">
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
