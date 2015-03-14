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
 * AutotweetModelChannels
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelChannels extends F0FModel
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

		$query = F0FQueryAbstract::getNew($db)->select('*')->from($db->quoteName('#__autotweet_channels'));

		$fltName = $this->getState('name', null, 'string');

		if ($fltName)
		{
			$fltName = "%$fltName%";
			$query->where($db->qn('name') . ' LIKE ' . $db->q($fltName));
		}

		$fltChannelId = $this->getState('channel_id', null, 'int');

		if ($fltChannelId)
		{
			$query->where($db->qn('id') . ' = ' . $db->q($fltChannelId));
		}

		$fltCreatedBy = $this->getState('created_by', null, 'int');

		if ($fltCreatedBy)
		{
			$query->where($db->qn('created_by') . ' = ' . $db->q($fltCreatedBy));
		}

		$fltChanneltype = $this->getState('channeltype', null, 'int');

		if ($fltChanneltype)
		{
			$query->where($db->qn('channeltype_id') . ' = ' . $db->q($fltChanneltype));
		}

		$exclude_channeltypes = $this->getState('exclude_channeltypes', null, 'array');

		if (($exclude_channeltypes) && (!empty($exclude_channeltypes)))
		{
			$exclude_channeltypes = implode(',', $exclude_channeltypes);
			$query->where($db->qn('channeltype_id') . ' not in (' . $exclude_channeltypes . ')');
		}

		$fltPublished = $this->getState('published', 1, 'cmd');

		if (($fltPublished != '') && ($fltPublished != 'nofilter'))
		{
			$query->where($db->qn('published') . ' = ' . $db->q($fltPublished));
		}

		$search = $this->getState('search', null);

		if ($search)
		{
			$search = '%' . $search . '%';
			$query->where('(' . $db->qn('name') . ' LIKE ' . $db->quote($search) . ' OR ' . $db->qn('description') . ' LIKE ' . $db->quote($search) . ')');
		}

		$fltScope = $this->getState('scope', 'S', 'string');

		if (($fltScope) && ($fltScope != 'N'))
		{
			$query->where($db->qn('scope') . ' = ' . $db->q($fltScope));
		}

		$fltFrontendChannel = $this->getState('frontendchannel', null, 'int');

		if ($fltFrontendChannel)
		{
			$fltFrontendChannel = '"frontendchannel":"' . $fltFrontendChannel . '"';
			$query->where($db->qn('params') . ' like ' . $db->q('%' . $fltFrontendChannel . '%'));
		}
		else
		{
			AclPermsHelper::whereOwnership($query);
		}

		$order = $this->getState('filter_order', 'id', 'cmd');

		if (!in_array($order, array_keys($this->getTable()->getData())))
		{
			$order = 'id';
		}

		$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
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
		// Check for unique frontendchannel / channeltype
		if ($data['xtform']['frontendchannel'])
		{
			$channel = F0FModel::getTmpInstance('Channels', 'AutotweetModel');
			$channel->setState('frontendchannel', 1);
			$channel->setState('channeltype', $data['channeltype_id']);
			$frontChannels = $channel->getItemList(true);

			if ((count($frontChannels) > 0) && ($frontChannels[0]->id != $data['id']))
			{
				$this->setError(JText::_('COM_AUTOTWEET_CHANNEL_ERR_ALREADY_FRONTENDCHANNELTYPE'));

				return false;
			}
		}

		EForm::onBeforeSaveWithParams($data);

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
	 * getQueryMain.
	 *
	 * @param   string  $db             Params
	 * @param   string  $table_channel  Params
	 * @param   string  $table_type     Params
	 *
	 * @return	string
	 */
	private static function getQueryMain($db, $table_channel, $table_type)
	{
		$query = 'SELECT' . ' c.' . $db->quoteName('id') . ' AS id,' . ' c.' . $db->quoteName('name') . ' AS name,' . ' c.' . $db->quoteName('published') . ' AS published,' . ' c.' . $db->quoteName('autopublish') . ' AS autopublish,' . ' c.' . $db->quoteName('media_mode') . ' AS media_mode,' . ' c.' . $db->quoteName('description') . ' AS description,' . ' t.' . $db->quoteName('name') . ' AS type,' . ' t.' . $db->quoteName('max_chars') . ' AS max_chars,' . ' c.' . $db->quoteName('id_1') . ' AS id_1,' . ' c.' . $db->quoteName('id_1_secret') . ' AS id_1_secret,' . ' c.' . $db->quoteName('id_2') . ' AS id_2,' . ' c.' . $db->quoteName('id_2_secret') . ' AS id_2_secret,' . ' c.' . $db->quoteName('selection') . ' AS selection,' . ' c.' . $db->quoteName('other') . ' AS other,' . ' c.' . $db->quoteName('use_own_api') . ' AS use_own_api,' . ' c.' . $db->quoteName('api_key') . ' AS api_key,' . ' c.' . $db->quoteName('api_secret') . ' AS api_secret,' . ' c.' . $db->quoteName('api_authurl') . ' AS api_authurl,' . ' c.' . $db->quoteName('api_other') . ' AS api_other,' . ' t.' . $db->quoteName('auth_url') . ' AS auth_url,' . ' t.' . $db->quoteName('auth_key') . ' AS auth_key,' . ' t.' . $db->quoteName('auth_secret') . ' AS auth_secret' . ' FROM ' . $db->quoteName($table_channel) . ' c, ' . $db->quoteName($table_type) . ' t';

		return $query;
	}

	/**
	 * getQueryAll.
	 *
	 * @param   string  $db             Params
	 * @param   string  $table_channel  Params
	 * @param   string  $table_type     Params
	 *
	 * @return	string
	 */
	public static function getQueryAll($db, $table_channel, $table_type)
	{
		$query = self::getQueryMain($db, $table_channel, $table_type) . ' WHERE c.' . $db->quoteName('published') . ' = 1' . ' AND c.' . $db->quoteName('channeltype_id') . ' = t.' . $db->quoteName('id');

		return $query;
	}

	/**
	 * getQueryAll.
	 *
	 * @param   int     $id             Params
	 * @param   string  $db             Params
	 * @param   string  $table_channel  Params
	 * @param   string  $table_type     Params
	 *
	 * @return	string
	 */
	public static function getQueryEntity($id, $db, $table_channel, $table_type)
	{
		$query = self::getQueryMain($db, $table_channel, $table_type) . ' WHERE c.' . $db->quoteName('id') . ' = ' . (int) $id . ' AND c.' . $db->quoteName('channeltype_id') . ' = t.' . $db->quoteName('id');

		return $query;
	}

	/**
	 * getChannelTypes
	 *
	 * @param   array  &$channels  Params
	 *
	 * @return	array
	 */
	public static function getChannelTypes(&$channels)
	{
		$retid = function($o) {
			return $o->channeltype_id;
		};

		$ids = array_map($retid, $channels);
		$ids = array_unique($ids);
		sort($ids);

		return $ids;
	}
}
