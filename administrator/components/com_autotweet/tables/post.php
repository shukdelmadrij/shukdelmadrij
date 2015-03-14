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
 * AutotweetTablePost
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetTablePost extends F0FTable
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
		parent::__construct('#__autotweet_posts', 'id', $db);

		$this->_columnAlias = array(
						// 'enabled' => 'published',

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
		if (!$this->title)
		{
			$this->setError(JText::_('COM_AUTOTWEET_POST_ERR_NEEDS_TITLE'));

			return false;
		}

		// If the catid is missing, throw an error
		if (!$this->channel_id)
		{
			$this->setError(JText::_('COM_AUTOTWEET_POST_ERR_NEEDS_CHANNEL'));

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
			$this->postdate = JFactory::getDate()->format('Y-m-d');
			$this->ref_id = JFactory::getDate()->toUnix();

			$this->url = JUri::root();
			$this->org_url = $this->url;

			$this->plugin = 'autotweetpost';
			$this->show_url = 'end_of_message';
		}
	}

	/**
	 * The event which runs before storing (saving) data to the database
	 *
	 * @param   boolean  $updateNulls  Should nulls be saved as nulls (true) or just skipped over (false)?
	 *
	 * @return  boolean  True to allow saving
	 */
	protected function onBeforeStore($updateNulls)
	{
		// BLOB/TEXT default value
		if (!isset($this->params))
		{
			$this->params = '';
		}

		return parent::onBeforeStore($updateNulls);
	}

	/**
	 * purge
	 *
	 * @return  boolean  True on success
	 */
	public function purge()
	{
		$query = 'DELETE FROM ' . $this->_db->qn($this->_tbl);
		$this->_db->setQuery($query);

		return $this->_db->execute();
	}
}
