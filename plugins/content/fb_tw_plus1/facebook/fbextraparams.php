<?php
//error_reporting(E_ALL);
// no direct access
defined("_JEXEC") or die("Restricted access");
if (!defined('DS')) {
  define('DS',DIRECTORY_SEPARATOR);
}
if (!class_exists('Facebook', false)) {
  require_once (JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'fb_tw_plus1'.DS.'facebook'.DS.'facebook.php');
}

jimport('joomla.html.html');
jimport('joomla.form.formfield');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.form.helper');

class JFormFieldfbextraparams extends JFormField {
  protected $type = "fbextraparams";
  
  private function getHTLMtextfield($label,$value,$name){
    $html = array();
    $html[] = '<div class="control-group">';
    $html[] = ' <div class="control-label">'.$label.'</div>';
    $html[] = ' <div class="controls">';
    $html[] = '  <input type="text"     name="'.$this->name.$name.'" value="'.$value.'" size="50" />';
    $html[] = ' </div>';
    $html[] = '</div>';
    $code=implode($html);
    return $code;
  }
  
  private function getHTLMcheckboxfield($label,$value,$name,$checked){
    $html = array();
    $html[] = '<div class="control-group">';
    $html[] = ' <div class="control-label">' . $label . '</div>';
    $html[] = ' <div class="controls">';
    $html[] = '  <input type="checkbox" name="'.$this->name.$name.'" value="'.$value.'" '.$checked.' />';
    $html[] = ' </div>';
    $html[] = '</div>';
    $code=implode($html);
    return $code;
  }
  
  private function getHTLMmsgfield($text,$class){
    $class = !empty($class) ? ' class="' . $class . '"' : '';
    $html = array();
    $html[] = '<div class="control-group">';
    $html[] = ' <span class="spacer">';
    $html[] = '  <span class="before"></span>';
    $html[] = '  <span '.$class.'>';
    $html[] = '   <label id="' . $this->id . '-lbl" class="hasTooltip"><b>' . $text . '</b></label>';
    $html[] = '  </span>';
    $html[] = '  <span class="after"></span>';
    $html[] = ' </span>';
    $html[] = '</div>';
    $code=implode($html);
    return $code;
  }
  
