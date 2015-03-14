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
 * Extly view renderer class
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetRenderBack3 extends F0FRenderJoomla3
{
	/**
	 * Renders the submenu (link bar) in F0F's classic style, using a Bootstrapped
	 * tab bar.
	 *
	 * @param   string    $view    The active view name
	 * @param   string    $task    The current task
	 * @param   F0FInput  $input   The input object
	 * @param   array     $config  Extra configuration variables for the toolbar
	 *
	 * @return  void
	 */
	protected function renderLinkbar_classic($view, $task, $input, $config = array())
	{
		if (F0FPlatform::getInstance()->isCli())
		{
			return;
		}

		// Do not render a submenu unless we are in the the admin area
		$toolbar				 = F0FToolbar::getAnInstance($input->getCmd('option', 'com_foobar'), $config);
		$renderFrontendSubmenu	 = $toolbar->getRenderFrontendSubmenu();

		if (!F0FPlatform::getInstance()->isBackend() && !$renderFrontendSubmenu)
		{
			return;
		}

		$links = $toolbar->getLinks();

		if (!empty($links))
		{
			echo "<ul class=\"nav nav-tabs\">\n";

			foreach ($links as $link)
			{
				$dropdown = false;

				if (array_key_exists('dropdown', $link))
				{
					$dropdown = $link['dropdown'];
				}

				if ($dropdown)
				{
					echo "<li";
					$class = 'dropdown';

					if ($link['active'])
					{
						$class .= ' active';
					}

					echo ' class="' . $class . '">';

					echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';

					if ($link['icon'])
					{
						echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
					}

					echo $link['name'];
					echo '<b class="caret"></b>';
					echo '</a>';

					echo "\n<ul class=\"dropdown-menu\">";

					foreach ($link['items'] as $item)
					{
						echo "<li";

						if ($item['active'])
						{
							echo ' class="active"';
						}

						echo ">";

						if ($item['link'])
						{
							echo "<a tabindex=\"-1\" href=\"" . $item['link'] . "\">" .
								($item['icon'] ? "<i class=\"" . $item['icon'] . "\"></i>" : '') .
								$item['name'] . "</a>";
						}
						else
						{
							if ($item['icon'])
							{
								echo "<i class=\"" . $item['icon'] . "\"></i>";
							}

							echo $item['name'];
						}

						echo "</li>";
					}

					echo "</ul>\n";
				}
				else
				{
					echo "<li";

					if ($link['active'])
					{
						echo ' class="active"';
					}

					echo ">";

					if ($link['link'])
					{
						echo "<a href=\"" . $link['link'] . "\">";

						if ($link['icon'])
						{
							echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
						}

						echo $link['name'] . "</a>";
					}
					else
					{
						if ($link['icon'])
						{
							echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
						}

						echo $link['name'];
					}
				}

				echo "</li>\n";
			}

			echo "</ul>\n";
		}
	}
}

