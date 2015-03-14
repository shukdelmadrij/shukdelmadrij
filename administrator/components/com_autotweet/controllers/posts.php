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
 * AutotweetControllerPosts
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetControllerPosts extends AutotweetControllerDefault
{
	/**
	 * publish.
	 *
	 * @return	void
	 */
	public function publish()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$this->_approveposts();
	}

	/**
	 * publish.
	 *
	 * @return	void
	 */
	public function unpublish()
	{
		// CSRF prevention
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		$this->_cancelposts();
	}

	/**
	 * _approveposts.
	 *
	 * @return	void
	 */
	private function _approveposts()
	{
		$model = $this->getThisModel();

		if (!$model->getId())
		{
			$model->setIDsFromRequest();
		}

		$status = $model->approve();

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
			$customUrl = base64_decode($customUrl);
		}

		$url = !empty($customUrl) ? $customUrl : 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

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
	 * _cancelposts.
	 *
	 * @return	void
	 */
	private function _cancelposts()
	{
		$model = $this->getThisModel();

		if (!$model->getId())
		{
			$model->setIDsFromRequest();
		}

		$status = $model->cancel();

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
			$customUrl = base64_decode($customUrl);
		}

		$url = !empty($customUrl) ? $customUrl : 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

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

		$batch_pubstate = $this->input->get('batch_pubstate', null, 'cmd');
		$status = true;

		if ($batch_pubstate)
		{
			$status = $model->moveToState($batch_pubstate);
		}

		// Check if i'm using an AJAX call, in this case there is no need to redirect
		$format = $this->input->get('format', '', 'string');

		if ($format == 'json')
		{
			echo json_encode($status);

			return;
		}

		// Redirect
		if ($customUrl = $this->input->get('returnurl', '', 'string'))
		{
			$customUrl = base64_decode($customUrl);
		}

		$url = !empty($customUrl) ? $customUrl : 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

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
		$format = $this->input->get('format', '', 'string');

		if ($format == 'json')
		{
			echo json_encode($status);

			return;
		}

		// Redirect
		if ($customUrl = $this->input->get('returnurl', '', 'string'))
		{
			$customUrl = base64_decode($customUrl);
		}

		$url = !empty($customUrl) ? $customUrl : 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

		if (!$status)
		{
			$this->setRedirect($url, $model->getError(), 'error');
		}
		else
		{
			$this->setRedirect($url);
		}
	}
}
