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
<h2><?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_TAB_ENCS'); ?></h2>
<?php

	echo '<div class="process_enclosures">';
	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('process_enc', 0),
			'xtform[process_enc]',
			'COM_AUTOTWEET_VIEW_FEED_PROCESS_ENC',
			'COM_AUTOTWEET_VIEW_FEED_PROCESS_ENC_DESC',
			'xtformsave_processenc');
	echo '</div>';

	// Enclosure Options - BEGIN

	echo '<div class="group-processenc well">';
	/*
	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('force_enc_image', 0),
			'xtform[force_enc_image]',
			'COM_AUTOTWEET_VIEW_FEED_FORCE_ENC_IMAGE',
			'COM_AUTOTWEET_VIEW_FEED_FORCE_ENC_IMAGE_DESC');
	*/
	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('process_enc_images', 0),
			'xtform[process_enc_images]',
			'COM_AUTOTWEET_VIEW_FEED_PROCESS_ENC_IMGS',
			'COM_AUTOTWEET_VIEW_FEED_PROCESS_ENC_IMGS_DESC');
	echo '</div>';

	// Enclosure Options - END

	/*
	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('save_enc', 0),
			'xtform[save_enc]',
			'COM_AUTOTWEET_VIEW_FEED_SAVE_ENC',
			'COM_AUTOTWEET_VIEW_FEED_SAVE_ENC_DESC');

	echo EHtmlSelect::yesNoControl(
			$this->item->xtform->get('save_enc_image_as_img', 0),
			'xtform[save_enc_image_as_img]',
			'COM_AUTOTWEET_VIEW_FEED_SAVE_ENC_IMAGE_AS_IMG',
			'COM_AUTOTWEET_VIEW_FEED_SAVE_ENC_IMAGE_AS_IMG_DESC');

	echo EHtml::textControl(
			$this->item->xtform->get('media_folder', 'media/autotweetng/enclosures'),
			'xtform[media_folder]',
			'COM_AUTOTWEET_VIEW_FEED_MEDIA_FOLDER',
			'COM_AUTOTWEET_VIEW_FEED_MEDIA_FOLDER_DESC');
		 */
?><?php