/**
 * Autotweet view renderer class
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetRenderFront3 extends F0FRenderJoomla3
{
	/**
	 * Echoes any HTML to show before the view template
	 *
	 * @param   string    $view    The current view
	 * @param   string    $task    The current task
	 * @param   F0FInput  $input   The input array (request parameters)
	 * @param   array     $config  The view configuration array
	 *
	 * @return  void
	 */
	public function preRender($view, $task, $input, $config = array())
	{
		$format	 = $input->getCmd('format', 'html');

		if (empty($format))
		{
			$format	 = 'html';
		}

		if ($format != 'html')
		{
			return;
		}

		// Render the submenu and toolbar
		if ($input->getBool('render_toolbar', true))
		{
			$this->renderLinkbar($view, $task, $input, $config);
			$this->renderButtons($view, $task, $input, $config);
		}
	}

	/**
	 * Renders the submenu (link bar) in F0F's classic style, using a Bootstrapped
	 * tab bar.
	 *
	 * @param   string    $view    The active view name
	 * @param   string    $task    The current task
	 * @param   F0FInput  $input   The input object
	 * @param   array     $config  Extra configuration variables for the toolbar
	 *
	 * @return  void
	 */
	protected function renderLinkbar_classic($view, $task, $input, $config = array())
	{
		if (F0FPlatform::getInstance()->isCli())
		{
			return;
		}

		if (isset(JFactory::getApplication()->JComponentTitle))
		{
			$title	 = JFactory::getApplication()->JComponentTitle;
		}
		else
		{
			$title = JText::_('COM_AUTOTWEET');
		}

		$title = strip_tags($title);

		echo '<h1 class="page-title">' .
			'<img src="media/com_autotweet/images/autotweet-logo-24.png" alt="' . $title . '"> ' . $title . '</h1>';

		// Do not render a submenu unless we are in the the admin area
		$toolbar				 = F0FToolbar::getAnInstance($input->getCmd('option', 'com_foobar'), $config);
		$renderFrontendSubmenu	 = $toolbar->getRenderFrontendSubmenu();

		if (!F0FPlatform::getInstance()->isBackend() && !$renderFrontendSubmenu)
		{
			return;
		}

		$links = $toolbar->getLinks();

		if (!empty($links))
		{
			echo "<p></p><div class=\"navbar\"><div class=\"navbar-inner\"><ul class=\"nav nav-tabs\">\n";

			foreach ($links as $link)
			{
				$dropdown = false;

				if (array_key_exists('dropdown', $link))
				{
					$dropdown = $link['dropdown'];
				}

				if ($dropdown)
				{
					echo "<li";
					$class = 'dropdown';

					if ($link['active'])
					{
						$class .= ' active';
					}

					echo ' class="' . $class . '">';

					echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';

					if ($link['icon'])
					{
						echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
					}

					echo $link['name'];
					echo '<b class="caret"></b>';
					echo '</a>';

					echo "\n<ul class=\"dropdown-menu\">";

					foreach ($link['items'] as $item)
					{
						echo "<li";

						if ($item['active'])
						{
							echo ' class="active"';
						}

						echo ">";

						if ($item['link'])
						{
							echo "<a tabindex=\"-1\" href=\"" . $item['link'] . "\">" .
								"<i class=\"" . $item['icon'] . "\"></i>" .
								$item['name'] . "</a>";
						}
						else
						{
							if ($item['icon'])
							{
								echo "<i class=\"" . $item['icon'] . "\"></i>";
							}

							echo $item['name'];
						}

						echo "</li>";
					}

					echo "</ul>\n";
				}
				else
				{
					echo "<li";

					if ($link['active'])
					{
						echo ' class="active"';
					}

					echo ">";

					if ($link['link'])
					{
						echo "<a href=\"" . $link['link'] . "\">";

						if ($link['icon'])
						{
							echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
						}

						echo $link['name'] . "</a>";
					}
					else
					{
						if ($link['icon'])
						{
							echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
						}

						echo $link['name'];
					}
				}

				echo "</li>\n";
			}

			echo "</ul></div></div>\n";
		}
	}

	/**
	 * Renders the toolbar buttons
	 *
	 * @param   string    $view    The active view name
	 * @param   string    $task    The current task
	 * @param   F0FInput  $input   The input object
	 * @param   array     $config  Extra configuration variables for the toolbar
	 *
	 * @return  void
	 */
	protected function renderButtons($view, $task, $input, $config = array())
	{
		if (F0FPlatform::getInstance()->isCli())
		{
			return;
		}

		// Do not render buttons unless we are in the the frontend area and we are asked to do so
		$toolbar				 = F0FToolbar::getAnInstance($input->getCmd('option', 'com_foobar'), $config);
		$renderFrontendButtons	 = $toolbar->getRenderFrontendButtons();

		// Load main backend language, in order to display toolbar strings
		// (JTOOLBAR_BACK, JTOOLBAR_PUBLISH etc etc)
		F0FPlatform::getInstance()->loadTranslations('joomla');

		if (F0FPlatform::getInstance()->isBackend() || !$renderFrontendButtons)
		{
			return;
		}

		$bar	 = JToolBar::getInstance('toolbar');
		$items	 = $bar->getItems();

		$substitutions = array(
						'icon-new'		 => 'xticon xticon-plus',
						'icon-white'	 => 'disabled-white',
						'icon-publish'	 => 'xticon xticon-check-sign',
						'icon-unpublish' => 'xticon xticon-times-circle',
						'icon-delete'	 => 'xticon xticon-times',
						'icon-edit'		 => 'xticon xticon-edit',
						'icon-copy'		 => 'xticon xticon-copy',
						'icon-cancel'	 => 'xticon xticon-times-circle',
						'icon-back'		 => 'xticon xticon-times-circle',
						'icon-apply'	 => 'xticon xticon-save',
						'icon-save'		 => 'xticon xticon-edit',
						'icon-save-new'	 => 'xticon xticon-plus',
						'icon-process'	 => 'xticon xticon-cog',
		);

		$html	 = array();
		$actions = array();

		$html[] = '<div id="F0FHeaderHolder" class="row-fluid"><div class="span12">';
		$html[] = '<div class="buttonsHolder btn-toolbar pull-right">';

		foreach ($items as $node)
		{
			$type	 = $node[0];
			$button	 = $bar->loadButtonType($type);

			if ($button !== false)
			{
				if (method_exists($button, 'fetchId'))
				{
					$id = call_user_func_array(array(&$button, 'fetchId'), $node);
				}
				else
				{
					$id = null;
				}

				$action	    = call_user_func_array(array(&$button, 'fetchButton'), $node);
				$action	    = str_replace('class="toolbar"', 'class="toolbar btn"', $action);
				$action	    = str_replace('<span ', '<i ', $action);
				$action	    = str_replace('</span>', '</i>', $action);
				$action	    = str_replace(array_keys($substitutions), array_values($substitutions), $action);
				$actions[]	= $action;
			}
		}

		$html   = array_merge($html, $actions);
		$html[] = '</div>';
		$html[] = '</div></div>';

		echo implode("\n", $html);
	}
}

