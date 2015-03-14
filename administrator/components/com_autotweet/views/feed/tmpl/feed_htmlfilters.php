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
<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_HTML_FLTRS'); ?>
</h2>
<?php

	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('xhtml_clean', 1),
			'xtform[xhtml_clean]',
			'COM_AUTOTWEET_VIEW_FEED_FORCE_XHTML',
			'COM_AUTOTWEET_VIEW_FEED_FORCE_XHTML_DESC');

	echo EHtmlSelect::booleanListControl(
			$this->item->xtform->get('strip_html_tags', 0),
			'xtform[strip_html_tags]',
			'COM_AUTOTWEET_VIEW_FEED_STRIP_HTML',
			'COM_AUTOTWEET_VIEW_FEED_STRIP_HTML_DESC2',
			'Strip All Tags',
			'Strip Custom Tag List');

	echo EHtml::textareaControl(
			$this->item->xtform->get('strip_list', 'img src=http://feeds.feedburner.com'),
			'xtform[strip_list]',
			'COM_AUTOTWEET_VIEW_FEED_STRIP_LIST',
			'COM_AUTOTWEET_VIEW_FEED_STRIP_LIST_DESC');

	echo EHtml::textareaControl(
			$this->item->xtform->get('remove_by_attrib', ''),
			'xtform[remove_by_attrib]',
			'COM_AUTOTWEET_VIEW_FEED_RMV_BY_ATTRIB',
			'COM_AUTOTWEET_VIEW_FEED_RMV_BY_ATTRIB_DESC');

	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('disallow_attribs', 0),
			'xtform[disallow_attribs]',
			'COM_AUTOTWEET_VIEW_FEED_DISALLOW_ATTRIBS',
			'COM_AUTOTWEET_VIEW_FEED_DISALLOW_ATTRIBS_DESC');

	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('remove_dups_emp', 1),
			'xtform[remove_dups_emp]',
			'COM_AUTOTWEET_VIEW_FEED_REMOVE_DUPS_EMP',
			'COM_AUTOTWEET_VIEW_FEED_REMOVE_DUPS_EMP_DESC');

	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('remove_bad', 1),
			'xtform[remove_bad]',
			'COM_AUTOTWEET_VIEW_FEED_REMOVE_BAD',
			'COM_AUTOTWEET_VIEW_FEED_REMOVE_BAD_DESC');

/*
	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('remove_ms', 1),
			'xtform[remove_ms]',
			'COM_AUTOTWEET_VIEW_FEED_REMOVE_MS',
			'COM_AUTOTWEET_VIEW_FEED_REMOVE_MS_DESC');
*/

	$options = array();
	$options[] = array('name' => 'None', 'value' => 0);
	$options[] = array('name' => 'Compress', 'value' => -1);
	$options[] = array('name' => 'Beautify', 'value' => 1);
	echo EHtmlSelect::btnGroupListControl(
		$this->item->xtform->get('tidy', 1),
		'xtform[tidy]',
		'COM_AUTOTWEET_VIEW_FEED_TIDY',
		'COM_AUTOTWEET_VIEW_FEED_TIDY_DESC',
		$options
	);
