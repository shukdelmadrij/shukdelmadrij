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
	<div id="fbchannel" class="tab-pane fade">

		<div class="control-group">
			<label class="required control-label" for="xtformfbchannel_id"
				id="fbchannel_id-lbl" rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_FIELD_FBCHANNEL_DESC');

					?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_FIELD_FBCHANNEL_TITLE'); ?> <span
				class="star">&nbsp;*</span></label>
			<div class="controls">
				<?php echo SelectControlHelper::fbchannels($this->item->xtform->get('fbchannel_id'), 'xtform[fbchannel_id]', array(), $this->item->xtform->get('app_id'), $this->item->xtform->get('secret'), $this->item->xtform->get('access_token')); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="required control-label" for="fbchannel_access_token"
				id="fbchannel_access_token-lbl" rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_USERTOKEN_DESC');

					?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_USERTOKEN_TITLE'); ?> <span
				class="star">&nbsp;*</span></label>
			<div class="controls">
				<input type="text" maxlength="255" size="50"
					value="<?php echo $this->item->xtform->get('fbchannel_access_token'); ?>"
					id="fbchannel_access_token" name="xtform[fbchannel_access_token]"
					class="required validate-token" required="required"
					readonly="readonly">
<?php

		require dirname(__FILE__) . '/../../channel/tmpl/social_url.php';

?>
			</div>
		</div>

		<?php

		if ($requireAlbum)
		{
			?>
		<div class="control-group">
			<label class="required control-label" for="xtformfbalbum_id"
				id="fbalbum_id-lbl" rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_FIELD_FBALBUM_DESC');

					?>"><?php
			echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_FIELD_FBALBUM_TITLE');
			?> <span class="star">&nbsp;*</span></label>
			<div class="controls">
				<a class="btn btn-info" id="fbalbumloadbutton"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_LOADBUTTON_TITLE'); ?></a>
				<?php

echo SelectControlHelper::fbalbums($this->item->xtform->get('fbalbum_id'), 'xtform[fbalbum_id]', array(), $this->item->xtform->get('app_id'), $this->item->xtform->get('secret'), $this->item->xtform->get('access_token'), $this->item->xtform->get('fbchannel_id'));
			?>
			</div>
		</div>
     	<?php
		}
		?>

		<div class="control-group">
			<label class="control-label" rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTONCHANNEL_DESC');

					?>"> <a class="btn btn-info"
				id="fbchvalidationbutton"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTONCHANNEL_TITLE'); ?></a>
			</label>
		</div>

		<div class="control-group" style="display:none">
			<label class=" required control-label" for="channel_issued_at" id="channel_issued_at-lbl" rel="tooltip" data-original-title="<?php

			echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_ISSUEDAT_DESC');

			?>"><?php
			echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_ISSUEDAT_TITLE'); ?><span
				class="star">&nbsp;*</span> </label>
			<div class="controls">
				<input type="text" maxlength="255" size="64"
					value="<?php echo $this->item->xtform->get('channel_issued_at'); ?>"
					id="channel_issued_at" name="xtform[channel_issued_at]"
					class="required" required="required"
					readonly="readonly">
			</div>
		</div>

		<div class="control-group">
			<label class=" required control-label" for="channel_expires_at" id="channel_expires_at-lbl" rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_EXPIRESAT_DESC');

					?>"><?php

			echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_EXPIRESAT_TITLE');

			?><span
				class="star">&nbsp;*</span> </label>
			<div class="controls">
				<input type="text" maxlength="255" size="64"
					value="<?php echo $this->item->xtform->get('channel_expires_at'); ?>"
					id="channel_expires_at" name="xtform[channel_expires_at]"
					class="required" required="required"
					readonly="readonly">
			</div>
		</div>

		<input class="channel-type" type="hidden" name="xtform[channel_type]" value="<?php echo $this->item->xtform->get('channel_type'); ?>">
<?php

		$isUserChannel = ($this->item->xtform->get('channel_type') == 'User');
		$isGroupChannel = ($this->item->xtform->get('channel_type') == 'Group');

		if ($isUserChannel)
		{
			$open_graph_features = '';
		}
		else
		{
			$open_graph_features = 'style="display: none;"';
		}

		if ($isGroupChannel)
		{
			$group_warn = '';
		}
		else
		{
			$group_warn = 'style="display: none;"';
		}

?>
		<div class="open_graph_features" <?php echo $open_graph_features; ?>>
<?php

		$attrs = array(
						'class' => 'required',
						'required' => 'required'
		);
		$control = SelectControlHelper::sharedWith(
				$this->item->xtform->get('sharedwith', 'EVERYONE'),
				'xtform[sharedwith]',
				$attrs
		);
		echo EHtml::genericControl(
				JText::_('COM_AUTOTWEET_VIEW_CHANNEL_SHARED_WITH_LABEL'),
				JText::_('COM_AUTOTWEET_VIEW_CHANNEL_SHARED_WITH_DESC'),
				'sharedwith',
				$control
		);

		$open_graph_features = $this->item->xtform->get('open_graph_features');
		echo EHtmlSelect::yesNoControl($open_graph_features, 'xtform[open_graph_features]', 'COM_AUTOTWEET_VIEW_OPENGRAPHF_TITLE', 'COM_AUTOTWEET_VIEW_OPENGRAPHF_DESC', 'og_features');

?>

			<div id="og-fields" class="og-fields alert" <?php
			echo $open_graph_features ? '' : 'style="display: none;"';
		?>>
<?php

		echo EHtmlSelect::yesNoControl($this->item->xtform->get('og_explicitly_shared'), 'xtform[og_explicitly_shared]', 'COM_AUTOTWEET_VIEW_EXPLICITLYSHARED_TITLE', 'COM_AUTOTWEET_VIEW_EXPLICITLYSHARED_DESC');
		echo EHtmlSelect::yesNoControl($this->item->xtform->get('og_user_generated'), 'xtform[og_user_generated]', 'COM_AUTOTWEET_VIEW_USERGENERATED_TITLE', 'COM_AUTOTWEET_VIEW_USERGENERATED_DESC');

?>
			</div>
		</div>

		<div class="group-warn alert" <?php echo $group_warn; ?>>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<p>
				<i class="xticon xticon-thumbs-up"></i> <b><?php
				echo JText::_('COM_AUTOTWEET_VIEW_FACEBOOK_GROUP_LABEL');
				?></b>
			</p>
			<p>
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_FACEBOOK_GROUP_DESC');
			?>
			</p>
		</div>

	</div>

<script type="text/javascript">/*
<![CDATA[*/
	jQuery('*[rel=tooltip]').tooltip();
/*]]>*/</script>
