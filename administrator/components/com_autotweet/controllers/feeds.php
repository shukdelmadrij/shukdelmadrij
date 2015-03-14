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

include_once 'default.php';

/**
 * AutotweetControllerFeeds
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerFeeds extends AutotweetControllerDefault
{
	/**
	 * import.
	 *
	 * @return	void
	 */
	public function import()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$cid = $this->input->get('cid', array(), 'array');

		if (empty($cid))
		{
			$id = $this->input->getInt('id', 0);

			if ($id)
			{
				$cid = array($id);
			}
		}

		$helper = FeedLoaderHelper::getInstance();
		$helper->importFeeds($cid);

		// Redirect
		if ($customURL = $this->input->get('returnurl', '', 'string'))
		{
			$customURL = base64_decode($customURL);
		}

		$url = !empty($customURL) ? $customURL : 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);
		$this->setRedirect($url);

		ELog::showMessage('COM_AUTOTWEET_VIEW_FEEDS_IMPORT_SUCCESS', JLog::INFO);
	}

	/**
	 * getImportBegin
	 *
	 * @return	void
	 */
	public function getImportBegin()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$message = array(
						'status' => false,
						'error_message' => JText::_('COM_AUTOTWEET_VIEW_FEEDS_IMPORT_FAILED_ERR')
		);

		try
		{
			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::INFO, "getImportBegin");

			$feedsModel = F0FModel::getTmpInstance('Feeds', 'AutoTweetModel');
			$feedsModel->set('published', 1);
			$feedsModel->set('filter_order', 'id');
			$feeds = $feedsModel->getItemList(true);

			$message = $this->_getImportBeginMessage($feeds);
		}
		catch (Exception $e)
		{
			$message['status'] = false;
			$message['error_message'] .= ' Start ' . $e->getMessage();
		}

		$message = json_encode($message);
		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getImportStatus
	 *
	 * @return	void
	 */
	public function getImportStatus()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$message = array(
						'status' => false,
						'error_message' => JText::_('COM_AUTOTWEET_VIEW_FEEDS_IMPORT_FAILED_ERR')
		);

		try
		{
			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::INFO, "getImportStatus");

			$feed_id = $this->input->get('feedId', null, 'int');
			$continue = $this->input->get('isContinue', 0, 'int');

			$cid = array($feed_id);
			$helper = FeedLoaderHelper::getInstance();
			$result = $helper->importFeeds($cid);

			$message = array(
							'status' => true,
							'completed' => true
			);

			$message['total'] = number_format($result);
		}
		catch (Exception $e)
		{
			$message['status'] = false;
			$message['error_message'] .= ' Status ' . $e->getMessage();
		}

		$message = json_encode($message);
		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * getImportBegin
	 *
	 * @return	void
	 */
	public function getImportEnd()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$message = array(
						'status' => false,
						'error_message' => JText::_('COM_AUTOTWEET_VIEW_FEEDS_IMPORT_FAILED_ERR')
		);

		try
		{
			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::INFO, "getImportEnd");

			$message = array(
							'status' => true,
							'error_message' => 'ok'
			);
		}
		catch (Exception $e)
		{
			$message['status'] = false;
			$message['error_message'] .= ' End ' . $e->getMessage();
		}

		$message = json_encode($message);
		echo EJSON_START . $message . EJSON_END;
		flush();

		JFactory::getApplication()->close();
	}

	/**
	 * _getImportBeginMessage
	 *
	 * @param   array  $feeds  Params
	 *
	 * @return	object
	 */
	private function _getImportBeginMessage($feeds)
	{
		$message = array(
						'status' => true,
						'error_message' => 'Ok'
		);

		$results = array();

		foreach ($feeds as $feed)
		{
			// $object =

			$result = new StdClass;
			$result->id = $feed->id;
			$result->name = $feed->name;

			$results[] = $result;
		}

		$message['feeds'] = $results;

		return $message;
	}
}
