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

<?php echo JText::_('COM_AUTOTWEET_CHANNEL_VK_DESC'); ?>

<div class="control-group">
	<label class="required control-label" for="application_id" id="application_id-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_VK_FIELD_APPLICATION_ID'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="text" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('application_id'); ?>" id="application_id" name="xtform[application_id]" class="required validate-token" required="required">
	</div>
</div>

<div class="control-group">
	<label class="required control-label" for="secure_key" id="secure_key-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_VK_FIELD_SECURE_KEY'); ?> <span class="star">&#160;*</span></label>
	<div class="controls">
		<input type="password" maxlength="255" size="50" value="<?php echo $this->item->xtform->get('secure_key'); ?>" id="secure_key" name="xtform[secure_key]" class="required validate-token" required="required">
	</div>
</div>

<?php

	$jsonAccessToken = null;
	$accessToken = null;
	$userId = null;
	$expiresIn = null;

	$authUrl = '#';
	$authUrlButtonStyle = 'disabled';

	$validationGroupStyle = 'hide';

	// New channel, not even saved
	if ($this->item->id == 0)
	{
		$message = JText::_('COM_AUTOTWEET_CHANNEL_VK_NEWCHANNEL_NOAUTHORIZATION');
		include_once 'auth_button.php';
	}
	else
	{
		$vkChannelHelper = new VkChannelHelper($this->item);
		$isAuth = $vkChannelHelper->isAuth();

		// New channel, but saved
		if ($isAuth)
		{
			// We have an access Token!
			$jsonAccessToken = $vkChannelHelper->getJsonAccessToken();
			$accessToken = $vkChannelHelper->getAccessToken();
			$userId = $vkChannelHelper->getUserId();
			$expiresIn = $vkChannelHelper->getExpiresIn();

			// $this->item->xtform->set('social_url', '#');

			$validationGroupStyle = null;

			include_once 'validation_button.php';
		}
		else
		{
			$message = JText::_('COM_AUTOTWEET_CHANNEL_VK_NEWCHANNEL_AUTHORIZATION');
			$application_id = $this->item->xtform->get('application_id');

			$thisUrl = 'http://api.vkontakte.ru/blank.html';
			$authUrl = $vkChannelHelper->getAuthorizeUrlDesktopStandalone(
					$application_id,
					$thisUrl
			);

			$authUrlButtonStyle = null;

			include_once 'auth_button.php';
			include_once 'validation_button.php';
		}
	}
?>

<p><br/></p>
<div class="alert alert-warning">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="xticon xticon-vk"></i> <b>About VK application (Standalone Application)</b>:
	VK <i>only</i> allows to publish Posts from a Standalone application.
	In practice, you have to generate the access token from the <i>same</i> computer (IP) where you are going to publish.
	Since you are generating a token for your site, there are two ways to do it:</a>
	<ul>
		<li>Being physically in the server</li>
		<li>Browsing VK through a web proxy in your site (IP) (E.g. Glype)</li>
		<li>Browsing VK through a proxy in your server (IP) (E.g. Squid)</li>
	</ul>
</div>
<!-- com_autotweet_OUTPUT_START -->
