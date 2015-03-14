<?php

/**
 * @package     Extly.Components
 * @subpackage  autotweetcontent - Plugin AutoTweetNG Content-Extension
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
 * PlgSystemAutotweetContent
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class PlgSystemAutotweetContent extends plgAutotweetBase
{
	// Typeinfo
	const TYPE_ARTICLE = 1;

	// Plugin params
	protected $categories = '';

	protected $excluded_categories = '';

	protected $post_modified = 0;

	protected $show_category = 0;

	protected $show_hash = 0;

	protected $tags_as_hash = 0;

	protected $use_text = 0;

	protected $use_text_count;

	protected $static_text = '';

	protected $static_text_pos = 1;

	protected $static_text_source = 0;

	protected $metakey_count = 1;

	protected $accesslevels = '';

	protected $interval = 60;

	protected $post_old = 0;

	protected $post_old_date = '0000-00-00';

	protected $polling = 1;

	// -1 means: nothing special to do
	private $_post_modified_as_new = -1;

	/**
	 * plgSystemAutotweetContent
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
		$this->show_category = (int) $pluginParams->get('show_category', 0);
		$this->show_hash = (int) $pluginParams->get('show_hash', 0);
		$this->tags_as_hash = (int) $pluginParams->get('tags_as_hash', 0);
		$this->use_text = (int) $pluginParams->get('use_text', 0);
		$this->use_text_count = $pluginParams->get('use_text_count', 100);
		$this->static_text = strip_tags($pluginParams->get('static_text', ''));
		$this->static_text_pos = (int) $pluginParams->get('static_text_pos', 1);
		$this->static_text_source = (int) $pluginParams->get('static_text_source', 0);
		$this->metakey_count = (int) $pluginParams->get('metakey_count', 1);
		$this->accesslevels = $pluginParams->get('accesslevels', '');
		$this->interval = (int) $pluginParams->get('interval', 180);
		$this->post_old = (int) $pluginParams->get('post_old', 0);
		$this->post_old_date = $pluginParams->get('post_old_date', '0000-00-00');
		$this->polling = (int) $pluginParams->get('polling', 1);
		$this->post_featured_only = (int) $pluginParams->get('post_featured_only', 0);

		// Correct value if value is under the minimum
		if ($this->interval < 180)
		{
			$this->interval = 180;
		}

		$this->extension_option = 'com_content';
	}

	/**
	 * Checks for new articles in the database (polling!!!)
	 *
	 * @return	boolean
	 */
	public function onContentPolling()
	{
		$cron_enabled = EParameter::getComponentParam(CAUTOTWEETNG, 'cron_enabled', false);

		if ( ($this->polling)
			&& ( (($cron_enabled) && (defined('AUTOTWEET_CRONJOB_RUNNING')))
			||   ((!$cron_enabled) && (JFactory::getApplication()->isSite()))) )
		{
			$this->executeContentPolling();
		}

		return true;
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
		// Autotweet Advanced Attrs
		parent::onContentBeforeSave($context, $article, $isNew);

		/*
		 * tricky things to enable posting for first publish of modified posts also when post modified is not enabled This means: When a post is published the first time it is always published as new now!
		 */
		if ((($context == 'com_content.article') || ($context == 'com_content.form'))
			&& (!$isNew)
			&& (!$this->post_modified)
			&& (1 == $article->state))
		{
			$old_article = JTable::getInstance('content');
			$old_article->load($article->id);

			// When article is modified and is not published, handle as new article
			if ($old_article->state = 0)
			{
				$this->_post_modified_as_new = $article->id;
			}
		}

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
		// Autotweet Advanced Attrs
		parent::onContentAfterSave($context, $article, $isNew);

		/*
		 * request from backend: - com_content.article - com_weblinks.weblink - com_contact.contact - com_banners.banner - com_newsfeeds.newsfeed requests form frontend: com_xxx.form content article
		 */
		if ((($context == 'com_content.article') || ($context == 'com_content.form'))
			&& (($isNew)
			|| ($this->post_modified)
			|| ($this->_post_modified_as_new == $article->id)
			|| (($this->advanced_attrs) && ($this->advanced_attrs->postthis == self::POSTTHIS_YES)))
			&& (($article->featured > 0) || (0 == $this->post_featured_only))
			&& (1 == $article->state))
		{
			$this->postArticle($article);
		}

		return true;
	}

	/**
	 * onContentAfterSave
	 *
	 * @param   object  $context  The context of the content passed to the plugin.
	 * @param   array   $pks      A list of primary key ids of the content that has changed state.
	 * @param   int     $value    The value of the state that the content has been changed to.
	 *
	 * @return	boolean
	 */
	public function onContentChangeState($context, $pks, $value)
	{
		// Content article
		if ((($context == 'com_content.article') || ($context == 'com_content.form')) && ($value == 1))
		{
			$article = JTable::getInstance('content');

			foreach ($pks as $id)
			{
				$article->load($id);
				$this->postArticle($article);
			}
		}

		return true;
	}

	/**
	 * postArticle
	 *
	 * @param   object  $article  The item object.
	 *
	 * @return	boolean
	 */
	protected function postArticle($article)
	{
		$cats = $this->getContentCategories($article->catid);
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

		if ((empty($this->categories) || ('' == $this->categories[0]) || empty($catids) || !$isIncludeFilter) && (empty($this->excluded_categories) || ('' == $this->excluded_categories[0]) || !$isExcludeFilter) && (empty($this->accesslevels) || ('' == $this->accesslevels[0]) || in_array($article->access, $this->accesslevels)))
		{
			$cat_alias = $cats[2];

			// Use main category for article url
			$cat_slug = $catids[0] . ':' . TextUtil::convertUrlSafe($cat_alias[0]);
			$id_slug = $article->id . ':' . TextUtil::convertUrlSafe($article->alias);

			// Create internal url for Joomla core content article
			JLoader::import('components.com_content.helpers.route', JPATH_ROOT);
			$url = ContentHelperRoute::getArticleRoute($id_slug, $cat_slug);

			// Get the first image from the text
			$fulltext = $article->introtext . ' ' . $article->fulltext;

			$images = null;

			if (isset($article->images))
			{
				$images = json_decode($article->images);
			}

			if (($images) && (isset($images->image_intro)) && (!empty($images->image_intro)))
			{
				$image_url = $images->image_intro;
			}
			elseif (($images) && (isset($images->image_fulltext)) && (!empty($images->image_fulltext)))
			{
				$image_url = $images->image_fulltext;
			}
			else
			{
				$image_url = $this->getImageFromText($fulltext);
			}

			$native_object = json_encode($article);
			$this->postStatusMessage($article->id, $article->publish_up, $article->title, self::TYPE_ARTICLE, $url, $image_url, $native_object);
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
		$article = json_decode($native_object);

		// Get category path for article
		$cats = $this->getContentCategories($article->catid);
		$catids = $cats[0];
		$cat_names = $cats[1];

		// Needed for url only
		$cat_alias = $cats[2];

		// Use article title or text as message
		$title = $article->title;
		$article_text = $article->introtext . ' ' . $article->fulltext;
		$text = $this->getMessagetext($this->use_text, $this->use_text_count, $article->title, $article_text);
		$hashtags = '';

		// Use metakey or static text or nothing
		if ((2 == $this->static_text_source) || ((1 == $this->static_text_source) && (empty($article->metakey))))
		{
			$title = $this->addStatictext($this->static_text_pos, $title, $this->static_text);
			$text = $this->addStatictext($this->static_text_pos, $text, $this->static_text);
		}
		elseif (1 == $this->static_text_source)
		{
			$hashtags = $hashtags . $this->getHashtags($article->metakey, $this->metakey_count);
		}

		// Title
		$categories_result = $this->addCategories($this->show_category, $cat_names, $title, 0);
		$title = $categories_result['text'];

		// Text
		$categories_result = $this->addCategories($this->show_category, $cat_names, $text, $this->show_hash);
		$text = $categories_result['text'];

		if ('' != $categories_result['hashtags'])
		{
			$hashtags = $hashtags . ' ';
			$hashtags = $hashtags . $categories_result['hashtags'];
		}

		if (($this->tags_as_hash) && (EXTLY_J3))
		{
			$tags = $this->getHashtagsFromTags($id);

			if ($tags)
			{
				$hashtags = $hashtags . ' ';
				$hashtags = $hashtags . $tags;
			}
		}

		$data = array(
						'title' => $title,
						'text' => $text,
						'hashtags' => $hashtags,

						// Already done when msg is inserted in queue
						// 'url' => '',

						// Already done when msg is inserted in queue
						// 'image_url' => '',

						'fulltext' => $article_text,
						'catids' => $catids,
						'cat_names' => $cat_names,
						'author' => $this->getArticleAuthor($article),
						'language' => $article->language,
						'access' => $article->access,

						'is_valid' => true
		);

		$target_id = null;

		if (isset($article->metadata))
		{
			$metadata = json_decode($article->metadata);

			if (isset($metadata->xreference))
			{
				$xreference = json_decode($metadata->xreference);

				if (isset($xreference->target_id))
				{
					$target_id = $xreference->target_id;
					$data['target_id'] = $target_id;
				}
			}
		}

		return $data;
	}

	/**
	 * getHashtagsFromTags
	 *
	 * @param   int  $id  Param.
	 *
	 * @return	string
	 */
	protected function getHashtagsFromTags($id)
	{
		if (EXTLY_J25)
		{
			return null;
		}

		jimport('cms.helper.tags');
		$jtags = new JHelperTags;
		$tags = $jtags->getItemTags('com_content.article', $id);

		if (count($tags) > 0)
		{
			$titles = array_map(
					function ($v)
					{
						return $v->title;
					},
					$tags
			);
			$c = count($titles);
			$tags = implode(',', $titles);

			return $this->getHashtags($tags, $c);
		}

		return null;
	}

	/**
	 * onAfterRender
	 *
	 * @return	void
	 */
	public function onAfterRender()
	{
		$app = JFactory::getApplication();

		// Get the response body .... an additional check for J! 3.0.0
		if ((EXTLY_J3) && (method_exists($app, 'getBody')))
		{
			$body = $app->getBody();
		}
		else
		{
			$body = JResponse::getBody();
		}

		if (class_exists('Extly'))
		{
			Extly::insertDependencyManager($body);
		}

		if ((EXTLY_J3) && (method_exists($app, 'setBody')))
		{
			$app->setBody($body);
		}
		else
		{
			JResponse::setBody($body);
		}

		$this->onContentPolling();
	}
}
