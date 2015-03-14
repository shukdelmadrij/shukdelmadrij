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
 * ImageHelper
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class ImageHelper
{
	/**
	 * isImage
	 *
	 * @param   object  $path  Param.
	 *
	 * @return	string
	 */
	public static function isImage($path)
	{
		$a = getimagesize($path);
		$image_type = $a[2];

		if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP)))
		{
			return true;
		}

		return false;
	}
}
