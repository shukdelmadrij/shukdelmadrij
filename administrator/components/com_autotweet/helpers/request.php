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
 * Helper for posts form AutoTweet to channels (twitter, Facebook, ...)
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class RequestHelp
{
	/**
	 * queueMessage
	 *
	 * @param   string  $articleid        Param
	 * @param   string  $source_plugin    Param
	 * @param   string  $publish_up       Param
	 * @param   string  $description      Param
	 * @param   string  $typeinfo         Param
	 * @param   string  $url              Param
	 * @param   string  $image_url        Param
	 * @param   object  &$native_object   Param
	 * @param   string  &$advanced_attrs  Param
	 * @param   string  &$params          Param
	 *
	 * @return	mixed - false, or id of request
	 */
	public static function insertRequest($articleid, $source_plugin, $publish_up, $description, $typeinfo = 0, $url = '', $image_url = '', &$native_object = null, &$advanced_attrs = null, &$params = null)
	{
		$logger = AutotweetLogger::getInstance();

		// Check if message is already queued (it makes no sense to queue message more than once when modfied)
		// if message is already queued, correct the publish date

		$requestsModel = F0FModel::getTmpInstance('Requests', 'AutoTweetModel');
		$requestsModel->set('ref_id', $articleid);
		$requestsModel->set('plugin', $source_plugin);
		$requestsModel->set('typeinfo', $typeinfo);
		$row = $requestsModel->getFirstItem();

		$id = $row->id;

		// Avoid databse warnings when desc is longer then expected
		if (!empty($description))
		{
			$description = JString::substr($description, 0, SharingHelper::MAX_CHARS_TITLE * 2);
		}

		$routeHelp = RouteHelp::getInstance();
		$url = $routeHelp->getAbsoluteUrl($url);

		if (empty($image_url))
		{
			// Default image: used in media mode when no image is available
			$image_url = EParameter::getComponentParam(CAUTOTWEETNG, 'default_image', '');
		}

		if (!empty($image_url))
		{
			$image_url = $routeHelp->getAbsoluteUrl($image_url, true);
		}

		$row->reset();

		if ($id)
		{
			$row->load($id);
		}

		// If there's no date, it means now
		if (empty($publish_up))
		{
			$publish_up = JFactory::getDate()->toSql();
		}

		$request = array(
						'id' => $id,
						'ref_id' => $articleid,
						'plugin' => $source_plugin,
						'publish_up' => $publish_up,
						'description' => $description,
						'typeinfo' => $typeinfo,
						'url' => $url,
						'image_url' => $image_url,
						'native_object' => $native_object,
						'params' => $params,
						'published' => 0
		);

		$logger->log(JLog::INFO, 'Enqueued request', $request);

		// Saving the request
		$queued = $row->save($request);

		if (!$queued)
		{
			$logger->log(JLog::ERROR, 'queueMessage: error storing message to database message queue, article id = ' . $articleid . ', error message = ' . $row->getError());
		}
		else
		{
			$logger->log(JLog::INFO, 'queueMessage: message stored/updated to database message queue, article id = ' . $articleid);
		}

		if (!$id)
		{
			$id = $row->id;
		}

		if (($advanced_attrs) && isset($advanced_attrs->attr_id))
		{
			$row = F0FModel::getTmpInstance('Advancedattrs', 'AutoTweetModel')->getTable();
			$row->reset();
			$row->load($advanced_attrs->attr_id);

			$attr = array(
				'id' => $advanced_attrs->attr_id,
				'request_id' => $id,
			);

			// Updating attr
			$result = $row->save($attr);

			if (!$result)
			{
				$logger->log(JLog::ERROR, 'Updating attr, attr_id = ' . $advanced_attrs->attr_id . ', error message = ' . $row->getError());
			}
			else
			{
				$logger->log(JLog::INFO, 'Updating attr, attr_id = ' . $advanced_attrs->attr_id);
			}
		}

		return ($queued ? $id : false);
	}

	/**
	 * processRequests
	 *
	 * @param   array  $rids  Param
	 *
	 * @return	boolean
	 */
	public static function processRequests($rids)
	{
		$requestsModel = F0FModel::getTmpInstance('Requests', 'AutoTweetModel');
		$requestsModel->set('rids', $rids);
		$requests = $requestsModel->getItemList(true);

		return self::publishRequests($requests);
	}

	/**
	 * publishRequests
	 *
	 * @param   array  &$requests  Param
	 *
	 * @return	boolean
	 */
	public static function publishRequests(&$requests)
	{
		JLoader::register('SharingHelper', JPATH_AUTOTWEET_HELPERS . '/sharing.php');

		$sharinghelper = SharingHelper::getInstance();

		foreach ($requests as $request)
		{
			try
			{
				if ($sharinghelper->publishRequest($request))
				{
					// Remove only, when post is logged successfully
					self::processed($request->id);
				}
				else
				{
					self::saveError($request->id);
				}
			}
			catch (Exception $e)
			{
				$message = $e->getMessage();
				self::saveError($request->id, $message);
			}
		}

		return true;
	}

	/**
	 * processed
	 *
	 * @param   int  $id  Param
	 *
	 * @return	boolean
	 */
	public static function processed($id)
	{
		$request = F0FModel::getTmpInstance('Requests', 'AutotweetModel')->getTable();
		$request->reset();

		if (!$request->load($id))
		{
			return;
		}

		// Native Object
		if (isset($request->native_object))
		{
			$nativeObject = json_decode($request->native_object);
		}
		else
		{
			$nativeObject = new StdClass;
		}

		$nativeObject->error = false;
		$nativeObject->error_message = 'Ok!';

		// It's processed
		$data = array();
		$data['published'] = true;
		$data['native_object'] = json_encode($nativeObject);

		// Saving
		if (!AUTOTWEETNG_JOOCIAL)
		{
			$request->save($data);

			return;
		}

		AdvancedattrsHelper::execute($id, $data);

		$request->save($data);
	}

	/**
	 * saveError
	 *
	 * @param   int     $id       Param
	 * @param   string  $message  Param
	 *
	 * @return	boolean
	 */
	public static function saveError($id, $message = null)
	{
		$request = F0FModel::getTmpInstance('Requests', 'AutotweetModel')->getTable();
		$request->reset();

		if ($request->load($id))
		{
			$nativeObject = json_decode($request->native_object);

			$nativeObject->error = true;

			if ($message)
			{
				$nativeObject->error_message = $message;
			}
			else
			{
				$nativeObject->error_message = 'COM_AUTOTWEET_ERROR_PROCESSING';
			}

			$data = array();
			$data['native_object'] = json_encode($nativeObject);
			$data['published'] = true;

			$request->save($data);
		}
	}

	/**
	 * getRequestList
	 *
	 * @param   JDate  $check_date  Param
	 * @param   int    $limit       Param
	 *
	 * @return	array
	 */
	public static function getRequestList($check_date, $limit)
	{
		$requestsModel = F0FModel::getTmpInstance('Requests', 'AutoTweetModel');
		$requestsModel->set('until_date', $check_date->toSql());
		$requestsModel->set('filter_order', 'publish_up');
		$requestsModel->set('filter_order_Dir', 'ASC');
		$requestsModel->set('limit', $limit);

		return $requestsModel->getItemList();
	}

	/**
	 * moveToState - This static function should used from backend only for manual (re)posting attempts
	 *
	 * @param   array   $ids        Param
	 * @param   int     $userid     Param
	 * @param   string  $published  Param
	 *
	 * @return	boolean
	 */
	public static function moveToState($ids, $userid = null, $published = null)
	{
		$success = true;

		if (!$pubstate)
		{
			$pubstate = 0;
		}

		if (count($ids) > 0)
		{
			$idslist = implode(',', $ids);
			$idslist = '(' . $idslist . ')';

			$now = JFactory::getDate();

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->update('#__autotweet_requests')
				->set($db->qn('published') . ' = ' . $db->q($published))
				->set($db->qn('modified') . ' = ' . $db->q($now->toSql()))
				->set($db->qn('modified_by') . ' = ' . $db->q($userid))
				->where($db->qn('id') . ' IN ' . $idslist);

			$db->setQuery($query);
			$db->execute();
		}

		return $success;
	}
}
