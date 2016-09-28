<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Getting params from template
$params = JFactory::getApplication()->getTemplate(true)->params;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');

if($task == "edit" || $layout == "form" ) {
  $fullWidth = 1;
} else {
  $fullWidth = 0;
}

//Page Title ---- FEDERICO LEVIN
$app = JFactory::getApplication();
$this->setTitle($this->getTitle() . ' - ' . $app->getCfg('sitename'));

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScript('templates/' .$this->template. '/js/template.js');

// Add Stylesheets
$doc->addStyleSheet('templates/'.$this->template.'/css/template.css?n=51');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Add current user information
$user = JFactory::getUser();

// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8')) {
  $span = "span6";
} elseif ($this->countModules('position-7') && !$this->countModules('position-8')) {
  $span = "span9";
} elseif (!$this->countModules('position-7') && $this->countModules('position-8')) {
  $span = "span9";
} else {
  $span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile')) {
  $logo = '<img src="'. JUri::root() . $this->params->get('logoFile') .'?n=125" alt="'. $sitename .'" />';
} elseif ($this->params->get('sitetitle')) {
  $logo = '<span class="site-title" title="'. $sitename .'">'. htmlspecialchars($this->params->get('sitetitle')) .'</span>';
} else {
  $logo = '<span class="site-title" title="'. $sitename .'">'. $sitename .'</span>';
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <jdoc:include type="head" />
  <?php // Use of Google Font
    if ($this->params->get('googleFont')) { ?>
      <link href='http://fonts.googleapis.com/css?family=<?php echo $this->params->get('googleFontName');?>' rel='stylesheet' type='text/css' />
      <style type="text/css">
        h1,h2,h3,h4,h5,h6,.site-title{
          font-family: '<?php echo str_replace('+', ' ', $this->params->get('googleFontName'));?>', sans-serif;
        }
      </style>
  <?php } ?>
  <?php // Template color
    if ($this->params->get('templateColor')) { ?>
      <style type="text/css">
        body.site {
          border-top: 3px solid <?php echo $this->params->get('templateColor');?>;
          background-color: <?php echo $this->params->get('templateBackgroundColor');?>
        }

        a {
          color: <?php echo $this->params->get('templateColor');?>;
        }

        .navbar-inner,
        .nav-list > .active > a,
        .nav-list > .active > a:hover,
        .dropdown-menu li > a:hover,
        .dropdown-menu .active > a,
        .dropdown-menu .active > a:hover,
        .nav-pills > .active > a,
        .nav-pills > .active > a:hover,
        .btn-primary {
          background: <?php echo $this->params->get('templateColor');?>;
        }

        .navbar-inner {
          -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
          -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
          box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
        }

        .navigation .nav > li {
          position: relative;
          float:left !important;
          background-color:#035325 !important;
          margin-left: 2px;
          border-radius: 5px;
          margin-top:5px;
          margin-bottom:5px;
          font-weight:bold;
        }

        .navigation .nav > li a{
          padding:8px !important;
          color:#fff;
        }

        .navigation {
          margin-bottom: 10px;
          display: inline-block;
          height:auto !important;
          width:100% !important;
          background-color:white;
          color:white;
          border-radius: 5px;
        }

        .breadcrumb {
          display:none;
        }

        .nav {
          margin-left:auto !important;
        }

        .navigation .menu li > a:hover,
        .navigation .menu li > a:focus,
        .navigation .menu:hover > a {
          color: #008932 !important;
          background-color:#eee;
          border-radius:5px;
          border-width:0px;
        }
        .navigation .nav-child li {
          background-color:#008932 !important;
          border-radius: 0px;
        }
        .navigation .nav-child {
          background-color:#008932;
        }
        .navigation .nav-child:before {
          border-bottom: 7px solid #008932;
          border-bottom-color: #008932;
        }
        .navigation .nav-child:after {
          border-bottom:#008932;
        }
        .navigation .nav-child li > a:hover,
        .navigation .nav-child li > a:focus,
        .navigation .nav-child:hover > a {
          text-decoration: none;
          color: #008932;
          background-color: #eee;
          background-image: none;
          border-radius: 0px;
        }

      	div#imagenFooter {
      		background-image: url('/images/shuk/rejaconflores.png');
      		height: 249px;
      		margin-left: -21px;
      		text-align: center;
      		overflow: visible !important;
      		position: relative;
      		z-index: 10000;
      		display: block;
      		background-position: 50% 50%;
      		background-repeat: repeat-x;
      		width: 982px;
          border-radius:4px;
      	}

        div#imagenFooter img {
          margin-top: 112px;
        }

        img#pajarito {
          width: 100px;
          margin-left: 25px;
        }

        img#planta {
          width: 100px;
          margin-left: 150px;
        }

        img#logoMacabiFooter {
          width: 300px;
          margin-left: 100px;
        }
      </style>
  <?php } ?>
  <!--[if lt IE 9]>
    <script src="<?php echo $this->baseurl ?>/media/jui/js/html5.js"></script>
  <![endif]-->
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-21997700-4', 'auto');
    ga('send', 'pageview');

  </script>
