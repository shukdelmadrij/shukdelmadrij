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

/**
 * Helper functions for AutoTweet plugins.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class AutotweetBaseHelper
{
	const TAG_AUTHOR = '[author]';

	const TAG_AUTHOR_NAME = '[author-name]';

	const TAG_HASHTAGS = '[hashtags]';

	const TAG_MAINCAT = '[maincat]';

	const TAG_MAINCAT_LIT = '[maincat-lit]';

	const TAG_LASTCAT = '[lastcat]';

	const TAG_LASTCAT_LIT = '[lastcat-lit]';

	const TAG_ALLCATS = '[allcats]';

	const TAG_ALLCATS_LIT = '[allcats-lit]';

	/**
	 * AutotweetBaseHelper
	 *
	 */
	private function __construct()
	{
		// Static class
	}

	// Check type and range of textcount parameter, and correct if needed

	/**
	 * getTextcount
	 *
	 * @param   string  $textcount  Param
	 *
	 * @return	int
	 */
	public static function getTextcount($textcount)
	{
		return SharingHelper::MAX_CHARS_TITLE;
	}

	/**
	 * Use title or text as twitter message
	 *
	 * @param   bool    $usetext    Param
	 * @param   int     $textcount  Param
	 * @param   string  $title      Param
	 * @param   string  $text       Param
	 *
	 * @return	string
	 */
	public static function getMessagetext($usetext, $textcount, $title, $text)
	{
		$message = '';

		switch ($usetext)
		{
			// Show title only
			case 0:
				$message = $title;
				break;

			// Show text only
			case 1:
				if ('' != $text)
				{
					$message = $text;
				}
				else
				{
					$message = $title;
				}
				break;

			// Show title and text
			case 2:
				if ('' != $text)
				{
					$message = $title . ': ' . $text;
				}
				else
				{
					$message = $title;
				}
				break;
			default:
				$message = $title;
		}

		return JString::substr(TextUtil::cleanText($message), 0, $textcount);
	}

	/**
	 * Replaces spaces for hashtags
	 *
	 * @param   string  $word  Param
	 *
	 * @return	string
	 */
	public static function getAsHashtag($word)
	{
		if ('' != $word)
		{
			$word = TextUtil::cleanText($word);
			$word = str_ireplace(' ', '', $word);
			$word = str_ireplace('-', '', $word);
			$hash = '#' . $word;
		}
		else
		{
			$hash = '';
		}

		return $hash;
	}

	/**
	 * Returns hashtags from comma seperated string
	 *
	 * @param   string  $tags   Param
	 * @param   int     $count  Param
	 *
	 * @return	string
	 */
	public static function getHashtags($tags, $count = 1)
	{
		$hashtags = '';

		if (!empty($tags))
		{
			$i = 0;
			$words = TextUtil::listToArray($tags);

			foreach ($words as $word)
			{
				$hashtags = $hashtags . ' ' . self::getAsHashtag($word);

				$i++;

				if ($i > $count)
				{
					break;
				}
			}
		}

		return $hashtags;
	}

	/**
	 * Add static text / hashtags to message
	 *
	 * @param   int     $textpos     Param
	 * @param   string  $text        Param
	 * @param   string  $statictext  Param
	 *
	 * @return	string
	 */
	public static function addStatictext($textpos, $text, $statictext)
	{
		if (AutotweetPostHelper::STATICTEXT_BEGINNING == $textpos)
		{
			$textpos = 1;
		}
		elseif (AutotweetPostHelper::STATICTEXT_END == $textpos)
		{
			$textpos = 2;
		}

		switch ($textpos)
		{
			// Dont use static_text, use original text
			case 0:
				$result_text = $text;
				break;

			// Show static text at the beginning of message text
			case 1:
				$result_text = $statictext;

				if (!empty($text))
				{
					$result_text .= ' ' . $text;
				}
				break;

			// Show static text at the end of message text
			case 2:
				$result_text = $text;

				if (!empty($result_text))
				{
					$result_text .= ' ';
				}

				$result_text .= $statictext;
				break;
			default:
				$result_text = $text;
		}

		return $result_text;
	}

	/**
	 * Apply the text pattern in the data array
	 *
	 * @param   string  $pattern  Param
	 * @param   array   &$post    Param
	 *
	 * @return	void
	 */
	static public function applyTextPattern($pattern, &$post)
	{
		$author = $post->xtform->get('author');
		$pattern = str_replace(self::TAG_AUTHOR, $author, $pattern);

		if ((strpos($pattern, self::TAG_AUTHOR_NAME) !== false) && (!empty($author)))
		{
			$author_userId = JUserHelper::getUserId($author);
			$author_user = JFactory::getUser($author_userId);
			$pattern = str_replace(self::TAG_AUTHOR_NAME, $author_user->name, $pattern);
		}

		$pattern = self::_applyTextPattern($pattern, 'introtext', $post->introtext);
		$pattern = self::_applyTextPattern($pattern, 'fulltext', $post->fulltext);
		$pattern = self::_applyTextPattern($pattern, 'text', $post->text);
		$pattern = self::_applyTextPattern($pattern, 'message', $post->message);
		$pattern = self::_applyTextPattern($pattern, 'title', $post->title);

		if (strpos($pattern, self::TAG_HASHTAGS) !== false)
		{
			$hashtags = $post->xtform->get('hashtags');
			$post->xtform->set('hashtags', null);
			$pattern = str_replace(self::TAG_HASHTAGS, $hashtags, $pattern);
		}

		$cats = $post->cat_names;
		$count_cats = count($cats);

		if ($count_cats == 0)
		{
			$post->message = $pattern;

			return;
		}

		if (strpos($pattern, self::TAG_MAINCAT) !== false)
		{
			$maincat = self::hashtize($cats[0]);
			$pattern = str_replace(self::TAG_MAINCAT, $maincat, $pattern);
		}

		if (strpos($pattern, self::TAG_MAINCAT_LIT) !== false)
		{
			$maincat = $cats[0];
			$pattern = str_replace(self::TAG_MAINCAT_LIT, $maincat, $pattern);
		}

		if (strpos($pattern, self::TAG_LASTCAT) !== false)
		{
			$lastcat = self::hashtize($cats[$count_cats - 1]);
			$pattern = str_replace(self::TAG_LASTCAT, $lastcat, $pattern);
		}

		if (strpos($pattern, self::TAG_LASTCAT_LIT) !== false)
		{
			$lastcat = $cats[$count_cats - 1];
			$pattern = str_replace(self::TAG_LASTCAT_LIT, $lastcat, $pattern);
		}

		if (strpos($pattern, self::TAG_ALLCATS) !== false)
		{
			array_walk($cats, 'AutotweetBaseHelper::hashtize');
			$allcats = join(' ', $cats);
			$pattern = str_replace(self::TAG_ALLCATS, $allcats, $pattern);
		}

		if (strpos($pattern, self::TAG_ALLCATS_LIT) !== false)
		{
			$allcats = join(' ', $cats);
			$pattern = str_replace(self::TAG_ALLCATS_LIT, $allcats, $pattern);
		}

		$post->message = $pattern;
	}

	/**
	 * Apply the text pattern in the data array
	 *
	 * @param   string  $text     Param
	 * @param   array   $tag      Param
	 * @param   array   $subject  Param
	 *
	 * @return	void
	 */
	static private function _applyTextPattern($text, $tag, $subject)
	{
		$pattern = '/\[' . $tag . '\,?([0-9]+)?\]/i';

		if (preg_match($pattern, $text, $matches))
		{
			if (count($matches) > 1)
			{
				$limit = $matches[1];
				$subject = TextUtil::truncString($subject, $limit);
			}

			$text = preg_replace($pattern, $subject, $text);
		}

		return $text;
	}

	/**
	 * Hashtize
	 *
	 * @param   string  $text  Param
	 *
	 * @return	string
	 */
	public static function hashtize($text)
	{
		$text = ucwords($text);

		// Replaces every non-letter and non-digit
		$text = preg_replace('/(?=\P{Nd})\P{L}/u', '', $text);

		return '#' . $text;
	}

	/**
	 * Add category / section to message text
	 *
	 * @param   bool    $show      Param
	 * @param   int     $section   Param
	 * @param   int     $category  Param
	 * @param   string  $text      Param
	 * @param   bool    $add_hash  Param
	 *
	 * @return	array
	 */
	public static function addCatsec($show, $section, $category, $text, $add_hash = false)
	{
		$result_text = $text;
		$hashtags = '';

		if ($add_hash)
		{
			// Show as hashtags
			$section = self::getAsHashtag($section);
			$category = self::getAsHashtag($category);

			switch ($show)
			{
				// Do nothing, use original text
				case 0:
					break;

				// Show section only
				case 1:
					$hashtags = $section;
					break;

				// Show section and category
				case 2:
					$hashtags = $section . ' ' . $category;
					break;

				// Show category only (new feature since 3.0 stable)
				case 3:
					$hashtags = $category;
					break;
			}
		}
		else
		{
			switch ($show)
			{
				// Show as pretext (part of message)
				// Do nothing, use original text
				case 0:
					break;

				// Show section only
				case 1:
					$result_text = $section . ': ' . $text;
					break;

				// Show section and category
				case 2:
					$result_text = $section . '/' . $category . ': ' . $text;
					break;

				// Show category only (new feature since 3.0 stable)
				case 3:
					$result_text = $category . ': ' . $text;
					break;
			}
		}

		$result = array(
						'text' => $result_text,
						'hashtags' => $hashtags
		);

		return $result;
	}

	/**
	 * Special implementation to ad multiple categories
	 *
	 * @param   bool    $show        Param
	 * @param   int     $categories  Param
	 * @param   string  $text        Param
	 * @param   bool    $add_hash    Param
	 *
	 * @return	array
	 */
	public static function addCategories($show, $categories, $text, $add_hash = false)
	{
		$result_text = $text;
		$hashtags = '';

		if (!empty($categories))
		{
			if ($add_hash)
			{
				switch ($show)
				{
					// Do nothing, use original text
					case 0:
						break;

					// Show first category only
					case 1:
						$hashtags = self::getAsHashtag($categories[0]);
						break;

					// Show all categories
					case 2:
						$hashtags = self::getHashtags(implode(',', $categories), count($categories));
						break;
				}
			}
			else
			{
				switch ($show)
				{
					// Do nothing, use original text
					case 0:
						break;

					// Show first category only
					case 1:
						$result_text = $categories[0] . ': ' . $text;
						break;

					// Show all categories
					case 2:
						$result_text = trim(implode('/', $categories)) . ': ' . $text;
						break;
				}
			}
		}

		$result = array(
						'text' => $result_text,
						'hashtags' => $hashtags
		);

		return $result;
	}

	/**
	 * Database helpers: returns the next free id for the table
	 *
	 * @param   string  $table  Param
	 *
	 * @return	int
	 */
	public static function getID($table)
	{
		$db = JFactory::getDBO();

		$prefix = $db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$query = 'SHOW TABLE STATUS LIKE ' . $db->Quote($table);
		$db->setQuery($query);
		$result = $db->loadAssoc();

		$next_key = (int) $result['Auto_increment'];

		return $next_key;
	}

	/**
	 * Better implementation to handle multiple menu entry for component (multiple itemids)
	 *
	 * @param   string  $comp_name  Param
	 * @param   array   $needles    Param
	 * @param   string  $q_view     Param
	 *
	 * @return	int
	 */
	public static function getItemid($comp_name, $needles, $q_view = 'view')
	{
		$component = JComponentHelper::getComponent($comp_name);

		$menus = JApplication::getMenu('site', array());
		$items = $menus->getItems('component_id', $component->id);

		$match = null;

		foreach ($needles as $needle => $id)
		{
			foreach ($items as $item)
			{
				if ((@$item->query[$q_view] == $needle) && (@$item->query['id'] == $id))
				{
					$match = $item;
					break;
				}
			}

			if (isset($match))
			{
				break;
			}
		}

		// Defaults if no item is found
		if (!isset($match))
		{
			// Get first public itemid
			foreach ($items as $item)
			{
				if (0 == (int) $item->access)
				{
					$match = $item;
					break;
				}
			}

			// Last chance: get first itemid (also when private)
			if (!isset($match) && !empty($items))
			{
				$match = $items[0];
			}
		}

		// Set id if item is found
		if (isset($match))
		{
			$match = $match->id;
		}

		return $match;
	}

	/**
	 * convertLocalUTCAgenda
	 *
	 * @param   array  &$agendas  Param
	 *
	 * @return	void
	 */
	public static function convertLocalUTCAgenda(&$agendas)
	{
		$result = array();

		if (($agendas) && (is_array($agendas)))
		{
			foreach ($agendas as $agenda)
			{
				$result[] = EParameter::convertLocalUTC($agenda);
			}
		}

		$agendas = $result;
	}

	/**
	 * convertUTCLocalAgenda
	 *
	 * @param   array  &$agendas  Param
	 *
	 * @return	void
	 */
	public static function convertUTCLocalAgenda(&$agendas)
	{
		$result = array();

		foreach ($agendas as $agenda)
		{
			$result[] = EParameter::convertUTCLocal($agenda);
		}

		$agendas = $result;
	}

	/**
	 * getControllerParams
	 *
	 * @return	array
	 */
	public static function getControllerParams()
	{
		list($isCli, $isAdmin) = F0FDispatcher::isCliAdmin();

		$input = new F0FInput;

		$option = $input->get('option');
		$controller = $input->get('controller');
		$task = $input->get('task');
		$view = $input->get('view');
		$layout = $input->get('layout');
		$id = $input->get('id', null, 'int');

		if (!$id)
		{
			$cid = $input->get('cid');

			if ((is_array($cid)) && (count($cid) == 1))
			{
				$id = $cid[0];
			}
			elseif ((is_numeric($cid)) && ($cid > 0))
			{
				$id = $cid;
			}
		}

		// EasyBlog
		if (!$id)
		{
			$id = $input->get('blogid', null, 'int');
		}

		// JoomShopping
		if (!$id)
		{
			$id = $input->get('product_id', null, 'int');
		}

		// Content - Front
		if (!$id)
		{
			$id = $input->get('a_id', null, 'int');
		}

		// SobiPro
		if (!$id)
		{
			$id = $input->get('sid', null, 'int');
		}

		// Zoo - Front
		if (!$id)
		{
			$id = $input->get('item_id', null, 'int');
		}

		// Joocial - Composer
		if (!$id)
		{
			$id = $input->get('ref_id', null, 'cmd');
		}

		return array($isAdmin, $option, $controller, $task, $view, $layout, $id);
	}

	/**
	 * getHash
	 *
	 * @return	string
	 */
	public static function getHash()
	{
		return MD5(MD5(MD5(JFactory::getDate()->toUnix() . rand()) . rand()) . rand());
	}
}
