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
<h2><?php

	echo JText::_('COM_AUTOTWEET_VIEW_FEED_PREVIEW_TITLE');

?></h2>

<div class="well">
<?php

	echo EHtml::genericControl(
			'JGLOBAL_TITLE',
			'JFIELD_TITLE_DESC',
			'preview_title',
			$preview->title
	);

	echo EHtml::genericControl(
			'JFIELD_ALIAS_LABEL',
			'JFIELD_ALIAS_DESC',
			'preview_alias',
			$preview->alias
	);

	$control = SelectControlHelper::feedCategories(
				$this->item->xtform->get('contenttype_id', 'feedcontent'),
				$preview->cat_id,
				'preview_cat_id'
			);
	echo EHtml::genericControl(
			'JCATEGORY',
			'JFIELD_CATEGORY_DESC',
			'preview_cat_id',
			$control
	);

	// Main Text
	echo FeedTextHelper::joinArticleText($preview->introtext, $preview->fulltext);

	echo EHtml::accessLevelControl(
			$preview->access,
			'preview_access',
			'JFIELD_ACCESS_LABEL',
			'JFIELD_ACCESS_DESC');

	echo EHtmlSelect::yesNoControl(
			$preview->featured,
			'preview_featured',
			'JFEATURED',
			'COM_CONTENT_FIELD_FEATURED_DESC');


	$control = SelectControlHelper::languages($preview->language);
	echo EHtml::genericControl(
		'JFIELD_LANGUAGE_LABEL',
		'COM_CONTENT_FIELD_LANGUAGE_DESC',
		'preview_language',
		$control
	);

	echo EHtml::readonlyTextControl($preview->metakey, 'preview_metakey', 'JFIELD_META_KEYWORDS_LABEL', 'JFIELD_META_KEYWORDS_DESC');

	echo EHtml::readonlyTextControl($preview->metadesc, 'preview_metadesc', 'JFIELD_META_DESCRIPTION_LABEL', 'JFIELD_META_DESCRIPTION_DESC');

	echo EHtmlSelect::yesNoControl(
			$preview->state,
			'preview_state',
			'JSTATUS',
			'JFIELD_PUBLISHED_DESC'
	);

	echo EHtml::readonlyTextControl(
		JHtml::_('date', $preview->created, JText::_('COM_AUTOTWEET_DATE_FORMAT')),
		'preview_created',
		'COM_CONTENT_FIELD_CREATED_LABEL',
		'COM_CONTENT_FIELD_CREATED_DESC'
	);

	echo EHtml::readonlyTextControl(
		JHtml::_('date', $preview->publish_up, JText::_('COM_AUTOTWEET_DATE_FORMAT')),
		'preview_publish_up',
		'COM_CONTENT_FIELD_PUBLISH_UP_LABEL',
		'COM_CONTENT_FIELD_PUBLISH_UP_DESC'
	);

	if (!empty($preview->publish_down))
	{
		echo EHtml::readonlyTextControl(
			JHtml::_('date', $preview->publish_down, JText::_('COM_AUTOTWEET_DATE_FORMAT')),
			'preview_publish_down',
			'COM_CONTENT_FIELD_PUBLISH_DOWN_LABEL',
			'COM_CONTENT_FIELD_PUBLISH_DOWN_DESC'
		);
	}

	echo EHtml::readonlyTextControl(
			FeedTextHelper::generateAuthor($preview->created_by, $preview->created_by_alias),
			'preview_created_by',
			'COM_CONTENT_FIELD_CREATED_BY_LABEL',
			'COM_CONTENT_FIELD_CREATED_BY_DESC'
	);

	echo EHtml::genericControl(
			'COM_AUTOTWEET_VIEW_FEED_PERMALINK',
			'COM_AUTOTWEET_VIEW_FEED_PERMALINK',
			'preview_permalink',
			"<a href='{$preview->permalink}' target='_blank'>"
					. $preview->permalink . ' <i class="xticon xticon-globe"></i></a>'
	);

	echo EHtml::genericControl(
		'COM_AUTOTWEET_VIEW_FEED_SHORTLINK',
		'COM_AUTOTWEET_VIEW_FEED_SHORTLINK',
		'preview_shortlink',
		"<a href='{$preview->shortlink}' target='_blank'> "
					. $preview->shortlink . ' <i class="xticon xticon-globe"></i></a>'
	);

	$images = $preview->images;

?>
<h3><?php

	echo JText::_('COM_AUTOTWEET_VIEW_FEED_PREVIEW_IMAGES');

?></h3>
<?php

	echo '<p>';

	if (count($images) > 0)
	{
		foreach ($images as $image)
		{
			echo $image->generateTag() . ' ';
		}
	}

	echo '</p>';

?>

</div>