</head>

<body class="site <?php echo $option . ' view-' . $view . ($layout ? ' layout-' . $layout : ' no-layout') . ($task ? ' task-' . $task : ' no-task') . ($itemid ? ' itemid-' . $itemid : '') . ($params->get('fluidContainer') ? ' fluid' : ''); ?>">
  <!-- Body -->
  <div class="body">
    <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : '');?>">
      <!-- Header -->
      <header class="header" role="banner">
        <div class="header-inner clearfix">
          <a class="brand pull-left" href="<?php echo $this->baseurl; ?>">
            <?php echo $logo;?> <?php if ($this->params->get('sitedescription')) { echo '<div class="site-description">'. htmlspecialchars($this->params->get('sitedescription')) .'</div>'; } ?>
          </a>
          <div class="header-search pull-right">
            <jdoc:include type="modules" name="position-0" style="none" />
          </div>
        </div>
      </header>

      <?php if ($this->countModules('position-1')) : ?>
      <nav class="navigation" role="navigation">
        <jdoc:include type="modules" name="position-1" style="none" />
      </nav>
      <div style="clear:'both';"></div>
      <?php endif; ?>

      <jdoc:include type="modules" name="banner" style="xhtml" />

      <div class="row-fluid" style="min-height: 500px;">
        <?php if ($this->countModules('position-8')) : ?>
        <!-- Begin Sidebar -->
        <div id="sidebar" class="span3">
          <div class="sidebar-nav">
            <jdoc:include type="modules" name="position-8" style="xhtml" />
          </div>
        </div>
        <!-- End Sidebar -->
        <?php endif; ?>
        <main id="content" role="main" class="<?php echo $span;?>">
          <!-- Begin Content -->
          <jdoc:include type="modules" name="position-3" style="xhtml" />
          <jdoc:include type="message" />
          <jdoc:include type="component" />
          <jdoc:include type="modules" name="position-2" style="none" />
          <!-- End Content -->
        </main>
        <?php if ($this->countModules('position-7')) : ?>
        <div id="aside" class="span3">
          <!-- Begin Right Sidebar -->
          <jdoc:include type="modules" name="position-7" style="well" />
          <!-- End Right Sidebar -->
        </div>
        <?php endif; ?>
      </div>

      <div id="imagenFooter">
        <div style="margin: 0 auto;">
          <img src="/images/shuk/pajaro.png" alt="pajarito" id="pajarito" />
          <img src="/images/shuk/planta.png" alt="planta" id="planta" />
          <a href="http://www.macabi.com.ar" target="_blank">
            <img id ="logoMacabiFooter" src="/images/shuk/logoMacabi.png?id=78" alt="Macabi" />
          </a>
        </div>
      </div>

    </div>
  </div>

  <!-- Footer -->
  <footer class="footer" role="contentinfo">
    <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : '');?>" style="font-weight:bold;">
      <hr />
      <jdoc:include type="modules" name="footer" style="none" />
      <p class="pull-right"><a href="#top" id="back-top"><?php echo JText::_('TPL_PROTOSTAR_BACKTOTOP'); ?></a></p>
      <p>&copy; <?php echo $sitename . " - O.H.A. Macabi"; ?> <?php echo date('Y');?></p>
    </div>
  </footer>

  <!-- Google Analytics Code -->
  <script>
    // (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    // (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    // m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    // })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    // ga('create', 'UA-21997700-4', 'shukdelmadrij.com.ar');
    // ga('send', 'pageview');
  </script>

  <jdoc:include type="modules" name="debug" style="none" />
</body>
</html>