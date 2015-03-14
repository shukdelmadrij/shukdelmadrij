/**
 * @package Extly.Library
 * @subpackage lib_extly - Extly Framework
 *
 * @author Prieco S.A. <support@extly.com>
 * @copyright Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link http://www.extly.com http://support.extly.com
 */

/*jslint plusplus: true, browser: true, sloppy: true */
/*global jQuery, Request, Joomla, alert, Backbone */

define('xtcronjob-expression-field', [], function( ) {
	
	var _this = this;

	var the_form = jQuery('.cronjob-expression-form');	
    var shortcuts_area = jQuery('.shortcuts');
    
    var minute_ctrl = jQuery('.cronjob-expression-form .minute-part');
    var hour_ctrl = jQuery('.cronjob-expression-form .hour-part');
    var day_ctrl = jQuery('.cronjob-expression-form .day-part');
    var month_ctrl = jQuery('.cronjob-expression-form .month-part');
    var weekday_ctrl = jQuery('.cronjob-expression-form .weekday-part');
    
    var all_ctrls = minute_ctrl.add(hour_ctrl).add(day_ctrl).add(month_ctrl).add(weekday_ctrl);
    
    var unix_mhdmd = jQuery('.cronjob-expression-form .unix_mhdmd-part');
 
    _this.onReset = function() {
    	the_form.find('select').val('*').trigger('liszt:updated');
    	unix_mhdmd.val('');
	};
	
	_this.onChangeMhdmd = function() {
		var minute2 = minute_ctrl.val();
		var hour2 = hour_ctrl.val();
		var day2 = day_ctrl.val();
		var month2 = month_ctrl.val();
		var weekday2 = weekday_ctrl.val();
		
		var mhdmd = minute2 + " " + hour2 + " " + day2 + " " + month2 + " " + weekday2;
		unix_mhdmd.val(mhdmd);			
	};
	
    all_ctrls.change(_this.onChangeMhdmd);
    
    shortcuts_area.find('.reset').click(_this.onReset);
    
    shortcuts_area.find('.example1').click(function() {
		_this.onReset();
		minute_ctrl.val('30').trigger('liszt:updated');
		hour_ctrl.val('9').trigger('liszt:updated');
		_this.onChangeMhdmd();
	});
    
    shortcuts_area.find('.example2').click(function() {
		_this.onReset();
		minute_ctrl.val('30').trigger('liszt:updated');
		hour_ctrl.val('9').trigger('liszt:updated');
		weekday_ctrl.val('1').trigger('liszt:updated');
		_this.onChangeMhdmd();
	});
    
    shortcuts_area.find('.example3').click(function() {
		_this.onReset();
		minute_ctrl.val('15').trigger('liszt:updated');
		hour_ctrl.val('0,6,12,18').trigger('liszt:updated');
		_this.onChangeMhdmd();
	});
    
    shortcuts_area.find('.example4').click(function() {
		_this.onReset();
		minute_ctrl.val('59').trigger('liszt:updated');
		hour_ctrl.val('11').trigger('liszt:updated');
		unix_mhdmd.val('59 11 * * 1,3,5');
	});
    
    shortcuts_area.find('.example5').click(function() {
		_this.onReset();
		minute_ctrl.val('47').trigger('liszt:updated');
		hour_ctrl.val('6').trigger('liszt:updated');
		day_ctrl.val('8').trigger('liszt:updated');
		month_ctrl.val('12').trigger('liszt:updated');
		_this.onChangeMhdmd();
	});

});
