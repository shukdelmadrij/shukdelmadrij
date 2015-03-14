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
 * AutotweetTableFeedContent
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetTableFeedContent extends F0FTable
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
		parent::__construct('#__content', 'id', $db,
				array('behaviors' => array(
					'tags',
					'autotweetcontentassets'
				)
			)
		);

		$this->_columnAlias = array(
						'enabled' => 'published',
						'created_on' => 'created',
						'modified_on' => 'modified',
						'locked_on' => 'checked_out_time',
						'locked_by' => 'checked_out'
		);

		$this->id = 0;
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
		$result = parent::onBeforeStore($updateNulls);

		$this->modified = '0000-00-00 00:00:00';

		return $result;
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @throws  UnexpectedValueException
	 *
	 * @return  string
	 */
	public function getAssetName()
	{
		$k = $this->_tbl_key;

		// If there is no assetKey defined, let's set it to table name

		if (!$this->_assetKey)
		{
			throw new UnexpectedValueException('Table must have an asset key defined in order to track assets');
		}

		return 'com_content.article.' . (int) $this->$k;
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 * By default, all assets are registered to the ROOT node with ID,
	 * which will default to 1 if none exists.
	 * The extended class can define a table and id to lookup.  If the
	 * asset does not exist it will be created.
	 *
	 * @param   F0FTable  $table  A F0FTable object for the asset parent.
	 * @param   integer   $id     Id to look up
	 *
	 * @return  integer
	 */
	public function getAssetParentId($table = null, $id = null)
	{
		// For simple cases, parent to the asset root.
		$assets = JTable::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));

		$assets->loadByName('com_content.category.' . (int) $this->catid);

		$rootId = $assets->id;

		if (!empty($rootId))
		{
			return $rootId;
		}

		$rootId = $assets->getRootId();

		if (!empty($rootId))
		{
			return $rootId;
		}

		return 1;
	}
}

/**
 * F0FTableBehaviorContentAssets
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class F0FTableBehaviorAutoTweetContentAssets extends F0FTableBehaviorAssets
{
	/**
	 * The event which runs after storing (saving) data to the database
	 *
	 * @param   F0FTable  &$table  The table which calls this event
	 *
	 * @return  boolean  True to allow saving without an error
	 */
	public function onAfterStore(&$table)
	{
		return $this->onBeforeStore($table, true);
	}
}
