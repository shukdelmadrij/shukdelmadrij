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
 * AutotweetTableChannel
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetTableChannel extends F0FTable
{
	/**
	 * Instantiate the table object
	 *
	 * @param   string     $table  Param
	 * @param   string     $key    Param
	 * @param   JDatabase  &$db    The Joomla! database object
	 */
	public function __construct($table, $key, &$db)
	{
		parent::__construct('#__autotweet_channels', 'id', $db);

		$this->_columnAlias = array(
						'enabled' => 'published',
						'created_on' => 'created',
						'modified_on' => 'modified',
						'locked_on' => 'checked_out_time',
						'locked_by' => 'checked_out'
		);

		$this->_trackAssets = true;
	}

	/**
	 * Checks the record for validity
	 *
	 * @return  int  True if the record is valid
	 */
	public function check()
	{
		// If the name is missing, throw an error
		if (empty($this->name))
		{
			$this->setError(JText::_('COM_AUTOTWEET_CHANNEL_ERR_NEEDS_TITLE'));

			return false;
		}

		// If the catid is missing, throw an error
		if (empty($this->channeltype_id))
		{
			$this->setError(JText::_('COM_AUTOTWEET_CHANNEL_ERR_NEEDS_TYPE'));

			return false;
		}

		return true;
	}

	/**
	 * onAfterLoad
	 *
	 * @param   bool  &$result  Param
	 *
	 * @return	void
	 */
	protected function onAfterLoad(&$result)
	{
		if ($this->id == 0)
		{
			$this->autopublish = true;
			$this->media_mode = 'both';
		}
	}

	/**
	 * setToken
	 *
	 * @param   int     $id           Param
	 * @param   string  $token_field  Param
	 * @param   string  $token        Param
	 *
	 * @return	void
	 */
	public function setToken($id, $token_field, $token)
	{
		if ($id)
		{
			$result = $this->load($id);

			if (!$result)
			{
				throw new Exception('Channel failed to load (setToken)!');
			}

			$params = $this->params;
			$registry = new JRegistry;
			$registry->loadString($params);
			$registry->set($token_field, $token);
			$this->bind(array('params' => (string) $registry));
			$this->store();
		}
		else
		{
			throw new Exception('Channel failed to load 0 (setToken)!');
		}
	}
}
