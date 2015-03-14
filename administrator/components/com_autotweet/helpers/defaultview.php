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
 * AutoTweetDefaultView
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutoTweetDefaultView extends F0FViewHtml
{
	// Options - controller - view - layout - task
	public static $enabledAttrComps = array(
		'com_content' => array('-' => array('article' => array('edit' =>	array('-' => true)))),

		'com_autotweet' => array('-' => array('request' => array('-' => array('edit' => true)))),

		'com_easyblog' => array('-' => array('blog' => array('-' => array(
						'edit' => true,
						'-' => true)))),
		'com_flexicontent' => array('items' => array('item' => array('-' => array(
						'add' => true,
						'edit' => true)))),
		'com_jcalpro' => array('products' => array('-' => array('-' => array(
						'add' => true,
						'edit' => true)))),
/*
		'com_jreviews' => array('-' => array('-' => array('-' => array(
						'-' => true)))),
*/
		'com_jshopping' => array('products' => array('-' => array('-' => array(
						'add' => true,
						'edit' => true)))),
		'com_k2' => array('-' => array('item' => array('-' => array('-' => true)))),
		'com_sobipro' => array('-' => array('-' => array('-' => array(
						'entry.add' => true,
						'entry.edit' => true)))),
		'com_zoo' => array(
						'item' => array('-' => array('-' => array('edit' => true))),
						'submission' => array('submission' => array('submission' => array('save' => true)))
		),
		'com_eshop' => array('-' => array('product' => array('-' => array('edit' => true))))
	);

	/**
	 * Class constructor
	 *
	 * @param   array  $config  Configuration parameters
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$blankImage = JUri::root() . 'media/lib_extly/images/Blank.gif';
		$this->assign('blankImage', $blankImage);
	}

	/**
	 * Runs before rendering the view template, echoing HTML to put before the
	 * view template's generated HTML
	 *
	 * @return void
	 */
	protected function preRender()
	{
		$view = $this->input->getCmd('view', 'cpanel');
		$task = $this->getModel()->getState('task', 'browse');

		// Don't load the toolbar on CLI

		if (!F0FPlatform::getInstance()->isCli())
		{
			$toolbar = F0FToolbar::getAnInstance($this->input->getCmd('option', 'com_foobar'), $this->config);

			// Channel Restriction
			if ((AUTOTWEETNG_FREE)
				&& (($view == 'channels') || ($view == 'channel')))
			{
				$channels = F0FModel::getTmpInstance('Channels', 'AutoTweetModel');
				$c = $channels->getTotal();

				if ($c >= 2)
				{
					$this->perms->create = false;
					$toolbar->perms->create = false;
				}
			}

			if ((!AUTOTWEETNG_JOOCIAL)
				&& (($view == 'feeds') || ($view == 'feed')))
			{
				$feeds = F0FModel::getTmpInstance('Feeds', 'AutoTweetModel');
				$c = $feeds->getTotal();

				if ($c >= 2)
				{
					$this->perms->create = false;
					$toolbar->perms->create = false;
				}
			}

			// ---
			$toolbar->renderToolbar($view, $task, $this->input);
		}

		$renderer = $this->getRenderer();

		if (!($renderer instanceof F0FRenderAbstract))
		{
			$this->renderLinkbar();
		}
		else
		{
			$renderer->preRender($view, $task, $this->input, $this->config);
		}
	}

	/**
	 * Get the renderer object for this view
	 *
	 * @return  F0FRenderAbstract
	 */
	public function &getRenderer()
	{
		if (!($this->rendererObject instanceof F0FRenderAbstract))
		{
			$isBackend = F0FPlatform::getInstance()->isBackend();

			if ($isBackend)
			{
				if (EXTLY_J25)
				{
					$this->rendererObject = new AutotweetRenderBack25;
				}
				else
				{
					$this->rendererObject = new AutotweetRenderBack3;
				}
			}
			else
			{
				if (EXTLY_J25)
				{
					$this->rendererObject = new AutotweetRenderFront25;
				}
				else
				{
					$this->rendererObject = new AutotweetRenderFront3;
				}
			}
		}

		return $this->rendererObject;
	}

	/**
	 * addItemeditorHelperApp
	 *
	 * @return string
	 */
	public static function addItemeditorHelperApp()
	{
		static $link = false;

		if ($link)
		{
			return $link;
		}

		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();

		list($isAdmin, $option, $controller, $task, $view, $layout, $id) = AutotweetBaseHelper::getControllerParams();

		$js = "var autotweetUrlRoot = '" . JUri::root() . "';\n";
		$js .= "var autotweetUrlBase = '" . JUri::base() . "';\n";

		$mediaPath = 'media/com_autotweet/js/itemeditor/templates/';
		$ext = '.txt';
		$joomlaPart = ('.j' . (EXTLY_J3 ? '3' : '25'));
		$sitePart = ($isAdmin ? '.admin' : '.site');

		$tpl0 = $mediaPath . $option . $ext;
		$tpl1 = $mediaPath . $option . $joomlaPart . $ext;
		$tpl2 = $mediaPath . $option . $sitePart . $joomlaPart . $ext;
		$tpl3 = $mediaPath . $option . $sitePart . $ext;

		if (file_exists(JPATH_ROOT . '/' . $tpl2))
		{
			$tpl = $tpl2;
		}
		elseif (file_exists(JPATH_ROOT . '/' . $tpl1))
		{
			$tpl = $tpl1;
		}
		elseif (file_exists(JPATH_ROOT . '/' . $tpl3))
		{
			$tpl = $tpl3;
		}
		elseif (file_exists(JPATH_ROOT . '/' . $tpl0))
		{
			$tpl = $tpl0;
		}
		else
		{
			$tpl = $mediaPath . 'com_joocial-default' . $joomlaPart . $ext;
		}

		$tpl = JUri::root() . $tpl . '?version=' . CAUTOTWEETNG_VERSION;

		$js .= "var autotweetPanelTemplate = 'text!" . $tpl . "';\n";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_autotweet&amp;view=itemeditor&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		// Add Advanced Attributes
		$params = null;

		// Case Request edit page
		if (($option == CAUTOTWEETNG) && ($view == 'request') && ($task == 'edit'))
		{
			$params = AdvancedattrsHelper::getAdvancedAttrByReq($id);
		}
		elseif ($id > 0)
		{
			$params = AdvancedattrsHelper::getAdvancedAttrs($option, $id);
		}

		if (!$params)
		{
			$params = new StdClass;
			$params->postthis = EParameter::getComponentParam(CAUTOTWEETNG, 'joocial_postthis', PlgAutotweetBase::POSTTHIS_DEFAULT);
			$params->evergreen = PlgAutotweetBase::POSTTHIS_NO;
			$params->agenda = array();
			$params->unix_mhdmd = '';
			$params->image = '';
			$params->channels = '';
			$params->channels_text = '';
		}

		$params->editorTitle = VersionHelper::getFlavourName() . ' ' . JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_TITLE');
		$params->postthisLabel = JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_POSTTHIS');
		$params->evergreenLabel = JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_EVERGREEN');
		$params->agendaLabel = JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_SCHEDULER');
		$params->unix_mhdmdLabel = JText::_('COM_XTCRONJOB_TASKS_FIELD_UNIX_MHDMD');
		$params->imageLabel = JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_IMAGES');
		$params->channelLabel = JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_CHANNELS');

		$params->postthisDefaultLabel = '<i class="xticon xticon-circle-o"></i> ' . JText::_('COM_AUTOTWEET_DEFAULT_LABEL');
		$params->postthisYesLabel = '<i class="xticon xticon-check"></i> ' . JText::_('JYES');
		$params->postthisNoLabel = '<i class="xticon xticon-times"></i> ' . JText::_('JNO');

		if (!isset($params->channels_text))
		{
			$params->channels_text = '';
		}

		AutotweetBaseHelper::convertUTCLocalAgenda($params->agenda);

		$js = 'var autotweetAdvancedAttrs = ' . json_encode($params) . ";\n";
		$doc->addScriptDeclaration($js);

		$file = EHtml::getRelativeFile('js', 'com_autotweet/itemeditor.helper.min.js');

		if ($file)
		{
			$paths = array();
			$paths = array(
							'text' => Extly::JS_LIB . 'require/text.min',
			);

			$deps = array(
							'itemeditor.helper' => array('text', 'underscore')
			);

			Extly::getScriptManager(false);
			Extly::initApp(CAUTOTWEETNG_VERSION, $file, $deps, $paths);
		}

		return $link;
	}

	/**
	 * addItemeditorHelperApp
	 *
	 * @return string
	 */
	public static function showWorldClockLink()
	{
		$offset = EParameter::getTimezone();

		$buffer = JText::_('COM_AUTOTWEET_SERVER_TIMEZONE_LABEL') . ': ' . trim($offset->getName());
		$buffer .= '<a onclick="window.open(this.href,\'World%2520Clock%2520%2526%2520Time%2520Zone%2520Map\',\'scrollbars=yes,resizable=yes,location=no,menubar=no,status=no,toolbar=no,left=0,top=0,width=800,height=500\');return false;" href="http://www.extly.com/timezone/tmz-201410.html" target="_blank" data-original-title="'
					. JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_WORLDCLOCK')
					. '" rel="tooltip"> <i class="xticon xticon-globe"></i></a>';

		return $buffer;
	}

	/**
	 * isEnabledAttrComps
	 *
	 * @return bool
	 */
	public static function isEnabledAttrComps()
	{
		$input = new F0FInput;

		$option = $input->get('option');
		$controller = $input->get('controller', '-');
		$view = $input->get('view', '-');
		$layout = $input->get('layout', '-');
		$task = $input->get('task', '-');

		if (array_key_exists($option, self::$enabledAttrComps)
			&& array_key_exists($controller, self::$enabledAttrComps[$option])
			&& array_key_exists($view, self::$enabledAttrComps[$option][$controller])
			&& array_key_exists($layout, self::$enabledAttrComps[$option][$controller][$view])
			&& array_key_exists($task, self::$enabledAttrComps[$option][$controller][$view][$layout]))
		{
			return self::$enabledAttrComps[$option][$controller][$view][$layout][$task];
		}

		return false;
	}

	/**
	 * Executes before rendering the page for the Add task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	protected function onAdd($tpl = null)
	{
		$result = parent::onAdd($tpl);

		if (($this->item->id == 0) && (isset($this->item->published)))
		{
			$this->item->published = $this->perms->editstate;
		}

		return $result;
	}
}
