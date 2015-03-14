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

$session = JFactory::getSession();
$session->set('channelId', $this->item->id);

?>
<!-- com_autotweet_OUTPUT_START -->
<p style="text-align:center;">
	<span class="loaderspinner">&nbsp;</span>
</p>

<legend><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_ACCOUNTDATA_TITLE'); ?></legend>

<?php echo JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_DESC'); ?>

<div class="control-group">
	<label class="required control-label" for="client_id" id="client_id-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_FIELD_CLIENT_ID'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('client_id'); ?>" id="client_id" name="xtform[client_id]" class="required" required="required">
	</div>
</div>

<div class="control-group">
	<label class="required control-label" for="client_secret" id="client_secret-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_FIELD_CLIENT_SECRET'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="password" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('client_secret'); ?>" id="client_secret" name="xtform[client_secret]" class="required" required="required">
	</div>
</div>

<div class="control-group">
	<label class="required control-label" for="developer_key" id="developer_key-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_FIELD_DEVELOPER_KEY'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('developer_key'); ?>" id="developer_key" name="xtform[developer_key]" class="required" required="required">
	</div>
</div>

<?php

echo EHtmlSelect::yesNoControl(
		$this->item->xtform->get('schemaorg_url', 0),
		'xtform[schemaorg_url]',
		'COM_AUTOTWEET_CHANNEL_GPLUS_POST_URL',
		'COM_AUTOTWEET_CHANNEL_GPLUS_POST_URL_DESC');

	$accessToken = null;
	$userId = null;
	$expiresIn = null;

	$authUrl = '#';
	$authUrlButtonStyle = 'disabled';

	$validationGroupStyle = 'hide';

		// New channel, not even saved
	if ($this->item->id == 0)
	{
		$message = JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_NEWCHANNEL_NOAUTHORIZATION');
		include_once 'auth_button.php';
	}
	else
	{
		$gplusChannelHelper = new GplusChannelHelper($this->item);
		$isAuth = $gplusChannelHelper->isAuth();

		// New channel, but saved
		if ($isAuth)
		{
			// We have an access Token!

			$accessToken = $gplusChannelHelper->getAccessToken();

			$user = $gplusChannelHelper->getUser();
			$userId = $user['id'];
			$this->item->xtform->set('social_url', $user['url']);

			$expiresIn = $gplusChannelHelper->getExpiresIn();

			$validationGroupStyle = null;

			include_once 'validation_button.php';
		}
		else
		{
			$message = JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_NEWCHANNEL_AUTHORIZATION');

			$authUrl = $gplusChannelHelper->getAuthorizationUrl();

			$authUrlButtonStyle = null;

			include_once 'auth_button.php';
			include_once 'validation_button.php';
		}
	}
?>

<div class="alert">
	<button type="button" class="close" data-dismiss="alert">&times;</button>

	<p>
		<i class="xticon xticon-thumbs-up"></i>
		<a href="http://www.extly.com/autotweetng-joocial-publishing-to-gplus-profiles-and-pages.html" target="_blank">
		Tutorial: Publishing to G+ Profiles and Pages</a>
	</p>
	<p>
		<i class="xticon xticon-thumbs-up"></i> <a href="http://www.extly.com/how-to-autotweet-in-5-minutes-from-joomla.html" target="_blank">
		Tutorial: How to AutoTweet from Joomla in 5 minutes</a>
	</p>
</div>

<!-- com_autotweet_OUTPUT_START -->
