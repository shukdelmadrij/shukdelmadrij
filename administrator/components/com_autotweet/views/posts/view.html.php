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
 * AutotweetViewPosts
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewPosts extends AutotweetDefaultView
{
	protected $isModule = false;

	/**
	 * Class constructor
	 *
	 * @param   array  $config  Configuration parameters
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$layout = $this->input->get('layout', null, 'cmd');
		$this->isModule = ($layout == 'module');
	}

	/**
	 * onBrowse.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 */
	protected function onBrowse($tpl = null)
	{
		Extly::initApp(CAUTOTWEETNG_VERSION);
		Extly::loadAwesome();

		if (!$this->isModule)
		{
			// When in interactive browsing mode, save the state to the session
			$this->getModel()->savestate(1);
		}

		return $this->onDisplay($tpl);
	}
}
