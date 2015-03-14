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
 * AutotweetControllerFeedArticles
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerFeedArticles extends F0FController
{
	private $_model = null;

	/**
	 * saveArticle
	 *
	 * @return  void
	 */
	public function saveArticle()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		include_once JPATH_COMPONENT_ADMINISTRATOR . '/models/article.php';
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_content/tables');

		$task = 'save';
		$model = JModelLegacy::getInstance('article', 'contentModel', array(
						'ignore_request' => true
			)
		);
		$this->_model = $model;

		$model->setState('task', $task);

		$table = $model->getTable();
		$data = $this->_getData();
		$checkin = property_exists($table, 'checked_out');

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		$recordId = $data['id'];

		// Populate the row id from the session.
		$data [$key] = $recordId;

		// Validate the posted data.

		// Sometimes the form needs some posted data, such as for plugins and modules.
		// Get the form.
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_content/models/forms');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_content/models/fields');
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_content/model/form');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_content/model/field');
		$form = $model->getForm($data, false);

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Not validated, but used in store
		$validData ['state'] = $data ['state'];
		$validData ['featured'] = $data ['featured'];

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			return false;
		}

		if (!isset($validData ['tags']))
		{
			$validData ['tags'] = null;
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			return false;
		}

		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData [$key]) === false)
		{
			return false;
		}

		return true;
	}

	/**
	 * onAfterSaveArticle
	 *
	 * @return  bool
	 */
	public function onAfterSaveArticle()
	{
		$error = $this->_model->getError();

		if ($error)
		{
			throw new Exception($error);
		}

		return true;
	}

	/**
	 * _getData
	 *
	 * @return  array
	 */
	private function _getData()
	{
		$article = $this->input->get('article', null, 'raw');
		$data = array(
						'id' => $article->id,
						'title' => $article->title,
						'catid' => $article->cat_id,
						'articletext' => $article->introtext,
						'images' => array(),
						'urls' => array(),
						'alias' => $article->alias,
						'created_by' => $article->created_by,
						'created_by_alias' => $article->created_by_alias,
						'created' => $article->created,
						'publish_up' => $article->publish_up,
						'publish_down' => $article->publish_down,
						'modified_by' => null,
						'modified' => null,
						'version' => null,
						'attribs' => array(),
						'metadesc' => null,
						'metakey' => $article->metakey,
						'xreference' => null,
						'metadata' => array(),
						'rules' => array(),
						'state' => $article->state,
						'access' => $article->access,
						'featured' => $article->featured,
						'language' => $article->language,
						'xreference' => $article->hash
		);

		return $data;
	}
}
