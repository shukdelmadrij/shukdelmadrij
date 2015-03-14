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
 * FeedImage
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class FeedImage
{
	public $src = null;

	public $title = null;

	public $alt = null;

	public $class = null;

	public $style = null;

	public $align = null;

	public $border = null;

	public $width = null;

	public $height = null;

	/**
	 * generateTag
	 *
	 * @return	string
	 */
	public function generateTag()
	{
		$tag = array();
		$tag[] = '<img src="';

		if (strpos($this->src, 'http://') === false)
		{
			$url = RouteHelp::getInstance()->getRoot();
			$tag[] = $url;
			$tag[] = '/';
		}

		$tag[] = $this->src;
		$tag[] = '"';

		if ($this->title)
		{
			$tag[] = ' title="';
			$tag[] = $this->title . '"';
		}

		if ($this->alt)
		{
			$tag[] = ' alt="';
			$tag[] = $this->alt . '"';
		}

		if ($this->class)
		{
			$tag[] = ' class="';
			$tag[] = $this->class . '"';
		}

		if ($this->style)
		{
			$tag[] = ' style="';
			$tag[] = $this->style . '"';
		}

		if ($this->align)
		{
			$tag[] = ' align="';
			$tag[] = $this->align . '"';
		}

		if ($this->border)
		{
			$tag[] = ' border="';
			$tag[] = $this->border . '"';
		}

		if ($this->width)
		{
			$tag[] = ' width="';
			$tag[] = $this->width . '"';
		}

		if ($this->height)
		{
			$tag[] = ' height="';
			$tag[] = $this->height . '"';
		}

		$tag[] = '/>';

		return implode('', $tag);
	}
}