  public function getControlGroup(){
    $fbparams      = $this->form->getValue('params');
    $fb_enable_autopublish = $fbparams->fb_enable_autopublish;
    if ($fb_enable_autopublish=='0') return '';
    $fb_app_id     = $fbparams->fb_app_id;
    $fb_secret_key = $fbparams->fb_secret_key;
    $fb_token      = (isset($fbparams->fb_extra_params))?$fbparams->fb_extra_params['fb_token']:'';
    $fb_admin      = (isset($fbparams->fb_extra_params))?$fbparams->fb_extra_params['fb_admin']:'';
    if (!isset($fbparams->fb_extra_params) || $fbparams->fb_extra_params['fb_ids']=='') {
      $fb_ids      = array();
    } else {
      $fb_ids      = $fbparams->fb_extra_params['fb_ids'];
    }
  
    $html = array();
  
    if (($fb_app_id != '') && ($fb_secret_key != '')) {
  
      $facebook = new Facebook(array('appId' => $fb_app_id, 'secret' => $fb_secret_key));
      $user = $facebook->getUser();
  
      $uri= JFactory::getURI();
      $url=urlencode($uri->current().'?'.$uri->getQuery());
      $loginUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fb_app_id."&redirect_uri=".$url."&scope=publish_stream,publish_actions,offline_access,manage_pages,user_groups";
  
      if ($fb_token != '') { //if there is a token => check the token
        $facebook->setAccessToken($fb_token);
        try {
          $user=$facebook->api('/me');
        } catch (Exception $e) {
          //Access token is not valid
          $user=null;
        }
      } else {
        //Access token missing
        $user=null;
      }
  
      if ($user) { //access token ok
        $fb_admin = $user['id'];
        $html[] = $this->getHTLMtextfield('Access Token'    ,$fb_token,'[fb_token]');
        $html[] = $this->getHTLMtextfield('Administrator ID',$fb_admin,'[fb_admin]');
        if (count($fb_ids)==0) {
          $html[] = $this->getHTLMmsgfield('Choose the walls account,groups or pages where to publish','fb_message');
        }
        $html[] = $this->getHTLMmsgfield('Admin account','');
        $html[] = $this->getHTLMcheckboxfield('Wall ID::Administrator ID',$fb_admin,'[fb_ids][]',(in_array($fb_admin, $fb_ids) ? " checked='checked'" : ""));
  
        try {
          $groups = $facebook->api('/'.$fb_admin.'/groups/','GET', array('access_token' => $fb_token));
        } catch (FacebookApiException $e) {
          $this->show($e);
          $groups = null;
        }
        if ($groups && $groups['data'] && is_array($groups['data']) && count($groups['data']) > 0) {
          $html[] = $this->getHTLMmsgfield('Groups','');
          foreach($groups['data'] as $group) {
            $html[] = $this->getHTLMcheckboxfield($group['name'],$group['id'],'[fb_ids][]',(in_array($group['id'], $fb_ids) ? " checked='checked'" : ""));
          }
        }
  
        try {
          $pages = $facebook->api('/'.$fb_admin.'/accounts/', 'GET', array('access_token' => $fb_token));
        } catch (FacebookApiException $e) {
          $this->show($e);
          $pages = null;
        }
        if ($pages && $pages['data'] && is_array($pages['data']) && count($pages['data']) > 0) {
          $html[] = $this->getHTLMmsgfield('Pages','');
          foreach($pages['data'] as $page) {
            if ($page['category'] != 'Application') {
              $html[] = $this->getHTLMcheckboxfield($page['name'],$page['id'],'[fb_ids][]',(in_array($page['id'], $fb_ids) ? " checked='checked'" : ""));
            }
          }
        }
      } else { //if access token is not valid or missing
        $code = JRequest::getVar('code');
        if ($code && !empty($code)) {
          $uri= JFactory::getURI();
          $url=urlencode($uri->current().'?'.$uri->getQuery());
          $token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$fb_app_id."&redirect_uri=".$url."&client_secret=".$fb_secret_key."&code=".$code;
          $response = $this->get_url_contents($token_url);
          if (empty($response)||preg_match('/error/i',$response)){
            JFactory::getApplication()->enqueueMessage( 'Error on access token:<pre>'.$response.'</pre>', 'error' );
          } else {
            $params = null;
            parse_str($response, $params);
            $fb_token = $params['access_token'];
          }
          $html[] = $this->getHTLMtextfield('Access Token',$fb_token,'[fb_token]');
          $html[] = '<input type="hidden" name="'.$this->name.'[fb_admin]" value="" />';
          $html[] = '<input type="hidden" name="'.$this->name.'[fb_ids]" value="" />';
          $html[] = $this->getHTLMmsgfield('Save to complete the configuration of the Facebook Application','fb_message');
        } else {
          $fb_token='';
          $fb_admin='';
          $html[] = '<input type="hidden" name="'.$this->name.'[fb_token]" value="" />';
          $html[] = '<input type="hidden" name="'.$this->name.'[fb_admin]" value="" />';
          $html[] = '<input type="hidden" name="'.$this->name.'[fb_ids]" value="" />';
          $html[] = $this->getHTLMmsgfield('Click the link to connect the Facebook Application <a class="fb_button" href="'.$loginUrl.'" title="Facebook login">Facebook login</a>','fb_message');
        }
      }
    } else {
      $fb_token='';
      $fb_admin='';
      $html[] = '<input type="hidden" name="'.$this->name.'[fb_token]" value="" />';
      $html[] = '<input type="hidden" name="'.$this->name.'[fb_admin]" value="" />';
      $html[] = '<input type="hidden" name="'.$this->name.'[fb_ids]" value="" />';
    }
    $html[] = '<style>.fb_message {border: 2px solid #a00;background-color: #FAF2EA;padding: 10px;margin: 5px;display: inline-block;}</style>';
  
    $code=implode($html);
    return $code;
  }
  
