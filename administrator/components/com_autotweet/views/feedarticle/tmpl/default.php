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

$article = $this->get('article');
$params = $this->get('params');

$imagesCounter = count($article->images);

$authors = null;

if (($params->get('author_article')) && (!empty($article->created_by_alias)))
{
	$authors = '<p class="authors"><span class="label">' . JText::_('COM_AUTOTWEET_FEED_AUTHORS') . ':</span> <span class="author">' . $article->created_by_alias . '</span></p>';
}

if (($params->get('author_article') == 'top') && ($authors))
{
	echo $authors;
}

// Default image, or Enclosure Image
if (($article->showDefaultImage) || ($article->showEnclosureImage))
{
	$image = $article->images[0];
	echo $image->generateTag();
}

if ((($params->get('onlyintro')) || (empty($article->fulltext))) && (empty($article->introtext)))
{
	$article->introtext = '<p>' . $params->get('default_introtext') . '</p>';
}

echo FeedTextHelper::joinArticleText($article->introtext, $article->fulltext);

if (($params->get('process_enc')) && (count($article->enclosures)))
{
	$enclosures = $this->formatEnclosures();
	echo $enclosures;
}

if (($params->get('author_article') == 'bottom') && ($authors))
{
	echo $authors;
}

if ($params->get('show_orig_link'))
{
	// Trackback Processing
	$readonlink = $this->formatReadonLink();
	echo $readonlink;
}
