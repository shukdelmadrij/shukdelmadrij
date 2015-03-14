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
 * Factory to create AutoTweet plugin classes.
 * This is the central point to get and handle plugin classes. Also all needed files are included here.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelPlugins extends F0FModel
{
	/**
	 * buildQuery / Get all plugins
	 *
	 * @param   bool  $overrideLimits  Param
	 *
	 * @return	F0FQuery
	 */
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();

		$element_id = $this->getState('element_id', null, 'string');
		$extension_plugins_only = $this->getState('extension_plugins_only', false, 'int');
		$published_only = $this->getState('published_only', false, 'int');

		// Plugins QUERY
		$query = F0FQueryAbstract::getNew($db)->select($db->quoteName('extension_id') . ' as ' . $db->quoteName('id'))->select($db->quoteName('name'))->select($db->quoteName('element'))->select($db->quoteName('folder'))->from($db->quoteName('#__extensions'))->where($db->quoteName('element') . ' like ' . $db->Quote('%autotweet%'))->where($db->quoteName('type') . ' = ' . $db->Quote('plugin'));

		if ($extension_plugins_only)
		{
			$query->where($db->quoteName('element') . ' <> ' . $db->Quote('autotweetautomator'));
		}

		if ($published_only)
		{
			$query->where($db->quoteName('enabled') . ' = ' . $db->Quote('1'));
		}

		if ($element_id)
		{
			$query->where($db->quoteName('element') . ' = ' . $db->Quote($element_id));
		}

		$query->order($db->quoteName('element') . ' ASC');

		return $query;
	}

	/**
	 * createPlugin
	 * typeinfo:	only needed when 2 different types of messages are returned (see Kunena plugin)
	 *
	 * @param   string  $type  Param
	 *
	 * @return	object
	 */
	public function createPlugin($type)
	{
		$result = null;

		// AutoTweet standard Joomla plugins
		$this->set('element_id', $type);
		$items = $this->getList();

		if (count($items) == 1)
		{
			$plugin = $items[0];
		}
		else
		{
			return null;
		}

		$className = 'plg' . $plugin->folder . $type;

		$dispatcher = JDispatcher::getInstance();

		if (!class_exists($className))
		{
			JPluginHelper::importPlugin($plugin->folder, $type, true, $dispatcher);
		}

		if (class_exists($className))
		{
			$plugin_data = JPluginHelper::getPlugin($plugin->folder, strtolower($type));
			$dispatcher = JDispatcher::getInstance();
			$result = new $className($dispatcher, (array) ($plugin_data));
		}

		return $result;
	}

	/**
	 * Additional service functions
	 *
	 * @param   array  &$plugins  Param
	 *
	 * @return	void
	 */
	public function loadLanguages(&$plugins)
	{
		$jlang = JFactory::getLanguage();

		foreach ($plugins as $plugin)
		{
			$jlang->load($plugin['name']);
		}
	}

	/**
	 * getSimpleName
	 *
	 * @param   string  $element  Param
	 *
	 * @return	object
	 */
	public static function getSimpleName($element)
	{
		if ($element === 'autotweetpost')
		{
			return JText::_('COM_AUTOTWEET_NAME');
		}
		else
		{
			return ucfirst(str_replace('autotweet', '', $element));
		}
	}
}
