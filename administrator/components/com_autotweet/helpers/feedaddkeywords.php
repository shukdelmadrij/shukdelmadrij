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
 * FeedAddKeywordsHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class FeedAddKeywordsHelper
{
	private $_addkeyParams;

	private $_akProcessDesc;

	private $_akProcessKeys;

	/**
	 * generateMeta
	 *
	 * @param   object  $article         Params
	 * @param   string  $non_object      Params
	 * @param   bool    $doDesc          Params
	 * @param   bool    $processGlobals  Params
	 *
	 * @return	string
	 */
	public function generateMeta($article, $non_object, $doDesc, $processGlobals = true)
	{
		// Check $non-object to ascertain whether to treat $article as an object or variable
		// Params redefined as this is a call from outside the plugin but able to set whether to do description, $doDesc
		if ($non_object)
		{
			if ($this->_addkeyParams->processPlugins)
			{
				$article = JHTML::_('content.prepare', $article);
			}

			$getText = strip_tags($article);
			$getKeys = '';
			$getDesc = '';

			$keywords = self::generateKeywords($getKeys, $getText, $article, $processGlobals, $author = false, $cat_enabled = false);

			$doDesc ? $description = self::generateDescription($getDesc, $getText, $processGlobals) : $description = '';
		}
		else
		{
			// Check if we should exclude this
			$endNow = self::categoryCheck($article->sectionid, $article->catid);

			if ($endNow)
			{
				return;
			}

			// Set up variables
			$getKeys = $article->metakey;
			$getDesc = $article->metadesc;

			$getTextDesc = $article->introtext . ' ' . $article->fulltext;

			if ($this->_addkeyParams->processPlugins)
			{
				$app = JFactory::getApplication();

				if ($app->isSite())
				{
					$getTextDesc = JHTML::_('content.prepare', $getTextDesc);
				}
			}

			$getTextDesc = strip_tags($getTextDesc);
			$this->_addkeyParams->useTitle ? $getText = strip_tags($article->title) . ' ' . $getTextDesc : $getText = $getTextDesc;

			if ($this->_addkeyParams->doKeys == 1 or $this->_addkeyParams->doDesc == 1)
			{
				// See if keywords and/or description should be replaced/updated
				// We're keeping all the existing metadata
				if (JString::strpos($getKeys, "@KEEP") === true && JString::strpos($getDesc, "@KEEP") === true)
				{
					$description = JString::trim(str_replace("@KEEP", '', $getDesc));
					$keywords = JString::trim(str_replace("@KEEP", '', $getKeys));
				}
				elseif (JString::strpos($getKeys, "@KEEP") === true)
				{
					// Keep the keywords but replace the description, if set
					$keywords = JString::trim(str_replace("@KEEP", '', $getKeys));

					if ($this->_addkeyParams->doDesc == 1)
					{
						$description = self::generateDescription($getDesc, $getTextDesc, $processGlobals);
					}
				}
				elseif (JString::strpos($getDesc, "@KEEP") === true)
				{
					// Keep the description but replace the keywords, if set
					$description = JString::trim(str_replace("@KEEP", '', $getDesc));

					if ($this->_addkeyParams->doKeys == 1)
					{
						$keywords = self::generateKeywords($getKeys, $getText, $article, $processGlobals);
					}
				}
				else
				{
					// Process whole article
					if ($this->_addkeyParams->doDesc == 1)
					{
						if ($this->_addkeyParams->doEmptyDesc == 1)
						{
							if ($getDesc == '')
							{
								$description = self::generateDescription($getDesc, $getTextDesc, $processGlobals);
							}
							else
							{
								$description = $getDesc;
							}
						}
						else
						{
							$description = self::generateDescription($getDesc, $getTextDesc, $processGlobals);
						}
					}

					if ($this->_addkeyParams->doKeys == 1)
					{
						if ($this->_addkeyParams->doEmptyKeys == 1)
						{
							if ($getKeys == '')
							{
								$keywords = self::generateKeywords($getKeys, $getText, $article, $processGlobals);
							}
							else
							{
								$keywords = $getKeys;
							}
						}
						else
						{
							$keywords = self::generateKeywords($getKeys, $getText, $article, $processGlobals);
						}
					}
				}
			}
			else
			{
				// Not processing - see if this should be overridden
				// See if keywords and/or description should be replaced/updated

				// We're processing all metadata
				if (JString::strpos($getKeys, "@PROCESS") === true && JString::strpos($getDesc, "@PROCESS") === true)
				{
					$description = JString::trim(str_replace("@PROCESS", '', $getDesc));
					$description = self::generateDescription($getDesc, $getTextDesc, $processGlobals);
					$keywords = JString::trim(str_replace("@PROCESS", '', $getKeys));
					$keywords = self::generateKeywords($getKeys, $getText, $article, $processGlobals);
				}
				elseif (JString::strpos($getKeys, "@PROCESS") === true)
				{
					// Process keywords but keep the description
					$keywords = JString::trim(str_replace("@PROCESS", '', $getKeys));
					$keywords = self::generateKeywords($getKeys, $getText, $article, $processGlobals);
					$description = $article->metadesc;
				}
				elseif (JString::strpos($getDesc, "@PROCESS") === true)
				{
					// Process the description but keep the keywords
					$description = JString::trim(str_replace("@PROCESS", '', $getDesc));
					$description = self::generateDescription($getDesc, $getTextDesc, $processGlobals);
					$keywords = $article->metakey;
				}
				else
				{
					// Don't change anything
					$description = $article->metadesc;
					$keywords = $article->metakey;
				}
			}
		}

		$meta_data = array();
		$meta_data ['keywords'] = $keywords;
		$meta_data ['description'] = $description;

		return $meta_data;
	}

	/**
	 * import
	 *
	 * @param   object  $article   Params
	 * @param   string  $keywords  Params
	 *
	 * @return	void
	 */
	private function addAuthor($article, $keywords)
	{
		if ($article->created_by_alias != '')
		{
			if ($keywords == '')
			{
				$keywords .= $article->created_by_alias;
			}
			else
			{
				$keywords .= ',' . $article->created_by_alias;
			}
		}
		else
		{
			$db = JFactory::getDBO();
			$query = "SELECT " . $db->nameQuote('name') . " FROM " . $db->nameQuote('#__users') . " WHERE " . $db->nameQuote('id') . " = " . $db->Quote($article->created_by);

			$db->setQuery($query);
			$author = $db->loadResult();

			if ($author)
			{
				if ($keywords == '')
				{
					$keywords .= $author;
				}
				else
				{
					$keywords .= ',' . $author;
				}
			}
		}

		return $keywords;
	}

	/**
	 * import
	 *
	 * @param   object  $article   Params
	 * @param   string  $keywords  Params
	 * @param   string  $type      Params
	 *
	 * @return	void
	 */
	private function addCategory($article, $keywords, $type)
	{
		$db = JFactory::getDBO();

		switch ($type)
		{
			case 'section':

				$query = "SELECT " . $db->nameQuote('title') . " FROM " . $db->nameQuote('#__sections') . " WHERE " . $db->nameQuote('id') . " = " . $db->Quote($article->sectionid);
				break;

			case 'category':

				$query = "SELECT " . $db->nameQuote('title') . " FROM " . $db->nameQuote('#__categories') . " WHERE " . $db->nameQuote('id') . " = " . $db->Quote($article->catid);
				break;

			case 'both':

				$query1 = "SELECT " . $db->nameQuote('title') . " FROM " . $db->nameQuote('#__sections') . " WHERE " . $db->nameQuote('id') . " = " . $db->Quote($article->sectionid);

				$query2 = "SELECT " . $db->nameQuote('title') . " FROM " . $db->nameQuote('#__categories') . " WHERE " . $db->nameQuote('id') . " = " . $db->Quote($article->catid);
				break;
		}

		if ($type == 'both')
		{
			$db->setQuery($query1);
			$sect = $db->loadResult();

			if ($sect == null)
			{
				$sect = "Uncategorised";
			}

			$db->setQuery($query2);
			$cat = $db->loadResult();

			if ($cat == null)
			{
				$cat = "Uncategorised";
			}

			if ($sect && $cat)
			{
				$cat_enabled = $sect . ',' . $cat;
			}
			elseif ($sect && !$cat)
			{
				$cat_enabled = $sect;
			}
			elseif ($cat && !$sect)
			{
				$cat_enabled = $cat;
			}

			if ($cat_enabled)
			{
				if ($keywords == '')
				{
					$keywords .= $cat_enabled;
				}
				else
				{
					$keywords .= ',' . $cat_enabled;
				}
			}
		}
		else
		{
			$db->setQuery($query);
			$cat_enabled = $db->loadResult();

			if ($cat_enabled == null)
			{
				$cat_enabled = "Uncategorised";
			}

			if ($cat_enabled)
			{
				if ($keywords == '')
				{
					$keywords .= $cat_enabled;
				}
				else
				{
					$keywords .= ',' . $cat_enabled;
				}
			}
		}

		return $keywords;
	}

	/**
	 * categoryCheck
	 *
	 * @param   int  $catid  Params
	 *
	 * @return	bool
	 */
	private function categoryCheck($catid)
	{
		// If this is an excluded section or category, return 0
		if (isset($this->_addkeyParams->akCategories))
		{
			if (is_array($this->_addkeyParams->akCategories) && in_array($catid, $this->_addkeyParams->akCategories))
			{
				return true;
			}
			elseif ($catid == $this->_addkeyParams->akCategories)
			{
				return true;
			}
		}

		// Otherwise 0 to continue
		return false;
	}

	/**
	 * cleanWhitespace
	 *
	 * @param   string  &$text  Params
	 *
	 * @return	void
	 */
	private function cleanWhitespace(&$text)
	{
		$text = str_replace(
				array(
						"\t','\n','\r','\0','\x0B"
			), ' ', $text
		);

		while (JString::strpos($text, "  "))
		{
			$text = JString::str_ireplace("  ', ' ", $text);
		}
	}

	/**
	 * generateKeywords
	 *
	 * @param   string  $oldKeys         Params
	 * @param   string  $text            Params
	 * @param   string  $article         Params
	 * @param   string  $processGlobals  Params
	 * @param   string  $author          Params
	 * @param   string  $cat_enabled     Params
	 *
	 * @return	void
	 */
	private function generateKeywords($oldKeys, $text, $article, $processGlobals, $author = true, $cat_enabled = true)
	{
		// Keywords to preserve
		if ($this->_addkeyParams->preserveKeys == 1)
		{
			$oldKeys = html_entity_decode($oldKeys, ENT_QUOTES, 'UTF-8');

			if (preg_match('#{([\s\S]*)}#u', $oldKeys, $matches))
			{
				$savedKeys = $matches [1];
			}
		}
		else
		{
			$savedKeys = null;
		}

		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

		// Get rid of &nbsp; - deprecated but kept for pre-PHP5.2 support
		if ($this->_addkeyParams->oldphp)
		{
			$replace = array(
							"&nbsp;','&bdquo;','&rdquo;','&rsquo;','&Idquo;','&Isquo;','&ndash;','&quot;"
			);
			$text = JString::str_ireplace($replace, ' ', $text);
		}

		// Start cleaning up the article text
		// Cleans up plugin calls
		$text = preg_replace('#{[^}]*?}(?(?=[^{]*?{\/[^}]*?})[^{]*?{\/[^}]*?})#u', '', $text);

		// Cleans any numbers or punctuation/newlines etc which were causing blanks/dashes etc in the final output
		if ($this->_addkeyParams->oldphp)
		{
			$text = preg_replace('#[\d\W]#u', ' ', $text);
		}
		else
		{
			// New syntax more forgiving for hyphenated words but may still break them and does not work with PHP <5.2
			// Non-English character safe!
			$text = preg_replace("#\P{L}#u', ' ", $text);
		}

		// More efficient to change entire string to lower case here than via array_map
		$text = preg_replace('#[\s]{2,}#u', ' ', $text);

		$text = JString::strtolower($text);

		// Get rid of undefined variables errors
		$whiteToAdd = '';
		$whiteToAddArray = array();
		$multiWordWhiteToAddArray = array();
		$keywords = '';

		if (isset($this->_addkeyParams->multiWordWhiteList))
		{
			JString::strtolower($this->_addkeyParams->multiWordWhiteList);
			$multiWordWhiteArray = TextUtil::listToArray($this->_addkeyParams->multiWordWhiteList);

			foreach ($multiWordWhiteArray as $multiWordWhiteWord)
			{
				$multiWordWhiteWord = JString::trim($multiWordWhiteWord);

				if ($multiWordWhiteWord)
				{
					if ($multiWordCount = substr_count($text, $multiWordWhiteWord))
					{
						$multiWordCount *= $this->_addkeyParams->multiWordWeighting;
						$multiWordWhiteToAddArray [$multiWordWhiteWord] = $multiWordCount;

						if ($this->_addkeyParams->unsetMultiWord)
						{
							JString::str_ireplace($multiWordWhiteWord, '', $text);
						}
					}
				}
			}
		}

		if (isset($this->_addkeyParams->whiteList))
		{
			JString::strtolower($this->_addkeyParams->whiteList);
			$whiteArray = TextUtil::listToArray($this->_addkeyParams->whiteList);

			foreach ($whiteArray as $whiteWord)
			{
				$whiteWord = JString::trim($whiteWord);

				if ($whiteWord)
				{
					if ($whiteWordCount = substr_count($text, $whiteWord))
					{
						$whiteWordCount *= $this->_addkeyParams->whiteWordWeighting;
						$whiteToAddArray [$whiteWord] = $whiteWordCount;
						JString::str_ireplace($whiteWord, '', $text);
					}
				}
			}
		}

		if ($this->_addkeyParams->whiteListOnly)
		{
			$textArray = array();
		}
		else
		{
			$textArray = explode(' ', $text);
			$textArray = array_count_values($textArray);

			// Remove blacklisted words
			JString::strtolower($this->_addkeyParams->blackList);
			$blackArray = TextUtil::listToArray($this->_addkeyParams->blackList);

			foreach ($blackArray as $blackWord)
			{
				if (isset($textArray [JString::trim($blackWord)]))
				{
					unset($textArray [JString::trim($blackWord)]);
				}
			}
		}

		$textArray = array_merge($textArray, $whiteToAddArray, $multiWordWhiteToAddArray);

		// Sort by frequency
		arsort($textArray);

		$i = 1;

		foreach ($textArray as $word => $instances)
		{
			if ($i > $this->_addkeyParams->keyCount)
			{
				break;
			}

			if (strlen(JString::trim($word)) >= $this->_addkeyParams->minLength)
			{
				if (!isset($keywordsIn))
				{
					$keywordsIn = array();
				}

				$keywordsIn [] = JString::trim($word);
				$i++;
			}
		}

		// Make the vars whiteToAdd and keywords, add in the whitelist words
		if (isset($keywordsIn))
		{
			$keywords = implode(',', $keywordsIn);
		}

		// Add in the preserved meta keywords
		if (isset($savedKeys))
		{
			$keywords .= ', ' . $savedKeys;
		}

		// Add the author or author alias as a keyword if desired
		if ($author)
		{
			if ($this->_addkeyParams->addAuthor == 1)
			{
				$keywords = self::addAuthor($article, $keywords);
			}
		}

		// Add section/category if set
		if ($cat_enabled)
		{
			if ($this->_addkeyParams->addSectCat)
			{
				$keywords = self::addCategory($article, $keywords, $this->_addkeyParams->addSectCat);
			}
		}

		if ($processGlobals)
		{
			$this->_akProcessKeys = 1;
		}

		// Do we need to revert encoding for non-English characters?
		return JString::trim(JString::strtolower($keywords));
	}

	/**
	 * generateDescription
	 *
	 * @param   string  $oldDesc         Params
	 * @param   string  $text            Params
	 * @param   string  $processGlobals  Params
	 *
	 * @return	void
	 */
	private function generateDescription($oldDesc, $text, $processGlobals)
	{
		// Description to preserve

		if ($this->_addkeyParams->preserveDesc == 1)
		{
			$oldDesc = html_entity_decode($oldDesc, ENT_QUOTES, 'UTF-8');
			self::cleanWhitespace($oldDesc);

			if (preg_match('#{([^}][\s\S]*)}#u', $oldDesc, $matches))
			{
				$savedDesc = $matches [1];

				if (JString::strpos($savedDesc, '[start]'))
				{
					$position = "start";
					$savedDesc = JString::str_ireplace("[start]", '', $savedDesc);
				}
				else
				{
					$position = "end";
				}
			}
		}

		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

		// Start cleaning up the article text
		// Cleans up plugin calls
		$text = preg_replace('#{[^}]*?}(?(?=[^{]*?{\/[^}]*?})[^{]*?{\/[^}]*?})#u', '', $text);

		// Get rid of all forms of whitespace except single spaces
		$text = preg_replace('#[\s]{2,}#u', ' ', $text);

		// Use sentence, word or char count to make description
		// Char count is now the fallback method
		if ($this->_addkeyParams->descPrimary == 'sentence')
		{
			// Setup pattern to find sentences and create description depending on defined number of sentences
			$description = '';
			$pattern = '#\b(.+?[\.|\!|\?])#u';

			for ($i = 0; $i < $this->_addkeyParams->descSentCount; $i++)
			{
				$offset = '';

				if (preg_match($pattern, $text, $matches))
				{
					$match = $matches [1];
				}
				else
				{
					break;
				}

				$description .= ' ' . $match;

				$offset = JString::strpos($text, $match);
				$offset += strlen($match);
				$text = JString::substr($text, $offset);
			}
		}

		if ($this->_addkeyParams->descPrimary == 'word')
		{
			$explode = explode(' ', JString::trim($text));
			$string = '';

			for ($i = 0; $i < $this->_addkeyParams->descWordCount; $i++)
			{
				if (isset($explode [$i]))
				{
					$string .= $explode [$i] . ' ';
				}
				else
				{
					break;
				}
			}

			$description = JString::trim($string);
		}

		// If description is null, fallback to char count
		if ($this->_addkeyParams->descPrimary == 'char' or $description == '')
		{
			$description = JString::substr(JString::trim($text), 0, $this->_addkeyParams->descCharCount);
		}

		// Add in the preserved description
		if (isset($savedDesc))
		{
			if ($position == "start")
			{
				$description = JString::trim($savedDesc) . ' ' . JString::trim($description);
			}
			elseif ($position == "end")
			{
				$description = JString::trim($description) . ' ' . JString::trim($savedDesc);
			}
		}

		if ($this->_addkeyParams->dotdotdot)
		{
			if (!JString::strpos($description, '...'))
			{
				$description .= '...';
			}
		}

		if ($processGlobals)
		{
			$this->_akProcessDesc = 1;
		}

		return JString::trim($description);
	}
}
