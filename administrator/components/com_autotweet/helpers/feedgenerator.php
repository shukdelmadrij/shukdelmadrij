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
 * FeedGeneratorHelper class.
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class FeedGeneratorHelper
{
	private $_token = null;

	/**
	 * FeedHelper
	 *
	 */
	public function __construct()
	{
		include_once 'feedcontent.php';

		$this->_token = JFactory::getSession()->getFormToken();
	}

	/**
	 * removeDuplicates
	 *
	 * @param   array  &$contents  Params
	 *
	 * @return	void
	 */
	public function removeDuplicates(&$contents)
	{
		if (!count($contents))
		{
			return;
		}

		$keys = array();
		$output = array();

		foreach ($contents as $content)
		{
			$k = $content->hash;

			if (!array_key_exists($k, $keys))
			{
				$keys[$k] = $k;
				$output[] = $content;
			}
		}

		$contents = $output;
	}

	/**
	 * generate
	 *
	 * @param   array   &$content  Params
	 * @param   object  &$params   Params
	 *
	 * @return	int
	 */
	public function execute(&$content, &$params)
	{
		$i = 0;
		$logger = AutotweetLogger::getInstance();
		$articles = array();

		foreach ($content as $article)
		{
			try
			{
				$output = $this->_execute($article, $params);

				list($introtext, $fulltext) = FeedTextHelper::splitArticleText($output);
				$article->introtext = $introtext;
				$article->fulltext = $fulltext;
				$articles[] = $article;

				$i++;
			}
			catch (Exception $e)
			{
				$logger->log(JLog::ERROR, 'FeedGeneratorHelper: save ' . $e->getMessage());
			}
		}

		$content = $articles;

		return $i;
	}

	/**
	 * _execute
	 *
	 * @param   object  $article  Params
	 * @param   object  $params   Params
	 *
	 * @return	string
	 */
	private function _execute($article, $params)
	{
		$input = array(
						'task' => 'read',
						'article' => $article,
						'params' => $params,

						// State not in the session
						'savestate'	=> 0
		);

		$config = array(
						'option' => 'com_autotweet',
						'view' => 'feedarticle',
						'input' => $input,

						// Disable cache
						'cacheableTasks' => array()
		);

		ob_start();
		F0FDispatcher::getTmpInstance('com_autotweet', 'feedarticle', $config)->dispatch();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * save
	 *
	 * @param   array   &$content  Params
	 * @param   object  &$params   Params
	 *
	 * @return	int
	 */
	public function save(&$content, &$params)
	{
		$pending_to_feature = array();
		$value = 0;

		$c = 0;
		$logger = AutotweetLogger::getInstance();

		foreach ($content as $article)
		{
			try
			{
				$id = $this->_save($article, $params);

				if ($id)
				{
					$c++;

					if ($article->featured)
					{
						$pending_to_feature[] = $id;
						$value = $article->featured;
					}
				}
				else
				{
					$logger->log(JLog::WARNING, 'FeedGeneratorHelper: save ID=' . $id . ' Title=' . $article->title);
				}
			}
			catch (Exception $e)
			{
				$logger->log(JLog::ERROR, 'FeedGeneratorHelper: save ' . $e->getMessage());
			}
		}

		if (!empty($pending_to_feature))
		{
			$modelName = $params->get('contenttype_id');
			$model = F0FModel::getTmpInstance($modelName, 'AutoTweetModel');
			$model->featured($pending_to_feature, $value);
		}

		return $c;
	}

	/**
	 * _execute
	 *
	 * @param   array   &$article  Params
	 * @param   object  &$params   Params
	 *
	 * @return	int
	 */
	private function _save(&$article, &$params)
	{
		$modelName = $params->get('contenttype_id');

		$model = F0FModel::getTmpInstance($modelName, 'AutoTweetModel');

		$id = 0;

		$data = $this->_getData($article);

		// Article form
		$model->setState('form_name', 'article');

		$status = $model->save($data);

		$id = $model->getId();

		if (($status) && ($id != 0))
		{
			// Try to check-in the record if it's not a new one
			$status = $model->checkin();
		}

		return $id;
	}

	/**
	 * _processEnclosure
	 *
	 * @param   string  &$enclosure  Params
	 * @param   string  &$text       Params
	 *
	 * @return	void
	 */
	private function processEnclosure(&$enclosure, &$text)
	{
		if (!JFolder::exists($this->_params->get('savepath')))
		{
			JFolder::create($this->_params->get('savepath'));
		}

		$real_type = strtolower($e->get_real_type());
		$src = $enclosure->get_link();
		$real_name = array_pop(explode('/', $src));
		$name = $enclosure->get_title() ? $enclosure->get_title() : $e->get_caption();

		if (!$name)
		{
			$name = $real_name;
		}

		$e_inf = '';
		$saved = false;

		if (strpos($real_type, 'audio') !== false)
		{
			// Audio
			$e_img = 'audio';

			if ($this->_params->get('save_enc'))
			{
				$saved = $this->_saveEnclosure($name, 'audio', $src, $this->_params);
			}

			$e_lnk = '<a href="' . ($saved ? $this->_params->get('srcpath') . 'audio/' . $name : $src) . '">' . $name . '</a>';

			if ($e->get_duration())
			{
				$e_inf .= 'Duration: ' . $e->get_duration() . ' seconds<br />';
			}

			if ($e->get_size())
			{
				$e_inf .= 'Size: ' . $e->get_size() . ' Mb';
			}

			if (($saved) && (!$this->_params->get('create_art', 1)))
			{
				$content ['id'] = -1;
			}
		}
		elseif (strpos($real_type, 'video') !== false)
		{
			// Videos
			$e_img = $e->get_thumbnail();

			if ($this->_params->get('save_enc'))
			{
				$saved = $this->_saveEnclosure($name, 'videos', $src, $this->_params);
			}

			$e_lnk = '<a href="' . ($saved ? $this->_params->get('srcpath') . 'videos/' . $name : $src) . '">' . $name . '</a>';

			if ($e->get_duration())
			{
				$e_inf .= 'Duration: ' . $e->get_duration() . ' seconds<br />';
			}

			if ($e->get_size())
			{
				$e_inf .= 'Size: ' . $e->get_size() . ' Mb';
			}

			if (($saved) && (!$this->_params->get('create_art', 1)))
			{
				$content ['id'] = -2;
			}
		}
		elseif (strpos($real_type, 'image') !== false)
		{
			// Images
			$e_img = 'image';

			if ($this->_params->get('save_enc'))
			{
				$saved = $this->_saveEnclosure($name, 'images', $src, $this->_params);
			}

			$e_lnk = '<a href="' . ($saved ? ($this->_params->get('save_enc_image_as_img') ? $this->_params->get('img_srcpath') : $this->_params->get('srcpath') . 'images/') . $name : $src) . '">' . $name . '</a>';

			if ($e->get_size())
			{
				$e_inf .= 'Size: ' . $e->get_size() . ' Mb';
			}

			if (($saved) && (!$this->_params->get('create_art', 1)))
			{
				$content ['id'] = -3;
			}
		}
		elseif (strpos($real_type, 'pdf') !== false)
		{
			// It is this needed - depends on user/dev requirements - possible google viewer link...
			$e_img = 'pdf';

			if ($this->_params->get('save_enc'))
			{
				$saved = $this->_saveEnclosure($name, 'attachments', $src, $this->_params);
			}

			$e_lnk = '<a href="' . ($saved ? $this->_params->get('srcpath') . 'attachments/' . $name : $src) . '">' . $name . '</a>';

			if ($e->get_size())
			{
				$e_inf .= 'Size: ' . $e->get_size() . ' Mb';
			}

			if (($saved) && (!$this->_params->get('create_art', 1)))
			{
				$content ['id'] = -4;
			}
		}
		elseif (strpos($real_type, 'doc') !== false)
		{
			// Support various "serious" doctypes
			switch ($e->get_extension())
			{
				case '.doc':
				case '.docx':
					$e_img = 'word';
					break;
				case '.xls':
				case '.xlsx':
					$e_img = 'xls';
					break;
				case '.ppt':
				case '.pptx':
					$e_img = 'ppt';
					break;
				case '.odf':
					$e_img = 'odf';
					break;
				default:
					$e_img = 'generic';
					break;
			}

			if ($this->_params->get('save_enc'))
			{
				$saved = $this->_saveEnclosure($name, 'attachments', $src, $this->_params);
			}

			$e_lnk = '<a href="' . ($saved ? $this->_params->get('srcpath') . 'attachments/' . $name : $src) . '">' . $name . '</a>';

			if ($e->get_size())
			{
				$e_inf .= 'Size: ' . $e->get_size() . ' Mb';
			}

			if (($saved) && (!$this->_params->get('create_art', 1)))
			{
				$content ['id'] = -5;
			}
		}
		elseif (strpos($real_type, 'zip') !== false)
		{
			// Archives - need to look into how rar/gz etc are shown in enclosures
			$e_img = 'archive';

			if ($this->_params->get('save_enc'))
			{
				$saved = $this->_saveEnclosure($name, 'attachments', $src, $this->_params);
			}

			$e_lnk = '<a href="' . ($saved ? $this->_params->get('srcpath') . 'attachments/' . $name : $src) . '">' . $name . '</a>';

			if ($e->get_size())
			{
				$e_inf .= 'Size: ' . $e->get_size() . ' Mb';
			}

			if (($saved) && (!$this->_params->get('create_art', 1)))
			{
				$content ['id'] = -6;
			}
		}
		else
		{
			$e_img = 'generic';

			if ($this->_params->get('save_enc'))
			{
				$saved = $this->_saveEnclosure($name, 'attachments', $src, $this->_params);
			}

			$e_lnk = '<a href="' . ($saved ? $this->_params->get('srcpath') . 'attachments/' . $name : $src) . '">' . $name . '</a>';

			if ($e->get_size())
			{
				$e_inf .= 'Size: ' . $e->get_size() . ' Mb';
			}

			if (($saved) && (!$this->_params->get('create_art', 1)))
			{
				$content ['id'] = -7;
			}
		}

		$img = sprintf('<img class="xt_enclosure_img" src="%sadministrator/components/com_feedgator/images/%s.png" height="16" width="16" style="margin:8px 8px;">', $this->_params->get('base'), $e_img);
		$e_lnk = sprintf('<div class="xt_enclosure_lnk" style="padding-left:34px;white-space:nowrap;">%s</div>', $e_lnk);

		if ($e_inf)
		{
			$e_inf = sprintf('<div class="xt_enclosure_inf" style="padding-left:34px;white-space:nowrap;"">%s</div>', $e_inf);
		}

		$e_out = sprintf('<div class="xt_enclosure" style="margin:10px 0px;"><div class="xt_enclosure_img" style="display:inline-block;position:absolute;">%s</div>%s%s</div>', $img, $e_lnk, $e_inf);
		$text .= $e_out;
	}

	/**
	 * _saveEnclosure
	 *
	 * @param   string  $name  Params
	 * @param   string  $type  Params
	 * @param   string  $src   Params
	 *
	 * @return	void
	 */
	public function _saveEnclosure($name, $type, $src)
	{
		if ($type == 'images')
		{
			$savepath = $this->_params->get('save_enc_image_as_img', 1) ? $this->_params->get('img_savepath') : $this->_params->get('savepath') . $type . '/';

			if (!JFolder::exists($savepath))
			{
				JFolder::create($savepath);
			}

			$file_path = $savepath . $name;
		}
		else
		{
			$savepath = $this->_params->get('savepath') . $type . '/';

			if (!JFolder::exists($savepath))
			{
				JFolder::create($savepath);
			}

			$file_path = $savepath . $name;
		}

		if (!file_exists($file_path))
		{
			if (!$contents = TextUtil::getUrl(TextUtil::encode_url($src), $this->_params->get('scrape_type'), $type, $file_path))
			{
				// Enclosure Not Saved');
				return false;
			}
			else
			{
				// Enclosure Saved');
			}
		}
		else
		{
			// Enclosure Already Saved');
		}

		return true;
	}

	/**
	 * _getData
	 *
	 * @param   array  &$article  Params
	 *
	 * @return  array
	 */
	private function _getData(&$article)
	{
		$data = array(
						'id' => $article->id,
						'title' => $article->title,
						'catid' => $article->cat_id,
						'articletext' => $article->introtext,
						'introtext' => $article->introtext,
						'fulltext' => $article->fulltext,
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
