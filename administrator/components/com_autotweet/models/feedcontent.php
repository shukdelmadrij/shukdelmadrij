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
 * AutotweetModelFeedContents
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetModelFeedContent extends F0FModel
{
	/**
	 * Binds the data to the model and tries to save it
	 *
	 * @param   array|object  $data  The source data array or object
	 *
	 * @return  boolean  True on success
	 */
	public function save($data)
	{
		// Images
		$this->_loadImages($data);

		// Urls
		$this->_loadUrls($data);

		// Saving
		$status = $this->_save_fixed($data);

		if ($status)
		{
			$status = $this->_save_asset($data);
		}

		/* Save associations
		if (EXTLY_J3)
		{
			$status = $this->_saveAssociations();
		}
		*/

		return $status;
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
		$error = $this->getError();

		if ($error)
		{
			throw new Exception($error);
		}

		return true;
	}

	/**
	 * A method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @param   boolean  $source    The name of the form. If not set we'll try the form_name state variable or fall back to default.
	 *
	 * @return  mixed  A F0FForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true, $source = null)
	{
		$f0fPlatform = F0FPlatform::getInstance();
		$isFrontend = $f0fPlatform->isFrontend();

		$this->input->set('option', 'com_content');
		$this->input->set('view', $isFrontend ? 'form' : 'article');

		return parent::getForm($data, $loadData, $source);
	}

	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param   array    $pks    The ids of the items to toggle.
	 * @param   integer  $value  The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public static function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			throw new Exception(JText::_('COM_CONTENT_NO_ITEM_SELECTED'));
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__content'))
			->set('featured = ' . (int) $value)
			->where('id IN (' . implode(',', $pks) . ')');
		$db->setQuery($query);
		$db->execute();

		if ((int) $value == 0)
		{
			// Adjust the mapping table.
			// Clear the existing features settings.
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__content_frontpage'))
				->where('content_id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();
		}
		else
		{
			// First, we find out which of our new featured articles are already featured.

			$query = $db->getQuery(true)
				->select('f.content_id')
				->from('#__content_frontpage AS f')
				->where('content_id IN (' . implode(',', $pks) . ')');

			$db->setQuery($query);

			$old_featured = $db->loadColumn();

			// We diff the arrays to get a list of the articles that are newly featured

			$new_featured = array_diff($pks, $old_featured);

			// Featuring.
			$tuples = array();

			foreach ($new_featured as $pk)
			{
				$tuples[] = $pk . ', 0';
			}

			if (count($tuples))
			{
				$db = JFactory::getDbo();
				$columns = array('content_id', 'ordering');
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__content_frontpage'))
					->columns($db->quoteName($columns))
					->values($tuples);
				$db->setQuery($query);
				$db->execute();
			}
		}

		self::_reorder();
		self::_cleanCache();

		return true;
	}

	/**
	 * reorder
	 *
	 * @return  void
	 */
	private static function _reorder()
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_content/tables');
		$table = JTable::getInstance('Featured', 'ContentTable');
		$table->reorder();
	}

	/**
	 * Custom clean the cache of com_content and content modules
	 *
	 * @return  void
	 */
	private static function _cleanCache()
	{
		self::_cleanCacheGroup('com_content');
		self::_cleanCacheGroup('mod_articles_archive');
		self::_cleanCacheGroup('mod_articles_categories');
		self::_cleanCacheGroup('mod_articles_category');
		self::_cleanCacheGroup('mod_articles_latest');
		self::_cleanCacheGroup('mod_articles_news');
		self::_cleanCacheGroup('mod_articles_popular');
	}

	/**
	 * _loadImages
	 *
	 * @param   array  &$data  Param
	 *
	 * @return  void
	 */
	private function _loadImages(&$data)
	{
		if (isset($data['images']) && is_array($data['images']))
		{
			$registry = new JRegistry;
			$registry->loadArray($data['images']);
			$data['images'] = (string) $registry;
		}
	}

	/**
	 * _loadImages
	 *
	 * @param   array  &$data  Param
	 *
	 * @return  void
	 */
	private function _loadUrls(&$data)
	{
		if (isset($data['urls']) && is_array($data['urls']))
		{
			foreach ($data['urls'] as $i => $url)
			{
				if ($url != false && ($i == 'urla' || $i == 'urlb' || $i = 'urlc'))
				{
					if (class_exists('JStringPunycode'))
					{
						$data['urls'][$i] = JStringPunycode::urlToPunycode($url);
					}
					else
					{
						$data['urls'][$i] = $url;
					}
				}
			}

			$registry = new JRegistry;
			$registry->loadArray($data['urls']);
			$data['urls'] = (string) $registry;
		}
	}

	/**
	 * Clean the cache
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 */
	private static function _cleanCacheGroup($group, $client_id = 0)
	{
		$conf = JFactory::getConfig();

		$options = array(
						'defaultgroup' => $group,
						'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();
	}

	/**
	 * Binds the data to the model and tries to save it
	 *
	 * @param   array|object  $data  The source data array or object
	 *
	 * @return  boolean  True on success
	 */
	private function _save_fixed($data)
	{
		$this->otable = null;

		$table = $this->getTable($this->table);

		if (is_object($data))
		{
			$data = clone $data;
		}

		$key = $table->getKeyName();

		if (array_key_exists($key, (array) $data))
		{
			$aData = (array) $data;
			$oid = $aData[$key];
			$table->load($oid);
		}

		if ($data instanceof F0FTable)
		{
			$allData = $data->getData();
		}
		elseif (is_object($data))
		{
			$allData = (array) $data;
		}
		else
		{
			$allData = $data;
		}

		// Get the form if there is any
		$form = $this->getForm($allData, false);

		if ($form instanceof F0FForm)
		{
			// Make sure that $allData has for any field a key
			$fieldset = $form->getFieldset();

			/*
			 * THIS IS THE FIX
			 * https://github.com/akeeba/f0f/pull/167
			 */

			// Generated attribs_show_title
			// $keys = array_keys($fieldset);

			foreach ($fieldset as $nfield => $fldset)
			{
				if (!array_key_exists($nfield, $allData))
				{
					$field = $form->getField($fldset->fieldname, $fldset->group);
					$type  = strtolower($field->type);

					switch ($type)
					{
						case 'checkbox':
							$allData[$nfield] = 0;
							break;

						default:
							$allData[$nfield] = '';
							break;
					}
				}
			}

			// Force validation  ... remember onBeforeSave when removing _save_fixed
			$serverside_validate = true;

			// $serverside_validate = strtolower($form->getAttribute('serverside_validate'));

			$validateResult = true;

			if (in_array($serverside_validate, array('true', 'yes', '1', 'on')))
			{
				$validateResult = $this->validateForm($form, $allData);
			}

			if ($validateResult === false)
			{
				return false;
			}
		}

		if (!$this->onBeforeSave($allData, $table))
		{
			return false;
		}
		else
		{
			// If onBeforeSave successful, refetch the possibly modified data
			if ($data instanceof F0FTable)
			{
				$data->bind($allData);
			}
			elseif (is_object($data))
			{
				$data = (object) $allData;
			}
			else
			{
				$data = $allData;
			}
		}

		if (!$table->save($data))
		{
			foreach ($table->getErrors() as $error)
			{
				if (!empty($error))
				{
					$this->setError($error);
					$session = JFactory::getSession();
					$tableprops = $table->getProperties(true);
					unset($tableprops['input']);
					unset($tableprops['config']['input']);
					unset($tableprops['config']['db']);
					unset($tableprops['config']['dbo']);
					$hash = $this->getHash() . 'savedata';
					$session->set($hash, serialize($tableprops));
				}
			}

			return false;
		}
		else
		{
			$this->id = $table->$key;

			// Remove the session data
			JFactory::getSession()->set($this->getHash() . 'savedata', null);
		}

		$this->onAfterSave($table);

		$this->otable = $table;

		return true;
	}
}
