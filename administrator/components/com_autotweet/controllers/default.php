<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * AutotweetControllerChannels
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerDefault extends F0FController
{
	/**
	 * Method to get a reference to the current view and load it if necessary.
	 *
	 * @param   string  $name    The view name. Optional, defaults to the controller name.
	 * @param   string  $type    The view type. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for view. Optional.
	 *
	 * @return  F0FView  Reference to the view or an error.
	 */
	public function getView($name = '', $type = '', $prefix = '', $config = array())
	{
		$config['linkbar_style'] = 'classic';

		return parent::getView($name, $type, $prefix, $config);
	}

	/**
	 * Returns true if there is a redirect set in the controller
	 *
	 * @return  boolean
	 */
	public function hasRedirect()
	{
		return ((!empty($this->redirect)) && ($this->input->getCmd('format', 'html') != 'json'));
	}
}
