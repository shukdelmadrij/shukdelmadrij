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
	<div id="fbapp" class="tab-pane fade">

		<?php

		$options = array();

		$options[] = array('name' => 'COM_AUTOTWEET_VIEW_CHANNEL_USEOWNAPP_YES', 'value' => 2);
		$options[] = array('name' => 'COM_AUTOTWEET_VIEW_CHANNEL_USEOWNAPP_YESSSL', 'value' => 1);
		echo EHtmlSelect::btnGroupListControl(
				$useownapi,
				'xtform[use_own_api]',
				'COM_AUTOTWEET_VIEW_CHANNEL_USEOWNAPP_TITLE_LABEL',
				'COM_AUTOTWEET_VIEW_CHANNEL_USEOWNAPP_TITLE_DESC',
				$options,
				'use_own_api'
		);

		?>

		<div id="own-app-testing" <?php

			// No
			if ($useownapi != 0)
			{
				echo 'style="display: none;"';
			}

			?>>
			<div class="alert alert-info">
				<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_FBTESTINGAPP_DESC'); ?>
			</div>
		</div>

		<div id="own-app-details"
			<?php

			// No
			if ($useownapi == 0)
			{
				echo 'style="display: none;"';
			}

			?>>
			<p class="lead"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_APPDATA_TITLE'); ?></p>

			<div class="control-group">
				<label class="control-label <?php

				echo $required;

				?>"
					for="app_id" id="app_id-lbl"
					rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY_DESC');

					?>"><?php

					echo JText::_('COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIKEY_LABEL');

					?> <span class="star">&nbsp;*</span></label>
				<div class="controls">
					<input type="text" maxlength="255" size="50"
						value="<?php

						$app_id = $this->item->xtform->get('app_id');
						echo $app_id;

						?>"
						id="app_id" name="xtform[app_id]"
						class="<?php

						echo $required . $requiredId;

						?>"
						<?php

						echo $requiredTag;

						?>>
					<?php

					if ((is_numeric($app_id)) && ($app_id > 0))
					{
						echo JText::sprintf('COM_AUTOTWEET_VIEW_CHANNEL_APPDATA_EDIT', $app_id);
					}

					?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label <?php

				echo $required;

				?>"
					for="secret" id="secret-lbl" rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET_DESC');

					?>"><?php

					echo JText::_('COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APISECRET_LABEL');

					?> <span class="star">&nbsp;*</span></label>
				<div class="controls">
					<input type="password" maxlength="255" size="50"
						value="<?php

						echo $this->item->xtform->get('secret');

						?>"
						id="secret" name="xtform[secret]"
						class="<?php

						echo $required . $requiredToken;

						?>"
						<?php

						echo $requiredTag;

						?>>
				</div>
			</div>
			<div id="own-app-details-canvas-page" class="control-group" <?php

			// Yes (no Canvas Page)
			if (!$authorizeCanvas)
			{
				echo 'style="display: none;"';
			}

			?>>
				<label class="control-label" for="canvas_page" id="canvas_page-lbl"rel="tooltip" data-original-title="<?php

					echo JText::_('COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL_DESC');

					?>"><?php

					echo JText::_('COM_AUTOTWEET_CHANNEL_FACEBOOK_FIELD_APIAUTHURL_LABEL');

					?> <span class="star">&nbsp;*</span></label>
				<div class="controls">
					<input type="text" maxlength="255" size="50"
						value="<?php

						echo $this->item->xtform->get('canvas_page');

						?>"
						id="canvas_page" name="xtform[canvas_page]"
						class="<?php

						echo $requiredCanvasPage;

						?>">
				</div>
			</div>
		</div>

		<hr />

		<div class="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_APPDATA_DESC');
			?>
			<ul class="unstyled">
				<li><i class="xticon xticon-thumbs-up"></i> <a
				href="http://www.extly.com/how-to-autotweet-in-5-minutes-from-joomla.html"
				target="_blank"> Tutorial: How to AutoTweet from Joomla in 5 minutes</a></li>

				<li><i class="xticon xticon-thumbs-up"></i> <a
				href="http://www.extly.com/how-to-autotweet-from-your-own-facebook-app.html"
				target="_blank"> Tutorial: How to AutoTweet from Your Own Facebook
				App</a></li>
<?php
/*
				<li><i class="xticon xticon-thumbs-up"></i> <a
				href="http://www.extly.com/how-to-autotweet-from-your-own-facebook-heroku-app.html"
				target="_blank"> Tutorial: How to AutoTweet from Facebook-Heroku App</a></li>
*/
?>
			</ul>
		</div>

	</div>
