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
class FeedLoaderHelper
{
	private static $_instance = null;

	private $_logger = null;

	/**
	 * FeedLoaderHelper. No public access (singleton pattern).
	 *
	 */
	protected function __construct()
	{
		$this->_logger = AutotweetLogger::getInstance();
	}

	/**
	 * getInstance
	 *
	 * @return	Instance
	 */
	public static function &getInstance()
	{
		if (!self::$_instance)
		{
			include_once 'feedimporter.php';
			include_once 'feedgenerator.php';
			include_once 'feedprocessor.php';

			self::$_instance = new FeedLoaderHelper;
		}

		return self::$_instance;
	}

	/**
	 * getPreview
	 *
	 * @param   object  &$feed  Params
	 *
	 * @return	object
	 */
	public function getPreview(&$feed)
	{
		if (isset($feed->params))
		{
			$feed->xtform = EForm::paramsToRegistry($feed);
		}

		$import_limit = $feed->xtform->get('import_limit');
		$feed->xtform->set('import_limit', 3);

		$check_existing = $feed->xtform->get('check_existing');
		$feed->xtform->set('check_existing', 0);

		$loadResult = null;

		try
		{
			$start_time = time();

			$feedImporterHelper = new FeedImporterHelper;
			$feedProcessorHelper = new FeedProcessorHelper;
			$feedGeneratorHelper = new FeedGeneratorHelper;

			$loadResult = $feedImporterHelper->import($feed);
			$contents = $feedProcessorHelper->process($feed, $loadResult);
			$feedGeneratorHelper->execute($contents, $feed->xtform);

			$loadResult->processed_time = time() - $start_time;
			$loadResult->preview = $contents;
		}
		catch (Exception $e)
		{
			ELog::showMessage($e->getMessage(), JLog::ERROR);
		}

		$feed->xtform->set('import_limit', $import_limit);
		$feed->xtform->set('check_existing', $check_existing);

		return $loadResult;
	}

	/**
	 * importFeeds
	 *
	 * @param   array  $cid  Params
	 *
	 * @return	int
	 */
	public function importFeeds($cid = array())
	{
		$init_date = JFactory::getDate()->format(JText::_('COM_AUTOTWEET_DATE_FORMAT'));
		$this->_logger->log(JLog::INFO, 'importFeeds: Starting ' . $init_date);

		// Allows importing images and text
		if (!ini_get('allow_url_fopen'))
		{
			ini_set('allow_url_fopen', 1);
		}

		$feedsModel = F0FModel::getTmpInstance('Feeds', 'AutoTweetModel');
		$feedsModel->set('published', 1);
		$feedsModel->set('ids', $cid);
		$feeds = $feedsModel->getItemList(true);

		$total_time = 0;
		$item_counter = 0;

		// Process each feed
		foreach ($feeds as $feed)
		{
			// Attempt to stop timeouts and errors stopping all imports in cron/pseudo-cron
			try
			{
				$start_time = time();
				$this->_logger->log(JLog::INFO, 'importFeeds: Feed ' . $feed->name . ' (' . $feed->id . ')');

				$loadResult = $this->importFeed($feed);

				$processed_time = time() - $start_time;

				$this->_logger->log(JLog::INFO, 'importFeeds: Items=' . $loadResult->added_items . ' Processed time ' . $processed_time . ' secs.');

				$total_time += $processed_time;
				$item_counter += $loadResult->added_items;
			}
			catch (Exception $e)
			{
				$this->_logger->log(JLog::ERROR, 'importFeeds: Exception! ' . $e->getMessage());
			}
		}

		$this->_logger->log(JLog::INFO, 'importFeeds: Total Items=' . $item_counter . ' Processed time ' . $total_time . ' secs.');

		return $item_counter;
	}

	/**
	 * importFeed
	 *
	 * @param   object  &$feed  Params
	 *
	 * @return	array
	 */
	public function importFeed(&$feed)
	{
		$noresult = new StdClass;
		$noresult->added_items = 0;

		$feedImporterHelper = new FeedImporterHelper;
		$feedProcessorHelper = new FeedProcessorHelper;
		$feedGeneratorHelper = new FeedGeneratorHelper;

		$loadResult = $feedImporterHelper->import($feed);

		if ((!isset($loadResult->items)) || (count($loadResult->items) == 0))
		{
			return $noresult;
		}

		$contents = $feedProcessorHelper->process($feed, $loadResult);

		if (count($contents) == 0)
		{
			return $noresult;
		}

		// Simple check for duplicates in feed contents
		$feedGeneratorHelper->removeDuplicates($contents);

		if (count($contents) == 0)
		{
			return $noresult;
		}

		$feedGeneratorHelper->execute($contents, $feed->xtform);

		$loadResult->added_items = $feedGeneratorHelper->save($contents, $feed->xtform);

		return $loadResult;
	}
}
