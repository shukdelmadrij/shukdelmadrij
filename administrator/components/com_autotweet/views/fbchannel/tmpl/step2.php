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
	<div id="fbauth" class="tab-pane fade">

		<p class="lead"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTH_TITLE'); ?></p>

		<div class="control-group">
			<label class="control-label" rel="tooltip"
				data-original-title="<?php

				echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTH_DESC');

				?>">

				<input type="button" id="authextendbutton"
				value="<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTHBUTTON_TITLE'); ?>"
				class="btn btn-info" <?php

				// No or Yes, with Canvas Page
				if ($authorizeCanvas)
				{
					echo 'style="display: none;"';
				}

				?>/>

				<input type="button" id="authbutton"
				value="<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTHBUTTON_TITLE'); ?>"
				class="btn btn-info" <?php

				// Yes (no Canvas Page)
				if (!$authorizeCanvas)
				{
					echo 'style="display: none;"';
				}

				?>/>

			</label>
		</div>

		<div class="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTH_DESC');
			?>
		</div>

		<div class="control-group">
			<label class="required control-label" for="access_token"
				id="access_token-lbl" rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_USERTOKEN_DESC');

					?>"><?php

					echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_USERTOKEN_TITLE');

					?><span
				class="star">&nbsp;*</span> </label>
			<div class="controls">
				<input type="text" maxlength="255" size="64"
					value="<?php echo $this->item->xtform->get('access_token'); ?>"
					id="access_token" name="xtform[access_token]"
					class="required validate-token" required="required">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">

				<a class="btn btn-info"
					id="fbextendbutton"<?php

				// Yes (No Canvas Page)
				if ($authorizeCanvas)
				{
					echo 'style="display: none;"';
				}

				?>><?php
					echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTON_TITLE'); ?></a>

				<a class="btn btn-info"
					id="fbvalidationbutton"<?php

				// No or Yes, with Canvas Page
				if (!$authorizeCanvas)
				{
					echo 'style="display: none;"';
				}

				?>><?php
					echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTON_TITLE'); ?></a>

			</label>

			<div id="validation-notchecked" class="controls">
				<span class="lead"><i class="xticon xticon-question"></i> </span>
				<span class="loaderspinner72"><?php echo JText::_('COM_AUTOTWEET_LOADING'); ?></span>
			</div>

			<div id="validation-success" class="controls" style="display: none">
				<span class="lead"><i class="xticon xticon-check"></i> <?php
				echo JText::_('COM_AUTOTWEET_STATE_PUBSTATE_SUCCESS');
				?> - <?php
				echo JText::_('COM_AUTOTWEET_CHANNEL_SELECTFBCHANNEL');
				?></span>
				<span class="loaderspinner72"><?php echo JText::_('COM_AUTOTWEET_LOADING'); ?></span>
			</div>

			<div id="validation-error" class="controls" style="display: none">
				<span class="lead"><i class="xticon xticon-exclamation"></i> <?php echo JText::_('COM_AUTOTWEET_STATE_PUBSTATE_ERROR'); ?></span>
				<span class="loaderspinner72"><?php echo JText::_('COM_AUTOTWEET_LOADING'); ?></span>
			</div>

		</div>

		<div id="validation-errormsg" class="alert alert-block alert-error"
			style="display: none">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div id="validation-theerrormsg">
				<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTH_FBMSG'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class=" required control-label" for="user_id" id="user_id-lbl" rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_ACCOUNTID_DESC');

					?>"><?php

			echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_ACCOUNTID_TITLE');

			?><span
				class="star">&nbsp;*</span> </label>
			<div class="controls">
				<input type="text" maxlength="255" size="64"
					value="<?php echo $this->item->xtform->get('user_id'); ?>"
					id="user_id" name="xtform[user_id]"
					class="required validate-numeric" required="required"
					readonly="readonly">
			</div>
		</div>

		<div class="control-group" style="display:none">
			<label class="required control-label" for="issued_at" id="issued_at-lbl"  rel="tooltip" data-original-title="<?php

			echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_ISSUEDAT_DESC');

			?>"><?php

			echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_ISSUEDAT_TITLE');

			?><span
				class="star">&nbsp;*</span> </label>
			<div class="controls">
				<input type="text" maxlength="255" size="64"
					value="<?php echo $this->item->xtform->get('issued_at'); ?>"
					id="issued_at" name="xtform[issued_at]"
					class="required" required="required"
					readonly="readonly">
			</div>
		</div>

		<div class="control-group">
			<label class=" required control-label" for="expires_at" id="expires_at-lbl" rel="tooltip" data-original-title="<?php

			echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_EXPIRESAT_DESC');

			?>"><?php

			echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_EXPIRESAT_TITLE');

			?><span
				class="star">&nbsp;*</span> </label>
			<div class="controls">
				<input type="text" maxlength="255" size="64"
					value="<?php echo $this->item->xtform->get('expires_at'); ?>"
					id="expires_at" name="xtform[expires_at]"
					class="required" required="required"
					readonly="readonly">
			</div>
		</div>

	</div>
