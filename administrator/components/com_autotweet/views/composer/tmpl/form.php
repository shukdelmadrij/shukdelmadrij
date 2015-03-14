<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - Extended Directory for SobiPro
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

$this->loadHelper('select');

JHtml::_('behavior.formvalidation');

$config = JFactory::getConfig();
$offset = $config->get('offset');

JHTML::_('behavior.calendar');

?>

<div class="extly ng-cloak">
	<div class="extly-body">

		<div class="row-fluid">
			<div class="span6">
<?php

include_once '1-editor.php';
?>
			</div>
			<div class="span6">
<?php

/*
						<div class="navbar">
							<div class="navbar-inner">
								<ul class="nav pull-right">
									<li class="active"><a> <i
											class="xticon xticon-calendar"></i> Requests
									</a></li>
									<li class="dropdown"><a data-toggle="dropdown"
										class="dropdown-toggle"> <i class="xticon xticon-tasks"></i>
											Posts <b class="caret"></b></a>
										<ul class="dropdown-menu">
											<li><a>Action</a></li>
											<li><a>Another action</a></li>
											<li><a>Something else here</a></li>
											<li class="divider"></li>
											<li><a>Separated link</a></li>
										</ul></li>
									<li><a> <i class="xticon xticon-leaf"></i> Evergreen
									</a></li>
								</ul>
							</div>
						</div>
 */

include_once '2-requests.php';
?>

			</div>
		</div>

	</div>
</div>
