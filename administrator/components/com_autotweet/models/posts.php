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
 * AutotweetModelPosts
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelPosts extends F0FModel
{
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

		$query = F0FQueryAbstract::getNew($db)->select('*')->from($db->quoteName('#__autotweet_posts'));

		$fltPostdate = $this->getState('postdate', null, 'date');

		if ($fltPostdate)
		{
			$fltPostdate = $fltPostdate . '%';
			$query->where($db->qn('postdate') . ' LIKE ' . $db->q($fltPostdate));
		}

		$fltAfterDate = $this->getState('after_date', null, 'date');

		if ($fltAfterDate)
		{
			$query->where($db->qn('postdate') . ' >= ' . $db->q($fltAfterDate));
		}

		$fltChannel = $this->getState('channel', null, 'int');

		if ($fltChannel)
		{
			$query->where($db->qn('channel_id') . ' = ' . $db->q($fltChannel));
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

		$fltNotId = $this->getState('not_id', null, 'string');

		if ($fltNotId)
		{
			$query->where($db->qn('id') . ' <> ' . $db->q($fltNotId));
		}

		$fltMessage = $this->getState('message', null, 'string');

		if ($fltMessage)
		{
			$query->where($db->qn('message') . ' = ' . $db->q($fltMessage));
		}

		$fltPubstate = $this->getState('pubstate', null);

		if (is_array($fltPubstate))
		{
			if (count($fltPubstate) > 0)
			{
				$list = array();

				foreach ($fltPubstate as $pubstate)
				{
					$list[] = $db->q($pubstate);
				}

				$fltPubstate = implode(',', $list);
				$query->where($db->qn('pubstate') . ' IN (' . $fltPubstate . ')');
			}
		}
		else
		{
			if ($fltPubstate != '')
			{
				$query->where($db->qn('pubstate') . ' = ' . $db->q($fltPubstate));
			}
		}

		$fltPubstates = $this->getState('pubstates', null, 'string');

		if ($fltPubstates != '')
		{
			$fltPubstates = TextUtil::listToArray($fltPubstates);
			$list = array();

			foreach ($fltPubstates as $p)
			{
				$list[] = $db->q($p);
			}

			$fltPubstates = implode(',', $list);
			$query->where($db->qn('pubstate') . ' IN (' . $fltPubstates . ')');
		}

		$search = $this->getState('search', null);

		if ($search)
		{
			$search = '%' . $search . '%';
			$query->where('(' . $db->qn('id') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('ref_id') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('resultmsg') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('message') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('title') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('url') . ' LIKE ' . $db->quote($search) . ')');
		}

		AclPermsHelper::whereOwnership($query);

		$order = $this->getState('filter_order', 'postdate', 'cmd');

		if (!in_array($order, array_keys($this->getTable()->getData())))
		{
			$order = 'postdate';
		}

		$dir = $this->getState('filter_order_Dir', 'DESC', 'cmd');
		$query->order($order . ' ' . $dir);

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
		$data['postdate'] = EParameter::convertLocalUTC($data['postdate']);

		// Cleaning annoying spaces
		$data = array_map('trim', $data);

		return parent::onBeforeSave($data, $table);
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
	 * approve
	 *
	 * @return	bool
	 */
	public function approve()
	{
		JLoader::register('PostHelper', JPATH_AUTOTWEET_HELPERS . '/post.php');

		if (is_array($this->id_list) && !empty($this->id_list))
		{
			if (empty($user))
			{
				$oUser = JFactory::getUser();
				$userid = $oUser->id;
			}

			if (!PostHelper::publishPosts($this->id_list, $userid))
			{
				$this->setError('approve - postMessages failed');

				return false;
			}
		}

		return true;
	}

	/**
	 * cancel
	 *
	 * @return	bool
	 */
	public function cancel()
	{
		JLoader::register('PostHelper', JPATH_AUTOTWEET_HELPERS . '/post.php');

		if (is_array($this->id_list) && !empty($this->id_list))
		{
			if (empty($user))
			{
				$oUser = JFactory::getUser();
				$userid = $oUser->id;
			}

			if (!PostHelper::moveToState($this->id_list, $userid, 'cancel'))
			{
				$this->setError('cancel - postMessages failed');

				return false;
			}
		}

		return true;
	}

	/**
	 * moveToState
	 *
	 * @param   int  $pubstate  Param
	 *
	 * @return	bool
	 */
	public function moveToState($pubstate)
	{
		JLoader::register('PostHelper', JPATH_AUTOTWEET_HELPERS . '/post.php');

		if (is_array($this->id_list) && !empty($this->id_list))
		{
			if (empty($user))
			{
				$oUser = JFactory::getUser();
				$userid = $oUser->id;
			}

			if (!PostHelper::moveToState($this->id_list, $userid, $pubstate))
			{
				$this->setError('moveToState - postMessages failed');

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
