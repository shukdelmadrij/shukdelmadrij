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
<h2><?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_LANGS'); ?></h2>
<?php

	echo EHtml::textControl(
			$this->item->xtform->get('encoding', ''),
			'xtform[encoding]',
			'COM_AUTOTWEET_VIEW_FEED_FEED_ENCODING',
			'COM_AUTOTWEET_VIEW_FEED_FEED_ENCODING_DESC');

	echo EHtml::textareaControl(
			$this->item->xtform->get('custom_translit', ''),
			'xtform[custom_translit]',
			'COM_AUTOTWEET_VIEW_FEED_CUSTOM_TRANSLIT',
			'COM_AUTOTWEET_VIEW_FEED_CUSTOM_TRANSLIT_DESC');
