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
 * Rule engine handles publushing rules
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class RuleEngineHelper
{
	// Rule types
	const CATEGORY_IN = 1;
	const CATEGORY_NOTIN = 2;
	const TERM_OR = 3;
	const TERM_AND = 4;
	const CATCH_ALL_NOTFITS = 5;
	const WORDTERM_OR = 6;
	const WORDTERM_AND = 7;
	const REG_EXPR = 8;
	const TERM_NOTIN = 9;
	const WORDTERM_NOTIN = 10;
	const AUTHOR_IN = 11;
	const AUTHOR_NOTIN = 12;
	const CATCH_ALL = 13;
	const LANGUAGE_IN = 14;
	const LANGUAGE_NOTIN = 15;
	const ACCESS_IN = 16;
	const ACCESS_NOTIN = 17;

	// Joocial types
	const IS_USERCHANNEL = 18;
	const ISNOT_USERCHANNEL = 19;

	const REGISTERED_GROUP = 2;

	// Token separators
	const TOKEN_DELIMITER = ' ,.;!?\n\t"\'\\/';

	// Rule params
	const RULE_AUTOPUBLISH = 'autopublish';
	const RULE_SHOW_URL = 'show_url';
	const RULE_RMC_TEXTPATTERN = 'rmc_textpattern';
	const RULE_SHOW_STATIC_TEXT = 'show_static_text';
	const RULE_STATIC_TEXT = 'statix_text';
	const RULE_REG_EX = 'reg_ex';
	const RULE_REG_REPLACE = 'reg_replace';
	const RULE_TARGET_ID = 'target_id';

	protected $rules = array();

	private static $_instance = null;

	/**
	 * Constructor.
	 *
	 */
	protected function __construct()
	{
		// Nothing to do
	}

	/**
	 * getInstance.
	 *
	 * @return	Object.
	 */
	public static function &getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new RuleEngineHelper;
		}

		return self::$_instance;
	}

	/**
	 * init.
	 *
	 * @param   string  $plugin_name  Params
	 *
	 * @return	void
	 */
	public function load($plugin_name)
	{
		$requestsModel = F0FModel::getTmpInstance('Rules', 'AutoTweetModel');
		$requestsModel->set('plugin', $plugin_name);
		$requestsModel->set('published', 1);
		$requestsModel->set('filter_order', 'ordering');
		$requestsModel->set('filter_order_Dir', 'ASC');
		$this->rules = $requestsModel->getItemList(true);
	}

	/**
	 * getChannels.
	 *
	 * @param   string  $plugin  Param
	 * @param   array   &$post   Param
	 *
	 * @return	array
	 */
	public function getChannels($plugin, &$post)
	{
		$channels = $this->getAllowedChannels($post);

		// There's one Universal Channel rule
		if (count($channels) == 1)
		{
			reset($channels);
			$key = key($channels);
			$rule = $channels[$key];

			// There's one Universal Channel rule CHANNEL-ID = 0
			if ($key === 0)
			{
				$author = $post->xtform->get('author');
				$all_channels = ChannelFactory::getInstance()->getChannels($author);
				$channels = array();

				// Generate virtual rules
				foreach ($all_channels as $channel_id => $channel)
				{
					$channels[$channel_id] = $rule;
				}
			}
		}

		return $channels;
	}

	/**
	 * hasRules.
	 *
	 * @return	boolean.
	 */
	public function hasRules()
	{
		if (!empty($this->rules))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * getAllowedChannels.
	 *
	 * @param   object  &$post  Params
	 *
	 * @return	array
	 */
	protected function getAllowedChannels(&$post)
	{
		$categories_to_check = $post->xtform->get('catids');

		$text_to_check = $post->xtform->get('text');

		if (isset($post->title))
		{
			$text_to_check .= ' ' . $post->title;
		}

		if (isset($post->fulltext))
		{
			$text_to_check .= ' ' . $post->fulltext;
		}

		$author_to_check = $post->xtform->get('author');
		$language_to_check = $post->xtform->get('language');
		$access_to_check = $post->xtform->get('access');

		$allowed_channels = array();
		$catch_all_channels = array();

		foreach ($this->rules as $rule)
		{
			if (self::REG_EXPR == $rule->ruletype_id)
			{
				$condition = $rule->cond;
			}
			else
			{
				$condition = TextUtil::listToArray(trim($rule->cond));
			}

			$hasMatched = false;

			switch ($rule->ruletype_id)
			{
				case self::CATEGORY_IN:
					$matched = array_intersect($condition, $categories_to_check);

					if (!empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::CATEGORY_NOTIN:
					$matched = array_intersect($condition, $categories_to_check);

					if (empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::TERM_OR:
					$matched = '';

					foreach ($condition as $term)
					{
						$matched = JString::stristr($text_to_check, $term);

						if (!empty($matched))
						{
							break;
						}
					}

					if (!empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::TERM_AND:
					$matched = '';

					foreach ($condition as $term)
					{
						$matched = JString::stristr($text_to_check, $term);

						if (empty($matched))
						{
							break;
						}
					}

					if (!empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::TERM_NOTIN:
					$matched = '';

					foreach ($condition as $term)
					{
						$matched = JString::stristr($text_to_check, $term);

						if (!empty($matched))
						{
							break;
						}
					}

					if (empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::WORDTERM_OR:
					$matched = '';
					$token = strtok($text_to_check, self::TOKEN_DELIMITER);

					while (false != $token)
					{
						$matched = in_array(trim($token), $condition);

						if (!empty($matched))
						{
							break;
						}

						$token = strtok(self::TOKEN_DELIMITER);
					}

					if (!empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::WORDTERM_AND:
					$matched = '';
					$text = array();
					$token = strtok($text_to_check, self::TOKEN_DELIMITER);

					while (false != $token)
					{
						$text[] = trim($token);
						$token = strtok(self::TOKEN_DELIMITER);
					}

					foreach ($condition as $term)
					{
						$matched = in_array($term, $text);

						if (empty($matched))
						{
							break;
						}
					}

					if (!empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::WORDTERM_NOTIN:
					$matched = '';
					$text = array();
					$token = strtok($text_to_check, self::TOKEN_DELIMITER);

					while (false != $token)
					{
						$text[] = trim($token);
						$token = strtok(self::TOKEN_DELIMITER);
					}

					foreach ($condition as $term)
					{
						$matched = in_array($term, $text);

						if (!empty($matched))
						{
							break;
						}
					}

					if (empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;

				case self::AUTHOR_IN:
					$matched = '';

					foreach ($condition as $term)
					{
						// Take care: strcmp returns 0 if strings are matching!
						$matched = strcmp(trim($author_to_check), trim($term));

						if (0 == $matched)
						{
							break;
						}
					}

					if ((0 == $matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::AUTHOR_NOTIN:
					$matched = '';

					foreach ($condition as $term)
					{
						// Take care: strcmp returns 0 if strings are matching!
						$matched = strcmp(trim($author_to_check), trim($term));

						if (0 == $matched)
						{
							break;
						}
					}

					if ((0 != $matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;

				case self::LANGUAGE_IN:
					$matched = '';

					foreach ($condition as $term)
					{
						// Take care: strcmp returns 0 if strings are matching!
						$matched = strcmp(trim($language_to_check), trim($term));

						if (0 == $matched)
						{
							break;
						}
					}

					if ((0 == $matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::LANGUAGE_NOTIN:
					$matched = '';

					foreach ($condition as $term)
					{
						// Take care: strcmp returns 0 if strings are matching!
						$matched = strcmp(trim($language_to_check), trim($term));

						if (0 == $matched)
						{
							break;
						}
					}

					if ((0 != $matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;

				case self::ACCESS_IN:
					$matched = '';

					foreach ($condition as $term)
					{
						// Take care: strcmp returns 0 if strings are matching!
						$matched = strcmp(trim($access_to_check), trim($term));

						if (0 == $matched)
						{
							break;
						}
					}

					if ((0 == $matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::ACCESS_NOTIN:
					$matched = '';

					foreach ($condition as $term)
					{
						// Take care: strcmp returns 0 if strings are matching!
						$matched = strcmp(trim($access_to_check), trim($term));

						if (0 == $matched)
						{
							break;
						}
					}

					if ((0 != $matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;

				case self::REG_EXPR:
					$matched = preg_match($condition, $text_to_check);

					if (!empty($matched) && !array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;

				case self::IS_USERCHANNEL:
					$channel = ChannelFactory::getInstance()->getChannel($rule->channel_id);
					$created_by = $channel->get('created_by');
					$user = JFactory::getUser($created_by);
					$hasMatched = in_array(self::REGISTERED_GROUP, $user->getAuthorisedGroups());
					break;

				case self::ISNOT_USERCHANNEL:
					$channel = ChannelFactory::getInstance()->getChannel($rule->channel_id);
					$created_by = $channel->get('created_by');
					$user = JFactory::getUser($created_by);
					$hasMatched = !in_array(self::REGISTERED_GROUP, $user->getAuthorisedGroups());
					break;

				case self::CATCH_ALL:
					if (!array_key_exists($rule->channel_id, $allowed_channels))
					{
						$hasMatched = true;
					}
					break;
				case self::CATCH_ALL_NOTFITS:
					if (!array_key_exists($rule->channel_id, $catch_all_channels))
					{
						$catch_all_channels[$rule->channel_id] = $rule;
					}
					break;
			}

			if ($hasMatched)
			{
				$allowed_channels[$rule->channel_id] = $rule;
			}
		}

		if (empty($allowed_channels))
		{
			$allowed_channels = $catch_all_channels;
		}

		return $allowed_channels;
	}

	/**
	 * executeRule.
	 *
	 * @param   object  &$rule     Params
	 * @param   object  &$channel  Params
	 * @param   object  &$post     Params
	 *
	 * @return	void
	 */
	public static function executeRule(&$rule, &$channel, &$post)
	{
		// Correct autopublish options when rules engine is used
		$post->nextstate = self::getValue($rule, self::RULE_AUTOPUBLISH);

		switch ($post->nextstate)
		{
			case 'on':
				$post->autopublish = true;
				break;
			case 'off':
				$post->autopublish = false;
				break;
			case 'cancel':
				$post->autopublish = false;
				break;
			case 'default':
				// Use default value from plugin/channel: do nothing
				break;
		}

		// Correct url link mode options when rules engine is used
		$show_url = self::getValue($rule, self::RULE_SHOW_URL);

		if ('default' != $show_url)
		{
			$post->show_url = $show_url;
		}

		// Target_id
		if (!isset($rule->xtform))
		{
			$rule->xtform = Eform::paramsToRegistry($rule);
		}

		$target_id = $rule->xtform->get(self::RULE_TARGET_ID);

		if ($target_id)
		{
			$post->xtform->set('target_id', $target_id);
		}

		$message = TextUtil::cleanText($post->text);

		// Create message for new post (logged posts uses existing message text)
		// Filter first full and msgtext if there is an regex in rule
		$rule_reg_ex = self::getValue($rule, self::RULE_REG_EX);

		if (!empty($rule_reg_ex))
		{
			$is_json = json_decode($rule_reg_ex);

			if ($is_json)
			{
				$rule_reg_ex = $is_json;
			}

			$rule_reg_replace = self::getValue($rule, self::RULE_REG_REPLACE);
			$is_json = json_decode($rule_reg_replace);

			if ($is_json)
			{
				$rule_reg_replace = $is_json;
			}

			$message = preg_replace($rule_reg_ex, $rule_reg_replace, $message);

			$post->title = preg_replace($rule_reg_ex, $rule_reg_replace, $post->title);

			$post->text = preg_replace($rule_reg_ex, $rule_reg_replace, $post->text);

			$post->introtext = preg_replace($rule_reg_ex, $rule_reg_replace, $post->introtext);

			$post->fulltext = preg_replace($rule_reg_ex, $rule_reg_replace, $post->fulltext);
		}

		$post->message = $message;

		// Apply a custom pattern to the text
		$pattern = self::getValue($rule, self::RULE_RMC_TEXTPATTERN);

		if (!empty($pattern))
		{
			AutotweetBaseHelper::applyTextPattern($pattern, $post);
		}

		$message = $post->message;

		// Add static text from rules engine
		$show_static_text = self::getValue($rule, self::RULE_SHOW_STATIC_TEXT);
		$rule_static_text = self::getValue($rule, self::RULE_STATIC_TEXT);
		$message = AutotweetBaseHelper::addStatictext($show_static_text, $message, $rule_static_text);

		$post->message = $message;
	}

	/**
	 * getValue
	 *
	 * @param   object  &$rule  Param
	 * @param   string  $key    Param
	 *
	 * @return	string
	 */
	protected static function getValue(&$rule, $key)
	{
		if (isset($rule->$key))
		{
			return $rule->$key;
		}
		else
		{
			return null;
		}
	}
}
