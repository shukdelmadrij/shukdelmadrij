<?php
/**
 * @version  3.2
 * @Project  Facebook Like, Twitter and google +1 buttons
 * @author   Compago TLC
 * @package  j2.5-j3.0
 * @copyright Copyright (C) 2013 Compago TLC. All rights $this->str_encode.
 * @license  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */
//error_reporting(E_ALL|E_STRICT);
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
if (!defined('DS')) {
  define('DS', DIRECTORY_SEPARATOR);
}

jimport('joomla.version');
$version = new JVersion();
if ($version->RELEASE == '1.5') {
  define('J_VERSION', $version->RELEASE);
  define('CMP_OG_LINKIMG'         , 'plugins/content/linkcmp.png');
  define('CMP_FB_LOGOIMG'         , 'plugins/content/fbcmp.png');
  define('CMP_PLG_NOTIFY'         , 'plugins/content/facebook/notify.php');
  define('CUSTOM_FB_SHARE_IMG'    , '/plugins/content/fbicon.png');
  define('CUSTOM_TWITTER_IMG'     , '/plugins/content/twittericon.png');
  define('CUSTOM_GOOGLE_IMG'      , '/plugins/content/googleicon.png');
  define('CUSTOM_LINKEDIN_IMG'    , '/plugins/content/linkedinicon.png');
  define('CUSTOM_PINTEREST_IMG'   , '/plugins/content/pinteresticon.png');
} else {
  define('J_VERSION', $version->RELEASE);
  define('CMP_OG_LINKIMG'         , 'plugins/content/fb_tw_plus1/media/linkcmp.png');
  define('CMP_FB_LOGOIMG'         , 'plugins/content/fb_tw_plus1/media/fbcmp.png');
  define('CMP_PLG_NOTIFY'         , 'plugins/content/fb_tw_plus1/facebook/notify.php');
  define('CUSTOM_FB_SHARE_IMG'    , '/plugins/content/fb_tw_plus1/media/fbicon.png');
  define('CUSTOM_TWITTER_IMG'     , '/plugins/content/fb_tw_plus1/media/twittericon.png');
  define('CUSTOM_GOOGLE_IMG'      , '/plugins/content/fb_tw_plus1/media/googleicon.png');
  define('CUSTOM_LINKEDIN_IMG'    , '/plugins/content/fb_tw_plus1/media/linkedinicon.png');
  define('CUSTOM_PINTEREST_IMG'   , '/plugins/content/fb_tw_plus1/media/pinteresticon.png');
}

