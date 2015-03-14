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

$channeltypeId = $this->input->get('channeltype_id', AutotweetModelChanneltypes::TYPE_VKCHANNEL, 'cmd');
$requireGroup = ($channeltypeId == AutotweetModelChanneltypes::TYPE_VKGROUPCHANNEL);

?>
<input type="hidden" maxlength="255" size="64" value='<?php echo $jsonAccessToken; ?>' id="access_token" name="xtform[access_token]">

<div id="validationGroup" class=" <?php echo $validationGroupStyle; ?>">

	<div class="control-group">

		<label class="required control-label" for="token_url" id="token_url-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_VK_TOKEN_URL'); ?> <span class="star">&nbsp;*</span>
		</label>

		<div class="controls">
			<input type="text" maxlength="255" size="64" value="<?php echo $this->item->xtform->get('token_url'); ?>" id="token_url" name="xtform[token_url]" class="required" required="required">
		</div>

		<label class="control-label">

		<a class="btn btn-info" id="vkvalidationbutton"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTON'); ?></a>&nbsp;

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
		<label class="required control-label" for="raw_access_token" id="access_token_raw-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_VK_ACCESS_TOKEN'); ?> <span class="star">&nbsp;*</span>
		</label>
		<div class="controls">
			<input type="text" maxlength="255" size="64" value="<?php echo $accessToken; ?>" id="raw_access_token" name="xtform[access_token_raw]" readonly="readonly" class="required" required="required">
		</div>
	</div>

	<div class="control-group">
		<label class="required control-label" for="raw_user_id" id="user_id-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_VK_USERID_TITLE'); ?> <span class="star">&nbsp;*</span>
		</label>
		<div class="controls">
			<input type="text" maxlength="255" size="64" value="<?php echo $userId; ?>" id="raw_user_id" name="xtform[user_id]" readonly="readonly" class="required validate-numeric" required="required">
<?php

		require dirname(__FILE__) . '/../../channel/tmpl/social_url.php';

?>
		</div>
	</div>

	<div class="control-group">
		<label class="required control-label" for="raw_expires_in" id="expires_in-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_VK_EXPIRES_TITLE'); ?> <span class="star">&nbsp;*</span>
		</label>
		<div class="controls">
			<input type="text" maxlength="255" size="64" value="<?php echo $expiresIn; ?>" id="raw_expires_in" name="expires_in" readonly="readonly"  class="required validate-numeric" required="required"> (0 = Never)
		</div>
	</div>

	<?php
		if ($requireGroup)
		{
	?>
		<div class="control-group">
			<label class="required control-label" for="xtformvkgroup_id"
				id="vkgroup_id-lbl"><?php
			echo JText::_('COM_AUTOTWEET_CHANNEL_VK_GROUP');
			?> <span class="star">&nbsp;*</span></label>
			<div class="controls">
				<a class="btn btn-info" id="vkgrouploadbutton"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_LOADBUTTON_TITLE'); ?></a>
				<?php
					echo SelectControlHelper::vkgroups(
						$this->item->xtform->get('vkgroup_id'),
						'xtform[vkgroup_id]',
						array(),
						$this->item->xtform->get('access_token'),
						$this->item->id
					);

					$social_target = 'social_url_vkgroup';
					require dirname(__FILE__) . '/../../channel/tmpl/social_url.php';

			?>
			</div>
		</div>
     	<?php
		}
	?>

</div>
