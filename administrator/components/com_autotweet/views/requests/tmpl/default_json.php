<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

F0FModel::getTmpInstance('Plugins', 'AutoTweetModel');

$result = array();

if ($count = count($this->items))
{
	foreach ($this->items as $item)
	{
		$native_object = json_decode($item->native_object);
		$has_error = ((isset($native_object->error)) && ($native_object->error));

		// $link = JRoute::_('index.php?option=com_autotweet&view=request&task=edit&id=' . (int) $item->id);
		// $description = htmlentities($item->description, ENT_COMPAT, 'UTF-8');

		$description = TextUtil::truncString($item->description, SharingHelper::MAX_CHARS_TITLE_SCREEN);

		$elem = array(
			'id' => $item->id,
			'title' => $description,
			'start' => JHtml::_('date', $item->publish_up, DateTime::RFC3339),
			// 'url' => $link,
			'className' => ($item->published ?
					($has_error ?  'req-error' : 'req-success') :
					($has_error ? 'req-warning' : 'req-info')),

			'checked_out' => ($item->checked_out != 0),

			// JHtml::_('date', $item->publish_up, "j M h:i") - 4 Oct 07:51
			'publish_up' => JHtml::_('date', $item->publish_up, DateTime::RFC3339),
			'description' => $description,
			'plugin' => AutoTweetModelPlugins::getSimpleName($item->plugin),
			'published' => $item->published
		);

		if (!empty($item->url))
		{
			$elem['url'] = TextUtil::renderUrl($item->url);
		}

		if (!empty($item->image_url))
		{
			$elem['image_url'] = TextUtil::renderUrl($item->image_url);
		}

		$result[] = $elem;
	}
}

echo EJSON_START . json_encode($result) . EJSON_END;
