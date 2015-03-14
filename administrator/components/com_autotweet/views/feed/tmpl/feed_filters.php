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
<div id="filters" class="tab-pane fade">
<?php

	echo EHtmlSelect::yesNoControl(
		$this->item->xtform->get('ignore_empty_intro', 0),
		'xtform[ignore_empty_intro]',
		'COM_AUTOTWEET_VIEW_FEED_IGNORE_EMPTY',
		'COM_AUTOTWEET_VIEW_FEED_IGNORE_EMPTY_DESC');

	include_once 'feed_duplicates.php';
	include_once 'feed_importfilters.php';
	include_once 'feed_htmlfilters.php';
	include_once 'feed_textfilters.php';

?>
</div>
