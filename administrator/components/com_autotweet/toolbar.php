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
 * AutoTweetToolbar
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetToolbar extends F0FToolbar
{
	protected $isModule = false;

	protected $isModal = false;

	protected $isBackend = false;

	protected $isSubview = false;

	/**
	 * Class constructor
	 *
	 * @param   array  $config  Configuration parameters
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$platform = F0FPlatform::getInstance();
		$this->perms->editown = $platform->authorise('core.edit.own', $this->input->getCmd('option', 'com_foobar'));
		$this->perms->manage = $platform->authorise('core.manage', $this->input->getCmd('option', 'com_foobar'));

		$layout = $this->input->get('layout', null, 'cmd');
		$toolbar = $this->input->get('toolbar', null, 'cmd');

		$this->isModule = ($layout == 'module');
		$this->isModal = ($layout == 'modal');
		$this->isSubview = ($toolbar == 'none');

		$this->isBackend = $platform->isBackend();

		if ((!$this->isBackend) && (!$this->isSubview))
		{
			$this->renderFrontendSubmenu = true;
			$this->renderFrontendButtons = true;
		}
	}

	/**
	 * getMyViews.
	 *
	 * @return	array
	 */
	protected function getMyViews()
	{
		$views = array('cpanel');

		$allViews = parent::getMyViews();

		foreach ($allViews as $view)
		{
			if (!in_array($view, $views))
			{
				$views[] = $view;
			}
		}

		return $views;
	}

	/**
	 * onCpanelBrowse.
	 *
	 * @return	void
	 */
	public function onCpanelsBrowse()
	{
		$this->_onAllPages();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_CPANELS')), 'autotweet');

		if ($this->isBackend)
		{
			JToolBarHelper::preferences('com_autotweet', '600', '900');
		}
	}

	/**
	 * onComposers
	 *
	 * @return	void
	 */
	public function onComposers()
	{
		if ((AUTOTWEETNG_JOOCIAL) || ($this->isBackend))
		{
			parent::onAdd();
		}
		else
		{
			if (($this->perms->edit) || ($this->perms->editown))
			{
				JToolBarHelper::apply();
			}
		}

		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_COMPOSERS_EDIT')), 'autotweet-logo.png');

		if (AUTOTWEETNG_JOOCIAL)
		{
			self::calendar('xticon xticon-calendar-o', 'COM_AUTOTWEET_VIEW_CALENDAR_TITLE');
		}
	}

	/**
	 * onPostsBrowse.
	 *
	 * @return	void
	 */
	public function onPostsBrowse()
	{
		if (!$this->isModule)
		{
			$this->_onAllPages();
			$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_POSTS')), 'autotweet-logo.png');

			$allow_new_reqpost = EParameter::getComponentParam(CAUTOTWEETNG, 'allow_new_reqpost', 0);

			if (($this->perms->create) && ($allow_new_reqpost))
			{
				JToolBarHelper::addNew();
			}

			if (($this->perms->edit) || ($this->perms->editown))
			{
				JToolBarHelper::editList();
			}

			if ($this->perms->create || (($this->perms->edit) || ($this->perms->editown)))
			{
				JToolBarHelper::divider();
			}

			if ($this->perms->editstate)
			{
				JToolBarHelper::publishList();

				// JToolBarHelper::unpublishList();

				JToolBarHelper::divider();
			}

			if ($this->perms->create)
			{
				JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', 'COM_AUTOTWEET_COMMON_COPY_LABEL', false);
				JToolBarHelper::divider();
			}

			if ($this->perms->delete)
			{
				$option = $this->input->get('option', 'com_autotweet', 'cmd');
				$msg = JText::_($option . '_CONFIRM_DELETE');
				JToolBarHelper::deleteList($msg);
			}

			if ($this->perms->manage)
			{
				$this->trash('purge', 'COM_AUTOTWEET_COMMON_PURGE_LABEL', false);
			}
		}
	}

	/**
	 * onRequestsBrowse.
	 *
	 * @return	void
	 */
	public function onRequestsBrowse()
	{
		$this->_onAllPages();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_REQUESTS')), 'autotweet-logo.png');

		$allow_new_reqpost = EParameter::getComponentParam(CAUTOTWEETNG, 'allow_new_reqpost', 0);

		if (($this->perms->create) && ($allow_new_reqpost))
		{
			JToolBarHelper::addNew();
		}

		if (($this->perms->edit) || ($this->perms->editown))
		{
			JToolBarHelper::editList();
		}

		if ($this->perms->create || (($this->perms->edit) || ($this->perms->editown)))
		{
			JToolBarHelper::divider();
		}

		if ($this->perms->editstate)
		{
			JToolBarHelper::custom('process', 'process.png', 'process.png', 'COM_AUTOTWEET_COMMON_PROCESS_LABEL', false);
			JToolBarHelper::divider();
		}

		if ($this->perms->create)
		{
			JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', 'COM_AUTOTWEET_COMMON_COPY_LABEL', false);
			JToolBarHelper::divider();
		}

		if ($this->perms->delete)
		{
			$option = $this->input->get('option', 'com_autotweet', 'cmd');
			$msg = JText::_($option . '_CONFIRM_DELETE');
			JToolBarHelper::deleteList($msg);
		}

		if ($this->perms->manage)
		{
			$this->trash('purge', 'COM_AUTOTWEET_COMMON_PURGE_LABEL', false);
		}

		if (AUTOTWEETNG_JOOCIAL)
		{
			self::calendar('xticon xticon-calendar-o', 'COM_AUTOTWEET_VIEW_CALENDAR_TITLE');
		}
	}

	/**
	 * calendar
	 *
	 * @param   string  $icon  Param
	 * @param   string  $alt   Param
	 *
	 * @return  void
	 */
	public static function calendar($icon, $alt)
	{
		$title = JText::_($alt);

		if (EXTLY_J25)
		{
			$dhtml = "<a onclick=\"window.open(this.href,'{$title}','scrollbars=yes,resizable=yes,location=no,menubar=no,status=no,toolbar=no,left=0,top=0,width=960,height=720');return false;\" href=\"index.php?option=com_autotweet&view=calendar&tmpl=component\" target=\"_blank\" class=\"toolbar\"><span class=\"icon-32-calendar\"></span>{$title}</a>";
		}
		else
		{
			$dhtml = "<a class=\"btn btn-small\" onclick=\"window.open(this.href,'{$title}','scrollbars=yes,resizable=yes,location=no,menubar=no,status=no,toolbar=no,left=0,top=0,width=960,height=720');return false;\" href=\"index.php?option=com_autotweet&view=calendar&tmpl=component\" target=\"_blank\" data-original-title=\"{$title}\" rel=\"tooltip\"><i class=\"{$icon}\"></i> {$title}</a>";
		}

		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Custom', $dhtml, 'calendar');
	}

	/**
	 * onRequests.
	 *
	 * @return	void
	 */
	public function onRequests()
	{
		parent::onAdd();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_REQUEST_EDIT')), 'autotweet-logo.png');
	}

	/**
	 * onChannelsBrowse.
	 *
	 * @return	void
	 */
	public function onChannelsBrowse()
	{
		$this->_onAllPages();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_CHANNELS')), 'autotweet-logo.png');
		$this->_onBrowseWithCopy();
	}

	/**
	 * onChannels.
	 *
	 * @return	void
	 */
	public function onChannels()
	{
		// Parent::onAdd(); - Not fixed in F0F v2.1.1

		// On frontend, buttons must be added specifically
		if (!F0FPlatform::getInstance()->isBackend() && !$this->renderFrontendButtons)
		{
			return;
		}

		$option = $this->input->getCmd('option', 'com_foobar');
		$componentName = str_replace('com_', '', $option);

		// Set toolbar title
		$subtitle_key = strtoupper($option . '_TITLE_' . F0FInflector::pluralize($this->input->getCmd('view', 'cpanel'))) . '_EDIT';
		JToolBarHelper::title(JText::_(strtoupper($option)) . ' &ndash; <small>' . JText::_($subtitle_key) . '</small>', $componentName);

		// Set toolbar icons
		if ($this->perms->edit || $this->perms->editown)
		{
			// Show the apply button only if I can edit the record, otherwise I'll return to the edit form and get a
			// 403 error since I can't do that
			JToolBarHelper::apply();
		}

		JToolBarHelper::save();

		// Parent::onAdd(); - Not fixed in F0F v2.1.1
		if ((isset($this->perms->create)) && ($this->perms->create))
		{
			JToolBarHelper::custom('savenew', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		JToolBarHelper::cancel();

		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_CHANNELS_EDIT')), 'autotweet-logo.png');
	}

	/**
	 * onRulesBrowse.
	 *
	 * @return	void
	 */
	public function onRulesBrowse()
	{
		$this->_onAllPages();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_RULES')), 'autotweet-logo.png');
		$this->_onBrowseWithCopy();
	}

	/**
	 * onRules.
	 *
	 * @return	void
	 */
	public function onRules()
	{
		parent::onAdd();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_RULE_EDIT')), 'autotweet-logo.png');
	}

	/**
	 * onPostBrowse.
	 *
	 * @return	void
	 */
	public function onPostBrowse()
	{
		throw new Exception('What? onPostBrowse');
	}

	/**
	 * onPosts.
	 *
	 * @return	void
	 */
	public function onPosts()
	{
		parent::onAdd();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_POST_EDIT')), 'autotweet-logo.png');
	}

	/**
	 * onTargetsBrowse.
	 *
	 * @return	void
	 */
	public function onTargetsBrowse()
	{
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_TARGETS')), 'autotweet-logo.png');

		if (!$this->isModal)
		{
			$this->_onAllPages();
			$this->_onBrowseWithCopy();
		}
	}

	/**
	 * onTargets.
	 *
	 * @return	void
	 */
	public function onTargets()
	{
		parent::onAdd();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_TARGETS_EDIT')), 'autotweet-logo.png');
	}

	/**
	 * onFeedsBrowse.
	 *
	 * @return	void
	 */
	public function onFeedsBrowse()
	{
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_FEEDS')), 'autotweet-logo.png');
		$this->_onAllPages();
		$this->_onBrowseWithCopy();

		if ($this->perms->manage)
		{
			JToolBarHelper::custom('import', 'process.png', 'process.png', 'COM_AUTOTWEET_COMMON_IMPORT_LABEL', false);
		}
	}

	/**
	 * onFeedsAdd.
	 *
	 * @return	void
	 */
	public function onFeeds()
	{
		// On frontend, buttons must be added specifically
		if (!F0FPlatform::getInstance()->isBackend() && !$this->renderFrontendButtons)
		{
			return;
		}

		$option = $this->input->getCmd('option', 'com_foobar');
		$componentName = str_replace('com_', '', $option);

		// Set toolbar title
		$subtitle_key = strtoupper($option . '_TITLE_' . F0FInflector::pluralize($this->input->getCmd('view', 'cpanel'))) . '_EDIT';
		$this->title(JText::_(strtoupper($option)) . ' &ndash; <small>' . JText::_($subtitle_key) . '</small>', $componentName);

		// Set toolbar icons
		$bar = JToolbar::getInstance('toolbar');

		// Add an 'Apply & Preview' button
		$bar->appendButton('Standard', 'apply', 'COM_AUTOTWEET_VIEW_FEED_PREVIEW_JTOOLBAR_APPLY', 'apply', false);

		JToolBarHelper::save();

		if ((isset($this->perms->create)) && ($this->perms->create))
		{
			JToolBarHelper::custom('savenew', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		if ((isset($this->perms->manage)) && ($this->perms->manage))
		{
			JToolBarHelper::custom('import', 'process.png', 'process.png', 'COM_AUTOTWEET_COMMON_IMPORT_LABEL', false);
		}

		JToolBarHelper::cancel();

		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_FEEDS_EDIT')), 'autotweet-logo.png');
	}

	/**
	 * onInfosBrowse.
	 *
	 * @return	void
	 */
	public function onInfosBrowse()
	{
		$this->_onAllPages();
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_INFOS')), 'autotweet-logo.png');
		JToolBarHelper::preferences('com_autotweet', '600', '900');
	}

	/**
	 * onManagersEdit.
	 *
	 * @return	void
	 */
	public function onManagersEdit()
	{
		$this->title(VersionHelper::getTitle(JText::_('COM_AUTOTWEET_TITLE_MANAGERS_EDIT')), 'autotweet-logo.png');

		// Set toolbar icons
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	/**
	 * onChannelsBrowse.
	 *
	 * @return	void
	 */
	public function onUserChannelsBrowse()
	{
		$doc = JFactory::getDocument();
		$doc->setTitle(JText::_('COM_AUTOTWEET_VIEW_USERCHANNELS_TITLE'));
	}

	/**
	 * _onAllPages.
	 *
	 * @return	void
	 */
	public function _onAllPages()
	{
		// On frontend, buttons must be added specifically
		list($isCli, $isAdmin) = F0FDispatcher::isCliAdmin();

		if ($isAdmin || $this->renderFrontendSubmenu)
		{
			$this->renderSubmenu();
		}

		if (!$isAdmin && !$this->renderFrontendButtons)
		{
			return;
		}
	}

	/**
	 * _onBrowseWithCopy.
	 *
	 * @param   bool  $allowCopy  Param.
	 *
	 * @return	void
	 */
	public function _onBrowseWithCopy($allowCopy = true)
	{
		// Add toolbar buttons
		if ($this->perms->create)
		{
			JToolBarHelper::addNew();
		}

		if (($this->perms->edit) || ($this->perms->editown))
		{
			JToolBarHelper::editList();
		}

		if ($this->perms->create || (($this->perms->edit) || ($this->perms->editown)))
		{
			JToolBarHelper::divider();
		}

		if ($this->perms->editstate)
		{
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::divider();
		}

		if (($allowCopy) && ($this->perms->create))
		{
			JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', 'COM_AUTOTWEET_COMMON_COPY_LABEL', false);
			JToolBarHelper::divider();
		}

		if ($this->perms->delete)
		{
			$option = $this->input->get('option', 'com_autotweet', 'cmd');
			$msg = JText::_($option . '_CONFIRM_DELETE');
			JToolBarHelper::deleteList($msg);
		}
	}

	/**
	 * Renders the submenu (toolbar links) for all detected views of this component
	 *
	 * @return  void
	 */
	public function renderSubmenu()
	{
		$views = $this->_getInternalViews();

		foreach ($views as $label => $view)
		{
			if (!is_array($view))
			{
				$this->addSubmenuLink($view);
			}
			else
			{
				$label_text = JText::_($label);
				$label_icon = JText::_(str_replace('_TITLE_', '_ICON_', $label));
				$label = $label_icon . ' ' . $label_text;
				$this->appendLink($label, '', false);

				foreach ($view as $v)
				{
					$this->addSubmenuLink($v, $label);
				}
			}
		}
	}

	/**
	 * _getInternalViews
	 *
	 * @return  array
	 */
	private function _getInternalViews()
	{
		$views = array(
			'cpanels',
			'composer'
		);

		if (EXTLY_J3)
		{
			// Activities menu definition
			$activities = array('requests');

			// Rules - Only in the backend
			if ($this->isBackend)
			{
				$activities[] = 'rules';
			}

			$activities[] = 'posts';
			$views['COM_AUTOTWEET_TITLE_ACTIVITIES'] = $activities;
		}
		else
		{
			$views[] = 'requests';
			$views[] = 'rules';
			$views[] = 'posts';
		}

		$views[] = 'channels';

		// Feeds and System Check - Only in the backend
		if ($this->isBackend)
		{
			// Targeting - Only in Joocial
			if ( (AUTOTWEETNG_JOOCIAL) && (EParameter::getComponentParam(CAUTOTWEETNG, 'targeting', false)) )
			{
				array_push($views, 'targets');
			}

			$views[] = 'feeds';
			$views[] = 'infos';
			$views[] = 'usermanual';
		}

		return $views;
	}

	/**
	 * Append a link to the link bar
	 *
	 * @param   string       $name    The text of the link
	 * @param   string|null  $link    The link to render; set to null to render a separator
	 * @param   boolean      $active  True if it's an active link
	 * @param   string|null  $icon    Icon class (used by some renderers, like the Bootstrap renderer)
	 * @param   string|null  $parent  The parent element (referenced by name)) Thsi will create a dropdown list
	 *
	 * @return  void
	 */
	public function appendLink($name, $link = null, $active = false, $icon = null, $parent = '')
	{
		if (!$this->isBackend)
		{
			$link = JRoute::_($link);
		}

		parent::appendLink($name, $link, $active, $icon, $parent);
	}

	/**
	 * addSubmenuLink
	 *
	 * @param   object  $view    Param
	 * @param   object  $parent  Param
	 *
	 * @return  void
	 */
	private function addSubmenuLink($view, $parent = null)
	{
		static $activeView = null;

		if (empty($activeView))
		{
			$activeView = $this->input->getCmd('view', 'cpanel');
		}

		if ($activeView == 'cpanels')
		{
			$activeView = 'cpanel';
		}

		$icon_key = strtoupper($this->component) . '_ICON_' . strtoupper($view);
		$icon = JText::_($icon_key);

		$key = strtoupper($this->component) . '_TITLE_' . strtoupper($view);

		if (strtoupper(JText::_($key)) == $key)
		{
			$altview = F0FInflector::isPlural($view) ? F0FInflector::singularize($view) : F0FInflector::pluralize($view);
			$key2 = strtoupper($this->component) . '_TITLE_' . strtoupper($altview);

			if (strtoupper(JText::_($key2)) == $key2)
			{
				$name = ucfirst($view);
			}
			else
			{
				$name = JText::_($key2);
			}
		}
		else
		{
			$name = JText::_($key);
		}

		if ($view == 'usermanual')
		{
			$link = 'http://documentation.extly.com/';
		}
		else
		{
			$link = 'index.php?option=' . $this->component . '&view=' . $view;
		}

		$active = $view == $activeView;

		$this->appendLink($icon . ' ' . $name, $link, $active, null, $parent);
	}

	/**
	 * Title cell.
	 * For the title and toolbar to be rendered correctly,
	 * this title fucntion must be called before the starttable function and the toolbars icons
	 * this is due to the nature of how the css has been used to postion the title in respect to the toolbar.
	 *
	 * @param   string  $title  The title.
	 * @param   string  $icon   The space-separated names of the image.
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public static function title($title, $icon = 'generic.png')
	{
		if (EXTLY_J25)
		{
			// Strip the extension.
			$icons = explode(' ', $icon);

			foreach ($icons as &$icon)
			{
				$icon = 'icon-48-' . preg_replace('#\.[^.]*$#', '', $icon);
			}

			$html = '<div class="pagetitle ' . htmlspecialchars(implode(' ', $icons)) . '"><h2>' . $title . '</h2></div>';

			$app = JFactory::getApplication();
			$app->JComponentTitle = $html;
			$doc = JFactory::getDocument();
			$doc->setTitle($app->getCfg('sitename') . ' - ' . JText::_('JADMINISTRATION') . ' - ' . $title);
		}
		else
		{
			$layout = new JLayoutFile('joomla.toolbar.title');
			$html = $layout->render(array('title' => $title, 'icon' => $icon));

			$app = JFactory::getApplication();
			$app->JComponentTitle = $html;
			JFactory::getDocument()->setTitle($app->getCfg('sitename') . ' - ' . $title);
		}
	}

	/**
	 * Writes a common 'trash' button for a list of records.
	 *
	 * @param   string  $task   An override for the task.
	 * @param   string  $alt    An override for the alt text.
	 * @param   bool    $check  True to allow lists.
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public static function trash($task = 'remove', $alt = 'JTOOLBAR_TRASH', $check = true)
	{
		$bar = JToolbar::getInstance('toolbar');

		// Add a trash button.
		$bar->appendButton('Confirm', JText::_('COM_AUTOTWEET_CONFIRM_PURGE'), 'trash', $alt, $task, $check, false);
	}
}
