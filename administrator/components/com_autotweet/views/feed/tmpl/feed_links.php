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
<h3><?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_TRACKBACKS'); ?></h3>
<?php

	echo '<div class="trackback">';
	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('show_orig_link', 1),
			'xtform[show_orig_link]',
			'COM_AUTOTWEET_VIEW_FEED_SHOW_TRACKBACK',
			'COM_AUTOTWEET_VIEW_FEED_SHOW_TRACKBACK_DESC',
			'xtformsave_trackback');
	echo '</div>';

	// Trackback Options - BEGIN
	echo '<div class="group-trackback well">';
	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('shortlink', 0),
			'xtform[shortlink]',
			'COM_AUTOTWEET_VIEW_FEED_SHOW_TRACKBACK_SHORT',
			'COM_AUTOTWEET_VIEW_FEED_SHOW_TRACKBACK_SHORT_DESC');

	echo EHtml::textControl(
			$this->item->xtform->get('orig_link_text', 'Read more '),
			'xtform[orig_link_text]',
			'COM_AUTOTWEET_VIEW_FEED_TRACKBACK_TEXT',
			'COM_AUTOTWEET_VIEW_FEED_TRACKBACK_TEXT_DESC');

	$options = array();
	$options[] = array('name' => 'None', 'value' => 'none');
	$options[] = array('name' => '_blank', 'value' => '_blank');
	$options[] = array('name' => '_parent', 'value' => '_parent');
	$options[] = array('name' => '_self', 'value' => '_self');
	$options[] = array('name' => '_top', 'value' => '_top');
	$options[] = array('name' => 'Custom', 'value' => 'custom');
	echo EHtmlSelect::btnGroupListControl(
			$this->item->xtform->get('target_frame', '_blank'),
			'xtform[target_frame]',
			'COM_AUTOTWEET_VIEW_FEED_TARGET_FRAME',
			'COM_AUTOTWEET_VIEW_FEED_TARGET_FRAME_DESC',
			$options
	);

	echo EHtml::textControl(
			$this->item->xtform->get('custom_frame', ''),
			'xtform[custom_frame]',
			'COM_AUTOTWEET_VIEW_FEED_CUST_FRAME',
			'COM_AUTOTWEET_VIEW_FEED_CUST_FRAME_DESC');

	echo EHtml::textControl(
			$this->item->xtform->get('trackback_class', ''),
			'xtform[trackback_class]',
			'COM_AUTOTWEET_VIEW_FEED_TRACKBACK_CLASS',
			'COM_AUTOTWEET_VIEW_FEED_TRACKBACK_CLASS_DESC');

	echo EHtml::textControl(
			$this->item->xtform->get('trackback_rel', ''),
			'xtform[trackback_rel]',
			'COM_AUTOTWEET_VIEW_FEED_TRACKBACK_REL',
			'COM_AUTOTWEET_VIEW_FEED_TRACKBACK_REL_DESC');
	echo '</div>';

	// Trackback Options - END
