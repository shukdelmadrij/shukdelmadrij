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
<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_PROCESSING_DUPS'); ?>
</h2>
<?php

/*

	echo EHtmlSelect::booleanListControl(
			$this->item->xtform->get('check_text', 1),
			'xtform[check_text]',
			'COM_AUTOTWEET_VIEW_FEED_CHECK_TEXT_TYPE',
			'COM_AUTOTWEET_VIEW_FEED_CHECK_TEXT_TYPE_DESC',
			'Intro Text',
			'Full Text');

	$options = array();
	$options[] = array('name' => 'Basic', 'value' => 0);
	$options[] = array('name' => 'Merge Text', 'value' => 1);
	$options[] = array('name' => 'Overwrite', 'value' => 2);
	echo EHtmlSelect::btnGroupListControl(
			$this->item->xtform->get('merging', 0),
			'xtform[merging]',
			'COM_AUTOTWEET_VIEW_FEED_MERGE_TYPE',
			'COM_AUTOTWEET_VIEW_FEED_MERGE_TYPE_DESC',
			$options
	);

*/

echo '<div class="duplicates">';
echo EHtmlSelect::booleanListControl(
		$this->item->xtform->get('check_existing', 1),
		'xtform[check_existing]',
		'COM_AUTOTWEET_VIEW_FEED_CHECK_EXIST',
		'COM_AUTOTWEET_VIEW_FEED_CHECK_EXIST_DESC',
		'JYES',
		'JNO',
		'xtformsave_duplicates');
echo '</div>';

// Duplicate Options - BEGIN
echo '<div class="group-duplicates well">';

$options = array();
$options[] = array('name' => 'Basic', 'value' => 1);
$options[] = array('name' => 'Thorough', 'value' => 0);

// $options[] = array('name' => 'Exhaustive', 'value' => 2);

echo EHtmlSelect::btnGroupListControl(
		$this->item->xtform->get('compare_existing', 0),
		'xtform[compare_existing]',
		'COM_AUTOTWEET_VIEW_FEED_COMPARE_EXIST',
		'COM_AUTOTWEET_VIEW_FEED_COMPARE_EXIST_DESC',
		$options
);
echo '</div>';

// Duplicate Options - END
