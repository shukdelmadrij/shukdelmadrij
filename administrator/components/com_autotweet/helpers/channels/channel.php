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
 * ChannelHelper class.
 *
 * Base class for AutoTweet channels like Twitter of Facebook.
 * Works also as interface for usage.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class ChannelHelper
{
	protected $channel;

	protected $cached_max_chars = null;

	protected $has_tmp_file = false;

	/**
	 * sendMessage.
	 *
	 * @param   string  $message  Params
	 * @param   object  $data     Params
	 *
	 * @return  boolean
	 */
	abstract public function sendMessage($message, $data);

	/**
	 * ChannelHelper.
	 *
	 * @param   object  &$ch  Params
	 */
	public function __construct(&$ch)
	{
		$channel = clone $ch;

		if ( (property_exists($channel, 'params')) && (is_string($channel->params)) )
		{
			$params = $channel->params;
			unset($channel->params);

			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($params);
			$channel->params = $registry;
		}
		elseif ( (isset($channel->xtform)) && (is_object($channel->xtform)) )
		{
			$params = $channel->xtform;
			unset($channel->xtform);

			$channel->params = $params;
		}
		else
		{
			throw new Exception('Invalid channel!');
		}

		$this->channel = $channel;
	}

	/**
	 * getChannelId
	 *
	 * @return  int
	 */
	public function getChannelId()
	{
		return $this->channel->id;
	}

	/**
	 * getChannelType
	 *
	 * @return  string
	 */
	public function getChannelType()
	{
		return $this->channel->channeltype_id;
	}

	/**
	 * getChannelType
	 *
	 * @return  string
	 */
	public function getChannelName()
	{
		return $this->channel->name;
	}

	/**
	 * getChannelDesc
	 *
	 * @return  string
	 */
	public function getChannelDesc()
	{
		return $this->channel->description;
	}

	/**
	 * isAutopublish
	 *
	 * @return  bool
	 */
	public function isAutopublish()
	{
		return $this->channel->autopublish;
	}

	/**
	 * isPublished
	 *
	 * @return  bool
	 */
	public function isPublished()
	{
		return $this->channel->published;
	}

	/**
	 * getMediaMode
	 *
	 * @return  int
	 */
	public function getMediaMode()
	{
		return $this->channel->media_mode;
	}

	/**
	 * get
	 *
	 * @param   string  $property  Params.
	 * @param   mixed   $default   Params.
	 *
	 * @return  mixed
	 */
	public function get($property, $default = null)
	{
		return $this->channel->params->get($property, $default);
	}

	/**
	 * getMaxChars
	 *
	 * @return  int
	 */
	public function getMaxChars()
	{
		if ($this->cached_max_chars)
		{
			return $this->cached_max_chars;
		}
		else
		{
			$channeltype = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')->getTable();
			$channeltype->reset();
			$channeltype->load($this->channel->channeltype_id);
			$this->cached_max_chars = $channeltype->max_chars;

			return $channeltype->max_chars;
		}
	}

	/**
	 * loadImage
	 *
	 * @param   string  $image_url  Param
	 *
	 * @return	string
	 */
	protected function loadImage($image_url)
	{
		$logger = AutotweetLogger::getInstance();
		$this->has_tmp_file = false;

		if (F0FPlatform::getInstance()->isCli())
		{
			$routeHelp = RouteHelp::getInstance();
			$base_url = $routeHelp->getRoot() . '/';
		}
		else
		{
			$base_url = JUri::root();
		}

		$imagefile = str_replace($base_url, JPATH_ROOT . '/', $image_url);

		// External Image? Download it into a tmp file, just to post it
		if (!is_file($imagefile))
		{
			$imagefile = JInstallerHelper::downloadPackage($image_url);

			if ($imagefile)
			{
				$this->has_tmp_file = true;

				$config = JFactory::getConfig();
				$imagefile = $config->get('tmp_path') . '/' . $imagefile;
			}
			else
			{
				$logger->log(JLog::ERROR, '_loadImage: Error ' . $image_url);

				return null;
			}
		}

		$logger->log(JLog::INFO, '_loadImage: ' . $imagefile);

		list($width, $height, $type, $attr) = getimagesize($imagefile);
		$logger->log(JLog::INFO, "_getimagesize: ({$width}, {$height}, {$type}, {$attr})");

		$image_minx = EParameter::getComponentParam(CAUTOTWEETNG, 'image_minx', 100);

		if ($width < $image_minx)
		{
			$this->cleanTmpImage($imagefile);

			return null;
		}

		$image_miny = EParameter::getComponentParam(CAUTOTWEETNG, 'image_miny', 0);

		if ($height < $image_miny)
		{
			$this->cleanTmpImage($imagefile);

			return null;
		}

		return $imagefile;
	}

	/**
	 * cleanTmpImage
	 *
	 * @param   string  $imagefile  Param
	 *
	 * @return	void
	 */
	protected function cleanTmpImage($imagefile)
	{
		$config = JFactory::getConfig();
		$tmppath = $config->get('tmp_path');

		// Double check
		if (($this->has_tmp_file) && (strpos($imagefile, $tmppath) == 0))
		{
			JFile::delete($imagefile);
		}
	}

	/**
	 * hasWeight
	 *
	 * @return	bool
	 */
	public function hasWeight()
	{
		return false;
	}

	/**
	 * getChannelId
	 *
	 * @return  int
	 */
	public function getTargetId()
	{
		return $this->channel->params->get('target_id');
	}

	/**
	 * includeHashTags
	 *
	 * @return  bool
	 */
	public function includeHashTags()
	{
		return $this->channel->params->get('hashtags', false);
	}
}
