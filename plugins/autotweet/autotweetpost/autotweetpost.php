<?php

/**
 * @package     Extly.Components
 * @subpackage  autotweetpost - Plugin AutoTweetNG Post-Extension
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

// Check for component
if (!JComponentHelper::getComponent('com_autotweet', true)->enabled)
{
	JError::raiseWarning('5', 'AutoTweet NG Component is not installed or not enabled. - ' . __FILE__);

	return;
}

include_once JPATH_ROOT . '/administrator/components/com_autotweet/helpers/autotweetbase.php';

/**
 * PlgAutotweetAutotweetPost
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class PlgAutotweetAutotweetPost extends plgAutotweetBase
{
	// Typeinfo
	const TYPE_POST = 2;

	// Plugin params
	protected $categories = '';

	protected $excluded_categories = '';

	protected $post_modified = 0;

	protected $show_catsec = 0;

	protected $show_hash = 0;

	protected $use_text = 0;

	protected $use_text_count;

	protected $static_text = '';

	protected $static_text_pos = 1;

	protected $static_text_source = 0;

	protected $metakey_count = 1;

	protected $interval = 60;

	// -1 means: nothing special to do
	private $_post_modified_as_new = -1;

	/**
	 * plgContentAutotweetPost
	 *
	 * @param   string  &$subject  Param
	 * @param   object  $params    Param
	 */
	public function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);

		$pluginParams = $this->pluginParams;

		// Joomla article specific params
		$this->categories = $pluginParams->get('categories', '');
		$this->excluded_categories = $pluginParams->get('excluded_categories', '');
		$this->post_modified = (int) $pluginParams->get('post_modified', 0);
		$this->show_catsec = (int) $pluginParams->get('show_catsec', 0);
		$this->show_hash = (int) $pluginParams->get('show_hash', 0);
		$this->use_text = (int) $pluginParams->get('use_text', 0);
		$this->use_text_count = $pluginParams->get('use_text_count75', SharingHelper::MAX_CHARS_TITLE);
		$this->static_text = strip_tags($pluginParams->get('static_text', ''));
		$this->static_text_pos = (int) $pluginParams->get('static_text_pos', 1);
		$this->static_text_source = (int) $pluginParams->get('static_text_source', 0);
		$this->metakey_count = (int) $pluginParams->get('metakey_count', 1);
		$this->interval = (int) $pluginParams->get('interval', 60);

		// Correct value if value is under the minimum
		if ($this->interval < 60)
		{
			$this->interval = 60;
		}
	}

	/**
	 * postArticle
	 *
	 * @param   object  $article  The item object.
	 *
	 * @return	boolean
	 */
	public function postArticle($article)
	{
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'Manual Post', $article);

		$xtform = json_decode($article['params']);

		$cats = $this->getContentCategories($xtform->catid);
		$catids = $cats[0];

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

		if ((empty($this->categories) || ('' == $this->categories[0]) || empty($catids) || !$isIncludeFilter) && (empty($this->excluded_categories) || ('' == $this->excluded_categories[0]) || !$isExcludeFilter) && (empty($this->accesslevels) || ('' == $this->accesslevels[0]) || in_array($article['access'], $this->accesslevels)))
		{
			if ((AUTOTWEETNG_JOOCIAL) && ($article['autotweet_advanced_attrs']))
			{
				$this->advanced_attrs = AdvancedattrsHelper::retrieveAdvancedAttrs($article['autotweet_advanced_attrs']);

				if (isset($this->advanced_attrs->ref_id))
				{
					// Safe to save
					$this->saveAdvancedAttrs($this->advanced_attrs->ref_id);
					unset($article['autotweet_advanced_attrs']);
				}
			}

			$params = null;

			if (array_key_exists('params', $article))
			{
				$params = $article['params'];
			}

			// To avoid duplication
			unset($article['id']);
			$native_object = json_encode($article);

			if (empty($article['plugin']))
			{
				$article['plugin'] = 'autotweetpost';
			}

			$this->_name = $article['plugin'];

			return $this->postStatusMessage(
					$article['ref_id'],
					$article['publish_up'],
					$article['description'],
					self::TYPE_POST,
					$article['url'],
					$article['image_url'],
					$native_object,
					$params
			);
		}
	}

	/**
	 * getExtendedData
	 *
	 * @param   string  $id              Param.
	 * @param   string  $typeinfo        Param.
	 * @param   string  &$native_object  Param.
	 *
	 * @return	array
	 */
	public function getExtendedData($id, $typeinfo, &$native_object)
	{
		$articles = F0FModel::getTmpInstance('Requests', 'AutoTweetModel');
		$articles->set('ref_id', $id);
		$article = $articles->getFirstItem($id);
		$article->xtform = EForm::paramsToRegistry($article);

		// Get category path for article
		$cats = $this->getContentCategories($article->xtform->get('catid'));
		$catIds = $cats[0];
		$catNames = $cats[1];

		// Needed for url only
		$catAlias = $cats[2];

		// Use article title or text as message
		$title = $article->xtform->get('title');
		$articleText = $article->xtform->get('article_text');
		$text = $this->getMessagetext($this->use_text, $this->use_text_count, $title, $articleText);
		$hashtags = $article->xtform->get('hashtags');

		// Use metakey or static text or nothing
		if ((2 == $this->static_text_source) || ((1 == $this->static_text_source) && (empty($article->metakey))))
		{
			$title = $this->addStatictext($this->static_text_pos, $title, $this->static_text);
			$text = $this->addStatictext($this->static_text_pos, $text, $this->static_text);
		}
		elseif (1 == $this->static_text_source)
		{
			$hashtags .= $this->getHashtags($article->xtform->get('metakey'), $article->xtform->get('metakey_count'));
		}

		// Title
		$result = $this->addCategories($this->show_catsec, $catNames, $title, 0);
		$title = $result['text'];

		// Text
		$result = $this->addCategories($this->show_catsec, $catNames, $text, $this->show_hash);
		$text = $result['text'];

		if (!empty($result['hashtags']))
		{
			$hashtags .= ' ' . $result['hashtags'];
		}

		$data = array(
						'title' => $title,
						'text' => $text,
						'hashtags' => $hashtags,

						'fulltext' => $articleText,
						'catids' => $catIds,
						'cat_names' => $catNames,
						'author' => $this->getAuthorUsername($article->xtform->get('author')),
						'language' => $article->xtform->get('language'),
						'access' => $article->xtform->get('access'),
						'is_valid' => true
		);

		return $data;
	}
}