/**
 * Extly view renderer class
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetRenderBack25 extends F0FRenderJoomla
{
	/**
	 * do the rendering job for the linkbar
	 *
	 * @param   F0FToolbar  $toolbar  A toolbar object
	 *
	 * @return  void
	 */
	protected function renderLinkbarItems($toolbar)
	{
		$links = $toolbar->getLinks();

		if (!empty($links))
		{
			foreach ($links as $link)
			{
				JSubMenuHelper::addEntry(
					($link['icon'] ? "<i class=\"" . $link['icon'] . "\"></i>&nbsp;" : '') .
					$link['name'],
					$link['link'],
					$link['active']
				);
			}
		}
	}
}

/**
 * Extly view renderer class
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetRenderFront25 extends AutotweetRenderFront3
{
	/**
	 * Renders the submenu (link bar) in F0F's classic style, using a Bootstrapped
	 * tab bar.
	 *
	 * @param   string    $view    The active view name
	 * @param   string    $task    The current task
	 * @param   F0FInput  $input   The input object
	 * @param   array     $config  Extra configuration variables for the toolbar
	 *
	 * @return  void
	 */
	protected function renderLinkbar_classic($view, $task, $input, $config = array())
	{
		if (F0FPlatform::getInstance()->isCli())
		{
			return;
		}

		if (isset(JFactory::getApplication()->JComponentTitle))
		{
			$title	 = JFactory::getApplication()->JComponentTitle;
		}
		else
		{
			$title = JText::_('COM_AUTOTWEET');
		}

		$title = strip_tags($title);

		echo '<h1 class="page-title">' .
			'<img src="media/com_autotweet/images/autotweet-icon.png" alt="' . $title . '"> ' . $title . '</h1>';

		// Do not render a submenu unless we are in the the admin area
		$toolbar				 = F0FToolbar::getAnInstance($input->getCmd('option', 'com_foobar'), $config);
		$renderFrontendSubmenu	 = $toolbar->getRenderFrontendSubmenu();

		if (!F0FPlatform::getInstance()->isBackend() && !$renderFrontendSubmenu)
		{
			return;
		}

		$links = $toolbar->getLinks();

		if (!empty($links))
		{
			echo "<div class=\"extly\"><div class=\"navbar\"><div class=\"navbar-inner\"><ul class=\"nav nav-tabs\">\n";

			foreach ($links as $link)
			{
				$dropdown = false;

				if (array_key_exists('dropdown', $link))
				{
					$dropdown = $link['dropdown'];
				}

				if ($dropdown)
				{
					echo "<li";
					$class = 'dropdown';

					if ($link['active'])
					{
						$class .= ' active';
					}

					echo ' class="' . $class . '">';

					echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';

					if ($link['icon'])
					{
						echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
					}

					echo $link['name'];
					echo '<b class="caret"></b>';
					echo '</a>';

					echo "\n<ul class=\"dropdown-menu\">";

					foreach ($link['items'] as $item)
					{
						echo "<li";

						if ($item['active'])
						{
							echo ' class="active"';
						}

						echo ">";

						if ($item['link'])
						{
							echo "<a tabindex=\"-1\" href=\"" . $item['link'] . "\">" .
									"<i class=\"" . $item['icon'] . "\"></i>" .
									$item['name'] . "</a>";
						}
						else
						{
							if ($item['icon'])
							{
								echo "<i class=\"" . $item['icon'] . "\"></i>";
							}

							echo $item['name'];
						}

						echo "</li>";
					}

					echo "</ul>\n";
				}
				else
				{
					echo "<li";

					if ($link['active'])
					{
						echo ' class="active"';
					}

					echo ">";

					if ($link['link'])
					{
						echo "<a href=\"" . $link['link'] . "\">";

						if ($link['icon'])
						{
							echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
						}

						echo $link['name'] . "</a>";
					}
					else
					{
						if ($link['icon'])
						{
							echo "<i class=\"" . $link['icon'] . "\"></i>&nbsp;";
						}

						echo $link['name'];
					}
				}

				echo "</li>\n";
			}

			echo "</ul></div></div></div>\n";
		}
	}

	/**
	 * Renders the toolbar buttons
	 *
	 * @param   string    $view    The active view name
	 * @param   string    $task    The current task
	 * @param   F0FInput  $input   The input object
	 * @param   array     $config  Extra configuration variables for the toolbar
	 *
	 * @return  void
	 */
	protected function renderButtons($view, $task, $input, $config = array())
	{
		if (F0FPlatform::getInstance()->isCli())
		{
			return;
		}

		// Do not render buttons unless we are in the the frontend area and we are asked to do so
		$toolbar				 = F0FToolbar::getAnInstance($input->getCmd('option', 'com_foobar'), $config);
		$renderFrontendButtons	 = $toolbar->getRenderFrontendButtons();

		// Load main backend language, in order to display toolbar strings
		// (JTOOLBAR_BACK, JTOOLBAR_PUBLISH etc etc)
		F0FPlatform::getInstance()->loadTranslations('joomla');

		if (F0FPlatform::getInstance()->isBackend() || !$renderFrontendButtons)
		{
			return;
		}

		$bar	 = JToolBar::getInstance('toolbar');
		$items	 = $bar->getItems();

		$substitutions = array(
						// Joomla 25
						'icon-32-new'		 => 'xticon xticon-plus',
						'icon-32-publish'	 => 'xticon xticon-check-sign',
						'icon-32-unpublish' => 'xticon xticon-times-circle',
						'icon-32-delete'	 => 'xticon xticon-times',
						'icon-32-edit'		 => 'xticon xticon-edit',
						'icon-32-copy'		 => 'xticon xticon-copy',
						'icon-32-cancel'	 => 'xticon xticon-times-circle',
						'icon-32-back'		 => 'xticon xticon-times-circle',
						'icon-32-apply'	 => 'xticon xticon-save',
						'icon-32-save'		 => 'xticon xticon-edit',
						'icon-32-save-new'	 => 'xticon xticon-plus',
						'icon-32-process'	 => 'xticon xticon-cog'
		);

		$html	 = array();
		$actions = array();

		$html[] = '<div class="extly"><div id="F0FHeaderHolder" class="row-fluid"><div class="span12">';
		$html[] = '<div class="buttonsHolder btn-toolbar pull-right">';

		foreach ($items as $node)
		{
			$type	 = $node[0];
			$button	 = $bar->loadButtonType($type);

			if ($button !== false)
			{
				if (method_exists($button, 'fetchId'))
				{
					$id = call_user_func_array(array(&$button, 'fetchId'), $node);
				}
				else
				{
					$id = null;
				}

				$action	    = call_user_func_array(array(&$button, 'fetchButton'), $node);
				$action	    = str_replace('class="toolbar"', 'class="toolbar btn"', $action);
				$action	    = str_replace('<span ', '<i ', $action);
				$action	    = str_replace('</span>', '</i>', $action);
				$action	    = str_replace(array_keys($substitutions), array_values($substitutions), $action);
				$actions[]	= $action;
			}
		}

		$html   = array_merge($html, $actions);
		$html[] = '</div>';
		$html[] = '</div></div></div>';

		echo implode("\n", $html);
	}
}
