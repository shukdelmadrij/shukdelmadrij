<?php echo '<?xml version="1.0" encoding="utf-8"?' .'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php require("suckerfish.php"); ?>
<html>

<head>
    <jdoc:include type="head" />
	<link rel="stylesheet" href="templates/system/css/general.css" type="text/css" />
	<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
</head>

<body>
<div id="Banner"><!--Banner Area--></div>

<div id="Top"><!--Pull down menu and search box area just below the banner-->
<div id="TopLeft"><!--Left section of top display area, i.e., the pull down menu-->
	<div id="navFish">
		<?php mosShowListMenu("topmenu"); ?><!--Suckerfish pull down menu-->
	</div>
</div>
<div id="TopRight"><!--Right section of top display area, i.e., the search box--><jdoc:include type="modules" name="TopRight" style="xhtml" /></div>
</div><!--end Top-->

<div id="Main"><!--Main display area -->
<div id="MainLeft"><!--Left section of main display area--> <jdoc:include type="modules" name="MainLeft" style="xhtml" /></div>
<div id="MainRight"><!--Right section of main display area--> <jdoc:include type="component" /></div>
</div>

<div id="Footer"><!--Footer display area / Footer-->
<h6>This is a Joomla 1.5 template example by Jim Yuzwalk</h6>
</div>

</body>
</html>