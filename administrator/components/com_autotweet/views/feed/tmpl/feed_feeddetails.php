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
<div id="feeddetails" class="tab-pane fade">

	<div class="control-group">
		<label for="name" class="control-label required" rel="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_FEED_TITLE_DESC'); ?>"><?php
		echo JText::_('COM_AUTOTWEET_VIEW_FEED_FEED_TITLE'); ?> <span class="star">&#160;*</span> </label>
		<div class="controls">
			<input type="text" name="name" id="name" value="<?php
			echo $this->item->name; ?>" class="required" maxlength="64" required="required" />
		</div>
	</div>

<?php

	echo EHtmlSelect::publishedControl($this->item->get('published'), 'published');

?>

	<hr />

	<div class="control-group">
		<label for="url" class="control-label" rel="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_FEED_FEED_URL_DESC'); ?>"><?php
		echo JText::_('COM_AUTOTWEET_VIEW_FEED_FEED_URL'); ?> <span class="star">&#160;*</span> </label>
		<div class="controls">
			<input type="text" name="xtform[url]" id="url" value="<?php
			echo $this->item->xtform->get('url'); ?>" class="required" maxlength="512" required="required" />
		</div>
	</div>

	<div class="control-group">
		<label for="xtformcontenttype_id" class="control-label required" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_FEED_CONTENT_TYPE_DESC');
			?>"> <?php
			echo JText::_('COM_AUTOTWEET_VIEW_FEED_CONTENT_TYPE');
			?> <span class="star">&#160;*</span>
		</label>
		<div class="controls">
			<?php echo SelectControlHelper::contenttypes($this->item->xtform->get('contenttype_id', 'feedcontent'), 'xtform[contenttype_id]', array('class' => 'required')); ?>
		</div>
	</div>

	<div class="control-group">
		<label for="xtformcat_id" class="control-label required" rel="tooltip" data-original-title="<?php
			echo JText::_('COM_AUTOTWEET_VIEW_FEED_CATEGORY_TYPE_DESC');
			?>"> <?php
			echo JText::_('COM_AUTOTWEET_VIEW_FEED_CATEGORY_TYPE');
			?> <span class="star">&#160;*</span>
		</label>
		<div class="controls">
			<?php echo SelectControlHelper::feedCategories($this->item->xtform->get('contenttype_id', 'feedcontent'), $this->item->xtform->get('cat_id'), 'xtform[cat_id]', array('class' => 'required'), 'catid'); ?>
		</div>
	</div>

<?php

	echo EHtml::textControl(
			$this->item->xtform->get('import_limit', 3),
			'xtform[import_limit]',
			'COM_AUTOTWEET_VIEW_FEED_IMPORT_LIMIT',
			'COM_AUTOTWEET_VIEW_FEED_IMPORT_LIMIT_DESC');
?>

	<hr />

	<div class="control-group">
		<label for="feed_id" class="control-label" rel="tooltip" data-original-title="<?php echo JText::_('JGLOBAL_FIELD_ID_DESC'); ?>"><?php
		echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?> </label>
		<div class="controls">
			<input type="text" name="id" id="feed_id" value="<?php echo $this->item->id; ?>" class="uneditable-input" readonly="readonly">
		</div>
	</div>

</div>
