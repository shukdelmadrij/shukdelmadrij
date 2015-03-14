<?php
/*
 * Joomla 2.5
 * Name: fb_tw_plus1
 * Plugin version 3.0
*/
//error_reporting(E_ALL);
define('_JEXEC',1);
if (! defined('DS')) {
  define('DS',DIRECTORY_SEPARATOR);
}
define('PLG_NAME','fb_tw_plus1');
define('JPATH_BASE',realpath(dirname(__FILE__) . "/../../../../"));
require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');

jimport('joomla.plugin.helper');

require_once (JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'factory.php');
$app = JFactory::getApplication('site');   

if (JPluginHelper::isEnabled('content',PLG_NAME)){ 
  $plugin = JPluginHelper::getPlugin('content',PLG_NAME);  
  // get plugin parameter
  if (class_exists('JParameter')) {
    jimport('joomla.html.parameter');
    $pluginParams = new JParameter($plugin->params);
  } else {
    jimport('joomla.registry.registry');
    $pluginParams = new JRegistry($plugin->params);
  }
  
  if ($pluginParams->get('enable_notification_comment') == '1') {
    if ((isset($_REQUEST['href'])) && ($_REQUEST['href'] != '')) {
      $href = $_REQUEST['href'];
      if (preg_match('%^(|http:|https:)//www\.facebook\.com.*?url=(.*?)$%im',$href,$regs)) {
        $href = urldecode($regs[2]);
        if (preg_match('%^(|http:|https:)//' . $_SERVER['HTTP_HOST'] . '.*?$%i',$href,$regs)) {
          notify($href,$app);
        }
      } elseif (preg_match('%^(|http:|https:)//' . $_SERVER['HTTP_HOST'] . '.*?$%i',$href,$regs)) {
        notify($href,$app);
      }
    }
  }
}
function notify($href, &$app){
  $app->initialise();
  $mailer = JFactory::getMailer();
  $config = new JConfig();
  $sender = array($config->mailfrom,$config->fromname);
  $mailer->setSender($sender);
  $mailer->addRecipient($config->mailfrom);
  $mailer->setSubject("New comment");
  $mailer->setBody("There is a new comment on " . $config->sitename . " for this <a href='" . $href . "'>page</a>.");
  $mailer->isHTML(true);
  $send = $mailer->send();
}



