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
 * AutotweetControllerRequests
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerRequests extends AutotweetControllerDefault
{
	/**
	 * Public constructor of the Controller class
	 *
	 * @param   array  $config  Optional configuration parameters
	 */
	public function __construct($config = array())
	{
		// No JInputJSON in J2.5
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		if (($data) && (array_key_exists('ajax', $data)) && ($data['ajax'] === 1))
		{
			$input = new F0FInput;
			$param = array_merge($input->getData(), $data);
			$config['input'] = $param;
		}

		parent::__construct($config);
	}

	/**
	 * publish.
	 *
	 * @return	void
	 */
	public function publish()
	{
		$this->process();
	}

	/**
	 * process.
	 *
	 * @return	void
	 */
	public function process()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$model = $this->getThisModel();

		if (!$model->getId())
		{
			$model->setIDsFromRequest();
		}

		$status = $model->process();

		// Check if i'm using an AJAX call, in this case there is no need to redirect
		$format = $this->input->get('format', null, 'string');

		if ($format == 'json')
		{
			echo json_encode($status);

			return;
		}

		// Redirect
		$customUrl = $this->input->get('returnurl', null, 'string');

		if ($customUrl)
		{
			$customURL = base64_decode($customURL);
		}

		$url = !empty($customURL) ? $customURL : 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

		if (!$status)
		{
			$this->setRedirect($url, $model->getError(), 'error');
		}
		else
		{
			$this->setRedirect($url);
		}

		return;
	}

	/**
	 * purge.
	 *
	 * @return	void
	 */
	public function purge()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$model = $this->getThisModel();
		$status = $model->purge();

		// Check if i'm using an AJAX call, in this case there is no need to redirect
		$format = $this->input->get('format', null, 'string');

		if ($format == 'json')
		{
			echo json_encode($status);

			return;
		}

		// Redirect
		$customUrl = $this->input->get('returnurl', null, 'string');

		if ($customUrl)
		{
			$customURL = base64_decode($customURL);
		}

		$url = !empty($customURL) ? $customURL : 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

		if (!$status)
		{
			$this->setRedirect($url, $model->getError(), 'error');
		}
		else
		{
			$this->setRedirect($url);
		}

		return;
	}

	/**
	 * batch.
	 *
	 * @return	void
	 */
	public function batch()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$model = $this->getThisModel();

		if (!$model->getId())
		{
			$model->setIDsFromRequest();
		}

		$batch_published = $this->input->get('batch_published', null, 'int');
		$status = true;
		$status = $model->moveToState($batch_published);

		// Check if i'm using an AJAX call, in this case there is no need to redirect
		$format = $this->input->get('format', '', 'string');

		if ($format == 'json')
		{
			echo json_encode($status);

			return;
		}

		// Redirect
		if ($customURL = $this->input->get('returnurl', '', 'string'))
		{
			$customURL = base64_decode($customURL);
		}

		$url = !empty($customURL) ? $customURL : 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

		if (!$status)
		{
			$this->setRedirect($url, $model->getError(), 'error');
		}
		else
		{
			$this->setRedirect($url);
		}
	}

	/**
	 * applyAjaxPluginAction
	 *
	 * @return	void
	 */
	public function applyAjaxPluginAction()
	{
		try
		{
			// CSRF prevention
			if ($this->csrfProtection)
			{
				$this->_csrfProtection();
			}

			$data = $this->_getAjaxData();

			if (($data['id'] == 0) || ($data['ref_id'] == 0))
			{
				throw new Exception('Unknown Plugin Action (id/ref_id)');
			}

			$attr_id = null;

			// Autotweet_advanced_attrs
			if ((AUTOTWEETNG_JOOCIAL) && ($data['autotweet_advanced_attrs']))
			{
				$advanced_attrs = AdvancedattrsHelper::retrieveAdvancedAttrs($data['autotweet_advanced_attrs']);

				if (isset($advanced_attrs->ref_id))
				{
					// Safe to save
					$attr_id = AdvancedattrsHelper::saveAdvancedAttrs($advanced_attrs, $advanced_attrs->ref_id);
					unset($data['autotweet_advanced_attrs']);
				}
			}

			// Load the model
			$model = $this->getThisModel();

			if (!$model->getId())
			{
				$model->setIDsFromRequest();
			}

			$id = $model->getId();

			if (!$this->onBeforeApplySave($data))
			{
				return false;
			}

			// Set the layout to form, if it's not set in the URL

			if (is_null($this->layout))
			{
				$this->layout = 'form';
			}

			// Do I have a form?
			$model->setState('form_name', 'form.' . $this->layout);

			$status = $model->save($data);

			if ($status && ($id != 0))
			{
				F0FPlatform::getInstance()->setHeader('Status', '201 Created', true);

				// Try to check-in the record if it's not a new one
				$status = $model->checkin();
			}

			if ($status)
			{
				$status = $this->onAfterApplySave();
			}

			$req_id = $model->getId();

			if ($attr_id)
			{
				AdvancedattrsHelper::assignRequestId($attr_id, $req_id);
			}

			$this->input->set('id', $req_id);

			$message = json_encode(
				array(
					'status' => $status,
					'request_id' => ($status ? $model->getId() : false),
					'message' => ($status ? JText::_('COM_AUTOTWEET_COMPOSER_MESSAGE_SAVED') : implode('', $model->getErrors()) ),
					'messageType' => ($status ? 'success' : 'error'),
					'hash' => AutotweetBaseHelper::getHash()
				)
			);
		}
		catch (Exception $e)
		{
			$message = json_encode(
				array(
					'status' => false,
					'message' => $e->getMessage(),
					'messageType' => 'error',
					'hash' => AutotweetBaseHelper::getHash()
				)
			);
		}

		echo EJSON_START . $message . EJSON_END;
	}

	/**
	 * applyAjaxOwnAction
	 *
	 * @return	void
	 */
	public function applyAjaxOwnAction()
	{
		try
		{
			// CSRF prevention
			if ($this->csrfProtection)
			{
				$this->_csrfProtection();
			}

			$data = $this->_getAjaxData();

			// On Before Save

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

			// Ready to Save

			require_once JPATH_PLUGINS . '/autotweet/autotweetpost/autotweetpost.php';
			$plugin = JPluginHelper::getPlugin('autotweet', 'autotweetpost');
			$className = 'plgAutotweet' . $plugin->name;

			if (!class_exists($className))
			{
				throw new Exception(JText::_('COM_AUTOTWEET_COMPOSER_DISABLED_ERROR'));
			}

			$dispatcher = JDispatcher::getInstance();
			$plugin = new $className($dispatcher, (array) $plugin);
			$status = $plugin->postArticle($data);

			$id = null;

			if ($status !== false)
			{
				$id = $status;
				$status = true;
			}

			$message = json_encode(
				array(
					'status' => $status,
					'request_id' => $id,
					'message' => ($status ? JText::_('COM_AUTOTWEET_COMPOSER_MESSAGE_SAVED') : 'Unable to addAjaxAction.' ),
					'messageType' => ($status ? 'success' : 'error'),
					'hash' => AutotweetBaseHelper::getHash()
				)
			);
		}
		catch (Exception $e)
		{
			$message = json_encode(
				array(
					'status' => false,
					'message' => $e->getMessage(),
					'messageType' => 'error',
					'hash' => AutotweetBaseHelper::getHash()
				)
			);
		}

		echo EJSON_START . $message . EJSON_END;
	}

	/**
	 * publishAjaxAction.
	 *
	 * @return	void
	 */
	public function publishAjaxAction()
	{
		try
		{
			// CSRF prevention
			if ($this->csrfProtection)
			{
				$this->_csrfProtection();
			}

			$model = $this->getThisModel();

			if (!$model->getId())
			{
				$model->setIDsFromRequest();
			}

			$status = $model->process();

			$message = json_encode(
				array(
					'status' => $status,
					'message' => JText::_('COM_AUTOTWEET_COMPOSER_MESSAGE_PROCESSED'),
					'messageType' => 'success',
					'hash' => AutotweetBaseHelper::getHash()
				)
			);
		}
		catch (Exception $e)
		{
			$message = json_encode(
				array(
					'status' => false,
					'message' => $e->getMessage(),
					'messageType' => 'error',
					'hash' => AutotweetBaseHelper::getHash()
				)
			);
		}

		echo EJSON_START . $message . EJSON_END;
	}

	/**
	 * _getAjaxData.
	 *
	 * @return	array
	 */
	private function _getAjaxData()
	{
		$data = array();

		$publish_up = $this->input->get('publish_up', null, 'string');

		if (empty($publish_up))
		{
			$publish_up = EParameter::convertUTCLocal(JFactory::getDate()->toSql());
		}

		$description = $this->input->get('description', null, 'string');

		if (empty($description))
		{
			throw new Exception('Invalid message');
		}

		$url = $this->input->get('url', null, 'string');

		if (is_numeric($url))
		{
			$url = 'index.php?Itemid=' . $url;
		}

		$title = $this->input->get('title', null, 'string');

		if (empty($title))
		{
			$title = $description;
		}

		$data['publish_up'] = $publish_up;
		$data['plugin'] = $this->input->get('plugin', null, 'cmd');
		$data['ref_id'] = $this->input->get('ref_id', null, 'string');
		$data['description'] = $description;
		$data['url'] = $url;
		$data['image_url'] = $this->input->get('image_url', null, 'string');
		$data['published'] = $this->input->get('published', 0, 'int');
		$data['id'] = $this->input->get('id', 0, 'int');

		$xtform = array();

		$xtform['title'] = $title;
		$xtform['article_text'] = $this->input->get('article_text', null, 'string');
		$xtform['hashtags'] = $this->input->get('hashtags', null, 'string');
		$xtform['catid'] = $this->input->get('catid', null, 'string');
		$xtform['author'] = $this->input->get('author', null, 'string');
		$xtform['language'] = $this->input->get('language', null, 'string');
		$xtform['access'] = $this->input->get('access', null, 'string');
		$xtform['target_id'] = $this->input->get('target_id', null, 'string');

		$data['xtform'] = $xtform;

		if (AUTOTWEETNG_JOOCIAL)
		{
			$data['autotweet_advanced_attrs'] = $this->input->get('autotweet_advanced_attrs', null, 'string');
		}

		return $data;
	}

	/**
	 * cancelAjaxAction.
	 *
	 * @return	void
	 */
	public function cancelAjaxAction()
	{
		$this->_moveToState(1);
	}

	/**
	 * backtoQueueAjaxAction.
	 *
	 * @return	void
	 */
	public function backtoQueueAjaxAction()
	{
		$this->_moveToState(0);
	}

	/**
	 * _moveToState.
	 *
	 * @param   int  $published  Param
	 *
	 * @return	void
	 */
	private function _moveToState($published)
	{
		try
		{
			// CSRF prevention
			if ($this->csrfProtection)
			{
				$this->_csrfProtection();
			}

			$model = $this->getThisModel();

			if (!$model->getId())
			{
				$model->setIDsFromRequest();
			}

			$status = $model->moveToState($published);

			$message = json_encode(
					array(
									'status' => $status,
									'message' => JText::_('COM_AUTOTWEET_COMPOSER_MESSAGE_PROCESSED'),
									'messageType' => 'success',
									'hash' => AutotweetBaseHelper::getHash()
					)
			);
		}
		catch (Exception $e)
		{
			$message = json_encode(
					array(
									'status' => false,
									'message' => $e->getMessage(),
									'messageType' => 'error',
									'hash' => AutotweetBaseHelper::getHash()
					)
			);
		}

		echo EJSON_START . $message . EJSON_END;
	}

	/**
	 * readAjaxAction
	 *
	 * @return	void
	 */
	public function readAjaxAction()
	{
		try
		{
			$this->task = 'read';
			parent::read();
		}
		catch (Exception $e)
		{
			$message = json_encode(
					array(
									'status' => false,
									'message' => $e->getMessage(),
									'messageType' => 'error',
									'hash' => AutotweetBaseHelper::getHash()
					)
			);
			echo EJSON_START . $message . EJSON_END;
		}
	}
}
