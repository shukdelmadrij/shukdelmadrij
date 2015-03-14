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
<h2>
<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_TXT_FLTRS'); ?>
</h2>
<?php

	echo '<div class="text_filter">';
	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('text_filter', 0),
			'xtform[text_filter]',
			'COM_AUTOTWEET_VIEW_FEED_TEXT_FILTERING',
			'COM_AUTOTWEET_VIEW_FEED_TEXT_FILTERING_DESC',
			'xtformsave_text_filter');
	echo '</div>';

	// Text Filter Options - BEGIN
	echo '<div class="group-text_filter well">';
	echo EHtml::textControl(
			$this->item->xtform->get('text_filter_remove'),
			'xtform[text_filter_remove]',
			'COM_AUTOTWEET_VIEW_FEED_TEXT_FLTR_RMV',
			'COM_AUTOTWEET_VIEW_FEED_TEXT_FLTR_RMV_DESC');

	echo EHtml::textareaControl(
			$this->item->xtform->get('text_filter_replace'),
			'xtform[text_filter_replace]',
			'COM_AUTOTWEET_VIEW_FEED_TEXT_FLTR_RPLC',
			'COM_AUTOTWEET_VIEW_FEED_TEXT_FLTR_RPLC_DESC');

	echo EHtml::textareaControl(
			$this->item->xtform->get('text_filter_regex'),
			'xtform[text_filter_regex]',
			'COM_AUTOTWEET_VIEW_FEED_TEXT_FLTR_RGX',
			'COM_AUTOTWEET_VIEW_FEED_TEXT_FLTR_RGX_DESC');
	echo '</div>';

	// Text Filter Options - END
