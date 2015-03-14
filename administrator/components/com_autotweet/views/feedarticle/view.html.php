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
 * AutotweetViewFeedArticle
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class AutotweetViewFeedArticle extends F0FViewRaw
{
	/**
	 * onRead.
	 *
	 * @param   string  $tpl  Param
	 *
	 * @return	void
	 *
	 * @since	1.5
	 */
	protected function onRead($tpl = null)
	{
		$article = $this->input->get('article', null, 'raw');
		$this->assign('article', $article);

		$params = $this->input->get('params', null, 'raw');
		$this->assign('params', $params);

		$model = $this->getModel();
		$this->assign('item', $model->getItem());

		return true;
	}

	/**
	 * formatReadonLink.
	 *
	 * @return	string
	 */
	protected function formatReadonLink()
	{
		$article = $this->get('article');
		$params = $this->get('params');

		$target = null;

		if ($params->get('target_frame') == 'none')
		{
			$target = '';
		}
		elseif ($params->get('target_frame') == 'custom')
		{
			$target = 'target="' . $params->get('custom_frame') . '"';
		}
		else
		{
			$target = 'target="' . $params->get('target_frame') . '"';
		}

		$linkTitle = JString::trim(JString::substr($article->title, 0, 50));

		if ($params->get('shortlink'))
		{
			$readonPattern = '<a class="shortlink %s" rel="%s" title="%s" href="%s" %s>%s</a>';
			$readonlink = sprintf($readonPattern, $params->get('trackback_class'), $params->get('trackback_rel'), $linkTitle, $article->shortlink, $target, $params->get('orig_link_text'));
		}
		else
		{
			$readonPattern = '<span class="permalink-label">%s</span> <a class="permalink %s" rel="%s" title="%s" href="%s" %s>%s</a>';
			$readonlink = sprintf($readonPattern, $params->get('orig_link_text'), $params->get('trackback_class'), $params->get('trackback_rel'), $linkTitle, $article->permalink, $target, $article->permalink);
		}

		$readonlink = '<p class="trackback">' . $readonlink . '</p>';

		return $readonlink;
	}

	/**
	 * formatEnclosures.
	 *
	 * @return	string
	 */
	protected function formatEnclosures()
	{
		$result = array();

		$article = $this->get('article');

		$result[] = '<div class="joo-enclosures"><h3>' . JText::_('COM_AUTOTWEET_VIEW_FEED_ENCLOSURES') . '</h3><ol class="enclosures-list">';
		$i = 1;

		foreach ($article->enclosures as $enclosure)
		{
			$result[] = '<li><small><sup>';

			$result[] = "<a title='{$enclosure->title}' href='{$enclosure->link}'>^</a></sup></small> ";

			// Thesite.com
			$uri = JUri::getInstance($enclosure->link);
			$host = $uri->getHost();
			$parts = explode('.', $host);
			$o = count($parts) - 2;
			$dotcom = array_splice($parts, $o);
			$host = implode('.', $dotcom);

			$result[] = "<a name='enclosureLink-{$i}' rel='' href='{$enclosure->link}'>{$enclosure->title}</a><small> ({$host})";

			$result[] = '</small></li>';
		}

		$result[] = '</ol></div>';

		return implode('', $result);
	}
}