$document = JFactory::getDocument();
$docType = $document->getType();
// only in html
if ($docType != 'html') {
  return;
}
if (!class_exists('ContentHelperRoute'))
  require_once(JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');
if (!class_exists('JSite'))
  require_once(JPATH_SITE . DS . 'includes' . DS . 'application.php');

jimport('joomla.plugin.plugin');
jimport('joomla.environment.browser');

class plgContentfb_tw_plus1 extends JPlugin {

  function plgContentfacebooklikeandshare(&$subject, $params) {
    parent::__construct($subject, $params);
  }
  public $images = array();
  public $contents = array();
  public $descriptions = array();
  public $description = '';

  // Joomla 1.5
  function onPrepareContent(&$article, &$params, $limitstart) {
    $this->InjectCode($article, $params, $limitstart, 0);
  }
  // Joomla >1.5
  function onContentPrepare($context, &$article, &$params, $page = 0) {
    $this->InjectCode($article, $params, $page, 0);
  }
  // Joomla 1.5
  function onBeforeDisplayContent(&$article, &$params, $limitstart) {
    $this->InjectCode($article, $params, $limitstart, 1);
  }
  // Joomla >1.5
  function onContentBeforeDisplay($context, &$article, &$params, $page = 0) {
    $this->InjectCode($article, $params, $page, 1);
  }
  // Joomla 1.5
  function onAfterContentSave(&$article, $isNew) {
    if (!isset($context))
      $context = NULL;
    $this->Save($context, $article, $isNew);
  }
  // Joomla >1.5
  function onContentAfterSave($context, $article, $isNew) {
    if (!isset($context))
      $context = NULL;
    $this->Save($context, $article, $isNew);
  }

  private function InjectCode(&$article, &$params, $limitstart, $mode) {

    $app = JFactory::getApplication();

    if (!property_exists($article, 'id') || !isset($article->id))
      return;
    $format = '';
    if (isset($app->input)) {
      $format = $app->input->get('format');
    } else {
      $format = JRequest::getCmd('format');
    }

    if (($format == 'pdf') || ($format == 'feed'))
      return;
    $enable_fb_comments = $this->params->get('enable_fb_comments');
    $enable_fb_like = $this->params->get('enable_fb_like');
    $enable_fb_share = $this->params->get('enable_fb_share');
    $enable_fb_send = $this->params->get('enable_fb_send');
    $enable_twitter = $this->params->get('enable_twitter');
    $enable_google = $this->params->get('enable_google');
    $enable_google_share = $this->params->get('enable_google_share');
    $enable_linkedin = $this->params->get('enable_linkedin');
    $enable_pinterest = $this->params->get('enable_pinterest');

    $pinterest_selection = $this->params->get('pinterest_selection');

    $view = '';
    if (isset($app->input)) {
      $view = $app->input->get('view');
    } else {
      $view = JRequest::getCmd('view');
    }

    $view_article_buttons = $this->params->get('view_article_buttons');
    $view_frontpage_buttons = $this->params->get('view_frontpage_buttons');
    $view_category_buttons = $this->params->get('view_category_buttons');
    if (J_VERSION == '1.5')
      $view_section_buttons = $this->params->get('view_section_buttons');
    $view_virtuemart_buttons = $this->params->get('view_virtuemart_buttons');

    $view_article_comments = $this->params->get('view_article_comments');
    $view_frontpage_comments = $this->params->get('view_frontpage_comments');
    $view_category_comments = $this->params->get('view_category_comments');
    if (J_VERSION == '1.5')
      $view_section_comments = $this->params->get('view_section_comments');
    $view_virtuemart_comments = $this->params->get('view_virtuemart_comments');

    $enable_view_comments = 0;
    $enable_view_buttons = 0;
    $enable_buttons = 0;

    if (J_VERSION == '1.5') {
      if (($view == 'article') && ($view_article_buttons) || ($view == 'frontpage') && ($view_frontpage_buttons) || ($view == 'category') && ($view_category_buttons)
          || ($view == 'section') && ($view_section_buttons) || ($view == 'productdetails') && ($view_virtuemart_buttons)) {
        $enable_view_buttons = 1;
      }
      if (($view == 'article') && ($view_article_comments) || ($view == 'frontpage') && ($view_frontpage_comments) || ($view == 'category') && ($view_category_comments)
          || ($view == 'section') && ($view_section_comments) || ($view == 'productdetails') && ($view_virtuemart_comments)) {
        $enable_view_comments = 1;
      }
    } else {
      if (($view == 'article') && ($view_article_buttons) || ($view == 'featured') && ($view_frontpage_buttons) || ($view == 'category') && ($view_category_buttons)
          || ($view == 'productdetails') && ($view_virtuemart_buttons)) {
        $enable_view_buttons = 1;
      }
      if (($view == 'article') && ($view_article_comments) || ($view == 'featured') && ($view_frontpage_comments) || ($view == 'category') && ($view_category_comments)
          || ($view == 'productdetails') && ($view_virtuemart_comments)) {
        $enable_view_comments = 1;
      }
    }

    if (($enable_fb_like == '1') || ($enable_fb_share == '1') || ($enable_fb_send == '1') || ($enable_linkedin == '1') || ($enable_google == '1') || ($enable_google_share == '1')
        || ($enable_twitter == '1') || (($enable_pinterest == '1') && ($pinterest_selection == '0'))) {
      $enable_buttons = 1;
    }

    if (($enable_buttons == 1) && ($enable_view_buttons == 1)) {
      if ($view == 'productdetails') {
        $enable_buttons = 1;
      } else {
        $category_tobe_excluded_buttons = $this->params->get('category_tobe_excluded_buttons', '');
        $content_tobe_excluded_buttons = $this->params->get('content_tobe_excluded_buttons', '');
        $excludedContentList_buttons = @explode(",", $content_tobe_excluded_buttons);
        if (isset($article->id)) {
          if (in_array($article->id, $excludedContentList_buttons))
            $enable_buttons = 0;
          if (is_array($category_tobe_excluded_buttons)) {
            if (in_array($article->catid, $category_tobe_excluded_buttons))
              $enable_buttons = 0;
          } else {
            $excludedCatList_buttons = @explode(",", $category_tobe_excluded_buttons);
            if (in_array($article->catid, $excludedCatList_buttons))
              $enable_buttons = 0;
          }
        } else {
          if (isset($app->input)) {
            $id = $app->input->get('id');
          } else {
            $id = JRequest::getCmd('id');
          }
          ;
          if (is_array($category_tobe_excluded_buttons)) {
            if (in_array($id, $category_tobe_excluded_buttons))
              $enable_buttons = 0;
          } else {
            $excludedCatList_buttons = @explode(",", $category_tobe_excluded_buttons);
            if (in_array($id, $excludedCatList_buttons))
              $enable_buttons = 0;
          }
        }
      }
    } else {
      $enable_buttons = 0;
    }

    if (($enable_fb_comments == '1') && ($enable_view_comments == 1)) {
      if ($view == 'productdetails') {
        $enable_fb_comments = '1';
      } else {
        $category_tobe_excluded_comments = $this->params->get('category_tobe_excluded_comments');
        $content_tobe_excluded_comments = $this->params->get('content_tobe_excluded_comments', '');
        $excludedContentList_comments = @explode(",", $content_tobe_excluded);
        if (isset($article->id)) {
          if (in_array($article->id, $excludedContentList_comments))
            $enable_fb_comments = '0';
          if (is_array($category_tobe_excluded_comments)) {
            if (in_array($article->catid, $category_tobe_excluded_comments))
              $enable_fb_comments = '0';
          } else {
            $excludedCatList_comments = @explode(",", $category_tobe_excluded_comments);
            if (in_array($article->catid, $excludedCatList_comments))
              $enable_fb_comments = '0';
          }
        } else {
          if (isset(JFactory::getApplication()->input)) {
            $id = JFactory::getApplication()->input->get('id');
          } else {
            $id = JRequest::getCmd('id');
          }

          if (is_array($category_tobe_excluded_comments)) {
            if (in_array($id, $category_tobe_excluded_comments))
              $enable_fb_comments = '0';
          } else {
            $excludedCatList_comments = @explode(",", $category_tobe_excluded_comments);
            if (in_array($id, $excludedCatList_comments))
              $enable_fb_comments = '0';
          }
        }
      }
    } else {
      $enable_fb_comments = '0';
    }

    if (isset($app->input)) {
      $print = $app->input->get('print');
    } else {
      $print = JRequest::getCmd('print');
    }

    if ($print == 1) { // print page mode
      $enable_buttons = 0; // no buttons
      if ($this->params->get('fb_comments_print', '0') == '0')
        $enable_fb_comments = '0';
    }

    $title = $this->str_encode($this->getTitle($article));

    $url = JURI::base();
    $url = new JURI($url);
    $base = $url->toString(array('scheme', 'host', 'port'));

    if (empty($article->catslug)) {
      if (!isset($article->catid)) {
        $url = $article->link;
      } else {
        $url = JRoute::_(ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias, $article->catid));
      }
    } else {
      $url = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug));
    }

    $url = $base . $url;

    $n = mt_rand(1, 10000);
    $document = JFactory::getDocument();

    $htmlButtonsCode0 = '';
    $htmlButtonsCode1 = '';
    if ($mode == 1) { //on content before display
      if ($enable_buttons == 1) {
        if ($this->description == '') {
          $this->description = $this->getDescription($article, $view);
        }
        $htmlButtonsCode0 = "{idkey=" . $n . "b0[url=" . urlencode($url) . "][title=" . urlencode($title) . "][desc=" . urlencode($this->description) . "]}";
        $htmlButtonsCode1 = "{idkey=" . $n . "b1[url=" . urlencode($url) . "][title=" . urlencode($title) . "][desc=" . urlencode($this->description) . "]}";
      }
      $htmlCommentsCode = "";
      if ($enable_fb_comments == '1') {
        $htmlCommentsCode = "{idkey='" . $n . "c'[url=" . urlencode($url) . "]}";
      }
      $article->text = $htmlButtonsCode0 . $article->text . $htmlButtonsCode1 . $htmlCommentsCode;
      if (isset($article->introtext)) {
        $article->introtext = $htmlButtonsCode0 . $article->introtext . $htmlButtonsCode1 . $htmlCommentsCode;
      }
    } else { // on content prepare
      if ($enable_buttons == 1) {
        if ($this->description == '') {
          $this->description = $this->getDescription($article, $view);
        }
        $htmlButtonsCode0 = "{cmp_start idkey=" . $n . "[url=" . urlencode($url) . "][title=" . urlencode($title) . "][desc=" . urlencode($this->description) . "]}";
        $htmlButtonsCode1 = "{cmp_end}";
      }
      $htmlCommentsCode = "";
      if ($enable_fb_comments == '1') {
        $htmlCommentsCode = "{cmp_comments idkey='" . $n . "c'[url=" . urlencode($url) . "]}";
      }
      $article->text = $htmlButtonsCode0 . $article->text . $htmlButtonsCode1 . $htmlCommentsCode;
      if (isset($article->introtext)) {
        $article->introtext = $htmlButtonsCode0 . $article->introtext . $htmlButtonsCode1 . $htmlCommentsCode;
      }
    }
    return true;
  } //end InjectCode

  function onBeforeRender(){
    $cache = JFactory::getCache('');
    $cache->setCaching(false);
  }
 
  //Just render the plugins already created and add opengraph informations
  function onAfterRender(){
    //avoid caching
    $cache = JFactory::getCache('');
    if (JPluginHelper::isEnabled('system', 'cache')) {
      $cache->clean();
    } 
/*
    //avoid caching
    $cache = JFactory::getCache('');
    if (JPluginHelper::isEnabled('system', 'cache')) {
      $cache->clean();
    } else {
      if (isset($cache->options)) {
        if ($cache->options['caching'])
          $cache->clean();
      } else {
        if ($cache->_options['caching'])
          $cache->clean();
      }
    }
*/

    // no action on administration side
    $app = JFactory::getApplication();
    if ($app->isAdmin()) {
      return;
    }

    $document = JFactory::getDocument();
    $pagetitle = str_replace('"', '\'', $document->getTitle());

    if (isset($app->input)) {
      $view = $app->input->get('view');
    } else {
      $view = JRequest::getCmd('view');
    }

    if (isset($app->input)) {
      $option = $app->input->get('option');
    } else {
      $option = JRequest::getCmd('option');
    }

    if (isset($app->input)) {
      $limitstart = $app->input->get('limitstart');
    } else {
      $limitstart = JRequest::getCmd('limitstart');
    }

    $ignore_pagination = $this->params->get('ignore_pagination');
    if (is_numeric($limitstart) && ($ignore_pagination == '0')) {
      if ($view == 'productdetails')
        $pageurl = JFactory::getURI()->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path'));
      else
        $pageurl = JFactory::getURI()->toString();
    } else {
      $uri=JURI::getInstance();
      $query = $app->getRouter()->parse($uri);
      if ((isset($query['id'])) && (strpos($query['id'], ':') === false)) {
        if ($option == 'com_content' && $view == 'article') {
          $article = JTable::getInstance('content');
          $article->load($query['id']);
          $alias = $article->get('alias');
          if (empty($alias)) {
            jimport('joomla.filter.output');
            $alias = JFilterOutput::stringURLSafe($article->get('title'));
          }
          $slug = $article->get('id') . ':' . $alias;
          $query['id'] = $slug;
        }
      }
      $pageurl = JURI::root() . 'index.php?' . JURI::getInstance()->buildQuery($query);
      if ($view == 'productdetails')
        $pageurl = $app->getRouter()->build($pageurl)->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path'));
      elseif (($view == 'featured') || ($view == 'frontpage'))
        $pageurl = $app->getRouter()->build($pageurl)->toString(array('scheme', 'user', 'pass', 'host', 'port'));
      else
        $pageurl = $app->getRouter()->build($pageurl)->toString();
    }
    //fix double base path in page url
    $basepath = JURI::base(true);
    $pageurl=preg_replace('|('.$basepath.')('.$basepath.')|i','\1',$pageurl);
    
    $body = JResponse::getBody();

    $enable = array('fb_comments' => $this->params->get('enable_fb_comments'), 
                    'fb_like'     => $this->params->get('enable_fb_like'), 
                    'fb_share'    => $this->params->get('enable_fb_share'),
                    'fb_send'     => $this->params->get('enable_fb_send'), 
                    'fb_photo'    => $this->params->get('enable_fb_photo'), 
                    'twitter'     => $this->params->get('enable_twitter'),
                    'google'      => $this->params->get('enable_google'), 
                    'google_share'=> $this->params->get('enable_google_share'), 
                    'linkedin'    => $this->params->get('enable_linkedin'),
                    'pinterest'   => $this->params->get('enable_pinterest'));
    $pinterest_selection = $this->params->get('pinterest_selection');
    $fb_enable_admin = $this->params->get('fb_enable_admin');
    $fb_admin_ids = $this->params->get('fb_admin_ids');
    $fb_app_id = $this->params->get('fb_app_id');
    if ($fb_app_id == '')
      $enable['fb_photo'] = '0';
    // enable fb photo button only if an app id it's set
    $enable_opengraph = $this->params->get('enable_opengraph', '0');

    if (($enable['fb_like'] == '1') || ($enable['fb_share'] == '1') || ($enable['fb_send'] == '1') || ($enable['linkedin'] == '1') || ($enable['google'] == '1') || ($enable['google_share'] == '1')
        || ($enable['twitter'] == '1') || (($enable['pinterest'] == '1') && ($pinterest_selection == '0'))) {
      $enable_buttons = 1;
    } else {
      $enable_buttons = 0;
    }

    $position = array(
        'fb_like'      => $this->params->get('Position_fb_like'),
        'fb_share'     => $this->params->get('Position_fb_share'),
        'fb_send'      => $this->params->get('Position_fb_send'),
        'google'       => $this->params->get('Position_google'),
        'google_share' => $this->params->get('Position_google_share'),
        'twitter'      => $this->params->get('Position_twitter'),
        'linkedin'     => $this->params->get('Position_linkedin'),
        'pinterest'    => $this->params->get('Position_pinterest')
    );
    $weight = array(
        'fb_like'     =>$this->params->get('weight_fb_like'),
        'fb_share'    =>$this->params->get('weight_fb_share'),
        'fb_send'     =>$this->params->get('weight_fb_send'),
        'google'      =>$this->params->get('weight_google'),
        'google_share'=>$this->params->get('weight_google_share'),
        'twitter'     =>$this->params->get('weight_twitter'),
        'linkedin'    =>$this->params->get('weight_linkedin'),
        'pinterest'   =>$this->params->get('weight_pinterest')
    );
    asort($weight);

    $isprint = 0;
    if (isset($app->input)) {
      $isprint = $app->input->get('print');
    } else {
      $isprint = JRequest::getCmd('print');
    }

    if ($isprint == 1) { // print page mode
      $enable_buttons = 0; // no buttons
      if ($this->params->get('fb_comments_print', '0') == '0')
        $enable['fb_comments'] = '0';
    }

    if (($enable['fb_comments'] == '1') || ($enable['fb_photo'] == '1') || ($enable['fb_like'] == '1') || ($enable['fb_share'] == '1') || ($enable['fb_send'] == '1')) {
      // add root tag if necessary
      if (!preg_match('%<div[\s]+id[\s]*=[\s]*["\']fb-root["\'][\s]*>[\s]*</div>%', $body)) {
        $s = "<div id='fb-root'></div>";
        $body = preg_replace('/(<body.*?>)/', '\1' . $s, $body);
      }
      if ($this->params->get('fb_mode') == "xfbml") {
        if (!preg_match('/xmlns:fb="http:\/\/ogp\.me\/ns\/fb#"/', $body)) {
          $body = preg_replace('/<html(.*?)>/', '<html \1 xmlns:fb="http://ogp.me/ns/fb#">', $body);
        }
      }
    }

    // render buttons

    if ($enable_buttons) {
      //content prepare + before content display
      //-----------------------( 1  )--------(   2  )----------(   3  )---------(   4  )-----------------------( 5 )-----------( 6 )---------------------
      if (preg_match_all('/\{idkey=(\d*?)b0\[url=([^]]*?)\]\[title=([^]]*?)\]\[desc=([^]]*?)\]\}\{cmp_start[^}]*?\}(.*?)\{cmp_end\}(.*?)\{idkey=\d*?b1[^}]*?\}/sim', $body, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
          $this->contents[] = $match[5];
          $this->descriptions[] = $match[4];
          $randomid = $match[1];
          $url = $match[2];
          $title = $match[3];

          $code_buttons = $this->getButtonsHTMLcode($url, $title, $randomid, $enable, $position, $weight);
          if (isset($match[6]) && !empty($match[6])) {
            $code_buttons[1] = $code_buttons[1] . $match[6];
          }
          // apply change to the page body
          $body = str_replace($match[0], $code_buttons[0] . $match[5] . $code_buttons[1], $body);
        }
      }

      //content prepare (Virtuemart)
      //----------------------------------( 1  )------( 2 )----------( 3 )---------( 4 )------( 5 )-----------
      if (preg_match_all('/\{cmp_start idkey=(\d*?)\[url=(.*?)\]\[title=(.*?)\]\[desc=(.*?)\]]*\}(.*?)\{cmp_end\}/sim', $body, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
          $this->contents[] = $match[5];
          $this->descriptions[] = $match[4];
          $randomid = $match[1];
          $url = $match[2];
          $title = $match[3];

          $code_buttons = $this->getButtonsHTMLcode($url, $title, $randomid, $enable, $position, $weight);
          // apply change to the page body
          $body = str_replace($match[0], $code_buttons[0] . $match[5] . $code_buttons[1], $body);
        }
      }

      //before content display + pagination
      //-----------------------( 1  )--------(   2  )----------(   3  )---------(   4  )----(( 6 )         5        )
      if (preg_match_all('/\{idkey=(\d*?)b0\[url=([^]]*?)\]\[title=([^]]*?)\]\[desc=([^]]*?)\]\}((.*?)\{cmp_start[^}]*\}){0,1}/sim', $body, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
          $this->descriptions[] = $match[4];
          $randomid = $match[1];
          if (is_numeric($limitstart) && ($ignore_pagination == '0')) {
            $url = JFactory::getURI()->toString();
          } else {
            $url = $match[2];
          }
          $title = $match[3];

          $code_buttons = $this->getButtonsHTMLcode($url, $title, $randomid, $enable, $position, $weight);
          if ($ignore_pagination == '1') {
            if (isset($match[5]) && !empty($match[5])) {
              $code_buttons[0] = $code_buttons[0] . $match[6];
            } else {
              $code_buttons[0] = '';
            }
          } else {
            if (isset($match[5]) && !empty($match[5])) {
              $code_buttons[0] = $code_buttons[0] . $match[6];
            }
          }

          // apply change to the page body
          $body = str_replace($match[0], $code_buttons[0], $body);
        }
      }
      //------------(        1          ( 2 ))-------------(  3 )--------( 4 )----------( 5 )---------( 6 )---
      if (preg_match_all('/(\{cmp_end[^}]*\}(.*?)){0,1}\{idkey=(\d*?)b1\[url=(.*?)\]\[title=(.*?)\]\[desc=(.*?)\]\}/sim', $body, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
          $this->descriptions[] = $match[6];
          $randomid = $match[3];
          if (is_numeric($limitstart) && ($ignore_pagination == '0')) {
            $url = JFactory::getURI()->toString();
          } else {
            $url = $match[4];
          }

          $title = $match[5];

          $code_buttons = $this->getButtonsHTMLcode($url, $title, $randomid, $enable, $position, $weight);
          if ($ignore_pagination == '1') {
            if (isset($match[1]) && !empty($match[1])) {
              $code_buttons[1] = $match[2] . $code_buttons[1];
            } else {
              $code_buttons[1] = '';
            }
          } else {
            if (isset($match[1]) && !empty($match[1])) {
              $code_buttons[1] = $match[2] . $code_buttons[1];
            }
          }
          // apply change to the page body
          $body = str_replace($match[0], $code_buttons[1], $body);
        }
      }
    } // end render buttons

    // render fb comment box
    if ($enable['fb_comments'] == '1') {
      //content prepare + before content display
      //------------------------(  1 )------------( 2 )----( 3 )----------(  4 )------------( 5 )----
      if (preg_match_all('/\{cmp_comments idkey=\'(\d*?)c\'.*?\[url=(.*?)\]\}(.*?)\{idkey=\'(\d*?)c\'.*?\[url=(.*?)\]\}/sim', $body, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
          if (is_numeric($limitstart) && ($ignore_pagination == '0')) {
            $url = JFactory::getURI()->toString();
          } else {
            $url = $match[2];
          }
          $code_comments = $this->getFbCommentsCode($url);
          if (isset($match[3])) {
            $code_comments = $match[3] . $code_comments;
          }

          // apply change to the page body
          $body = str_replace($match[0], $code_comments, $body);
        }
      }
      //content prepare
      //------------------------(  1 )------------(  2 )---
      if (preg_match_all('/\{cmp_comments idkey=\'(\d*?)c\'.*?\[url=(.*?)\]\}/sim', $body, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
          $url = $match[2];
          $code_comments = $this->getFbCommentsCode($url);
          // apply change to the page body
          $body = str_replace($match[0], $code_comments, $body);
        }
      }
      //before content display + pagination
      //-----------(  1 )------------( 2 )----
      if (preg_match_all('/\{idkey=\'(\d*?)c\'[^[]*?\[url=(.*?)\]\}/sim', $body, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
          if (is_numeric($limitstart) && ($ignore_pagination == '0')) {
            $url = JFactory::getURI()->toString();
          } else {
            $url = $match[2];
          }

          $code_comments = $this->getFbCommentsCode($url);
          if ($ignore_pagination == '1') {
            $code_comments = '';
          }
          // apply change to the page body
          $body = str_replace($match[0], $code_comments, $body);
        }
      }
    } // end render comments box

    // Facebook photo button
    // put fb_photo_button in every images
    if ($enable['fb_photo'] == '1') {
      // find images in the text content
      preg_match_all('/(<\s*img)(\s+[^>]*?src\s*=\s*["\'])(.*?)(["\'][^>]*?>)/i', $body, $m, PREG_SET_ORDER);
      $m = array_unique($m, SORT_REGULAR);
      $mm = array();
      // for every photo fix the url
      foreach ($m as $key => &$img) {
        if (!preg_match('%^(?://|http://|https://)%', $img[3])) {
          $img[3] = JURI::root() . preg_replace('#^/#', '', $img[3]);
        }
        $img[3] = preg_replace('%^//%', JFactory::getURI()->getScheme() . '://', $img[3]);
        if ($this->check_img_size($img[3]))
          $mm[] = $img;
      }
      $pic = array();
      $img = array();
      $fbp = array();
      $pin = array();
      // for every photo assign a ID
      foreach ($mm as $key => &$img) {
        $n = mt_rand(1, 10000);
        $pic[] = 'img' . $n;
        $fbp[] = 'fbp' . $n;
        $pin[] = 'pin' . $n;
        $mm[$key][5] = $mm[$key][1] . ' id="img' . $n . '" ' . $mm[$key][2] . $img[3] . $mm[$key][4];
        $body = str_replace($mm[$key][0], $mm[$key][5], $body);
      }

      // add the button code at the end of the text
      $tmp = '';
      foreach ($fbp as $key => $id) {
        $tmp .= "<span id='" . $id . "' class='css_fb_photo'><img src=\"" . JURI::root() . CMP_FB_LOGOIMG . "\" title=\"Add to Facebook\" onclick=\"fb_click_photo('" . $mm[$key][3] . "','"
            . $pagetitle . "');\" /></span>" . PHP_EOL;
      }
      $body = str_replace("</body>", $tmp . "</body>", $body);
      if (count($pic) > 0) {
        $tmp = '';
        foreach ($pic as $key => $id) {
          $tmp .= "
          img = document.getElementById('" . $id . "');
          e = document.getElementById('" . $fbp[$key]
              . "');
          if (img.height > 80 && img.width > 80) {
          p=getPos(img);
          pT = p.top+img.height-35;
          pL = p.left+4;
          e.style.top=pT+'px';
          e.style.left=pL+'px';
        } else {
        e.parentNode.removeChild(e);
        }
        ";
        }
        $n = mt_rand(1, 10000);
        $tmp = "function SetFbpButtons" . $n . "(){ " . PHP_EOL . $tmp . PHP_EOL . " }
        window.addEvent('load', function() { SetFbpButtons" . $n . "() });
        ";
        $tmp = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . $tmp . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
        $body = str_replace("</body>", $tmp . "</body>", $body);
      }
    } // end fb photo button

    // opengraph
    if ($enable_opengraph) {
      $opengraph_defaultimage     = $this->params->get('opengraph_defaultimage');
      $opengraph_onlydefaultimage = $this->params->get('opengraph_onlydefaultimage', '0');
      $opengraph_directyoutube    = $this->params->get('opengraph_directyoutube', '0');
      $opengraph_description      = $this->params->get('opengraph_description');

      $meta = '';
      $type = 'article';

      //description
      if (!preg_match('/<meta property="og:description"/i', $body)) {
        $og_description = '';
        if ((count($this->contents) == 1) && ($this->descriptions[0] != '')) {
          $og_description = urldecode($this->descriptions[0]); //the descriptions array as been already filled for buttons rendering  (no buttons=>no descriptions)
        } elseif ((count($this->contents) > 1) or (count($this->contents) == 0)) { // featured or category views ->meta description
          $og_description = $this->str_encode(strip_tags($document->getMetaData("description")));
        }
        $meta .= "<meta property=\"og:description\" content=\"" . $og_description . "\"/>" . PHP_EOL;
      }

      //video
      if ((preg_match('/<meta property="og:video"/i', $body) == 0)) {
        if (preg_match('%<object.*(?:data|value)=[\\\\"\'](.*?\.(?:flv|swf))["\'].*?</object>%si', $body, $regsu)) {
          if ((preg_match('%<object.*width=["\'](.*?)["\'].*</object>%si', $body, $regsw)) && (preg_match('%<object.*height=["\'](.*?)["\'].*</object>%si', $body, $regsh))) {
            if (preg_match('/^http/i', $regsu[1])) {
              $video = $regsu[1];
            } else {
              $video = JURI::root() . preg_replace('#^/#', '', $regsu[1]);
            }
            $type = "video";
          }
        } elseif (preg_match('%<iframe.*src=["\'](.*?(?:www\.(?:youtube|youtube-nocookie)\.com|vimeo.com)/(?:embed|v)/(?!videoseries).*?)["\'].*?</iframe>%si', $body, $regsu)) {
          if ((preg_match('%<iframe.*width=["\'](.*?)["\'].*</iframe>%si', $body, $regsw)) && (preg_match('%<iframe.*height=["\'](.*?)["\'].*</iframe>%si', $body, $regsh))) {
            if ($opengraph_directyoutube == 0) {
              $video = $regsu[1];
            } else {
              $video = preg_replace('%embed/(?!videoseries)%i', 'v/', $regsu[1]);
            }
            $type = "video";
          }
        }
        if ($type == "video") {
          $meta .= "<meta property=\"og:video\" content=\"$video\"/>" . PHP_EOL;
          $meta .= "<meta property=\"og:video:type\" content=\"application/x-shockwave-flash\"/>" . PHP_EOL;
          $meta .= "<meta property=\"og:video:width\" content=\"$regsw[1]\">" . PHP_EOL;
          $meta .= "<meta property=\"og:video:height\" content=\"$regsh[1]\">" . PHP_EOL;
        }

      }

      //image
      if (preg_match('/<meta property="og:image"/i', $body) == 0) {
        if ($opengraph_defaultimage == "") {
          $opengraph_defaultimage = JURI::root() . CMP_OG_LINKIMG;
        } else {
          if (! preg_match('%^(?://|http://|https://)%',$opengraph_defaultimage)) {
            $opengraph_defaultimage = preg_replace('#^/#','',$opengraph_defaultimage);
            $opengraph_defaultimage = JURI::root() . $opengraph_defaultimage;
          } 
        }
        if ($opengraph_onlydefaultimage == '1') {
          $this->images[]= $opengraph_defaultimage;
        } else {
          $this->find_youtube_images($body,$this->images);
          $this->find_images($body,$this->images);
          if (count($this->images)==0)
            $this->images[]= $opengraph_defaultimage;
        }

        if (count($this->images) != 0) {
          foreach ($this->images as $value) {
            $meta .= "<meta property=\"og:image\" content=\"$value\"/>" . PHP_EOL;
          }
        }
      }

      //type
      if (preg_match('/<meta property="og:type"/i', $body) == 0) {
        if ($view == 'article') {
          $meta .= "<meta property=\"og:type\" content=\"$type\"/>" . PHP_EOL;
        } else {
          $meta .= "<meta property=\"og:type\" content=\"website\"/>" . PHP_EOL;
        }
      }
      //url
      if (preg_match('/<meta property="og:url"/i', $body) == 0) {
        $meta .= "<meta property=\"og:url\" content=\"" . urldecode($pageurl) . "\"/>" . PHP_EOL;
      }

      //title
      if (preg_match('/<meta property="og:title"/i', $body) == 0) {
        $meta .= "<meta property=\"og:title\" content=\"$pagetitle\"/>" . PHP_EOL;
      }

      //admins
      if ($fb_enable_admin == '0') {
        $fb_admin_ids = "";
      } else {
        if (preg_match('/<meta property="fb:admins"/i', $body) == 0) {
          $meta .= "<meta property=\"fb:admins\" content=\"$fb_admin_ids\"/>" . PHP_EOL;
        }
      }

      //app id
      if ($fb_app_id != '') {
        if (preg_match('/<meta property="fb:app_id"/i', $body) == 0) {
          $meta .= "<meta property=\"fb:app_id\" content=\"$fb_app_id\"/>" . PHP_EOL;
        }
      }

      //language
      if ($this->params->get('auto_language') == '1') {
        $fb_language = str_replace('-', '_', JFactory::getLanguage()->getTag());
      } else {
        $fb_language = $this->params->get('fb_language');
      }
      if (preg_match('/<meta property="og:locale"/i', $body) == 0) {
        $meta .= "<meta property=\"og:locale\" content=\"" . $fb_language . "\"/>" . PHP_EOL;
      }

      //site name
      $config = new JConfig();
      $site_name = $config->sitename;

      if (preg_match('/<meta property="og:site_name"/i', $body) == 0) {
        $meta .= "<meta property=\"og:site_name\" content=\"$site_name\"/>" . PHP_EOL;
      }
      $body = str_replace("<head>", "<head>" . PHP_EOL . $meta, $body);
    } //end opengraph

    JResponse::setBody($body);
    return true;
  } // end onAfterRender

  private function getButtonsHTMLcode($url, $title, $randomid, $enable, $position, $weight) {
    $code_fb_like0 = '';
    $code_fb_like1 = '';
    $code_fb_share0 = '';
    $code_fb_share1 = '';
    $code_fb_send0 = '';
    $code_fb_send1 = '';
    $code_google0 = '';
    $code_google1 = '';
    $code_google_share0 = '';
    $code_google_share1 = '';
    $code_twitter0 = '';
    $code_twitter1 = '';
    $code_linkedin0 = '';
    $code_linkedin1 = '';
    $code_pinterest0 = '';
    $code_pinterest1 = '';
    
    $title=urldecode($title);

    // fb like button
    if ($enable['fb_like'] == '1') {
      $code_fb_like = $this->getFbLikeCode($url); // url
      if ($position['fb_like'] == '0') {
        $code_fb_like0 = $code_fb_like;
        $code_fb_like1 = '';
      } elseif ($position['fb_like'] == '1') {
        $code_fb_like0 = '';
        $code_fb_like1 = $code_fb_like;
      } elseif ($position['fb_like'] == '2') {
        $code_fb_like0 = $code_fb_like;
        $code_fb_like1 = $code_fb_like;
      }
    }
    // fb_share_button
    if ($enable['fb_share'] == '1') {
      $code_fb_share = $this->getFbShareCode($url, $randomid); // url and random number
      if ($position['fb_share'] == '0') {
        $code_fb_share0 = $code_fb_share;
        $code_fb_share1 = '';
      } elseif ($position['fb_share'] == '1') {
        $code_fb_share0 = '';
        $code_fb_share1 = $code_fb_share;
      } elseif ($position['fb_share'] == '2') {
        $code_fb_share0 = $code_fb_share;
        $code_fb_share1 = $code_fb_share;
      }
    }
    // fb_send_button
    if ($enable['fb_send'] == '1') {
      $code_fb_send = $this->getFbSendCode($url); // url
      if ($position['fb_send'] == '0') {
        $code_fb_send0 = $code_fb_send;
        $code_fb_send1 = '';
      } elseif ($position['fb_send'] == '1') {
        $code_fb_send0 = '';
        $code_fb_send1 = $code_fb_send;
      } elseif ($position['fb_send'] == '2') {
        $code_fb_send0 = $code_fb_send;
        $code_fb_send1 = $code_fb_send;
      }
    }
    // twitter_button
    if ($enable['twitter'] == '1') {
      $code_twitter = $this->getTwitterCode($url, $title); // url and title
      if ($position['twitter'] == '0') {
        $code_twitter0 = $code_twitter;
        $code_twitter1 = '';
      } elseif ($position['twitter'] == '1') {
        $code_twitter0 = '';
        $code_twitter1 = $code_twitter;
      } elseif ($position['twitter'] == '2') {
        $code_twitter0 = $code_twitter;
        $code_twitter1 = $code_twitter;
      }
    }
    // google_button
    if ($enable['google'] == '1') {
      $code_google = $this->getGoogleCode($url); // url
      if ($position['google'] == '0') {
        $code_google0 = $code_google;
        $code_google1 = '';
      } elseif ($position['google'] == '1') {
        $code_google0 = '';
        $code_google1 = $code_google;
      } elseif ($position['google'] == '2') {
        $code_google0 = $code_google;
        $code_google1 = $code_google;
      }
    }
    // google_share_button
    if ($enable['google_share'] == '1') {
      $code_google_share = $this->getGoogleShareCode($url); // url
      if ($position['google_share'] == '0') {
        $code_google_share0 = $code_google_share;
        $code_google_share1 = '';
      } elseif ($position['google_share'] == '1') {
        $code_google_share0 = '';
        $code_google_share1 = $code_google_share;
      } elseif ($position['google_share'] == '2') {
        $code_google_share0 = $code_google_share;
        $code_google_share1 = $code_google_share;
      }
    }
    // linkedin_button
    if ($enable['linkedin'] == '1') {
      $code_linkedin = $this->getLinkedinCode($url); // url
      if ($position['linkedin'] == '0') {
        $code_linkedin0 = $code_linkedin;
        $code_linkedin1 = '';
      } elseif ($position['linkedin'] == '1') {
        $code_linkedin0 = '';
        $code_linkedin1 = $code_linkedin;
      } elseif ($position['linkedin'] == '2') {
        $code_linkedin0 = $code_linkedin;
        $code_linkedin1 = $code_linkedin;
      }
    }
    // pinterest_button
    if ($enable['pinterest'] == '1') {
      $code_pinterest = $this->getPinterestCode($url); // url
      if ($position['pinterest'] == '0') {
        $code_pinterest0 = $code_pinterest;
        $code_pinterest1 = '';
      } elseif ($position['pinterest'] == '1') {
        $code_pinterest0 = '';
        $code_pinterest1 = $code_pinterest;
      } elseif ($position['pinterest'] == '2') {
        $code_pinterest0 = $code_pinterest;
        $code_pinterest1 = $code_pinterest;
      }
    }
    $code_buttons0 = "";
    $code_buttons1 = "";

    // reorder buttons code
    foreach ($weight as $key => $val) {
      switch ($key) {
      case "fb_like":
        $code_buttons0 .= $code_fb_like0;
        $code_buttons1 .= $code_fb_like1;
        break;
      case "fb_share":
        $code_buttons0 .= $code_fb_share0;
        $code_buttons1 .= $code_fb_share1;
        break;
      case "fb_send":
        $code_buttons0 .= $code_fb_send0;
        $code_buttons1 .= $code_fb_send1;
        break;
      case "twitter":
        $code_buttons0 .= $code_twitter0;
        $code_buttons1 .= $code_twitter1;
        break;
      case "google":
        $code_buttons0 .= $code_google0;
        $code_buttons1 .= $code_google1;
        break;
      case "google_share":
        $code_buttons0 .= $code_google_share0;
        $code_buttons1 .= $code_google_share1;
        break;
      case "linkedin":
        $code_buttons0 .= $code_linkedin0;
        $code_buttons1 .= $code_linkedin1;
        break;
      case "pinterest":
        $code_buttons0 .= $code_pinterest0;
        $code_buttons1 .= $code_pinterest1;
        break;
      }
    } // end reorder buttons code

    if ($code_buttons0 != '') {
      $c = $this->params->get('container_buttons');
      if ($c != '0') {
        $css = $this->params->get('css_buttons');
        if ($css != '')
          $css = 'style="' . $css . '"';
        $code_buttons0 = "<" . $c . " class=\"css_buttons0\" " . $css . ">" . $code_buttons0 . "</" . $c . ">";
      }
    }
    if ($code_buttons1 != '') {
      $c = $this->params->get('container_buttons');
      if ($c != '0') {
        $css = $this->params->get('css_buttons');
        if ($css != '')
          $css = 'style="' . $css . '"';
        $code_buttons1 = "<" . $c . " class=\"css_buttons1\" " . $css . ">" . $code_buttons1 . "</" . $c . ">";
      }
    }
    return array($code_buttons0, $code_buttons1);
  } // end getButtonsHTMLcode

  function Save($context, $article, $isNew) {
    if (is_null($context)) { //joomla 1.5
      if ($_REQUEST['state'] != '1')
        return;
      if ($_REQUEST['details']['access'] != '0')
        return;
    } else { //joomla 2.5
      if ($_REQUEST['jform']['state'] != '1')
        return;
      if ($_REQUEST['jform']['access'] != '1')
        return;
      if ($context == 'com_media.file')
        return;
      //enabled "com_content.article" (backend) and "com_content.form" (frontend)
    }
    $app = JFactory::getApplication();
    $fb_enable_autopublish = $this->params->get('fb_enable_autopublish');
    $twitter_enable_autopublish = $this->params->get('twitter_enable_autopublish');

    //Enable autopublish only on the articles or categories where are rendered the share buttons
    $category_tobe_excluded = $this->params->get('category_tobe_excluded_buttons', '');
    $content_tobe_excluded = $this->params->get('content_tobe_excluded_buttons', '');
    $excludedContentList = @explode(",", $content_tobe_excluded);
    if (isset($article->id)) {
      if (in_array($article->id, $excludedContentList))
        return true;
      if (is_array($category_tobe_excluded)) {
        if (in_array($article->catid, $category_tobe_excluded))
          return true;
      } else {
        $excludedCatList = @explode(",", $category_tobe_excluded);
        if (in_array($article->catid, $excludedCatList))
          return true;
      }
    } else {
      if (isset($app->input)) {
        $id = $app->input->get('id');
      } else {
        $id = JRequest::getCmd('id');
      }
      ;
      if (is_array($category_tobe_excluded)) {
        if (in_array($id, $category_tobe_excluded))
          return true;
      } else {
        $excludedCatList = @explode(",", $category_tobe_excluded);
        if (in_array($id, $excludedCatList))
          return true;
      }
    }

    //Enable autopublish only on "apply" action
    if ($_REQUEST['task'] != 'apply') {
      return true;
    }
    if (($fb_enable_autopublish || $twitter_enable_autopublish) && (!extension_loaded('curl'))) {
      $this->show('Facebook or Twitter Autopublish is not possible because CURL extension is not loaded.', 'error');
      return true;
    }
    
    //Facebook autopublish
    if ($fb_enable_autopublish) {
      if (!class_exists('Facebook', false)) {
        require_once('facebook' . DS . 'facebook.php');
      }
      $fb_app_id = $this->params->get('fb_app_id');
      $fb_secret_key = $this->params->get('fb_secret_key');
      if ((method_exists($this->params, 'exists')) && ($this->params->exists('fb_extra_params'))) {
        $fb_extra_params = $this->params->get('fb_extra_params');
        $fb_ids = $fb_extra_params->fb_ids;
        $token = $fb_extra_params->fb_token;
      } else {
        $token = $this->params->get('fb_token');
        $fb_ids = $this->params->get('fb_ids');
        if ($fb_ids == '') {
          $fb_ids = array();
        }
        if (!is_array($fb_ids)) {
          $fb_ids = array($fb_ids);
        }
      }

      if (($fb_app_id != '') && ($fb_secret_key != '') && (count($fb_ids) > 0) && ($token != '')) {
        $title = $this->getTitle($article);
        $caption = '';
        $url = JUri::root() . ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias, $article->catid);
        $router = JSite::getInstance('site')->getRouter('site');
        $url = $router->build($url)->toString();
        $url = str_replace('administrator/', '', $url);
        $description = $this->getDescription($article, 'article');
        if ($this->params->get('fb_autopublish_image', '1') == '1') { //first image
          $images = $this->getPicture($article, 'article');
          if (count($images) > 0) {
            $pic = $images[0];
          } else {
            $pic = '';
          }
        } else {
          $pic = '';
        }

        if ($isNew) {
          $msg = $this->params->get('fb_text_new', '');
        } else {
          $msg = $this->params->get('fb_text_old', 'Update');
        }
        $facebook = new Facebook(array('appId' => $fb_app_id, 'secret' => $fb_secret_key, 'cookie' => true));
        $ok = true;
        try {
          $me = $facebook->api('/me/', array('access_token' => $token));
          $info_accounts = $facebook->api('/me/accounts', array('access_token' => $token));
          $info_groups = $facebook->api('/me/groups', array('access_token' => $token));
        } catch (FacebookApiException $e) {
          JError::raiseWarning('1', 'Facebook error: ' . $e->getMessage());
          $ok = false;
        }

        if ($ok) {
          if (in_array($me['id'], $fb_ids)) {
            $ok = true;
            try {
              $facebook->api('/' . $me['id'] . '/feed', 'post', array(
                                                                  'access_token' => $token, 
                                                                  'message' => $msg, 
                                                                  'link' => $url, 
                                                                  'picture' => $pic, 
                                                                  'name' => $title, 
                                                                  'caption' => $caption, 
                                                                  'description' => $description));
            } catch (FacebookApiException $e) {
              JError::raiseWarning('1', 'Facebook error: ' . $e->getMessage());
              $ok = false;
            }
            if ($ok) {
              $this->show('Content published on Facebook account: ' . "<a href='" . $me['link'] . "'>" . $me['name'] . "</a>", 'message');
            }
          }

          $accounts = $info_accounts['data'];
          foreach ($accounts as $account) {
            if (in_array($account['id'], $fb_ids)) {
              $ok = true;
              try {
                $pagetoken = $account['access_token'];
                $facebook->api('/' . $account['id'] . '/feed', 'post', array(
                                                                         'access_token' => $pagetoken, 
                                                                         'message' => $msg, 
                                                                         'link' => $url, 
                                                                         'picture' => $pic, 
                                                                         'name' => $title, 
                                                                         'caption' => $caption, 
                                                                         'description' => $description));
              } catch (FacebookApiException $e) {
                JError::raiseWarning('1', 'Facebook error: ' . $e->getMessage());
                $ok = false;
              }
              if ($ok) {
                $info = $facebook->api('/' . $account['id'] . '/', array('access_token' => $token));
                $this->show("Content published on Facebook page: <a href='" . $info['link'] . "'>" . $info['name'] . "</a>", 'message');
              }
            }
          }

          $accounts = $info_groups['data'];
          foreach ($accounts as $account) {
            if (in_array($account['id'], $fb_ids)) {
              try {
                $result = $facebook->api('/' . $account['id'] . '/feed', 'post', array(
                                                                                   'access_token' => $token, 
                                                                                   'message' => $msg, 
                                                                                   'link' => $url, 
                                                                                   'picture' => $pic, 
                                                                                   'name' => $title, 
                                                                                   'caption' => $caption, 
                                                                                   'description' => $description));
              } catch (FacebookApiException $e) {
                JError::raiseWarning('1', 'Facebook error: ' . $e->getMessage());
              }
              if (isset($result['id'])) {
                $this->show("Content published on Facebook group <a href='//www.facebook.com/" . $account['id'] . "/'>" . $account['name'] . "</a>", 'message');
              }
            }
          }
        }
      } else {
        if ($fb_app_id == '') {
          $this->show('App ID is missing', 'error');
        }
        if ($fb_secret_key == '') {
          $this->show('App secret key is missing', 'error');
        }
        if (count($fb_ids) == 0) {
          $this->show('Must be specified on at least one Facebook account ID where to publish the article', 'error');
        }
        if ($token == '') {
          $this->show('Valid access token missing', 'error');
        }
      }
    }


    //Twitter autopublish
    if ($twitter_enable_autopublish) {
      if (!class_exists('TwitterOAuth', false)) {
        require_once('twitteroauth' . DS . 'twitteroauth.php');
      }
      $consumer_key = $this->params->get('twitter_consumer_key', '');
      $consumer_secret = $this->params->get('twitter_consumer_secret', '');
      $oauth_token = $this->params->get('twitter_oauth_token', '');
      $oauth_token_secret = $this->params->get('twitter_oauth_token_secret', '');
      if (($consumer_key != '') && ($consumer_secret != '') && ($oauth_token != '') && ($oauth_token_secret != '')) {
        $conn = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
        if (!$conn) {
          $this->show('Connection error occurred', 'error');
          die();
        }
        $title = $this->getTitle($article);
        $url = JUri::root() . ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias, $article->catid);
        $router = JSite::getInstance('site')->getRouter('site');
        $url = $router->build($url)->toString();
        $url = str_replace('administrator/', '', $url);
        if ($isNew) {
          $msg = (substr($title, 0, 100) . " " . $url);
        } else {
          $msg = ("Update:" . substr($title, 0, 100) . " " . $url);
        }
        $status = $conn->post('statuses/update', array('status' => $msg));
        if (!isset($status->error)) {
          $this->show('Content published on Twitter', 'message');
        } else {
          $this->show('Content published on Twitter: ' . $status->error, 'error');
        }
      } else {
        if ($consumer_key == '') {
          $this->show('Consumer key is missing', 'error');
        }
        if ($consumer_secret == '') {
          $this->show('Consumer secret key is missing', 'error');
        }
        if ($oauth_token == '') {
          $this->show('Oauth token is missing', 'error');
        }
        if ($oauth_token_secret == '') {
          $this->show('Oauth token secret key is missing', 'error');
        }
      }
    }
    return true;
  }

  private function find_youtube_images($text, &$images) {
    if (preg_match_all('%(?:http|https)://www\.(?:youtube|youtube-nocookie)\.com/(?:v|embed)/(?!videoseries)(.*?)(?:\?|"|\')%i', $text, $regs)) {
      $regs[1] = array_unique($regs[1], SORT_REGULAR);
      foreach ($regs[1] as $value) {
        $img = "http://img.youtube.com/vi/$value/0.jpg";
        if (!in_array($img, $images)) {
          $images[] = $img;
        }
      }
    }
  }

  private function find_images(&$text, &$images) {
    if (preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*["\'](.*?)["\'][^>]*?>/i', $text, $regs)) {
      $regs[1] = array_unique($regs[1], SORT_REGULAR);
      foreach ($regs[1] as $value) {
        if (!preg_match('%^(?://|http://|https://)%', $value)) {
          $value = preg_replace('#^/#', '', $value);
          $value = JURI::root() . $value;
        }
        $value = preg_replace('%^//%', JFactory::getURI()->getScheme() . '://', $value);
        if ($this->check_img_size($value))
          $images[] = $value;
      }
    }
  }

  private function check_img_size($img) {
    $dim = @getimagesize($img);
    if (($dim[0] >= 200) && ($dim[1] >= 200)) {
      return true;
    } else
      return false;
  }

  // FB share
  private function getFbShareCode($url, $idrnd) {
    $url        = urldecode($url);
    $fb_mode    = $this->params->get('fb_mode');
    $text       = $this->params->get('fb_share_button_text', 'Share');
    $layout     = $this->params->get('fb_share_button_style', 'button_count');
    $width      = $this->params->get('fb_share_width');
    if ($width != "") {
      if ($fb_mode == 'html5') {
        $width = "data-width=\"$width\"";
      } else {
        $width = "width=\"$width\"";
      }
    }
    $custom_img = $this->params->get('fb_share_custom_img');
    if ($custom_img==''){
      $custom_img=CUSTOM_FB_SHARE_IMG;
    }
    $asynchronous = $this->params->get('fb_asynchronous');
    $container = $this->params->get('fb_share_container');
    $css = $this->params->get('fb_share_css');
    if ($css != "") {
      $css = "style=\"$css\"";
    }
    $script = "<script>";
    $script .= "function fbs_click$idrnd() {";
    $script .= "FB.ui({";
    $script .= "    method: \"stream.share\",";
    $script .= "    u: \"" . $url . "\"";
    $script .= "  } ";
    $script .= "); return false; };";
    $script .= "</script>";
    $tmp = $script;
    if ($fb_mode == 'html5') {
      $code = "<div class=\"fb-share-button\" data-href=\"$url\" data-type=\"$layout\" $width></div>";
    } else {
      $code = "<fb:share-button href=\"$url\" type=\"$layout\" $width></fb:share-button>";
    }
    switch ($layout) {
    case "standard":
      $tmp = $code;
      break;
    case "button_count":
      $tmp = $code;
      break;
    case "box_count":
      $tmp = $code;
      break;
    case "icontext":
      $tmp .= "<style>a.cmp_shareicontextlink { text-decoration: none !important; line-height: 20px !important;height: 20px !important; color: #3B5998 !important; font-size: 11px !important; font-family: arial, sans-serif !important;  padding:2px 4px 2px 20px !important; border:1px solid #CAD4E7 !important; cursor: pointer !important;  background:url(//static.ak.facebook.com/images/share/facebook_share_icon.gif?6:26981) no-repeat 1px 1px #ECEEF5 !important; -webkit-border-radius: 3px !important; -moz-border-radius: 3px !important;} .cmp_shareicontextlink:hover {   background:url(//static.ak.facebook.com/images/share/facebook_share_icon.gif?6:26981) no-repeat 1px 1px #ECEEF5 !important;  border-color:#9dacce !important; color: #3B5998 !important;} </style><a class=\"cmp_shareicontextlink\" href=\"#\" onclick=\"return fbs_click$idrnd()\" target=\"_blank\">"
          . $text . "</a>";
      break;
    case "text":
      $tmp .= "<style>a.cmp_sharetextlink { text-decoration: none !important; line-height: 20px !important;height: 20px !important; color: #3B5998 !important; font-size: 11px !important; font-family: arial, sans-serif !important;  padding:2px 4px 2px 4px !important; border:1px solid #CAD4E7 !important; cursor: pointer !important;  background-color: #ECEEF5 !important; -webkit-border-radius: 3px !important; -moz-border-radius: 3px !important;} .cmp_sharetextlink:hover {   background-color: #ECEEF5 !important;  border-color:#9dacce !important; color: #3B5998 !important;} </style><a class=\"cmp_sharetextlink\" rel=\"nofollow\" href=\"#\" onclick=\"return fbs_click$idrnd()\" target=\"_blank\">"
          . $text . "</a>";
      break;
    case "icon":
      $tmp .= "<style>.cmp_shareiconlink { text-decoration: none !important; line-height: 20px !important;height: 20px !important; color: #3B5998 !important; font-size: 11px !important; font-family: arial, sans-serif !important;  padding:2px 4px 2px 14px !important; border:1px solid #CAD4E7 !important; cursor: pointer;width: 20px !important;  background:url(//static.ak.facebook.com/images/share/facebook_share_icon.gif?6:26981) no-repeat 1px 1px #ECEEF5 !important; -webkit-border-radius: 3px !important; -moz-border-radius: 3px !important;} .cmp_shareiconlink:hover {   background:url(//static.ak.facebook.com/images/share/facebook_share_icon.gif?6:26981) no-repeat 1px 1px #ECEEF5 !important;  border-color:#9dacce !important; color: #3B5998 !important;} </style><a class=\"cmp_shareiconlink\" href=\"#\" onclick=\"return fbs_click$idrnd()\" target=\"_blank\"></a>";
      break;
    case "custom_icon":
      $tmp .= "<a class=\"cmp_sharecustomiconlink\" href=\"#\" onclick=\"return fbs_click$idrnd()\" target=\"_blank\"><img class=\"cmp_sharecustomiconimg\" src=\"$custom_img\"/></a>";
      break;
    }
    if ($asynchronous) {
      $tmp = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . preg_replace('/<\/script>/i', '<\/script>', $tmp) . "'); " . PHP_EOL . "//]]> " . PHP_EOL
          . "</script>";
    } else {
      $tmp = $tmp . PHP_EOL;
    }
    if ($container == '0') {
      $code = $tmp;
    } else {
      $code = "<$container class=\"css_fb_share\" $css>$tmp</$container>";
    }
    return $code;
  } // end FB share

  // FB like
  private function getFbLikeCode($url) {
    $url = urldecode($url);
    $fb_mode = $this->params->get('fb_mode');
    $layout = $this->params->get('fb_like_layout');
    $show_faces = $this->params->get('fb_like_show_faces');
    $width = $this->params->get('fb_like_width');
    if ($width != '') {
      if ($fb_mode == 'html5') {
        $width = "data-width=\"$width\"";
      } else {
        $width = "width=\"$width\"";
      }
    }
    $share = $this->params->get('fb_like_share');
    if ($share)
      $share = 'true';
    else
      $share = 'false';
    $action = $this->params->get('fb_like_action');
    $color_scheme = $this->params->get('fb_like_color_scheme');
    $kid_directed_site = $this->params->get('kid_directed_site');
    if ($kid_directed_site)
      $kid_directed_site = 'true';
    else
      $kid_directed_site = 'false';
    $asynchronous = $this->params->get('fb_asynchronous');
    $css = $this->params->get('fb_like_css');
    if ($css != "") {
      $css = "style=\"$css\"";
    }
    $container = $this->params->get('fb_like_container');
    if ($fb_mode == 'html5') {
      $tmp = "<div class=\"fb-like\" data-href=\"$url\" data-layout=\"$layout\" data-show_faces=\"$show_faces\" data-share=\"$share\" $width data-action=\"$action\" data-colorscheme=\"$color_scheme\" data-kid_directed_site=\"$kid_directed_site\"></div>";
    } else {
      $tmp = "<fb:like href=\"$url\" layout=\"$layout\" show_faces=\"$show_faces\" share=\"$share\" $width action=\"$action\" colorscheme=\"$color_scheme\" kid_directed_site=\"$kid_directed_site\"></fb:like>";
    }
    if ($asynchronous) {
      $tmp = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . $tmp . "'); " . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
    } else {
      $tmp = $tmp . PHP_EOL;
    }

    if ($container == '0') {
      $code = $tmp;
    } else {
      $code = "<$container class=\"css_fb_like\" $css>$tmp</$container>";
    }
    return $code;
  } // end FB like

  // FB send
  private function getFbSendCode($url) {
    $url = urldecode($url);
    $fb_mode = $this->params->get('fb_mode');
    $color_scheme = $this->params->get('fb_send_color_scheme');
    $kid_directed_site = $this->params->get('kid_directed_site');
    if ($kid_directed_site)
      $kid_directed_site = 'true';
    else
      $kid_directed_site = 'false';
    $asynchronous = $this->params->get('fb_asynchronous');
    $css = $this->params->get('fb_send_css');
    if ($css != "") {
      $css = "style=\"$css\"";
    }
    $container = $this->params->get('fb_send_container');
    if ($fb_mode == 'html5') {
      $tmp = "<div class=\"fb-send\" data-href=\"$url\" data-colorscheme=\"$color_scheme\" data-kid_directed_site=\"$kid_directed_site\"></div>";
    } else {
      $tmp = "<fb:send href=\"$url\" colorscheme=\"$color_scheme\" kid_directed_site=\"$kid_directed_site\"></fb:like>";
    }
    if ($asynchronous) {
      $tmp = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . $tmp . "'); " . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
    } else {
      $tmp = $tmp . PHP_EOL;
    }

    if ($container == '0') {
      $code = $tmp;
    } else {
      $code = "<$container class=\"css_fb_send\" $css>$tmp</$container>";
    }
    return $code;
  } // end FB send

  // FB comments
  private function getFbCommentsCode($url) {
    $url = urldecode($url);
    $idrnd = 'fbcom' . rand();
    $fb_mode = $this->params->get('fb_mode');
    $asynchronous = $this->params->get('fb_asynchronous');
    $width = $this->params->get('fb_comments_width');
    if ($width != '') {
      if ($fb_mode == 'html5') {
        $width = "data-width=\"$width\"";
      } else {
        $width = "width=\"$width\"";
      }
    }
    $max_number = $this->params->get('fb_comments_max_number');
    if ($max_number != '') {
      if ($fb_mode == 'html5') {
        $max_number = "data-num-posts=\"$max_number\"";
      } else {
        $max_number = "num-posts=\"$max_number\"";
      }
    }
    $autofit = $this->params->get('fb_comments_autofit');
    $color_scheme = $this->params->get('fb_comments_color_scheme');
    $container = $this->params->get('fb_comments_container');
    $css = $this->params->get('fb_comments_css');
    $notification = $this->params->get('fb_comments_notification');
    $print = $this->params->get('fb_comments_print');

    $count_enable = $this->params->get('fb_comments_count_enable');
    $container_count = $this->params->get('fb_comments_container_count');
    $css_count = $this->params->get('fb_comments_css_count');

    if ($css != '') {
      $css = "style=\"" . $css . "\"";
    }
    if ($css_count != '') {
      $css_count = "style=\"" . $css_count . "\"";
    }
    if ($fb_mode == 'html5') {
      $notification = ($notification == '1') ? 'data-notify="true" data-migrated="1"' : "";
      $code = "<div class=\"fb-comments\" data-href=\"" . $url . "\" $width $max_number data-colorscheme=\"$color_scheme\" $notification></div>";
    } else {
      $notification = ($notification == '1') ? 'notify="true" migrated="1"' : "";
      $code = "<fb:comments href=\"" . $url . "\" $width $max_number colorscheme=\"$color_scheme\" $notification></fb:comments>";
    }

    if ($asynchronous) { // async
      if ($autofit) { // async autofit
        $tmp = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL;
        $tmp .= "function getwfbcom() {" . PHP_EOL;
        $tmp .= "var efbcom = document.getElementById('" . $idrnd . "');" . PHP_EOL;
        $tmp .= "if (efbcom.currentStyle){" . PHP_EOL;
        $tmp .= " var pl=efbcom.currentStyle['paddingLeft'].replace(/px/,'');" . PHP_EOL;
        $tmp .= " var pr=efbcom.currentStyle['paddingRight'].replace(/px/,'');" . PHP_EOL;
        $tmp .= " return efbcom.offsetWidth-pl-pr;" . PHP_EOL;
        $tmp .= "} else {" . PHP_EOL;
        $tmp .= " var pl=window.getComputedStyle(efbcom,null).getPropertyValue('padding-left' ).replace(/px/,'');" . PHP_EOL;
        $tmp .= " var pr=window.getComputedStyle(efbcom,null).getPropertyValue('padding-right').replace(/px/,'');" . PHP_EOL;
        $tmp .= " return efbcom.offsetWidth-pl-pr;";
        $tmp .= "}}" . PHP_EOL;
        $code = preg_replace('/(width=".*?")/', 'width="\'+getwfbcom()+\'"', $code);
        $tmp .= "var tagfbcom = '" . $code . "';";
        $tmp .= "document.write(tagfbcom); " . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
        $code .= $tmp;
      } else { // async no autofit
        $code = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . $code . "'); " . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
      }
      if ($container != '0') {
        $code = "<$container $css id=\"" . $idrnd . "\" class=\"css_fb_comments\">$code</$container>";
      }
    } else { // no async
      if ($autofit) {
        $tmps = "function autofitfbcom() {";
        $tmps .= "var efbcom = document.getElementById('" . $idrnd . "');";
        $tmps .= "if (efbcom.currentStyle){";
        $tmps .= "var pl=efbcom.currentStyle['paddingLeft'].replace(/px/,'');";
        $tmps .= "var pr=efbcom.currentStyle['paddingRight'].replace(/px/,'');";
        $tmps .= "var wfbcom=efbcom.offsetWidth-pl-pr;";
        $tmps .= "try {efbcom.firstChild.setAttribute('width',wfbcom);}";
        $tmps .= "catch(e) {efbcom.firstChild.width=wfbcom+'px';}";
        $tmps .= "} else {";
        $tmps .= "var pl=window.getComputedStyle(efbcom,null).getPropertyValue('padding-left' ).replace(/px/,'');";
        $tmps .= "var pr=window.getComputedStyle(efbcom,null).getPropertyValue('padding-right').replace(/px/,'');";
        $tmps .= "efbcom.childNodes[0].setAttribute('width',efbcom.offsetWidth-pl-pr);" . PHP_EOL;
        $tmps .= "}}";
        $tmps .= "autofitfbcom();";
        $code .= "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . $tmps . PHP_EOL . "//]]> " . PHP_EOL . "</script>" . PHP_EOL;
      }
      if ($container != '0') {
        $code = "<$container $css id=\"" . $idrnd . "\" class=\"css_fb_comments\">$code</$container>";
      }
    } // end no async

    // comments counter
    if ($count_enable == '1') {
      if ($container != '0') {
        $code = "<$container class=\"css_fb_comments_count\" $css_count>
        <fb:comments-count href=\"" . $url . "\"></fb:comments-count> comments
        </$container>" . $code;
      } else {
        $code = "<fb:comments-count href=\"" . $url . "\"></fb:comments-count> comments" . $code;
      }
    }
    return $code;
  } // end comments code

  // Pinterest
  function getPinterestCode($url) {
    $selection    = $this->params->get('pinterest_selection');
    $counter      = $this->params->get('pinterest_counter');
    $size         = $this->params->get('pinterest_size');
    $color        = $this->params->get('pinterest_color');
    $language     = $this->params->get('pinterest_language');
    $asynchronous = $this->params->get('pinterest_asynchronous');
    $container    = $this->params->get('pinterest_container');
    $css          = $this->params->get('pinterest_css');
    $custom_img   = $this->params->get('pinterest_custom_img');
    if ($custom_img==''){
      $custom_img = CUSTOM_PINTEREST_IMG;
    }
    $code = "";
    if (($selection == '0')||($selection == '2')) { //user selection image
      if ($css != "") {
        $css = "style=\"$css\"";
      }
      $height = "20";
      $data_pin_height = "";
      if ($size == 'large') {
        $data_pin_height = "data-pin-height=\"28\"";
        $height = "28";
      }
      $code_color = "gray";
      $data_pin_color = "";
      if ($color != 'gray') {
        $data_pin_color = "data-pin-color=\"$color\"";
        $code_color = $color;
      }
      $lang = "en";
      $pin_lang = "";
      if ($language != 'en') {
        $pin_lang = "pin-lang=\"ja\"";
        $lang = $language;
      }
      if ($selection == '2') {
        $button_img = $custom_img;
        $tmp = "<a href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;http://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'>" . "<img src=\"$button_img\" /></a>";
      } else {
        $button_img = "//assets.pinterest.com/images/pidgets/pinit_fg_" . $lang . "_rect_" . $code_color . "_" . $height . ".png";
        $tmp = "<a href=\"//www.pinterest.com/pin/create/button/\" data-pin-do=\"buttonBookmark\" $data_pin_color $data_pin_height $pin_lang>" . "<img src=\"$button_img\" /></a>";
      }
      if ($container != '0') {
        $code = "<$container class=\"css_pinterest\" $css>$tmp</$container>";
      } else {
        $code = $tmp;
      }
      if ($asynchronous) {
        $code = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . $code . "'); " . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
      } else {
        $code = $code . PHP_EOL;
      }
    }
    return $code;
  } // end pinterest

  // Linkedin
  function getLinkedinCode($url) {
    $container = $this->params->get('linkedin_container', '1');
    $css = $this->params->get('linkedin_css');
    if ($css != "") {
      $css = "style=\"$css\"";
    }
    //custom icon mode
    if ($this->params->get('linkedin_style')=='custom_icon'){
      $custom_img = $this->params->get('linkedin_custom_img');
      if ($custom_img==''){
        $custom_img = CUSTOM_LINKEDIN_IMG;
      }
      $tmp = "<a class=\"cmp_linkedin_custom_icon_link\" href=\"http://www.linkedin.com/shareArticle?mini=true&url=$url\" target=\"_blank\"><img class=\"cmp_linkedin_custom_icon_img\" src=\"$custom_img\"/></a>";
      if ($container != '0') {
        $code = "<$container class=\"css_linkedin\" $css>$tmp</$container>";
      } else {
        $code = $tmp;
      }
      return $code;
    }//end custom icon mode
    
    $data_counter  = $this->params->get('linkedin_data-counter', 'none');
    $data_showzero = $this->params->get('linkedin_data-showzero', '0');
    $asynchronous  = $this->params->get('linkedin_asynchronous', '0');
    $url = urldecode($url);
    
    if ($data_counter == "none") {
      $data_counter  = "";
      $data_showzero = "";
    } else {
      $data_counter = "data-counter=\"$data_counter\"";
      if ($data_showzero == "0") {
        $data_showzero = "";
      } else {
        $data_showzero = "data-showzero=\"true\"";
      }
    }
    $tmp = "<script type=\"IN/Share\" data-url=\"" . $url . "\" $data_counter $data_showzero></script>";
    if ($asynchronous) {
      $tmp = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . preg_replace('/<\/script>/i', '<\/script>', $tmp) . "'); " . PHP_EOL . "//]]> " . PHP_EOL
          . "</script>";
    } else {
      $tmp = $tmp . PHP_EOL;
    }
    if ($container != '0') {
      $code = "<$container class=\"css_linkedin\" $css>$tmp</$container>";
    } else {
      $code = $tmp;
    }
    return $code;
  } // end linkedin

  function getGoogleCode($url) {
    $url = urldecode($url);
    $mode = $this->params->get('google_mode');
    $size = $this->params->get('google_size', 'standard');
    $annotation = $this->params->get('google_annotation', 'bubble');
    $width = $this->params->get('google_width', '250');
    $asynchronous = $this->params->get('google_asynchronous', '0');
    $align = $this->params->get('google_align');
    $recommendations = $this->params->get('google_recommendations');
    if ($this->params->get('auto_language')) {
      $language = JFactory::getLanguage()->getTag();
    } else {
      $language = $this->params->get('google_language', 'en-US');
    }
    $container = $this->params->get('google_container');
    $css = $this->params->get('google_css');
    if ($css != "") {
      $css = "style=\"$css\"";
    }
    if ($mode == "0") { // not html5
      if (($width != '') && ($annotation == "inline")) {
        $width = "width=\"$width\"";
      } else
        $width = "";
      if ($align == "right") {
        $align = "align=\"$align\"";
      } else
        $align = "";
      if ($annotation != "bubble") {
        $annotation = "annotation=\"$annotation\"";
      } else {
        $annotation = "";
      }
      if ($recommendations == "0") {
        $recommendations = "recommendations=\"false\"";
      }
      $code = "<g:plusone $width $align $recommendations size=\"$size\" href=\"" . $url . "\" $annotation></g:plusone>";
    } else { // html5
      if (($width != '') && ($annotation == "inline")) {
        $width = "data-width=\"$width\"";
      } else
        $width = "";
      if ($align == "right") {
        $align = "data-align=\"$align\"";
      } else
        $align = "";
      if ($annotation != "bubble") {
        $annotation = "data-annotation=\"$annotation\"";
      } else {
        $annotation = "";
      }
      if ($recommendations == "0") {
        $recommendations = "data-recommendations=\"false\"";
      }
      $code = "<div class=\"g-plusone\" $width $align $recommendations data-size=\"$size\" data-href=\"" . $url . "\" $annotation></div>";

    }
    if ($container != '0') {
      $code = "<$container class=\"css_google\" $css>$code</$container>";
    }
    $browser = JBrowser::getInstance();
    $browserType = $browser->getBrowser();
    if ($asynchronous && ($browserType != 'msie')) {
      $code = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . $code . "'); " . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
    } else {
      $code = $code . PHP_EOL;
    }
    return $code;
  } // end Google

  function getGoogleShareCode($url) {
    $container = $this->params->get('google_share_container');
    $css = $this->params->get('google_share_css');
    if ($css != "") {
      $css = "style=\"$css\"";
    }
    //custom icon mode
    if ($this->params->get('google_share_style')=='custom_icon'){
      $custom_img = $this->params->get('google_share_custom_img');
      if ($custom_img==''){
        $custom_img = CUSTOM_GOOGLE_IMG;
      }
      $tmp = "<a class=\"cmp_google_custom_icon_link\" href=\"//plus.google.com/share?url=$url\" target=\"_blank\"><img class=\"cmp_google_custom_icon_img\" src=\"$custom_img\"/></a>";
      if ($container != '0') {
        $code = "<$container class=\"css_google\" $css>$tmp</$container>";
      } else {
        $code = $tmp;
      }
      return $code;
    }//end custom icon mode
    
    $url = urldecode($url);
    $mode = $this->params->get('google_share_mode');
    $asynchronous = $this->params->get('google_asynchronous', '0');
    if ($this->params->get('auto_language')) {
      $language = JFactory::getLanguage()->getTag();
    } else {
      $language = $this->params->get('google_language', 'en-US');
    }
    $height = $this->params->get('google_share_height');
    $width = $this->params->get('google_share_width');
    $annotation = $this->params->get('google_share_annotation');
    $align = $this->params->get('google_share_align');
    
    if ($mode == "0") { // not html5
      if ($width != '') {
        $width = "width=\"$width\"";
      } else
        $width = "";
      if ($height != '') {
        $height = "height=\"$height\"";
      } else
        $height = "";
      if ($align == "right") {
        $align = "align=\"$align\"";
      } else
        $align = "";
      $annotation = "annotation=\"$annotation\"";
      $code = "<g:plus action=\"share\" $width $align $height href=\"" . $url . "\" $annotation></g:plusone>";
    } else { // html5
      if ($width != '') {
        $width = "data-width=\"$width\"";
      } else
        $width = "";
      if ($height != '') {
        $height = "data-height=\"$height\"";
      } else
        $height = "";
      if ($align == "right") {
        $align = "data-align=\"$align\"";
      } else
        $align = "";
      $annotation = "data-annotation=\"$annotation\"";
      $code = "<div class=\"g-plus\" data-action=\"share\" $width $align $height data-href=\"" . $url . "\" $annotation></div>";
    }
    if ($container != '0') {
      $code = "<$container class=\"css_google_share\" $css>$code</$container>";
    }
    $browser = JBrowser::getInstance();
    $browserType = $browser->getBrowser();
    if ($asynchronous && ($browserType != 'msie')) {
      $code = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . $code . "'); " . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
    } else {
      $code = $code . PHP_EOL;
    }
    return $code;
  } // end Google share

  // Twitter
  function getTwitterCode($url, $title) {
    $url          = urldecode($url);
    $show_count   = $this->params->get('twitter_show_count');
    $language     = $this->params->get('twitter_language');
    $data_via     = $this->params->get('twitter_data_via');
    $data_related = $this->params->get('twitter_data_related');
    $hashtags     = $this->params->get('twitter_hashtags');
    $datasize     = $this->params->get('twitter_datasize');
    $opt_out      = $this->params->get('twitter_opt_out');
    $container    = $this->params->get('twitter_container');
    $css          = $this->params->get('twitter_css');
    if ($css != "") {
      $css = "style=\"$css\"";
    }
    $asynchronous = $this->params->get('twitter_asynchronous', '1');
    
    //custom icon mode
    if ($this->params->get('twitter_style')=='custom_icon'){
      $custom_img = $this->params->get('twitter_custom_img');
      if ($custom_img==''){
        $custom_img = CUSTOM_TWITTER_IMG;
      }
      $max=140-22;//link
      if ($data_via!='') {
        $max=$max-5-strlen($data_via);
        $data_via='&via='.urlencode($data_via);
      }
      if ($max<strlen($title)+1) $title = substr($title,0,$max-4).'...';
      $link = "//twitter.com/intent/tweet?url=$url&text=".urldecode($title).$data_via;
      $tmp  = "<a class=\"cmp_twitter_custom_icon_link\" href=\"$link\" target=\"_blank\"><img class=\"cmp_twitter_custom_icon_img\" src=\"$custom_img\"/></a>";
      if ($container != '0') {
        $code = "<$container class=\"css_twitter\" $css>$tmp</$container>";
      } else {
        $code = $tmp;
      }
      return $code;
    }//end custom icon mode
    
    if ($this->params->get('auto_language')) {
      $language = substr(JFactory::getLanguage()->getTag(), 0, 2);
    }
    if ($language != "en") {
      $language = "data-lang=\"$language\"";
    } else {
      $language = '';
    }
    if ($data_via != "") {
      $data_via = "data-via=\"$data_via\"";
    } else {
      $data_via = '';
    }
    if ($data_related != "") {
      $data_related = "data-related=\"$data_related\"";
    } else {
      $data_related = '';
    }
    if ($hashtags != "") {
      $hashtags = "data-hashtags=\"$hashtags\"";
    }
    if ($datasize != "") {
      $datasize = "data-size=\"$datasize\"";
    }
    if ($opt_out == "true") {
      $opt_out = "data-dnt=\"$opt_out\"";
    }

    $code = "<a href=\"//twitter.com/share\" class=\"twitter-share-button\" ";
    $code .= "$language $data_via $hashtags $data_related ";
    $code .= "data-url=\"" . $url . "\" ";
    $code .= "data-text=\"$title\" ";
    $code .= "data-count=\"$show_count\">Tweet</a>";

    if ($container != '0') {
      $code = "<$container $css class=\"css_twitter\">$code</$container>";
    }
    if ($asynchronous) {
      $code = "<script type=\"text/javascript\">" . PHP_EOL . "//<![CDATA[" . PHP_EOL . "document.write('" . $code . "'); " . PHP_EOL . "//]]> " . PHP_EOL . "</script>";
    } else {
      $code = $code . PHP_EOL;
    }
    return $code;
  } // end twitter

  // include scripts and styles
  function onAfterDispatch() {
    // no action for administration interface
    $app = JFactory::getApplication();
    if ($app->isAdmin()) {
      return;
    }

    $document = JFactory::getDocument();

    $enable_fb_comments = $this->params->get('enable_fb_comments');
    $enable_fb_like = $this->params->get('enable_fb_like');
    $enable_fb_share = $this->params->get('enable_fb_share');
    $enable_fb_send = $this->params->get('enable_fb_send');
    $enable_fb_photo = $this->params->get('enable_fb_photo');
    $enable_twitter = $this->params->get('enable_twitter');
    $enable_google = $this->params->get('enable_google');
    $enable_google_share = $this->params->get('enable_google_share');
    $enable_linkedin = $this->params->get('enable_linkedin');
    $enable_pinterest = $this->params->get('enable_pinterest');

    $css_code = $this->params->get('css_code');
    if (!empty($css_code))
      $document->addStyleDeclaration($css_code, 'text/css');

    // Facebook
    if ($enable_fb_comments || $enable_fb_like || $enable_fb_share || $enable_fb_send || $enable_fb_photo) {
      $fb_app_id = $this->params->get('fb_app_id');
      if ($this->params->get('auto_language')) {
        $fb_language = str_replace('-','_',JFactory::getLanguage()->getTag());
      } else {
        $fb_language = $this->params->get('fb_language');
      }
      if ($this->params->get('fb_asynchronous','1') == '1') {
        $FbCode = "
             function AddFbScript(){
               var js,fjs=document.getElementsByTagName('script')[0];
               if (!document.getElementById('facebook-jssdk')) {
                 js = document.createElement('script');
                 js.id = 'facebook-jssdk';
                 js.setAttribute('async', 'true');
           ".PHP_EOL;
        if ($fb_app_id != '') {
          $FbCode .= "js.src = '//connect.facebook.net/" . $fb_language . "/all.js#xfbml=1&appId=" . $fb_app_id . "';".PHP_EOL;
        } else {
          $FbCode .= "js.src = '//connect.facebook.net/" . $fb_language . "/all.js#xfbml=1';".PHP_EOL;
        }
        $FbCode .= "fjs.parentNode.insertBefore(js, fjs);
               }
             }
             window.addEvent('load', function() { AddFbScript() });".PHP_EOL;
        $document->addScriptDeclaration($FbCode);
      } else { //not async
        if ($fb_app_id != '') {
          $document->addScript("//connect.facebook.net/" . $fb_language . "/all.js#xfbml=1&appId=" . $fb_app_id);
        } else {
          $document->addScript("//connect.facebook.net/" . $fb_language . "/all.js#xfbml=1");
        }
      }
      $fb_comments_notification=$this->params->get('enable_notification_comment');
      if (($fb_comments_notification=='1')&&($fb_app_id != '')){
        $FbCode = "
        var request;
        function initReq(reqType,url,isAsynch,query){
        //      request.onreadystatechange=handleResponse;
          request.open(reqType,url,isAsynch);
          request.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
          request.send(query);
        }
        function httpRequest(reqType,url,asynch,query){
          if(window.XMLHttpRequest){
            request = new XMLHttpRequest();
          } else if (window.ActiveXObject){
            request=new ActiveXObject('Msxml2.XMLHTTP');
            if (! request){
              request=new ActiveXObject('Microsoft.XMLHTTP');
            }
          }
          if(request){
            initReq(reqType,url,asynch,query);
          } else {
            alert('Your browser does not permit notifications.');
          }
        }
        //    function handleResponse(  ){if(request.readyState == 4){if(request.status == 200){alert(request.responseText);} else {alert('Communication error'); }}}
        function sendData(href){
          query='href='+encodeURIComponent(href);
          var url='".JURI::root().CMP_PLG_NOTIFY."';
          httpRequest('POST',url,true,query);
        }
        ";
        if ($this->params->get('fb_asynchronous','1') == '1') {
           $FbCode .= "
              window.fbAsyncInit = function() {
                FB.Event.subscribe('comment.create', function(comment) {
                  sendData(comment.href);
                });
              }
           ";
        } else {
          $FbCode .= "
             window.addEvent('load', function() {
               FB.Event.subscribe('comment.create', function(comment) {
                  sendData(comment.href);
                });
             });
          ";
        }
        $document->addScriptDeclaration($FbCode);
      }
  
      // fb photo button
      if ($enable_fb_photo == '1') {
        $FbPhotoCode = "      
        function getPos(a) { var b = 0,c = 0; if (a.offsetParent) { do b += a.offsetLeft, c += a.offsetTop; while (a = a.offsetParent); return {left: b,top: c}}}        
        
        function fb_click_photo(imgURL,msg){
          FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
              var uid = response.authResponse.userID;
              var accessToken = response.authResponse.accessToken;
              fb_upload_photo(uid,imgURL,msg);
            } else {
              FB.login(function(response) {
                if (response.authResponse) {
                  var uid = response.authResponse.userID;
                  fb_upload_photo(uid,imgURL,msg);
                }
              }, {scope: 'user_photos,publish_stream'});
            }
          });
        }
  
        function fb_upload_photo(id,imgURL,msg){
          FB.api('/'+id+'/photos', 'post', {
            message:msg,
            url:imgURL,
          }, function(response){
            if (!response || response.error) {
              alert('Error occured uploading the photo to your profile: '+response.error);
            } else {
              alert('The photo has been successfully uploaded.' );
            }
          });
        }";
        $document->addScriptDeclaration($FbPhotoCode);
        $FbPhotoCss = "
        .css_fb_photo {
          display: inline;
          position: absolute;
          -moz-opacity:.50;
          filter:alpha(opacity=50);
          opacity:.50;
          z-index: 20;
        }
        .css_fb_photo:hover {
          -moz-opacity:1;
          filter:alpha(opacity=100);
          opacity:1;
        }";
        $document->addStyleDeclaration($FbPhotoCss,'text/css');
      } // end fb photo button
  
    } // end facebook
    
    // Twitter
    if (($enable_twitter)&&($this->params->get('twitter_style')!='custom_icon')) {
      if ($this->params->get('twitter_asynchronous','1') == '1') {
        $TwCode = "
        function AddTwitterScript(){
          var js,fjs=document.getElementsByTagName('script')[0];
          if(!document.getElementById('twitter-wjs')){
            js=document.createElement('script');
            js.id='twitter-wjs';
            js.setAttribute('async', 'true');
            js.src=\"//platform.twitter.com/widgets.js\";
            fjs.parentNode.insertBefore(js,fjs);
          }
        }
        window.addEvent('load', function() { AddTwitterScript() });";
        $document->addScriptDeclaration($TwCode);
      } else {
        $document->addScript("//platform.twitter.com/widgets.js");
      }
    } // end Twitter
    
    // Google
    if ($enable_google||($enable_google_share&&($this->params->get('google_share_style')!='custom_icon'))) {
      if ($this->params->get('auto_language')) {
        $google_language = JFactory::getLanguage()->getTag();
      } else {
        $google_language = $this->params->get('google_language','en-US');
      }
      $GoogleCode = "
      function AddGoogleScript(){
        var js,fjs=document.getElementsByTagName('script')[0];
        if(!document.getElementById('google-wjs')){
          js=document.createElement('script');
          js.id='google-wjs';
          js.setAttribute('async', 'true');
          js.src=\"//apis.google.com/js/plusone.js\";
          js.text=\"{lang: '" . $google_language . "'}\";
          fjs.parentNode.insertBefore(js,fjs);
        }
      }
      window.addEvent('load', function() { AddGoogleScript() });";
      $document->addScriptDeclaration($GoogleCode);
    } // end Google
    
    // LinkedIn
    if (($enable_linkedin)&&($this->params->get('linkedin_style')!='custom_icon')) {
      $LinkedInCode = "
      function AddLinkedInScript(){
        var js,fjs=document.getElementsByTagName('script')[0];
        if(!document.getElementById('linkedin-js')){
          js=document.createElement('script');
          js.id='linkedin-js';
          js.setAttribute('async', 'true');
          js.src=\"//platform.linkedin.com/in.js\";
          fjs.parentNode.insertBefore(js,fjs);
        }
      }
      window.addEvent('load', function() { AddLinkedInScript() });";
      $document->addScriptDeclaration($LinkedInCode);
    } // end LinkedIn
    
    // Pinterest
    if ($enable_pinterest) {
      $pinterest_selection = $this->params->get('pinterest_selection');
      $PinterestCode = "
      function AddPinterestScript(){
        var js,fjs=document.getElementsByTagName('script')[0];
        if(!document.getElementById('pinterest-js')){
          js=document.createElement('script');
          js.id='pinterest-js';
      ";
      if ($pinterest_selection == '1') { // not user select
        $PinterestCode .= "js.setAttribute('data-pin-hover', true);";
      }  
      $PinterestCode .= "
          js.setAttribute('async', 'true');
          js.src=\"//assets.pinterest.com/js/pinit.js\";
          fjs.parentNode.insertBefore(js,fjs);
        }
      }
      window.addEvent('load', function() { AddPinterestScript() });";
      $document->addScriptDeclaration($PinterestCode);
    } // End Pinterest
    
    // print mode
    if ($this->params->get('fb_comments_print','0') == '0') { // no comments
      $PrintCss = " @media print { .css_buttons0,.css_buttons1,.css_fb_like,.css_fb_share,.css_fb_send,css_fb_photo,.css_twitter,.css_google,.css_google_share,.css_linkedin,.css_pinterest,.css_fb_comments,.css_fb_comments_count { display:none }}";
    } else {
      $PrintCss = " @media print { .css_buttons0,.css_buttons1,.css_fb_like,.css_fb_share,.css_fb_send,css_fb_photo,.css_twitter,.css_google,.css_google_share,.css_linkedin,.css_pinterest { display:none }}";
    }
    $document->addStyleDeclaration($PrintCss,'text/css');
 
  } // end on after dispatch

  private function getTitle($obj) {
    if (isset($obj->product_name)) {
      return $obj->product_name;
    } else {
      return $obj->title;
    }
  }

  private function getDescription($obj, $view) {
    if ($view == 'productdetails') {
      if (isset($obj->product_s_desc)) {
        $description = $obj->product_s_desc;
      } else {
        $description = $obj->product_desc;
      }
      return $this->str_encode(strip_tags($description));
    }
    if (isset($obj->text)) {
      $text = $obj->text;
    } else {
      $text=$obj->introtext.$obj->fulltext;
    }
    $opengraph_description = $this->params->get('opengraph_description');
    if (($opengraph_description == '1') || ($opengraph_description == '2') || ($opengraph_description == '3')) { // first paragraph or first 255 chars or only intr
      if ($opengraph_description == '2') { // first 255 chars
        $description = $this->str_encode(mb_substr(strip_tags($text), 0, 251) . "... ");
      } elseif ($opengraph_description == '3') { // only intro
        $description = $this->str_encode(strip_tags($obj->introtext));
      } else { // first paragraph
        $content = $this->str_encode(strip_tags($text));
        $pos = strpos($content, '.');
        if ($pos === false) {
          $description = $content;
        } else {
          $description = substr($content, 0, $pos + 1);
        }
      }
    } else { // article meta data
      $description = $this->str_encode(strip_tags($obj->metadesc));
    }
    $description = preg_replace('/[\r\n\s]+/', ' ', $description);
    return $description;
  }

  private function getPicture($obj, $view) {
    $images = array();
    if (($view == 'productdetails') || ($_REQUEST['option'] == 'com_virtuemart')) {
      return $images;
    }
    $defaultimage = $this->params->get('opengraph_defaultimage');
    $onlydefaultimage = $this->params->get('opengraph_onlydefaultimage');
    if ($defaultimage == "") {
      $defaultimage = JURI::root() . CMP_OG_LINKIMG;
    } else {
      if (!preg_match('%^(?://|http://|https://)%', $defaultimage)) {
        $defaultimage = preg_replace('#^/#', '', $defaultimage);
        $defaultimage = JURI::root() . $defaultimage;
      }
    }
    if ($opengraph_onlydefaultimage == '1') {
      $images[] = $defaultimage;
    } else {
      if (property_exists($obj,'images')) {
        if ($img = json_decode($obj->images)) {
          if ($img->{'image_intro'} != null) {
            $images[] = JURI::root() . $img->{'image_intro'};
          } elseif ($img->{'image_fulltext'} != null) {
            $images[] = JURI::root() . $img->{'image_fulltext'};
          }
        }
      }
      if (isset($obj->text)) {
        $text = $obj->text;
      } else {
        $text=$obj->introtext.$obj->fulltext;
      }
      if ($view == 'article') {
        $this->find_youtube_images($text, $images);
        $this->find_images($text, $images);
      }
      if (count($images) == 0)
        $images[] = $defaultimage;
    }
    return $images;
  }

  private function show($var, $mode = 'message') {
    JFactory::getApplication()->enqueueMessage('<pre>' . print_r($var, true) . '</pre>', $mode);
  }
  
  private function str_encode($str){
    return htmlspecialchars($str,ENT_QUOTES,"UTF-8");
  }
  

}
?>