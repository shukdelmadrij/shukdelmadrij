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
 * FeedProcessorHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class FeedProcessorHelper
{
	private static $_params = null;

	private $_search = null;

	private $_replace = null;

	private $_regex = null;

	private $_regplace = null;

	private $_clean_config = null;

	private $_clean_whitelistmode = null;

	private $_alltext = null;

	private $_rootUrl = null;

	private $_clean_feed_config = null;

	private $_spec = null;

	private $_shorturl_always = false;

	private static $_tags = null;

	private static $_hook_tag = null;

	private $_logger = null;

	/**
	 * FeedHelper
	 *
	 */
	public function __construct()
	{
		include_once 'feedcontent.php';
		include_once 'feeddupchecker.php';
		include_once 'feedimage.php';
		include_once 'feedtext.php';

		self::$_params = null;

		$this->_shorturl_always = EParameter::getComponentParam(CAUTOTWEETNG, 'shorturl_always', 1);

		$this->_logger = AutotweetLogger::getInstance();
	}

	/**
	 * getTags
	 *
	 * @return	array
	 */
	public static function getTags()
	{
		return self::$_tag;
	}

	/**
	 * getHookTag
	 *
	 * @return	string
	 */
	public static function getHookTag()
	{
		return self::$_hook_tag;
	}

	/**
	 * getParams
	 *
	 * @return	string
	 */
	public static function getParams()
	{
		return self::$_params;
	}

	/**
	 * setParams
	 *
	 * @param   object  &$params  Params
	 *
	 * @return	void
	 */
	public static function setParams(&$params)
	{
		self::$_params = $params;
	}

	/**
	 * getAllText
	 *
	 * @return	string
	 */
	public function getAllText()
	{
		return $this->_alltext;
	}

	/**
	 * setAllText
	 *
	 * @param   string  $alltext  Params
	 *
	 * @return	void
	 */
	public function setAllText($alltext)
	{
		$this->_alltext = $alltext;
	}

	/**
	 * getHash
	 *
	 * @param   object  $item  Params
	 *
	 * @return	void
	 */
	public static function getHash($item)
	{
		return md5($item->get_id());
	}

	/**
	 * process
	 *
	 * @param   object  &$feed        Params
	 * @param   array   &$loadResult  Params
	 *
	 * @return	void
	 */
	public function process(&$feed, &$loadResult)
	{
		$this->setParams($feed->xtform);
		$this->_rootUrl = RouteHelp::getInstance()->getRoot();

		$this->_textAdjustmentsInitialization();
		$this->_textCleaningInitialization();
		$this->_htmlCleaningInit();
		FeedTextHelper::hookTagCleaningInit();

		$articles = array();

		$feedTitle = $loadResult->title;
		$items = $loadResult->items;

		$this->_logger->log(
				JLog::INFO,
				'FeedProcessorHelper process: '
				. $feedTitle
		);

		$i = 0;

		foreach ($items as $item)
		{
			FeedTextHelper::hookTagCleaningItemInit();

			$article = new FeedContent;

			// Basic Initialization
			$article->cat_id = self::$_params->get('cat_id');
			$article->access = self::$_params->get('access');
			$article->featured = self::$_params->get('front_page');
			$article->language = self::$_params->get('language');

			// Hash
			$hash = $this->getHash($item);
			$article->hash = $hash;

			// Permalink
			$permalink = $item->get_permalink();
			$article->permalink = $permalink;

			// FeedItemBase
			preg_match('#^[a-zA-Z\d\-+.]+://[^/]+#', $permalink, $matches);
			$feedItemBase = $matches[0] . '/';
			$article->feedItemBase = $feedItemBase;

			$this->_clean_feed_config['base_url'] = $feedItemBase;

			// NamePrefix
			$namePrefix = $hash . '_';
			$article->namePrefix = $namePrefix;

			// Feed Text
			$theText = $this->_createFeedText($item);

			// Default Intro
			if (empty($theText))
			{
				$theText = self::$_params->get('introtext');
			}

			// Feed Title
			$title = $item->get_title();

			$this->_logger->log(
					JLog::INFO,
					'FeedProcessorHelper process: '
					. $title
					. ' - '
					. $permalink
			);

			// Get external fulltext
			if (self::$_params->get('fulltext'))
			{
				$readability_result = $this->_getFullText($permalink);

				if ($readability_result)
				{
					$theText = $readability_result->content;

					if (self::$_params->get('readability_title'))
					{
						$title = $readability_result->title;
					}
				}
			}

			// Text Cleaning
			$theText = $this->_htmlCleaning($theText);

			// Test for empty content
			if ((!self::$_params->get('ignore_empty_intro')) && (empty($theText)))
			{
				$this->_logger->log(
						JLog::INFO,
						"FeedProcessorHelper process: "
						. $article->title
						. ' - Empty intro!'
				);

				continue;
			}

			// Title & alias
			$article->title = $this->_createTitle($title, $feedTitle, $theText, $hash);
			$article->alias = $this->_createAlias($article->title);

			if ($this->_isDuplicated($article))
			{
				$this->_logger->log(
						JLog::INFO,
						"FeedProcessorHelper isDuplicated: "
						. $article->title
						. ' - Duplicated!'
				);
				continue;
			}

			// Black White Listing Control
			$article->blacklisted = false;
			$article->whitelisted = false;

			// Check item filtering
			if (self::$_params->get('filtering'))
			{
				$alltext = array();
				$alltext[] = $article->title;
				$alltext[] = $theText;
				$this->_alltext = strtolower(implode(' ', $alltext));

				if (self::$_params->get('filter_blacklist'))
				{
					$article->blacklisted = $this->_checkBlackListed();

					if ($article->blacklisted)
					{
						if (self::$_params->get('save_filter_result'))
						{
							$this->_logger->log(
									JLog::INFO,
									"FeedProcessorHelper process: "
									. $article->title
									. ' - Blacklisted!'
							);
						}

						continue;
					}
				}

				if (self::$_params->get('filter_whitelist'))
				{
					$article->whitelisted = $this->_checkWhiteListed();

					if (!$article->whitelisted)
					{
						if (self::$_params->get('save_filter_result'))
						{
							$this->_logger->log(
									JLog::INFO,
									"FeedProcessorHelper process: "
									. $article->title
									. ' - Not Whitelisted!'
							);
						}

						continue;
					}
				}
			}

			// Set Creator/Author
			$article->created_by = $this->_getCreatedBy();
			$author = $item->get_author();
			$article->created_by_alias = $this->_getCreatedByAlias($author, $article->created_by, $feedTitle);

			// Process Feed Images
			$article->images = $this->_processImages($theText);

			// Enclosures - (!self::$_params->get('create_art', 1
			if (self::$_params->get('process_enc'))
			{
				$enclosures = $item->get_enclosures();
				$enclosures = $this->_processEnclosures($enclosures);
				$article->enclosures = $enclosures;
			}

			$article->showEnclosureImage = (
					(self::$_params->get('process_enc_images'))
					&& (count($article->images) == 0)
					&& (count($article->enclosures))
			);

			if ($article->showEnclosureImage)
			{
				$article->images = $this->_setDefaultEnclosureImage($article->enclosures);

				// Ups, no image in enclosures
				if (empty($article->images))
				{
					$article->showEnclosureImage = false;
				}
			}

			$article->showDefaultImage = ((self::$_params->get('img')) && (count($article->images) == 0));

			// Set Default Image
			if ($article->showDefaultImage)
			{
				$article->images = $this->_setDefaultImage();
			}

			$article->introtext = $this->_trimText($theText, self::$_params->get('trim_to'), self::$_params->get('trim_type'));

			list($article->introtext, $article->fulltext) = $this->_onlyIntro($article->introtext, $theText);
			$article->introtext = $this->_dotDotDot($article->introtext);

			// Shortlink (or not)
			$article->shortlink = $article->permalink;

			if ((!empty($permalink)) && ($this->_shorturl_always) && (self::$_params->get('shortlink')))
			{
				$article->shortlink = ShorturlHelper::getInstance()->getShortUrl($article->permalink);
			}

			// Category
			if ($category = $item->get_category())
			{
				$article->metakey .= $category->get_label();
			}

			// Publication state and dates
			$this->_setPublicationState($article, $item->get_date());

			$articles[] = $article;
			$i++;

			// End Item Processing
		}

		return $articles;
	}

	/**
	 * _createFeedText
	 *
	 * @param   object  $item  Params
	 *
	 * @return	string
	 */
	private function _createFeedText($item)
	{
		// This will get full text if available in feed or return description if no full text
		$feedText = self::$_params->get('show_html') ? $item->get_content() : $item->get_description();
		$feedText = JString::trim($feedText);
		$feedText = $this->_adjustText($feedText);

		return $feedText;
	}

	/**
	 * _textAdjustmentsInitialization
	 *
	 * @return	void
	 */
	private function _textAdjustmentsInitialization()
	{
		$this->_search = array();
		$this->_replace = array();
		$this->_regex = array();
		$this->_regplace = array();

		// Clean out unwanted text
		if (self::$_params->get('text_filter'))
		{
			if (self::$_params->get('text_filter_remove'))
			{
				$this->_search = TextUtil::listToArray(self::$_params->get('text_filter_remove'));

				foreach ($this->_search as $s)
				{
					$s = str_replace('[[comma]]', ',', $s);
					$this->_replace[] = '';
				}
			}

			if (self::$_params->get('text_filter_replace'))
			{
				$pairs = explode("\n", self::$_params->get('text_filter_replace'));

				foreach ($pairs as $pair)
				{
					$pair = explode('===', $pair);
					$this->_search[] = trim($pair[0]);
					$this->_replace[] = trim($pair[1]);
				}
			}

			if (self::$_params->get('text_filter_regex'))
			{
				$pairs = explode("\n", self::$_params->get('text_filter_regex'));

				foreach ($pairs as $pair)
				{
					$pair = explode('===', $pair);
					$this->_regex[] = trim($pair[0]);
					$this->_regplace[] = trim($pair[1]);
				}
			}
		}
	}

	/**
	 * _textCleaningInitialization
	 *
	 * @return	void
	 */
	private function _textCleaningInitialization()
	{
		$this->_clean_config = array();
		$this->_clean_whitelistmode = false;

		$remove_by_attrib = self::$_params->get('remove_by_attrib');

		if (empty($remove_by_attrib))
		{
			return;
		}

		if (strpos($remove_by_attrib, '+') === 0)
		{
			$this->_clean_whitelistmode = true;
			$remove_by_attrib = str_replace('+', '', $remove_by_attrib);
		}

		$parts = TextUtil::listToArray($remove_by_attrib);

		if (count($parts) == 0)
		{
			return;
		}

		foreach ($parts as $part)
		{
			$p = explode('=', $part);

			if (count($p) != 2)
			{
				return;
			}

			list($key, $value) = $p;

			$p = explode(' ', $key);

			if (count($p) != 2)
			{
				return;
			}

			list($tag, $attrib) = $p;

			$tag = trim($tag);
			$attrib = trim($attrib);
			$value = trim($value);

			if (($tag) && ($attrib) && ($value))
			{
				$this->_clean_config[$tag][$attrib] = $value;
			}
		}
	}

	/**
	 * _htmlCleaningInit
	 *
	 * @return	void
	 */
	private function _htmlCleaningInit()
	{
		$this->_getTagsToStrip();

		$this->_spec = 'img=src,height,width;table=border,width,cellspacing,cellpadding;';

		$this->_clean_feed_config = array(
						'abs_url' => 1,
						'comment' => 1,
						'elements' => self::$_tags,
						'hook_tag' => 'hookTagCleaning',
						'tidy' => self::$_params->get('tidy'),
						'valid_xhtml' => (self::$_params->get('xhtml_clean') ? 1 : 0),
						'safe' => 1
		);

		if (self::$_params->get('link_nofollow'))
		{
			$this->_clean_feed_config["anti_link_spam"] = array(
							'`.`',
							''
			);
		}

		if (self::$_params->get('disallow_attribs'))
		{
			$this->_clean_feed_config['deny_attribute'] = '* -title -href -target -alt';
		}

		if (self::$_params->get('remove_bad'))
		{
			$this->_clean_feed_config['keep_bad'] = 6;
		}
	}

	/**
	 * _adjustText
	 *
	 * @param   string  $text  Params
	 *
	 * @return	string
	 */
	private function _adjustText($text)
	{
		// Clean out unwanted text
		if (self::$_params->get('text_filter'))
		{
			if (isset($this->_search))
			{
				// It may cause UTF-8 issues but needed to allow capitalisation to propagate
				$text = str_replace($this->_search, $this->_replace, $text);
			}

			if (isset($this->_regex))
			{
				$text = preg_replace($this->_regex, $this->_regplace, $text);
			}
		}

		return $text;
	}

	/**
	 * _createTitle
	 *
	 * @param   string  $title      Params
	 * @param   string  $feedTitle  Params
	 * @param   string  $feedText   Params
	 * @param   string  $hash       Params
	 *
	 * @return	string
	 */
	private function _createTitle($title, $feedTitle, $feedText, $hash)
	{
		$title = JString::trim($title);

		if (empty($title))
		{
			// See if feed text might have a likely candidate
			$regex = '#<(?:h1|h2|h3|h4|h5|h6)[^>]*>([\s\S]*?)<\/(?:h1|h2|h3|h4|h5|h6)>#i';
			preg_match($regex, $feedText, $matches);
			$title = $matches[1];

			if (empty($title))
			{
				$datenow = JFactory::getDate();
				$title = $feedTitle . ' - ' . $hash . ' - ' . $datenow->format(JText::_('COM_AUTOTWEET_DATE_FORMAT'));
			}
		}

		// Replace CR LF and Tabs
		$title = str_replace(array("\n", "\r", "\t"), ' ', $title);

		// Fix for long titles and htmlentities - Double encoding
		$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
		$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');

		// From JFilterOutput::cleanText
		$title = preg_replace("'<script[^>]*>.*?</script>'si", '', $title);
		$title = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '', $title);
		$title = preg_replace('/<!--.+?-->/', '', $title);
		$title = preg_replace('/{.+?}/', '', $title);

		// Clean Html Tags
		$title = strip_tags($title);

		// One space
		$title = preg_replace('#\s{2,}#', ' ', $title);

		// Text replacements and adjustments
		$title = $this->_adjustText($title);

		// No more than 255 chars - Joomla article
		$title = substr($title, 0, 255);

		return $title;
	}

	/**
	 * _createAlias
	 *
	 * @param   string  $title  Params
	 *
	 * @return	string
	 */
	private function _createAlias($title)
	{
		$alias = TextUtil::convertUrlSafe($title);

		$custom_translit = self::$_params->get('custom_translit');

		if (!empty($custom_translit))
		{
			$alias = FeedTextHelper::transliterate($alias, $custom_translit);
		}

		// Fix for trailing alias dashes
		$length = strlen($alias);

		if (strrpos($alias, '-') == $length - 1)
		{
			$alias = substr($alias, 0, $length - 1);
		}

		// Fix for long titles and htmlentities
		$alias = substr($alias, 0, 255);

		return $alias;
	}

	/**
	 * getFullText
	 *
	 * @param   string  $permalink  Params
	 *
	 * @return	string
	 */
	private function _getFullText($permalink)
	{
		// Get Source Full Text
		include_once JPATH_ADMINISTRATOR . '/components/com_autotweet/libs/readability/Readability.php';

		$result = false;

		try
		{
			$page = FeedTextHelper::getUrl($permalink, 'html');

			if (empty($page))
			{
				return false;
			}

			$parts = FeedTextHelper::extractHttp($page);

			if (!((array_key_exists('body', $parts)) && (array_key_exists('header', $parts))))
			{
				return false;
			}

			$body = FeedTextHelper::convertToUtf8($parts['body'], $parts['header']);

			if (empty($body))
			{
				// Failed to Get Source Full Text: body empty');
				return false;
			}

			if (function_exists('tidy_parse_string'))
			{
				$tidy = tidy_parse_string($body, array(), 'UTF8');
				$tidy->cleanRepair();
				$body = $tidy->value;
			}

			$readability = new phpreadability\Readability($body, $permalink);
			$readability->debug = false;
			$readability->convertLinksToFootnotes = self::$_params->get('link_table');

			if (!$readability->init())
			{
				return false;
			}

			$this->_cleanSpecifically($readability);
			$innerHTML = $readability->getContent()->innerHTML;

			if ($innerHTML == '<p>Sorry, Readability was unable to parse this page for content.</p>')
			{
				// Failed to Get Source Full Text: Readability unable to parse');
				return false;
			}

			if (function_exists('tidy_parse_string'))
			{
				$tidy = tidy_parse_string(
						$innerHTML, array(
								'indent' => true,
								'show-body-only' => true
						),
						'UTF8'
					);
				$tidy->cleanRepair();
				$innerHTML = $tidy->value;
			}

			// Got Source Full Text
			$result = new StdClass;
			$result->title = $readability->getTitle()->textContent;

			$text = $this->_adjustText($innerHTML);

			// No Ids or readability, classes pls
			$text = str_replace('id="readability-', 'class="joo-', $text);
			$text = str_replace('readability-', 'joo-', $text);
			$text = str_replace('<h3>References</h3>', '<h3>' . JText::_('COM_AUTOTWEET_VIEW_FEED_REFERENCES') . '</h3>', $text);

			$result->content = $text;
		}
		catch (Exception $e)
		{
			$error_message = $e->__toString();
			AutotweetLogger::getInstance()->log(JLog::ERROR, 'AutoTweetNG - ' . $error_message);
		}

		return $result;
	}

	/**
	 * _cleanSpecifically
	 *
	 * Apply filtering to a Readability node looking at all elements of type "tag" with attribute(s) set in params
	 *
	 * @param   object  $readability  Params
	 *
	 * @return	string
	 */
	private function _cleanSpecifically($readability)
	{
		$articleContent = $readability->articleContent;

		foreach ($this->_clean_config as $tag => $attribs)
		{
			$targetList = $articleContent->getElementsByTagName($tag);

			$n = $targetList->length - 1;

			for ($y = $n; $y >= 0; $y--)
			{
				foreach ($attribs as $k => $v)
				{
					$attr = $targetList->item($y)->getAttribute($k);

					if ( (($this->_clean_whitelistmode) && ($attr != $v))
						|| ((!$this->_clean_whitelistmode) && ($attr == $v)) )
					{
						$targetList->item($y)->parentNode->removeChild($targetList->item($y));

						// Current target removed, not further checking required
						break;
					}
				}
			}
		}
	}

	/**
	 * _processEnclosures
	 *
	 * @param   string  $enclosures  Params
	 *
	 * @return	array
	 */
	private function _processEnclosures($enclosures)
	{
		$links = array();
		$resulting_enclosures = array();

		foreach ($enclosures as $enclosure)
		{
			$link = $enclosure->get_link();

			if (($link) && (!isset($links[$link])))
			{
				$e = new StdClass;

				// Protects against duplicate enclosures
				$links[$link] = 1;

				$e->link = $link;
				$e->type = $enclosure->get_type();
				$e->real_type = $enclosure->get_real_type();
				$e->title = $enclosure->get_title();
				$e->caption = $enclosure->get_caption();
				$e->duration = $enclosure->get_duration();
				$e->size = $enclosure->get_size();
				$e->thumbnail = $enclosure->get_thumbnail();
				$e->extension = $enclosure->get_extension();

				$resulting_enclosures[] = $e;
			}
		}

		return $resulting_enclosures;
	}

	/**
	 * _checkBlackListed
	 *
	 * @return	boolean
	 */
	private function _checkBlackListed()
	{
		$blacklists = self::$_params->get('filter_blacklist');

		if (empty($blacklists))
		{
			return false;
		}

		$blacklists = TextUtil::listToArray(strtolower($blacklists));

		return $this->_checkList($blacklists);
	}

	/**
	 * _checkWhiteListed
	 *
	 * @return	boolean
	 */
	private function _checkWhiteListed()
	{
		$whitelists = self::$_params->get('filter_whitelist');

		if (empty($whitelists))
		{
			return false;
		}

		$whitelists = explode(',', strtolower($whitelists));

		return $this->_checkList($whitelists);
	}

	/**
	 * _checkListed
	 *
	 * @param   string  $list  Params
	 *
	 * @return	boolean
	 */
	private function _checkList($list)
	{
		if (count($list) == 0)
		{
			return false;
		}

		foreach ($list as $value)
		{
			if (JString::strpos($this->_alltext, $value) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * _getCreatedBy
	 *
	 * @return	string
	 */
	private function _getCreatedBy()
	{
		// Set Creator/Author
		$created_by = (int) self::$_params->get('default_author') ? (int) self::$_params->get('default_author') : JFactory::getUser()->get('id');

		if (empty($created_by))
		{
			// Get first admin user
			$db = JFactory::getDBO();
			$query = 'SELECT id FROM #__users WHERE sendEmail=1 ORDER by id LIMIT 1';
			$db->setQuery($query);
			$created_by = $db->loadResult();
		}

		return $created_by;
	}

	/**
	 * _getCreatedByAlias
	 *
	 * @param   object  $author      Params
	 * @param   int     $created_by  Params
	 * @param   string  $feedTitle   Params
	 *
	 * @return	string
	 */
	private function _getCreatedByAlias($author, $created_by, $feedTitle)
	{
		$created_by_alias = null;
		$name = null;

		if ($author)
		{
			$name = $author->get_name();
		}

		switch (self::$_params->get('save_author'))
		{
			// Use default alias
			case 1:
				$user = JFactory::getUser($created_by);

				if ($user)
				{
					$created_by_alias = $user->get('name');
				}
				break;

			// Use custom alias
			case 2:
				$created_by_alias = self::$_params->get('author_alias');
				break;

			// Use feed author alias, or title
			case 3:
				if ($author)
				{
					$created_by_alias = (!empty($name) ? $name : $feedTitle);
				}
				else
				{
					$created_by_alias = $feedTitle;
				}
				break;

			// Use feed author alias, or custom
			case 4:
				if ($author)
				{
					$created_by_alias = (!empty($name) ? $name : self::$_params->get('author_alias'));
				}
				else
				{
					$created_by_alias = self::$_params->get('author_alias');
				}
				break;
			default:
				// 0 - -Don't Save-
				break;
		}

		return $created_by_alias;
	}

	/**
	 * _processImages
	 *
	 * @param   string  $text  Params
	 *
	 * @return	array
	 */
	private function _processImages($text)
	{
		$replace = array();
		$regex = '/<img[^>]*>/';

		// String containing disallowed image sources to help prevent small images
		$disallowed = 'images.pheedo.com';

		$dom = new DomDocument;
		$result = @$dom->loadHTML($text);

		if (!$result)
		{
			return false;
		}

		$imgs = $dom->getElementsByTagName('img');

		$images = array();
		$loaded_images = array();

		foreach ($imgs as $img)
		{
			// No Source
			$src = $img->getAttribute('src');

			if ( (empty($src)) || (strpos($src, 'data:image') === 0) )
			{
				continue;
			}

			// Already loaded
			$loaded = (in_array($src, $loaded_images));

			if ($loaded)
			{
				continue;
			}

			// Local image
			$ourImage = (strpos($src, $this->_rootUrl) !== false);

			if ($ourImage)
			{
				continue;
			}

			// Not allowed
			$disallowedImage = (strpos($src, $disallowed) !== false);

			if ($disallowedImage)
			{
				continue;
			}

			$image = new FeedImage;
			$image->src = $src;
			$image->title = $img->getAttribute('title');
			$image->alt = $img->getAttribute('alt');

			$rmv_img_style = self::$_params->get('rmv_img_style');

			if (!$rmv_img_style)
			{
				$image->class = $img->getAttribute('class');
				$image->style = $img->getAttribute('style');
				$image->align = $img->getAttribute('align');
				$image->border = $img->getAttribute('border');
				$image->width = $img->getAttribute('width');
				$image->height = $img->getAttribute('height');
			}

			$img_class = self::$_params->get('img_class');

			if ($img_class)
			{
				$image->class = self::$_params->get('img_class');
			}

			$img_style = self::$_params->get('img_style');

			if ($img_style)
			{
				$image->style = self::$_params->get('img_style');
			}

			$images[] = $image;
			$loaded_images[] = $src;
		}

		return $images;
	}

	/**
	 * _htmlCleaning
	 *
	 * @param   string  $text  Params
	 *
	 * @return	string
	 */
	private function _htmlCleaning($text)
	{
		// Format br's as per HTML (not XHTML)
		$text = str_replace(
				array(
						'<br>',
						'<br/>'
				),
				'<br />',
				$text
		);

		if (self::$_params->get('remove_dups_emp'))
		{
			$pattern = '%<br />\s*<br />%';

			while (preg_match($pattern, $text))
			{
				$text = preg_replace($pattern, '<br />', $text);
			}
		}

		return $this->_trimText($text, self::$_params->get('max_length'), self::$_params->get('max_length_type'));
	}

	/**
	 * _onlyIntro
	 *
	 * @param   string  $introtext  Params
	 * @param   string  $fulltext   Params
	 *
	 * @return	array
	 */
	private function _onlyIntro($introtext, $fulltext)
	{
		$onlyintro = self::$_params->get('onlyintro');

		if ($onlyintro)
		{
			$fulltext = null;
		}

		return array($introtext, $fulltext);
	}

	/**
	 * _trimText
	 *
	 * @param   string  $text      Params
	 * @param   int     $trimTo    Params
	 * @param   string  $trimType  Params
	 *
	 * @return	void
	 */
	private function _trimText($text, $trimTo, $trimType)
	{
		$strip_html_tags = self::$_params->get('strip_html_tags');

		if ($trimTo)
		{
			$text = FeedTextHelper::trimText(
					$text,
					$trimTo,
					$trimType,

					// Keep Html Tags
					!$strip_html_tags
			);
		}

		// Keep Html Tags
		if (!$strip_html_tags)
		{
			$text = FeedTextHelper::cleanFeedText($text, $this->_clean_feed_config, $this->_spec);

			if (function_exists('tidy_parse_string'))
			{
				$tidy = tidy_parse_string(
						$text,
						array(
							'show-body-only' => true
						),
						'UTF8'
				);
				$tidy->cleanRepair();
				$text = $tidy->value;
			}
		}

		return $text;
	}

	/**
	 * _dotDotDot
	 *
	 * @param   string  $introtext  Params
	 *
	 * @return	string
	 */
	private function _dotDotDot($introtext)
	{
		$dotdotdot = self::$_params->get('dotdotdot');

		if ($dotdotdot)
		{
			if (strpos($introtext, '</p>') === false)
			{
				$introtext .= '...';
			}
			else
			{
				$introtext = FeedTextHelper::str_replace_last('</p>', '...</p>', $introtext);
			}
		}

		return $introtext;
	}

	/**
	 * _getTagsToStrip
	 *
	 * @return	array
	 */
	private function _getTagsToStrip()
	{
		$s = self::$_params->get('strip_list');
		$w = '';

		if (strpos($s, '+') === 0)
		{
			$s = FeedTextHelper::str_replace_first('+', '', $s);
			$w = '+';
		}

		$ts = TextUtil::listToArray($s);
		$ht = array();

		foreach ($ts as $k => $t)
		{
			if (JString::strpos($t, '='))
			{
				$ht[] = $t;
				unset($ts[$k]);
			}
		}

		list($tags, $hook_tag) = array(
						$w . implode(',', $ts),
						$w . implode(',', $ht)
		);

		if ($tags)
		{
			if (strpos($tags, '+') !== false)
			{
				$tags = str_replace('+', '', $tags);
			}
			else
			{
				$tags = str_replace(' ', '', $tags);
				$tags = '*-' . str_replace(',', ' -', $tags);
			}
		}

		self::$_tags = $tags;
		self::$_hook_tag = $hook_tag;

		return array($tags, $hook_tag);
	}

	/**
	 * _setPublicationState
	 *
	 * @param   object  $article  Params
	 * @param   string  $date     Params
	 *
	 * @return	object
	 */
	private function _setPublicationState($article, $date)
	{
		$itemDate = JFactory::getDate($date);
		$feedItemDate = $itemDate->toSql();
		$today = JFactory::getDate()->format(JText::_('COM_AUTOTWEET_DATE_FORMAT'));

		$zerodate_time = JFactory::getDate('2000-01-01 00:00:00')->toUnix();

		if ($itemDate->toUnix() < $zerodate_time)
		{
			$feedItemDate = $today;
		}

		$now_time = JFactory::getDate('now')->toUnix();

		if (!self::$_params->get('advance_date'))
		{
			if ($itemDate->toUnix() > $now_time)
			{
				$feedItemDate = $today;
			}
		}

		if (($feedItemDate) && (strlen(trim($feedItemDate)) <= 10))
		{
			$feedItemDate .= ' 00:00:00';
		}

		$article->created = self::$_params->get('created_date') ? $today : $feedItemDate;
		$article->publish_up = self::$_params->get('pub_date') ? $today : $feedItemDate;

		$article->state = intval(self::$_params->get('auto_publish'));

		$publishDays = intval(self::$_params->get('publish_duration'));

		if ($publishDays)
		{
			switch (self::$_params->get('pub_dur_type', 0))
			{
				// Days
				case 0:
					$publishDays = $publishDays * 24 * 60 * 60;
					break;

				// Hours
				case 1:
					$publishDays = $publishDays * 60 * 60;
					break;

				// Minutes
				case 2:
					$publishDays = $publishDays * 60;
					break;
			}

			$publish_down = JFactory::getDate($now_time + $publishDays);
			$publish_down = $publish_down->format(JText::_('COM_AUTOTWEET_DATE_FORMAT'));
			$article->publish_down = $publish_down;
		}
		else
		{
			$article->publish_down = null;
		}

		return $article;
	}

	/**
	 * _setDefaultImage
	 *
	 * @return	array
	 */
	private function _setDefaultImage()
	{
		$image = new FeedImage;

		$image->src = self::$_params->get('img');
		$image->title = JFactory::getConfig()->get('sitename');
		$image->alt = $image->title;

		$img_class = self::$_params->get('img_class');

		if ($img_class)
		{
			$image->class = self::$_params->get('img_class');
		}

		$img_style = self::$_params->get('img_style');

		if ($img_style)
		{
			$image->style = self::$_params->get('img_style');
		}

		return array($image);
	}

	/**
	 * _setDefaultEnclosureImage
	 *
	 * @param   array  $enclosures  Params
	 *
	 * @return	array
	 */
	private function _setDefaultEnclosureImage($enclosures)
	{
		foreach ($enclosures as $enclosure)
		{
			$real_type = strtolower($enclosure->real_type);

			if (JString::strpos($real_type, 'image/') === 0)
			{
				$image = new FeedImage;

				$image->src = $enclosure->link;
				$image->title = $enclosure->title;
				$image->alt = $enclosure->title;

				$img_class = self::$_params->get('img_class');

				if ($img_class)
				{
					$image->class = self::$_params->get('img_class');
				}

				$img_style = self::$_params->get('img_style');

				if ($img_style)
				{
					$image->style = self::$_params->get('img_style');
				}

				return array($image);
			}
		}

		return null;
	}

	/**
	 * _isDuplicated
	 *
	 * @param   object  $article  Params
	 *
	 * @return	bool
	 */
	private function _isDuplicated($article)
	{
		if (self::$_params->get('check_existing'))
		{
			$contenttype_id = self::$_params->get('contenttype_id');

			// Types: feedcontent, feedk2, and feedzoo
			$method = $contenttype_id . 'IsDuplicated';

			return FeedDupCheckerHelper::$method($article, self::$_params->get('compare_existing'));
		}

		return false;
	}
}

/**
 * Hook_tag function for htmLawed
 *
 * Cleanup and htmLawed text cleaning
 *
 * @param   object  $element          Param
 * @param   array   $attribute_array  Param
 *
 * @return	string
 */
function hookTagCleaning($element, $attribute_array = array())
{
	return FeedTextHelper::hookTagCleaning($element, $attribute_array);
}