  protected function getInput(){
    $fbparams      = $this->form->getValue('params'); 
    $fb_enable_autopublish = $fbparams->fb_enable_autopublish;
    if ($fb_enable_autopublish=='0') return '';
    $fb_app_id     = $fbparams->fb_app_id;
    $fb_secret_key = $fbparams->fb_secret_key;
    $fb_token      = (isset($fbparams->fb_extra_params))?$fbparams->fb_extra_params['fb_token']:'';
    $fb_admin      = (isset($fbparams->fb_extra_params))?$fbparams->fb_extra_params['fb_admin']:'';
    if (!isset($fbparams->fb_extra_params) || $fbparams->fb_extra_params['fb_ids']=='') {
      $fb_ids      = array();
    } else {
      $fb_ids      = $fbparams->fb_extra_params['fb_ids'];
    }
    echo "<div id='fb_extra_params'><ul>";
    if (($fb_app_id != '') && ($fb_secret_key != '')) {

      $facebook = new Facebook(array('appId' => $fb_app_id, 'secret' => $fb_secret_key));
      $user = $facebook->getUser();
      
      $uri= JFactory::getURI();
      $url=urlencode($uri->current().'?'.$uri->getQuery());
      $loginUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fb_app_id."&redirect_uri=".$url."&scope=publish_stream,publish_actions,offline_access,manage_pages,user_groups";
 
      if ($fb_token != '') { //if there is a token => check the token
        $facebook->setAccessToken($fb_token);
        try {
          $user=$facebook->api('/me');
        } catch (Exception $e) { 
          //Access token is not valid
          $user=null;
        }
      } else {
        //Access token missing
        $user=null;
      }

      if ($user) { //access token ok               
        $fb_admin = $user['id'];
        echo "<li><label id='jform_params_fbextraparams_fb_token-lbl' for='jform_params_fbextraparams_fb_token' class='hasTip' title='Facebook security access token'>Access Token</label>
              <input type='text' name='jform[params][".$this->fieldname."][fb_token]' id='jform_params_fbextraparams_fb_token' value='".$fb_token."' size='50'/></li>";
        echo "<li><label id='jform_params_fbextraparams_fb_admin-lbl' for='jform_params_fbextraparams_fb_admin' class='hasTip' title='Administrator ID'>Autopublish administrator ID</label>
              <input type='text' name='jform[params][".$this->fieldname."][fb_admin]' id='jform_params_fbextraparams_fb_admin' value='".$fb_admin."' size='50'/></li>";
        if (count($fb_ids)==0) {
          echo "<li><div class='fb_box'><label class='fb_message'>Choose the walls account,groups or pages where to publish</label></div></li>";
        }
        echo "<li><label class='fb_bold'>Admin account</label></li>";
        
        echo "<li><label class='hasTip' title='Wall ID::Administrator ID'>".$user['name']."</label>
              <input type='checkbox' name='jform[params][".$this->fieldname."][fb_ids][]' value='".$fb_admin."'".(in_array($fb_admin, $fb_ids) ? " checked='checked'" : "")."></li>";

        try {
          $groups = $facebook->api('/'.$fb_admin.'/groups/','GET', array('access_token' => $fb_token));
        } catch (FacebookApiException $e) {
          $this->show($e);
          $groups = null;
        }
        if ($groups && $groups['data'] && is_array($groups['data']) && count($groups['data']) > 0) {
          echo "<li><label class='fb_bold'>Groups</label></li>";
          foreach($groups['data'] as $group) {
            echo "<li><label class='fb_label'>".$group['name']."</label>
                  <input type='checkbox' name='jform[params][".$this->fieldname."][fb_ids][]' value='".$group['id']."'".(in_array($group['id'], $fb_ids) ? " checked='checked'" : "")."></li>";
          }
        }
 
        try {
          $pages = $facebook->api('/'.$fb_admin.'/accounts/', 'GET', array('access_token' => $fb_token));
        } catch (FacebookApiException $e) {
          $this->show($e);
          $pages = null;
        }        
        if ($pages && $pages['data'] && is_array($pages['data']) && count($pages['data']) > 0) {
          echo "<li><label class='fb_bold'>Pages</label></li>";
          foreach($pages['data'] as $page) {
            if ($page['category'] != 'Application') {
              echo "<li><label class='fb_label'>".$page['name']."</label>
                    <input type='checkbox' name='jform[params][".$this->fieldname."][fb_ids][]' value='".$page['id']."'".(in_array($page['id'], $fb_ids) ? " checked='checked'" : "")."></li>";
            }
          } 
        }
      } else { //if access token is not valid or missing
        $code = JRequest::getVar('code');
        if ($code && !empty($code)) {
          $uri= JFactory::getURI();
          $url=urlencode($uri->current().'?'.$uri->getQuery());       
          $token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$fb_app_id."&redirect_uri=".$url."&client_secret=".$fb_secret_key."&code=".$code;
          $response = $this->get_url_contents($token_url);
          if (empty($response)||preg_match('/error/i',$response)){
            JFactory::getApplication()->enqueueMessage( 'Error on access token:<pre>'.$response.'</pre>', 'error' );
          } else {
            $params = null;
            parse_str($response, $params);
            $fb_token = $params['access_token'];
          }   
          echo "<li><label id='jform_params_fbextraparams_fb_token-lbl' for='jform_params_fbextraparams_fb_token' class='hasTip' title='Facebook security access token'>Access Token</label>
                <input type='text' name='jform[params][".$this->fieldname."][fb_token]' id='jform_params_fbextraparams_fb_token' value='".$fb_token."' size='50'/></li>";
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_admin]' value=''/></li>";
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_ids]'   value=''/></li>";
          echo "<li><div class='fb_box'><label class='fb_message'>Save to complete the configuration of the Facebook Application</label></div></li>";
        } else {
          $fb_token='';
          $fb_admin='';
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_token]' value=''/></li>";
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_admin]' value=''/></li>";
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_ids]'   value=''/></li>"; 
          echo "<li><div class='fb_box'><label class='fb_message'>Click the link to connect the Facebook Application</label>
                <a class='fb_button' href='".$loginUrl."' title='Facebook login'>Facebook login</a></div></li>";
        }
      }
    } else {
      $fb_token='';
      $fb_admin='';
      echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_token]' id='jform_params_fbextraparams_fb_token' value=''/></li>";
      echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_admin]' id='jform_params_fbextraparams_fb_admin' value=''/></li>";
      echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_ids]'   id='jform_params_fbextraparams_fb_ids'   value='".array()."'/></li>";     
    }
    echo "</ul>";
    echo "</div>";
    echo "<style>  .controls #fb_extra_params {margin-left: -160px;} #fb_extra_params li,#fb_extra_params ul{list-style: none;list-style-type: none;margin:0;padding:0;}
                 #fb_extra_params input {float: left;margin: 2px 5px 5px 0;width: auto;padding: 2px;}
                 #fb_extra_params label {line-height: 2em;clear: left;min-width: 12em;float: left;margin:5px 5px 10px 0; font-weight: bold;margin: 0;padding: 0;border: 0;background: transparent;} .fb_bold { font-weight:bold; color:#686;} .fb_label{font-size:11px;width:145px;} 
                 div.fb_box label.fb_message { font-weight:bold; color:#600; width: auto; max-width:auto; background-color: #FEE; padding: 5px 8px; border: 1px solid #F99; float: none; margin: 5px auto;}
                 .fb_box { display: block; float: left; clear: left; text-align: center; width: 100%; }
                 .fb_button { display: inline-block; padding: 6px 8px; font-size: 12px; font-weight: bold; border: 1px solid #CCD; margin: 5px; text-decoration: none;}</style>"; 
  }

  private function get_url_contents($url){
    $ch = curl_init();
    $timeout = 5;
    curl_setopt ($ch, CURLOPT_URL,$url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
  }
  
  private function Show($var, $mode = 'message'){
    JFactory::getApplication()->enqueueMessage('<pre>' . print_r($var,true) . '</pre>',$mode);
  }
}

?>