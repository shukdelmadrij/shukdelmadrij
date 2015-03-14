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

// Autoloader
spl_autoload_register(
	array(
		new SimplePie_Autoloader,
		'autoload'
	)
);

if (!class_exists('SimplePie'))
{
	trigger_error('Autoloader not registered properly', E_USER_ERROR);
}

/**
 * SimplePie_Autoloader class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
*/
class SimplePie_Autoloader
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'SimplePie';
	}

	/**
	 * Autoloader
	 *
	 * @param   string  $class  The name of the class to attempt to load.
	 *
	 * @return	void
	 */
	public function autoload($class)
	{
		// Only load the class if it starts with "SimplePie"
		if (strpos($class, 'SimplePie') !== 0)
		{
			return;
		}

		$filename = $this->path . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		include $filename;
	}
}
