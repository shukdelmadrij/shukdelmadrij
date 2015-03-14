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

	if (!isset($social_target))
	{
		$social_target = 'social_url';
	}

?>
		<input type="hidden" value="<?php echo $this->item->xtform->get($social_target); ?>" id="<?php

			echo $social_target;

		?>" name="xtform[<?php

			echo $social_target;

		?>]">
		<div class="<?php

			echo $social_target;

		?>"><?php

			$social_url_value = $this->item->xtform->get($social_target);

			if (!empty($social_url_value))
			{
				echo AutotweetModelChanneltypes::formatUrl($this->item->channeltype_id, $social_url_value);
			}

		?></div>
