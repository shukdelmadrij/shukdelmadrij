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
 * AutotweetModelRequests
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelRequests extends F0FModel
{
	protected $advanced_attrs = null;

	/**
	 * buildQuery
	 *
	 * @param   bool  $overrideLimits  Param
	 *
	 * @return	F0FQuery
	 */
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();

		$query = F0FQueryAbstract::getNew($db)->select('*')->from($db->quoteName('#__autotweet_requests'));

		$fltPublishup = $this->getState('publish_up', null, 'date');

		if ($fltPublishup)
		{
			$fltPublishup = $fltPublishup . '%';
			$query->where($db->qn('publish_up') . ' LIKE ' . $db->q($fltPublishup));
		}

		$fltUntilDate = $this->getState('until_date', null, 'date');

		if ($fltUntilDate)
		{
			$query->where($db->qn('publish_up') . ' <= ' . $db->q($fltUntilDate));
		}

		$input = new F0FInput;
		$start = $input->get('start');

		if ($start)
		{
			$date = new JDate($start);
			$query->where($db->qn('publish_up') . ' >= ' . $db->q($date->toSql()));
		}

		$end = $input->get('end');

		if ($end)
		{
			$date = new JDate($end);
			$query->where($db->qn('publish_up') . ' <= ' . $db->q($date->toSql()));
		}

		$fltPlugin = $this->getState('plugin', null, 'string');

		if ($fltPlugin)
		{
			$query->where($db->qn('plugin') . ' = ' . $db->q($fltPlugin));
		}

		$fltRefId = $this->getState('ref_id', null, 'string');

		if ($fltRefId)
		{
			$query->where($db->qn('ref_id') . ' = ' . $db->q($fltRefId));
		}

		$fltRids = $this->getState('rids', null);

		if ($fltRids != '')
		{
			if (is_string($fltRids))
			{
				$fltRids = TextUtil::listToArray($fltRids);
			}

			$list = array();

			foreach ($fltRids as $p)
			{
				$list[] = $db->q($p);
			}

			$fltRids = implode(',', $list);
			$query->where($db->qn('id') . ' IN (' . $fltRids . ')');
		}

		$fltTypeinfo = $this->getState('typeinfo', null, 'string');

		if ($fltTypeinfo)
		{
			$query->where($db->qn('typeinfo') . ' = ' . $db->q($fltTypeinfo));
		}

		$fltPublished = $this->getState('published', 0, 'int');
		$query->where($db->qn('published') . ' = ' . $db->q($fltPublished));

		$search = $this->getState('search', null);

		if ($search)
		{
			$search = '%' . $search . '%';
			$query->where('(' . $db->qn('id') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('ref_id') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('description') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('url') . ' LIKE ' . $db->quote($search) . ')');
		}

		AclPermsHelper::whereOwnership($query);

		$order = $this->getState('filter_order', 'publish_up', 'cmd');

		if (!in_array($order, array_keys($this->getTable()->getData())))
		{
			$order = 'publish_up';
		}

		$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
		$query->order($order . ' ' . $dir);

		// $logger = AutotweetLogger::getInstance();
		// $logger->log(JLog::INFO, 'Requests : ' . $query->__toString());

		return $query;
	}

	/**
	 * This method runs before the $data is saved to the $table. Return false to
	 * stop saving.
	 *
	 * @param   array   &$data   Param
	 * @param   JTable  &$table  Param
	 *
	 * @return bool
	 */
	protected function onBeforeSave(&$data, &$table)
	{
		$data['params'] = EForm::paramsToString($data);

		if (array_key_exists('publish_up', $data))
		{
			$data['publish_up'] = EParameter::convertLocalUTC($data['publish_up']);
		}
		else
		{
			$data['publish_up'] = JFactory::getDate()->toSql();
		}

		// Cleaning annoying spaces
		$data = array_map('trim', $data);

		if (array_key_exists('autotweet_advanced_attrs', $data))
		{
			$this->advanced_attrs = AdvancedattrsHelper::retrieveAdvancedAttrs($data['autotweet_advanced_attrs']);
		}

		return parent::onBeforeSave($data, $table);
	}

	/**
	 * This method runs after the data is saved to the $table.
	 *
	 * @param   F0FTable  &$table  The table which was saved
	 *
	 * @return  boolean
	 */
	protected function onAfterSave(&$table)
	{
		$result = parent::onAfterSave($table);

		if (isset($this->advanced_attrs))
		{
			$this->advanced_attrs->ref_id = $table->ref_id;
			AdvancedattrsHelper::saveAdvancedAttrs($this->advanced_attrs, $table->ref_id);
		}

		return $result;
	}

	/**
	 * This method runs after an item has been gotten from the database in a read
	 * operation. You can modify it before it's returned to the MVC triad for
	 * further processing.
	 *
	 * @param   JTable  &$record  Param
	 *
	 * @return bool
	 */
	protected function onAfterGetItem(&$record)
	{
		$record->xtform = EForm::paramsToRegistry($record);

		return parent::onAfterGetItem($record);
	}

	/**
	 * Overwrites original method for AT specific handling
	 *
	 * @return	string
	 */
	public function process()
	{
		JLoader::register('RequestHelp', JPATH_AUTOTWEET_HELPERS . '/request.php');

		if (is_array($this->id_list) && !empty($this->id_list))
		{
			if (empty($user))
			{
				$oUser = JFactory::getUser();
				$userid = $oUser->id;
			}

			if (!RequestHelp::processRequests($this->id_list))
			{
				$this->setError('processRequests failed');

				return false;
			}
		}

		return true;
	}

	/**
	 * moveToState
	 *
	 * @param   int  $published  Param
	 *
	 * @return	string
	 */
	public function moveToState($published)
	{
		JLoader::register('RequestHelp', JPATH_AUTOTWEET_HELPERS . '/request.php');

		if (is_array($this->id_list) && !empty($this->id_list))
		{
			if (empty($user))
			{
				$oUser = JFactory::getUser();
				$userid = $oUser->id;
			}

			if (!RequestHelp::moveToState($this->id_list, $userid, $published))
			{
				$this->setError('Requests::moveToState failed');

				return false;
			}
		}

		return true;
	}

	/**
	 * purge
	 *
	 * @return  boolean True on success
	 */
	public function purge()
	{
		$table = $this->getTable($this->table);
		$table->purge();

		return true;
	}
}
