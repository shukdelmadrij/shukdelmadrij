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
 * FeedContent
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class FeedContent
{
	public $id = 0;

	public $cat_id = 0;

	public $access = 0;

	public $featured = 0;

	public $language = 0;

	public $hash = null;

	public $permalink = null;

	public $feedItemBase = null;

	public $namePrefix = null;

	public $title = null;

	public $alias = null;

	public $blacklisted = null;

	public $whitelisted = null;

	public $created_by = null;

	public $created_by_alias = null;

	public $images = null;

	public $enclosures = null;

	public $showEnclosureImage = null;

	public $showDefaultImage = null;

	public $introtext = null;

	public $fulltext = null;

	public $shortlink = null;

	public $metakey = '';

	public $metadesc = '';

	public $created = null;

	public $publish_up = null;

	public $state = null;

	public $publish_down = null;
}
