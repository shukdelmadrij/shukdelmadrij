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

// Base class for extension plugins for AutoTweet

jimport('joomla.database.table');
jimport('joomla.error.error');
jimport('joomla.plugin.plugin');

if (!defined('AUTOTWEET_API'))
{
	include_once JPATH_ADMINISTRATOR . '/components/com_autotweet/api/autotweetapi.php';
}

include_once 'autotweetplugin.php';

/**
 * plgAutotweetBase
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class PlgAutotweetBase extends JPlugin implements IAutotweetPlugin
{
	protected $pluginParams = null;

	protected $autopublish = true;

	protected $show_url;

	protected $advanced_attrs = null;

	protected $saved_advanced_attrs = false;

	protected $post_featured_only = false;

	const POSTTHIS_DEFAULT = 1;
	const POSTTHIS_NO = 2;
	const POSTTHIS_YES = 3;

	protected $published_field = 'state';

	// At least 5 minutes to detect new content with the polling query
	const MIN_POLLING_TIME = 5;

	protected $extension_option;

	/**
	 * plgAutotweetBase
	 *
	 * @param   string  &$subject  Param
	 * @param   object  $params    Param
	 */
	public function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);

		// Load component language file for use with plugin
		$jlang = JFactory::getLanguage();
		$jlang->load('com_autotweet');

		// Since Joomla 1.6 params can be used directly without creating a JParameter object
		$this->pluginParams = $this->params;

		if ((int) $this->pluginParams->get('autopublish', 1))
		{
			$this->autopublish = true;
		}
		else
		{
			$this->autopublish = false;
		}

		$surl = (int) $this->pluginParams->get('show_url', 2);

		if (2 == $surl)
		{
			$this->show_url = AutotweetPostHelper::SHOWURL_END;
		}
		elseif (1 == $surl)
		{
			$this->show_url = AutotweetPostHelper::SHOWURL_BEGINNING;
		}
		else
		{
			$this->show_url = AutotweetPostHelper::SHOWURL_OFF;
		}

		$this->published_field = 'state';
	}

	/**
	 * Method is called right before content is saved into the database.
	 * Article object is passed by reference, so any changes will be saved!
	 * NOTE:  Returning false will abort the save with an error.
	 * You can set the error by calling $article->setError($message)
	 *
	 * @param   object  $context  The context of the content passed to the plugin.
	 * @param   object  $article  A JTableContent object
	 * @param   bool    $isNew    If the content is just about to be created
	 *
	 * @return	boolean
	 */
	public function onContentBeforeSave($context, $article, $isNew)
	{
		$this->retrieveAdvancedAttrs();

		return true;
	}

	/**
	 * onContentAfterSave
	 *
	 * @param   object  $context  The context of the content passed to the plugin.
	 * @param   object  $article  A JTableContent object
	 * @param   bool    $isNew    If the content is just about to be created
	 *
	 * @return	boolean
	 */
	public function onContentAfterSave($context, $article, $isNew)
	{
		if ((isset($article->id)) && ($article->id))
		{
			$this->saveAdvancedAttrs($article->id);
		}

		return true;
	}

	/**
	 * retrieveAdvancedAttrs
	 *
	 * @return	void
	 */
	public function retrieveAdvancedAttrs()
	{
		if (!AUTOTWEETNG_JOOCIAL)
		{
			return;
		}

		$input = new F0FInput;
		$autotweet_advanced = $input->get('autotweet_advanced_attrs', null, 'string');

		if ($autotweet_advanced)
		{
			$this->advanced_attrs = AdvancedattrsHelper::retrieveAdvancedAttrs($autotweet_advanced);

			if (isset($this->advanced_attrs->ref_id))
			{
				// Safe to save
				$this->saveAdvancedAttrs($this->advanced_attrs->ref_id);
			}
		}
	}

	/**
	 * saveAdvancedAttrs
	 *
	 * @param   int  $id  Param
	 *
	 * @return	void
	 */
	public function saveAdvancedAttrs($id)
	{
		if (!AUTOTWEETNG_JOOCIAL)
		{
			return;
		}

		if (($this->advanced_attrs) && (!$this->saved_advanced_attrs))
		{
			// Safe to save
			AdvancedattrsHelper::saveAdvancedAttrs($this->advanced_attrs, $id);
			$this->saved_advanced_attrs = true;
		}
	}

	/**
	 * Returns publish mode for plugin (default is true, so this works also for plugin without autopublish option).
	 *
	 * @return	bool	true, if autopublishing is enabled for plugin
	 */
	public function isAutopublish()
	{
		return $this->autopublish;
	}

	/**
	 * Returns url mode for plugin.
	 *
	 * @return	int	urlmode (0 =  no url, 1 = show at the beginning, 2 = show at the end of message)
	 */
	public function getShowUrlMode()
	{
		return $this->show_url;
	}

	/**
	 * Queues a message for posting over AutoTweet.
	 * typeinfo:	only needed when 2 different types of messages are returned (see Kunena plugin)
	 *
	 * @param   int     $id              Param
	 * @param   date    $publish_up      Param
	 * @param   string  $description     Param
	 * @param   int     $typeinfo        Param
	 * @param   string  $url             Param
	 * @param   string  $image_url       Param
	 * @param   object  &$native_object  Param
	 * @param   object  &$params         Param
	 *
	 * @return	boolean		true, if message is queued for posting
	 */
	protected function postStatusMessage($id, $publish_up, $description, $typeinfo = 0, $url = '', $image_url = '', &$native_object = null, &$params = null)
	{
		$plug_id = $this->_name;

		$result = AutotweetAPI::insertRequest(
				$id,
				$plug_id,
				$publish_up,
				$description,
				$typeinfo,
				$url,
				$image_url,
				$native_object,
				$this->advanced_attrs,
				$params
		);

		return $result;
	}

	/**
	 * check type and range of textcount parameter, and correct if needed
	 *
	 * @param   int  $textcount  Param.
	 *
	 * @return	int
	 */
	protected function getTextcount($textcount)
	{
		return AutotweetBaseHelper::getTextcount($textcount);
	}

	/**
	 * Use title or text as twitter message
	 *
	 * @param   bool    $usetext    Param.
	 * @param   int     $textcount  Param.
	 * @param   string  $title      Param.
	 * @param   string  $text       Param.
	 *
	 * @return	int
	 */
	protected function getMessagetext($usetext, $textcount, $title, $text)
	{
		return AutotweetBaseHelper::getMessagetext($usetext, $textcount, $title, $text);
	}

	/**
	 * Replaces spaces for hashtags
	 *
	 * @param   string  $word  Param.
	 *
	 * @return	string
	 */
	protected function getAsHashtag($word)
	{
		return AutotweetBaseHelper::getAsHashtag($word);
	}

	/**
	 * Returns hashtags from comma sperated string (metakey field)
	 *
	 * @param   string  $metakey  Param.
	 * @param   int     $count    Param.
	 *
	 * @return	array
	 */
	protected function getHashtags($metakey, $count = 1)
	{
		return AutotweetBaseHelper::getHashtags($metakey, $count);
	}

	/**
	 * Add static text / hashtags to message
	 *
	 * @param   int     $textpos     Param.
	 * @param   string  $text        Param.
	 * @param   string  $statictext  Param.
	 *
	 * @return	string
	 */
	protected function addStatictext($textpos, $text, $statictext)
	{
		return AutotweetBaseHelper::addStatictext($textpos, $text, $statictext);
	}

	/**
	 * Add category / section to message text
	 *
	 * @param   int     $show      Param
	 * @param   int     $section   Param
	 * @param   int     $category  Param
	 * @param   string  $text      Param
	 * @param   bool    $add_hash  Param
	 *
	 * @return	string
	 */
	protected function addCatsec($show, $section, $category, $text, $add_hash = false)
	{
		return AutotweetBaseHelper::addCatsec($show, $section, $category, $text, $add_hash);
	}

	/**
	 * Special implementation to ad multiple categories
	 *
	 * @param   int     $show        Param
	 * @param   array   $categories  Param
	 * @param   string  $text        Param
	 * @param   bool    $add_hash    Param
	 *
	 * @return	string
	 */
	protected function addCategories($show, $categories, $text, $add_hash = false)
	{
		return AutotweetBaseHelper::addCategories($show, $categories, $text, $add_hash);
	}

	/**
	 * Database helpers: returns the next free id for the table
	 *
	 * @param   object  $table  Param
	 *
	 * @return	string
	 */
	protected function getID($table)
	{
		return AutotweetBaseHelper::getID($table);
	}

	/**
	 * Better implementation to handle multiple menu entry for component (multiple itemids)
	 *
	 * @param   object  $comp_name  Param
	 * @param   object  $needles    Param
	 * @param   object  $q_view     Param
	 *
	 * @return	int
	 */
	protected function getItemid($comp_name, $needles, $q_view = 'view')
	{
		return AutotweetBaseHelper::getItemid($comp_name, $needles, $q_view);
	}

	/**
	 * getData
	 *
	 * @param   string  $id        Param.
	 * @param   string  $typeinfo  Param.
	 *
	 * @return	array
	 */
	public function getData($id, $typeinfo)
	{
		JError::raiseWarning('5', 'AutoTweet NG Plugin - getData not implemented by plugin.');
	}

	/**
	 * getImageFromText
	 *
	 * @param   string  $text  Param.
	 *
	 * @return	string
	 */
	protected function getImageFromText($text)
	{
		$image = '';

		if (class_exists('DOMDocument'))
		{
			$doc = new DomDocument;
			@$doc->loadHTML($text);
			$imgtags = $doc->getElementsByTagName('img');

			if (0 < $imgtags->length)
			{
				$imgtag = $imgtags->item(0);
				$image = $imgtag->getAttribute('src');
			}
		}
		else
		{
			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::WARNING, 'Class DOMDocument not found in autotweetcontent.php - text not parsed for image');
		}

		if (empty($image))
		{
			$image = TextUtil::getImageFromTextWithBrackets($text);
		}

		if (empty($image))
		{
			$image = TextUtil::getImageFromGalleryTag($text);
		}

		if (empty($image))
		{
			$image = TextUtil::getImageFromYoutubeWithBrackets($text);
		}

		return $image;
	}

	/**
	 * checkIncludedCategoryFilter.
	 *
	 * @param   array  $catids  Param.
	 *
	 * @return	void
	 */
	protected function checkIncludedCategoryFilter($catids)
	{
		if (($this->categories) && (!is_array($this->categories)))
		{
			$this->categories = TextUtil::listToArray($this->categories);
		}

		// Handling for multiple categories
		$isIncludeFilter = true;

		if (!empty($catids) && !empty($this->categories))
		{
			foreach ($catids as $catid)
			{
				if (in_array($catid, $this->categories))
				{
					$isIncludeFilter = false;
					break;
				}
			}
		}

		$checkIncludeFilter = (empty($this->categories) || empty($catids) || !$isIncludeFilter);

		return $checkIncludeFilter;
	}

	/**
	 * checkExcludedCategoryFilter.
	 *
	 * @param   array  $catids  Param.
	 *
	 * @return	void
	 */
	protected function checkExcludedCategoryFilter($catids)
	{
		if (($this->excluded_categories) && (!is_array($this->excluded_categories)))
		{
			$this->excluded_categories = TextUtil::listToArray($this->excluded_categories);
		}

		$isExcludeFilter = false;

		if (!empty($catids) && !empty($this->excluded_categories))
		{
			foreach ($catids as $catid)
			{
				if (in_array($catid, $this->excluded_categories))
				{
					$isExcludeFilter = true;
					break;
				}
			}
		}

		$checkExcludeFilter = (empty($this->excluded_categories) || !$isExcludeFilter);

		return $checkExcludeFilter;
	}

	/**
	 * checkAccessLevelFilter.
	 *
	 * @param   int  $accesslevel  Param.
	 *
	 * @return	void
	 */
	protected function checkAccessLevelFilter($accesslevel)
	{
		if ((is_array($this->accesslevels)) && (count($this->accesslevels) == 1) && ($this->accesslevels[0] == 0))
		{
			return true;
		}

		return (empty($this->accesslevels) || in_array($accesslevel, $this->accesslevels));
	}

	/**
	 * getAuthorUsername.
	 *
	 * @param   int  $uid  Param.
	 *
	 * @return	string
	 */
	protected function getAuthorUsername($uid)
	{
		return JFactory::getUser($uid)->username;
	}

	/**
	 * getArticleAuthor
	 *
	 * @param   string  &$article  Param
	 *
	 * @return	string
	 */
	protected function getArticleAuthor(&$article)
	{
		if ((isset($article->modified_by)) && ($article->modified_by > 0))
		{
			$uid = $article->modified_by;
		}
		else
		{
			$uid = $article->created_by;
		}

		return JFactory::getUser($uid)->username;
	}

	/**
	 * disablePostOld
	 *
	 * @param   string  $plugin  Param.
	 *
	 * @return	void
	 */
	protected function disablePostOld($plugin = 'autotweetcontent')
	{
		// Get plugin id
		$table = '#__extensions';

		$db = JFactory::getDBO();

		$query = 'SELECT ' . $db->quoteName('extension_id') . ' FROM ' . $db->quoteName($table) . ' WHERE ' . $db->quoteName('element') . ' = ' . $db->Quote($plugin) . ' AND ' . $db->quoteName('type') . ' = ' . $db->Quote('plugin');

		$db->setQuery($query);
		$id = (int) $db->loadResult();

		// Save parameter
		$this->pluginParams->set('post_old', 0);
		$table = JTable::getInstance('extension');
		$table->load($id);

		$table->params = $this->pluginParams->toString();

		if (!$table->store())
		{
			JError::raiseWarning(500, 'Can not save parameter for AutoTweet ' . $plugin . 'Plugin: ' . $table->getError());
		}
	}

	/**
	 * executeContentPolling
	 *
	 * @return	boolean
	 */
	protected function executeContentPolling()
	{
		$automators = F0FModel::getTmpInstance('Automators', 'AutoTweetModel');

		if ($automators->lastRunCheck('content', $this->interval))
		{
			$check_from = $this->getContentPollingFrom();

			// Set date for posts
			$post_old_mode = false;

			if ($this->post_old)
			{
				// Special case: posting for old articles is enabled
				$post_old_mode = true;
				$last_post = JFactory::getDate($this->post_old_date);

				// Disable old article posting
				$this->disablePostOld();
			}
			else
			{
				$last_post = $check_from;
			}

			// Get new and changed articles form db
			$table_content = '#__content';

			// Get articles only when they are not in the queue and not in the message log for time horizon
			$db = JFactory::getDBO();
			$query = $this->getPollingQuery('autotweetcontent', $table_content, $last_post);

			$db->setQuery($query);
			$articles = $db->loadObjectList();

			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::INFO, 'PollingQuery: ' . $table_content . ' found ' . count($articles) . ' tasks.');

			// Post articles
			foreach ($articles as $article)
			{
				if (AUTOTWEETNG_JOOCIAL)
				{
					$this->advanced_attrs = AdvancedattrsHelper::getAdvancedAttrs($this->extension_option, $article->id);
				}

				$this->postArticle($article);
			}
		}
	}

	/**
	 * getPollingQuery
	 *
	 * @param   string  $plugin         Param
	 * @param   string  $table_content  Param
	 * @param   JDate   $check_from     Param
	 *
	 * @return	string
	 */
	protected function getPollingQuery($plugin, $table_content, $check_from)
	{
		$check_until = $this->getContentPollingUntil();
		$table_posts = '#__autotweet_posts';
		$table_requests = '#__autotweet_requests';

		$db = JFactory::getDBO();

		$query = array();

		$query[] = 'SELECT c.* FROM ' . $db->quoteName($table_content, 'c');
		$query[] = 'LEFT OUTER JOIN ' . $db->quoteName($table_requests, 'r') . ' ON r.`plugin` = ' . $db->Quote($plugin) . ' AND r.`ref_id` = c.`id`';
		$query[] = 'LEFT OUTER JOIN ' . $db->quoteName($table_posts, 'p') . ' ON p.`plugin` = ' . $db->Quote($plugin) . ' AND p.`ref_id` = c.`id` WHERE';
		$query[] = 'r.`ref_id` IS NULL AND p.`ref_id` IS NULL AND';
		$query[] = 'c.' . $db->quoteName($this->published_field) . ' = 1 ';

		if ($this->post_featured_only)
		{
			$query [] = ' AND c.' . $db->quoteName('featured') . ' = 1';
		}

		if ($this->post_modified)
		{
			$query[] = 'AND ((c.' . $db->quoteName('created') . ' > ' . $db->Quote($check_from);
			$query[] = 'AND c.' . $db->quoteName('created') . ' < ' . $db->Quote($check_until);
			$query[] = ') OR (c.' . $db->quoteName('modified') . ' > ' . $db->Quote($check_from);
			$query[] = 'AND c.' . $db->quoteName('modified') . ' < ' . $db->Quote($check_until) . '))';
		}
		else
		{
			$query[] = 'AND (c.' . $db->quoteName('created') . ' > ' . $db->Quote($check_from);
			$query[] = 'AND c.' . $db->quoteName('created') . ' < ' . $db->Quote($check_until);
			$query[] = ')';

			$query[] = 'AND (c.' . $db->quoteName('modified') . ' = ' . $db->quote('0000-00-00 00:00:00');
			$query[] = 'OR c.' . $db->quoteName('modified') . ' IS NULL)';
		}

		if ((isset($this->categories)) && (is_array($this->categories)))
		{
			$categories = array_filter($this->categories);

			if (count($categories) > 0)
			{
				$query[] = 'AND c.' . $db->quoteName('catid') . ' IN (' . implode(',', $categories) . ')';
			}
		}

		if ((isset($this->excluded_categories)) && (is_array($this->excluded_categories)))
		{
			$categories = array_filter($this->excluded_categories);

			if (count($categories) > 0)
			{
				$query[] = 'AND c.' . $db->quoteName('catid') . ' NOT IN (' . implode(',', $categories) . ')';
			}
		}

		$query = implode(' ', $query);

		return $query;
	}

	/**
	 * getContentCategories
	 *
	 * @param   array  $article_cat  Param.
	 *
	 * @return	array
	 */
	public static function getContentCategories($article_cat)
	{
		$cat_ids = array();
		$cat_names = array();
		$cat_alias = array();

		$row = JTable::getInstance('category');

		// JomSocial Conflict Category ?
		if (!method_exists($row, 'load'))
		{
			if (EXTLY_J3)
			{
				include_once JPATH_SITE . '/libraries/legacy/table/category.php';
			}
			else
			{
				include_once JPATH_SITE . '/libraries/joomla/database/table/category.php';
			}

			$db = JFactory::getDbo();
			$row = new JTableCategory($db);
		}

		$row->load($article_cat);

		while ($row->parent_id > 0)
		{
			$cat_ids[] = $row->id;
			$cat_names[] = $row->title;
			$cat_alias[] = $row->alias;

			$row->load($row->parent_id);
		}

		return array(
						$cat_ids,
						$cat_names,
						$cat_alias
		);
	}

	/**
	 * getContentPollingUntil
	 *
	 * @return	JDate
	 */
	protected function getContentPollingUntil()
	{
		$check_until = JFactory::getDate()->toUnix() - self::MIN_POLLING_TIME * 60;
		$check_until = JFactory::getDate($check_until);

		return $check_until;
	}

	/**
	 * getContentPollingFrom
	 *
	 * @return	JDate
	 */
	protected function getContentPollingFrom()
	{
		$polling_window = EParameter::getComponentParam(CAUTOTWEETNG, 'polling_window_intval', 24);
		$check_from = JFactory::getDate()->toUnix() - ($polling_window * 3600);
		$check_from = JFactory::getDate($check_from);

		return $check_from;
	}
}
