<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.select');

/**
 * Utility class for creating HTML select lists
 *
 * @package     Extly.Library
 * @subpackage  HTML
 * @since       11.1
 */
abstract class EHtmlSelect
{
	/**
	 * Default values for options. Organized by option group.
	 *
	 * @var     array
	 */
	protected static $_optionDefaults = array(
					'option' => array(
									'option.attr' => null,
									'option.disable' => 'disable',
									'option.id' => null,
									'option.key' => 'value',
									'option.key.toHtml' => true,
									'option.label' => null,
									'option.label.toHtml' => true,
									'option.text' => 'text',
									'option.text.toHtml' => true
					)
	);

	/**
	 * yesNo.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 * @param   string  $idTag     The id for the field
	 *
	 * @return  string  HTML
	 */
	public static function yesNo($selected = null, $name = 'yesno', $attribs = array(), $idTag = false)
	{
		return self::booleanlist($selected, $name, $attribs, 'JYES', 'JNO', $idTag);
	}

	/**
	 * Generates a boolean radio list.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $yes       Language key for Yes
	 * @param   string  $no        Language key for no
	 * @param   string  $idTag     The id for the field
	 *
	 * @return  string  HTML for the radio list
	 */
	public static function booleanList($selected, $name = null, $attribs = array(), $yes = 'JYES', $no = 'JNO', $idTag = false)
	{
		$options = array();
		$options[] = array(
						'name' => $yes,
						'value' => 1
		);
		$options[] = array(
						'name' => $no,
						'value' => 0
		);

		return self::btngrouplist($selected, $name, $attribs, $options, $idTag);
	}

	/**
	 * btnGroupList
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   array   $options   Array of options
	 * @param   string  $idTag     The id for the field
	 *
	 * @return  string  HTML for the radio list
	 */
	public static function btnGroupList($selected, $name = null, $attribs = array(), $options = array(), $idTag = false)
	{
		if (!$idTag)
		{
			$idTag = EHtml::generateIdTag();
		}

		if (isset($attribs['onchange-submit']))
		{
			$onchange = ' onchange-submit';
		}
		else
		{
			$onchange = '';
		}

		if (isset($attribs['class']))
		{
			$class = ' ' . $attribs['class'];
		}
		else
		{
			$class = ' btn-small';
		}

		if (isset($attribs['ng-model']))
		{
			$ngModel = ' ng-model="' . $attribs['ng-model'] . '"';
		}
		else
		{
			$ngModel = '';
		}

		$arr = array();

		$arr[] = '<input type="hidden" name="' . $name . '" id="' . $idTag . '" value="' . $selected . '"' . $ngModel . '>';
		$arr[] = '<div class="xt-group" data-toggle="buttons-radio">';

		if (count($options) > 0)
		{
			foreach ($options as $option)
			{
				if ($option['value'] == $selected)
				{
					$active = ' active btn-info';
				}
				else
				{
					$active = '';
				}

				$arr[] = '<a data-ref="' . $idTag . '" data-value="' . $option['value'] . '" class="xt-button btn' . $class . $active . $onchange . '">' . JText::_($option['name']) . '</a>';
			}
		}

		$arr[] = '</div>';

		return join("\n", $arr);
	}

	/**
	 * Generates an HTML selection list.
	 *
	 * @param   array    $data       An array of objects, arrays, or scalars.
	 * @param   string   $name       The value of the HTML name attribute.
	 * @param   mixed    $attribs    Additional HTML attributes for the <select> tag. This
	 *                               can be an array of attributes, or an array of options. Treated as options
	 *                               if it is the last argument passed. Valid options are:
	 *                               Format options, see {@see JHtml::$formatOptions}.
	 *                               Selection options, see {@see JHtmlSelect::options()}.
	 *                               list.attr, string|array: Additional attributes for the select
	 *                               element.
	 *                               id, string: Value to use as the select element id attribute.
	 *                               Defaults to the same as the name.
	 *                               list.select, string|array: Identifies one or more option elements
	 *                               to be selected, based on the option key values.
	 * @param   string   $optKey     The name of the object variable for the option value. If
	 *                               set to null, the index of the value array is used.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string).
	 * @param   mixed    $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True to translate
	 *
	 * @return  string  HTML for the select list.
	 */
	public static function genericList($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
		// Set default options
		$options = array_merge(
			JHtml::$formatOptions,
			array(
				'format.depth' => 0,
				'id' => false
			)
		);

		if (is_array($attribs) && func_num_args() == 3)
		{
			// Assume we have an options array
			$options = array_merge($options, $attribs);
		}
		else
		{
			// Get options from the parameters
			$options['id'] = $idtag;
			$options['list.attr'] = $attribs;
			$options['list.translate'] = $translate;
			$options['option.key'] = $optKey;
			$options['option.text'] = $optText;
			$options['list.select'] = $selected;
		}

		$attribs = '';

		if (isset($options['list.attr']))
		{
			if (is_array($options['list.attr']))
			{
				$attribs = JArrayHelper::toString($options['list.attr']);
			}
			else
			{
				$attribs = $options['list.attr'];
			}

			if ($attribs != '')
			{
				$attribs = ' ' . $attribs;
			}
		}

		$id = $options['id'] !== false ? $options['id'] : $name;
		$id = str_replace(
				array(
						'[',
						']'
			), '', $id
		);

		$baseIndent = str_repeat($options['format.indent'], $options['format.depth']++);
		$html = $baseIndent .
			'<select' . ($id !== '' ? ' id="' . $id . '"' : '') .
			' name="' . $name . '"' . $attribs . '>' .
			$options['format.eol'] .
			self::options($data, $options) . $baseIndent .
			'</select>' . $options['format.eol'];

		return $html;
	}

	/**
	 * Generates a grouped HTML selection list from nested arrays.
	 *
	 * @param   array   $data     An array of groups, each of which is an array of options.
	 * @param   string  $name     The value of the HTML name attribute
	 * @param   array   $options  Options, an array of key/value pairs. Valid options are:
	 *                            Format options, {@see JHtml::$formatOptions}.
	 *                            Selection options. See {@see JHtmlSelect::options()}.
	 *                            group.id: The property in each group to use as the group id
	 *                            attribute. Defaults to none.
	 *                            group.label: The property in each group to use as the group
	 *                            label. Defaults to "text". If set to null, the data array index key is
	 *                            used.
	 *                            group.items: The property in each group to use as the array of
	 *                            items in the group. Defaults to "items". If set to null, group.id and
	 *                            group. label are forced to null and the data element is assumed to be a
	 *                            list of selections.
	 *                            id: Value to use as the select element id attribute. Defaults to
	 *                            the same as the name.
	 *                            list.attr: Attributes for the select element. Can be a string or
	 *                            an array of key/value pairs. Defaults to none.
	 *                            list.select: either the value of one selected option or an array
	 *                            of selected options. Default: none.
	 *                            list.translate: Boolean. If set, text and labels are translated via
	 *                            JText::_().
	 *
	 * @return  string  HTML for the select list
	 *
	 * @throws  JException If a group has unprocessable contents.
	 */
	public static function groupedList($data, $name, $options = array())
	{
		// Set default options and overwrite with anything passed in
		$options = array_merge(
				JHtml::$formatOptions, array(
						'format.depth' => 0,
						'group.items' => 'items',
						'group.label' => 'text',
						'group.label.toHtml' => true,
						'id' => false
			),
			$options
		);

		// Apply option rules
		if ($options['group.items'] === null)
		{
			$options['group.label'] = null;
		}

		$attribs = '';

		if (isset($options['list.attr']))
		{
			if (is_array($options['list.attr']))
			{
				$attribs = JArrayHelper::toString($options['list.attr']);
			}
			else
			{
				$attribs = $options['list.attr'];
			}

			if ($attribs != '')
			{
				$attribs = ' ' . $attribs;
			}
		}

		$id = $options['id'] !== false ? $options['id'] : $name;
		$id = str_replace(
				array(
						'[',
						']'
				), '', $id
			);

		// Disable groups in the options.
		$options['groups'] = false;

		$baseIndent = str_repeat($options['format.indent'], $options['format.depth']++);
		$html = $baseIndent . '<select' . ($id !== '' ? ' id="' . $id . '"' : '') . ' name="' . $name . '"' . $attribs . '>' . $options['format.eol'];
		$groupIndent = str_repeat($options['format.indent'], $options['format.depth']++);

		foreach ($data as $dataKey => $group)
		{
			$label = $dataKey;
			$id = '';
			$noGroup = is_int($dataKey);

			if ($options['group.items'] == null)
			{
				// Sub-list is an associative array
				$subList = $group;
			}
			elseif (is_array($group))
			{
				// Sub-list is in an element of an array.
				$subList = $group[$options['group.items']];

				if (isset($group[$options['group.label']]))
				{
					$label = $group[$options['group.label']];
					$noGroup = false;
				}

				if (isset($options['group.id']) && isset($group[$options['group.id']]))
				{
					$id = $group[$options['group.id']];
					$noGroup = false;
				}
			}
			elseif (is_object($group))
			{
				// Sub-list is in a property of an object
				$subList = $group->$options['group.items'];

				if (isset($group->$options['group.label']))
				{
					$label = $group->$options['group.label'];
					$noGroup = false;
				}

				if (isset($options['group.id']) && isset($group->$options['group.id']))
				{
					$id = $group->$options['group.id'];
					$noGroup = false;
				}
			}
			else
			{
				throw new JException('Invalid group contents.', 1, E_WARNING);
			}

			if ($noGroup)
			{
				$html .= self::options($subList, $options);
			}
			else
			{
				$html .= $groupIndent . '<optgroup' . (empty($id) ? '' : ' id="' . $id . '"') . ' label="' . ($options['group.label.toHtml'] ? htmlspecialchars($label, ENT_COMPAT, 'UTF-8') : $label) . '">' . $options['format.eol'] . self::options($subList, $options) . $groupIndent . '</optgroup>' . $options['format.eol'];
			}
		}

		$html .= $baseIndent . '</select>' . $options['format.eol'];

		return $html;
	}

	/**
	 * Create and return a new Option Group
	 *
	 * @param   string  $label    Value and label for group [optional]
	 * @param   array   $options  Array of options to insert into group [optional]
	 *
	 * @return  array  Return the new group as an array
	 */
	public static function createOptionGroup($label = '', $options = array())
	{
		$group = array();
		$group['value'] = $label;
		$group['text']  = $label;
		$group['items'] = $options;

		return $group;
	}

	/**
	 * Create and return a new Option
	 *
	 * @param   string  $value  The option value [optional]
	 * @param   string  $text   The option text [optional]
	 *
	 * @return  object  The option as an object (stdClass instance)
	 */
	public static function createOption($value = '', $text = '')
	{
		if (empty($text))
		{
			$text = $value;
		}

		$option = new stdClass;
		$option->value = $value;
		$option->text  = $text;

		return $option;
	}

	/**
	 * Generates a selection list of integers.
	 *
	 * @param   integer  $start     The start integer
	 * @param   integer  $end       The end integer
	 * @param   integer  $inc       The increment
	 * @param   string   $name      The value of the HTML name attribute
	 * @param   mixed    $attribs   Additional HTML attributes for the <select> tag, an array of
	 *                              attributes, or an array of options. Treated as options if it is the last
	 *                              argument passed.
	 * @param   mixed    $selected  The key that is selected
	 * @param   string   $format    The printf format to be applied to the number
	 *
	 * @return  string   HTML for the select list
	 */
	public static function integerList($start, $end, $inc, $name, $attribs = null, $selected = null, $format = '')
	{
		// Set default options
		$options = array_merge(
				JHtml::$formatOptions,
					array(
						'format.depth' => 0,
						'option.format' => '',
						'id' => null
			)
		);

		if (is_array($attribs) && func_num_args() == 5)
		{
			// Assume we have an options array
			$options = array_merge($options, $attribs);

			// Extract the format and remove it from downstream options
			$format = $options['option.format'];
			unset($options['option.format']);
		}
		else
		{
			// Get options from the parameters
			$options['list.attr'] = $attribs;
			$options['list.select'] = $selected;
		}

		$start = intval($start);
		$end = intval($end);
		$inc = intval($inc);

		$data = array();

		for ($i = $start; $i <= $end; $i += $inc)
		{
			$data[$i] = $format ? sprintf($format, $i) : $i;
		}

		// Tell genericlist() to use array keys
		$options['option.key'] = null;

		return JHtml::_('select.genericlist', $data, $name, $options);
	}

	/**
	 * Create an object that represents an option in an option list.
	 *
	 * @param   string   $value    The value of the option
	 * @param   string   $text     The text for the option
	 * @param   mixed    $optKey   If a string, the returned object property name for
	 *                             the value. If an array, options. Valid options are:
	 *                             attr: String|array. Additional attributes for this option.
	 *                             Defaults to none.
	 *                             disable: Boolean. If set, this option is disabled.
	 *                             label: String. The value for the option label.
	 *                             option.attr: The property in each option array to use for
	 *                             additional selection attributes. Defaults to none.
	 *                             option.disable: The property that will hold the disabled state.
	 *                             Defaults to "disable".
	 *                             option.key: The property that will hold the selection value.
	 *                             Defaults to "value".
	 *                             option.label: The property in each option array to use as the
	 *                             selection label attribute. If a "label" option is provided, defaults to
	 *                             "label", if no label is given, defaults to null (none).
	 *                             option.text: The property that will hold the the displayed text.
	 *                             Defaults to "text". If set to null, the option array is assumed to be a
	 *                             list of displayable scalars.
	 * @param   string   $optText  The property that will hold the the displayed text. This
	 *                             parameter is ignored if an options array is passed.
	 * @param   boolean  $disable  Not used.
	 *
	 * @return  object
	 */
	public static function option($value, $text = '', $optKey = 'value', $optText = 'text', $disable = false)
	{
		$options = array(
						'attr' => null,
						'disable' => false,
						'option.attr' => null,
						'option.disable' => 'disable',
						'option.key' => 'value',
						'option.label' => null,
						'option.text' => 'text'
		);

		if (is_array($optKey))
		{
			// Merge in caller's options
			$options = array_merge($options, $optKey);
		}
		else
		{
			// Get options from the parameters
			$options['option.key'] = $optKey;
			$options['option.text'] = $optText;
			$options['disable'] = $disable;
		}

		$obj = new JObject;
		$obj->$options['option.key'] = $value;
		$obj->$options['option.text'] = trim($text) ? $text : $value;

		/*
		 * If a label is provided, save it. If no label is provided and there is a label name, initialise to an empty string.
		 */
		$hasProperty = $options['option.label'] !== null;

		if (isset($options['label']))
		{
			$labelProperty = $hasProperty ? $options['option.label'] : 'label';
			$obj->$labelProperty = $options['label'];
		}
		elseif ($hasProperty)
		{
			$obj->$options['option.label'] = '';
		}

		// Set attributes only if there is a property and a value
		if ($options['attr'] !== null)
		{
			$obj->$options['option.attr'] = $options['attr'];
		}

		// Set disable only if it has a property and a value
		if ($options['disable'] !== null)
		{
			$obj->$options['option.disable'] = $options['disable'];
		}

		return $obj;
	}

	/**
	 * Generates the option tags for an HTML select list (with no select tag
	 * surrounding the options).
	 *
	 * @param   array    $arr        An array of objects, arrays, or values.
	 * @param   mixed    $optKey     If a string, this is the name of the object variable for
	 *                               the option value. If null, the index of the array of objects is used. If
	 *                               an array, this is a set of options, as key/value pairs. Valid options are:
	 *                               -Format options, {@see JHtml::$formatOptions}.
	 *                               -groups: Boolean. If set, looks for keys with the value
	 *                                "&lt;optgroup>" and synthesizes groups from them. Deprecated. Defaults
	 *                                true for backwards compatibility.
	 *                               -list.select: either the value of one selected option or an array
	 *                                of selected options. Default: none.
	 *                               -list.translate: Boolean. If set, text and labels are translated via
	 *                                JText::_(). Default is false.
	 *                               -option.id: The property in each option array to use as the
	 *                                selection id attribute. Defaults to none.
	 *                               -option.key: The property in each option array to use as the
	 *                                selection value. Defaults to "value". If set to null, the index of the
	 *                                option array is used.
	 *                               -option.label: The property in each option array to use as the
	 *                                selection label attribute. Defaults to null (none).
	 *                               -option.text: The property in each option array to use as the
	 *                               displayed text. Defaults to "text". If set to null, the option array is
	 *                               assumed to be a list of displayable scalars.
	 *                               -option.attr: The property in each option array to use for
	 *                                additional selection attributes. Defaults to none.
	 *                               -option.disable: The property that will hold the disabled state.
	 *                                Defaults to "disable".
	 *                               -option.key: The property that will hold the selection value.
	 *                                Defaults to "value".
	 *                               -option.text: The property that will hold the the displayed text.
	 *                               Defaults to "text". If set to null, the option array is assumed to be a
	 *                               list of displayable scalars.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string)
	 * @param   boolean  $translate  Translate the option values.
	 *
	 * @return  string  HTML for the select list
	 */
	public static function options($arr, $optKey = 'value', $optText = 'text', $selected = null, $translate = false)
	{
		$options = array_merge(
				JHtml::$formatOptions, self::$_optionDefaults['option'],
				array(
						'format.depth' => 0,
						'groups' => true,
						'list.select' => null,
						'list.translate' => false
			)
		);

		if (is_array($optKey))
		{
			// Set default options and overwrite with anything passed in
			$options = array_merge($options, $optKey);
		}
		else
		{
			// Get options from the parameters
			$options['option.key'] = $optKey;
			$options['option.text'] = $optText;
			$options['list.select'] = $selected;
			$options['list.translate'] = $translate;
		}

		$html = '';
		$baseIndent = str_repeat($options['format.indent'], $options['format.depth']);

		foreach ($arr as $elementKey => &$element)
		{
			$attr = '';
			$extra = '';
			$label = '';
			$id = '';

			if (is_array($element))
			{
				$key = $options['option.key'] === null ? $elementKey : $element[$options['option.key']];
				$text = $element[$options['option.text']];

				if (isset($element[$options['option.attr']]))
				{
					$attr = $element[$options['option.attr']];
				}

				if (isset($element[$options['option.id']]))
				{
					$id = $element[$options['option.id']];
				}

				if (isset($element[$options['option.label']]))
				{
					$label = $element[$options['option.label']];
				}

				if (isset($element[$options['option.disable']]) && $element[$options['option.disable']])
				{
					$extra .= ' disabled="disabled"';
				}
			}
			elseif (is_object($element))
			{
				$key = $options['option.key'] === null ? $elementKey : $element->$options['option.key'];
				$text = $element->$options['option.text'];

				if (isset($element->$options['option.attr']))
				{
					$attr = $element->$options['option.attr'];
				}

				if (isset($element->$options['option.id']))
				{
					$id = $element->$options['option.id'];
				}

				if (isset($element->$options['option.label']))
				{
					$label = $element->$options['option.label'];
				}

				if (isset($element->$options['option.disable']) && $element->$options['option.disable'])
				{
					$extra .= ' disabled="disabled"';
				}
			}
			else
			{
				// This is a simple associative array
				$key = $elementKey;
				$text = $element;
			}

			/*
			 * The use of options that contain optgroup HTML elements was somewhat hacked for J1.5. J1.6 introduces the grouplist() method to handle this better. The old solution is retained through the "groups" option, which defaults true in J1.6, but should be deprecated at some point in the future.
			 */

			$key = (string) $key;

			if ($options['groups'] && $key == '<OPTGROUP>')
			{
				$html .= $baseIndent . '<optgroup label="' . ($options['list.translate'] ? JText::_($text) : $text) . '">' . $options['format.eol'];
				$baseIndent = str_repeat($options['format.indent'], ++$options['format.depth']);
			}
			elseif ($options['groups'] && $key == '</OPTGROUP>')
			{
				$baseIndent = str_repeat($options['format.indent'], --$options['format.depth']);
				$html .= $baseIndent . '</optgroup>' . $options['format.eol'];
			}
			else
			{
				// If no string after hyphen - take hyphen out
				$splitText = explode(' - ', $text, 2);
				$text = $splitText[0];

				if (isset($splitText[1]))
				{
					$text .= ' - ' . $splitText[1];
				}

				if ($options['list.translate'] && !empty($label))
				{
					$label = JText::_($label);
				}

				if ($options['option.label.toHtml'])
				{
					$label = htmlentities($label);
				}

				if (is_array($attr))
				{
					$attr = JArrayHelper::toString($attr);
				}
				else
				{
					$attr = trim($attr);
				}

				$extra = ($id ? ' id="' . $id . '"' : '') . ($label ? ' label="' . $label . '"' : '') . ($attr ? ' ' . $attr : '') . $extra;

				if (is_array($options['list.select']))
				{
					foreach ($options['list.select'] as $val)
					{
						$key2 = is_object($val) ? $val->$options['option.key'] : $val;

						if ($key == $key2)
						{
							$extra .= ' selected="selected"';
							break;
						}
					}
				}
				elseif ((string) $key == (string) $options['list.select'])
				{
					$extra .= ' selected="selected"';
				}

				if ($options['list.translate'])
				{
					$text = JText::_($text);
				}

				// Generate the option, encoding as required
				$html .= $baseIndent . '<option value="' . ($options['option.key.toHtml'] ? htmlspecialchars($key, ENT_COMPAT, 'UTF-8') : $key) . '"' . $extra . '>';
				$html .= $options['option.text.toHtml'] ? htmlentities(html_entity_decode($text, ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'UTF-8') : $text;
				$html .= '</option>' . $options['format.eol'];
			}
		}

		return $html;
	}

	/**
	 * Generates an HTML radio list.
	 *
	 * @param   array    $data       An array of objects
	 * @param   string   $name       The value of the HTML name attribute
	 * @param   string   $attribs    Additional HTML attributes for the <select> tag
	 * @param   mixed    $optKey     The key that is selected
	 * @param   string   $optText    The name of the object variable for the option value
	 * @param   string   $selected   The name of the object variable for the option text
	 * @param   boolean  $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True if options will be translated
	 *
	 * @return  string HTML for the select list
	 */
	public static function radioList($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
		reset($data);
		$html = '';

		if (is_array($attribs))
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		$id_text = $idtag ? $idtag : $name;

		foreach ($data as $obj)
		{
			$k = $obj->$optKey;
			$t = $translate ? JText::_($obj->$optText) : $obj->$optText;
			$id = (isset($obj->id) ? $obj->id : null);

			$extra = '';
			$extra .= $id ? ' id="' . $obj->id . '"' : '';

			if (is_array($selected))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object($val) ? $val->$optKey : $val;

					if ($k == $k2)
					{
						$extra .= ' selected="selected"';
						break;
					}
				}
			}
			else
			{
				$extra .= ((string) $k == (string) $selected ? ' checked="checked"' : '');
			}

			$html .= "\n\t" . '<label class="radio inline"><input type="radio" name="' . $name . '"' . ' id="' . $id_text . $k . '" value="' . $k . '"' . ' ' . $extra . ' >' . $t . '</label>';
		}

		$html .= "\n";

		return $html;
	}

	/**
	 * Generates an HTML checkbox list.
	 *
	 * @param   array    $data       An array of objects
	 * @param   string   $name       The value of the HTML name attribute
	 * @param   string   $attribs    Additional HTML attributes for the <select> tag
	 * @param   mixed    $optKey     The key that is selected
	 * @param   string   $optText    The name of the object variable for the option value
	 * @param   string   $selected   The name of the object variable for the option text
	 * @param   boolean  $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True if options will be translated
	 *
	 * @return  string HTML for the select list
	 */
	public static function checkboxList($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
		reset($data);
		$html = '';

		if (is_array($attribs))
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		$id_text = $idtag ? $idtag : $name;

		foreach ($data as $obj)
		{
			$k = $obj->$optKey;
			$t = $translate ? JText::_($obj->$optText) : $obj->$optText;
			$id = (isset($obj->id) ? $obj->id : null);

			$extra = '';
			$extra .= $id ? ' id="' . $obj->id . '"' : '';

			if (is_array($selected))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object($val) ? $val->$optKey : $val;

					if ($k == $k2)
					{
						$extra .= ' checked="checked"';
						break;
					}
				}
			}
			else
			{
				$extra .= ((string) $k == (string) $selected ? ' checked="checked"' : '');
			}

			$html .= "\n\t" . '<label class="checkbox inline"><input type="checkbox" name="' . $name . '"' . ' id="' . $id_text . $k . '" value="' . $k . '"' . ' ' . $extra . ' >' . $t . '</label>';
		}

		$html .= "\n";

		return $html;
	}

	/**
	 * radioListControl
	 *
	 * @param   array    $data      An array of objects
	 * @param   string   $name      The value of the HTML name attribute
	 * @param   string   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string   $selected  The name of the object variable for the option text
	 * @param   string   $label     Param
	 * @param   string   $desc      Param
	 * @param   boolean  $idtag     Value of the field id or null by default
	 *
	 * @return  string HTML for the select list
	 */
	public static function radioListControl($data, $name, $attribs, $selected, $label, $desc, $idtag = null)
	{
		if (!$idtag)
		{
			$idtag = EHtml::generateIdTag();
		}

		$control = self::radioList($data, $name, $attribs, 'value', 'text', $selected, $idtag, true);

		return EHtml::genericControl($label, $desc, $name, $control);
	}

	/**
	 * checkboxListControl
	 *
	 * @param   array    $data      An array of objects
	 * @param   string   $name      The value of the HTML name attribute
	 * @param   string   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string   $selected  The name of the object variable for the option text
	 * @param   string   $label     Param
	 * @param   string   $desc      Param
	 * @param   boolean  $idtag     Value of the field id or null by default
	 *
	 * @return  string HTML for the select list
	 */
	public static function checkboxListControl($data, $name, $attribs, $selected, $label, $desc, $idtag = null)
	{
		if (!$idtag)
		{
			$idtag = EHtml::generateIdTag();
		}

		$control = self::checkboxList($data, $name, $attribs, 'value', 'text', $selected, $idtag, true);

		return EHtml::genericControl($label, $desc, $name, $control);
	}

	/**
	 * yesNoControl.
	 *
	 * @param   string  $selected  Value
	 * @param   string  $name      The name for the field
	 * @param   string  $label     Label
	 * @param   string  $desc      Description
	 * @param   string  $idTag     Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML
	 */
	public static function yesNoControl($selected, $name, $label, $desc, $idTag = null)
	{
		if (!$idTag)
		{
			$idTag = EHtml::generateIdTag();
		}

		$control = self::yesNo($selected, $name, array(), $idTag);

		return EHtml::genericControl($label, $desc, $name, $control);
	}

	/**
	 * booleanListControl.
	 *
	 * @param   string  $selected  Value
	 * @param   string  $name      The name for the field
	 * @param   string  $label     Label
	 * @param   string  $desc      Description
	 * @param   string  $yes       Param
	 * @param   string  $no        Param
	 * @param   array   $idTag     Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function booleanListControl($selected, $name, $label, $desc, $yes = 'JYES', $no = 'JNO', $idTag = null)
	{
		if (!$idTag)
		{
			$idTag = EHtml::generateIdTag();
		}

		$control = self::booleanList($selected, $name, array(), $yes, $no, $idTag);

		return EHtml::genericControl($label, $desc, $name, $control);
	}

	/**
	 * btnGroupListControl.
	 *
	 * @param   string  $selected  Value
	 * @param   string  $name      The name for the field
	 * @param   string  $label     Label
	 * @param   string  $desc      Description
	 * @param   array   $options   Param
	 * @param   array   $idTag     Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function btnGroupListControl($selected, $name, $label, $desc, $options, $idTag = null)
	{
		if (!$idTag)
		{
			$idTag = EHtml::generateIdTag();
		}

		$control = self::btnGroupList($selected, $name, array(), $options, $idTag);

		return EHtml::genericControl($label, $desc, $name, $control);
	}

	/**
	 * published.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $yes       Param
	 * @param   string  $no        Param
	 * @param   string  $idTag     Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML
	 */
	public static function published($selected = null, $name = 'enabled', $attribs = array(), $yes = 'JPUBLISHED', $no = 'JUNPUBLISHED', $idTag = null)
	{
		$platform = F0FPlatform::getInstance();
		$input = new F0FInput;
		$editstate = $platform->authorise('core.edit.state', $input->getCmd('option', 'com_foobar'));

		if ($editstate)
		{
			if ($selected === null)
			{
				$selected = 1;
			}

			return self::booleanList($selected, $name, $attribs, $yes, $no, $idTag);
		}
		else
		{
			if ($selected)
			{
				$value = 1;
				$tag = JText::_($yes);
			}
			else
			{
				$value = 0;
				$tag = JText::_($no);
			}

			$control = EHtml::readonlyText($tag, $name . '-readonly');
			$control .= '<input type="hidden" value="' . $value . '" name="' . $name . '" id="' . $idTag . '">';

			return $control;
		}
	}

	/**
	 * publishedControl.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $idTag     Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML
	 */
	public static function publishedControl($selected = null, $name = 'enabled', $attribs = array(), $idTag = null)
	{
		if (!$idTag)
		{
			$idTag = EHtml::generateIdTag();
		}

		$control = self::published($selected, $name, $attribs, 'JPUBLISHED', 'JUNPUBLISHED', $idTag);

		return EHtml::genericControl(
				JText::_('JSTATUS'),
				JText::_('JFIELD_PUBLISHED_DESC'),
				$name,
				$control
		);
	}

	/**
	 * customGenericListControl
	 *
	 * @param   array   $list      The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 * @param   string  $selected  The key that is selected
	 * @param   string  $label     Label
	 * @param   string  $desc      Description
	 * @param   string  $idTag     The name for the field
	 *
	 * @return  string  HTML
	 */
	public static function customGenericListControl($list, $name, $attribs, $selected, $label, $desc, $idTag)
	{
		$control = self::customGenericList($list, $name, $attribs, $selected, $idTag);

		return EHtml::genericControl($label, $desc, $name, $control);
	}

	/**
	 * customGenericList.
	 *
	 * @param   array   $options   The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 * @param   string  $selected  The key that is selected
	 * @param   string  $idTag     The name for the field
	 *
	 * @return  string  HTML
	 */
	public static function customGenericList($options, $name, $attribs, $selected, $idTag)
	{
		if (empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		return self::genericlist($options, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	/**
	 * customGroupedList.
	 *
	 * @param   array   $options   The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 * @param   string  $selected  The key that is selected
	 * @param   string  $idTag     The name for the field
	 *
	 * @return  string  HTML
	 */
	public static function customGroupedList($options, $name, $attribs, $selected, $idTag)
	{
		if (empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		return self::_groupedList($options, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	/**
	 * Generates an HTML selection list.
	 *
	 * @param   array    $data       An array of objects, arrays, or scalars.
	 * @param   string   $name       The value of the HTML name attribute.
	 * @param   mixed    $attribs    Additional HTML attributes for the <select> tag. This
	 *                               can be an array of attributes, or an array of options. Treated as options
	 *                               if it is the last argument passed. Valid options are:
	 *                               Format options, see {@see JHtml::$formatOptions}.
	 *                               Selection options, see {@see JHtmlSelect::options()}.
	 *                               list.attr, string|array: Additional attributes for the select
	 *                               element.
	 *                               id, string: Value to use as the select element id attribute.
	 *                               Defaults to the same as the name.
	 *                               list.select, string|array: Identifies one or more option elements
	 *                               to be selected, based on the option key values.
	 * @param   string   $optKey     The name of the object variable for the option value. If
	 *                               set to null, the index of the value array is used.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string).
	 * @param   mixed    $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True to translate
	 *
	 * @return  string  HTML for the select list.
	 */
	private static function _groupedList($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
		// Set default options and overwrite with anything passed in
		$options = array_merge(
			JHtml::$formatOptions,
			array(
				'format.depth' => 0,
				'id' => false,

				'group.items' => 'items',
				'group.label' => 'text',
				'group.label.toHtml' => true
			)
		);

		if (is_array($attribs) && func_num_args() == 3)
		{
			// Assume we have an options array
			$options = array_merge($options, $attribs);
		}
		else
		{
			// Get options from the parameters
			$options['id'] = $idtag;
			$options['list.attr'] = $attribs;
			$options['list.translate'] = $translate;
			$options['option.key'] = $optKey;
			$options['option.text'] = $optText;
			$options['list.select'] = $selected;
		}

		// Apply option rules
		if ($options['group.items'] === null)
		{
			$options['group.label'] = null;
		}

		$attribs = '';

		if (isset($options['list.attr']))
		{
			if (is_array($options['list.attr']))
			{
				$attribs = JArrayHelper::toString($options['list.attr']);
			}
			else
			{
				$attribs = $options['list.attr'];
			}

			if ($attribs != '')
			{
				$attribs = ' ' . $attribs;
			}
		}

		$id = $options['id'] !== false ? $options['id'] : $name;
		$id = str_replace(
				array(
								'[',
								']'
				), '', $id
		);

		// Disable groups in the options.
		$options['groups'] = false;

		$baseIndent = str_repeat($options['format.indent'], $options['format.depth']++);
		$html = $baseIndent .
			'<select' . ($id !== '' ? ' id="' . $id . '"' : '') . ' name="' .
			$name . '"' .
			$attribs . '>' .
			$options['format.eol'];
		$groupIndent = str_repeat($options['format.indent'], $options['format.depth']++);

		foreach ($data as $dataKey => $group)
		{
			$label = $dataKey;
			$id = '';
			$noGroup = is_int($dataKey);

			if ($options['group.items'] == null)
			{
				// Sub-list is an associative array
				$subList = $group;
			}
			elseif (is_array($group))
			{
				// Sub-list is in an element of an array.
				$subList = $group[$options['group.items']];

				if (isset($group[$options['group.label']]))
				{
					$label = $group[$options['group.label']];
					$noGroup = false;
				}

				if (isset($options['group.id']) && isset($group[$options['group.id']]))
				{
					$id = $group[$options['group.id']];
					$noGroup = false;
				}
			}
			elseif (is_object($group))
			{
				// Sub-list is in a property of an object
				$subList = $group->$options['group.items'];

				if (isset($group->$options['group.label']))
				{
					$label = $group->$options['group.label'];
					$noGroup = false;
				}

				if (isset($options['group.id']) && isset($group->$options['group.id']))
				{
					$id = $group->$options['group.id'];
					$noGroup = false;
				}
			}
			else
			{
				throw new JException('Invalid group contents.', 1, E_WARNING);
			}

			if ($noGroup)
			{
				$html .= self::options($subList, $options);
			}
			else
			{
				$html .= $groupIndent . '<optgroup' . (empty($id) ? '' : ' id="' . $id . '"') . ' label="' . ($options['group.label.toHtml'] ? htmlspecialchars($label, ENT_COMPAT, 'UTF-8') : $label) . '">' . $options['format.eol'] . self::options($subList, $options) . $groupIndent . '</optgroup>' . $options['format.eol'];
			}
		}

		$html .= $baseIndent .
			'</select>' . $options['format.eol'];

		return $html;
	}

	/**
	 * selectTypename.
	 *
	 * @param   string  $model     Model
	 * @param   string  $prefix    Prefix
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $idTag     The idTag for the field
	 *
	 * @return  string  HTML
	 */
	public static function selectTypename($model, $prefix, $selected = null, $name = 'type', $attribs = array(), $idTag = null)
	{
		$typenames = self::_loadTypename($model, $prefix);

		$options = array();
		self::addDefaultSelectOption($attribs, $options);

		if (count($typenames))
		{
			foreach ($typenames as $key => $value)
			{
				$options[] = JHTML::_('select.option', $key, $value);
			}
		}

		return self::customGenericList($options, $name, $attribs, $selected, $idTag);
	}

	/**
	 * selectTypename.
	 *
	 * @param   string  $model     Model
	 * @param   string  $prefix    Prefix
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $idTag     The idTag for the field
	 *
	 * @return  string  HTML
	 */
	public static function btnGroupTypename($model, $prefix, $selected = null, $name = 'type', $attribs = array(), $idTag = null)
	{
		$typenames = self::_loadTypename($model, $prefix);

		$options = array();

		if (count($typenames))
		{
			foreach ($typenames as $key => $value)
			{
				$options[] = array(
								'name' => $value,
								'value' => $key
				);
			}
		}

		return self::btnGroupList($selected, $name, $attribs, $options, $idTag);
	}

	/**
	 * getTypeName.
	 *
	 * @param   string  $model     Model
	 * @param   string  $prefix    Prefix
	 * @param   string  $selected  Param
	 *
	 * @return  string
	 */
	public static function getTypename($model, $prefix, $selected)
	{
		$typenames = self::_loadTypename($model, $prefix);

		if (array_key_exists($selected, $typenames))
		{
			return $typenames[$selected];
		}
		else
		{
			return '?';
		}
	}

	/**
	 * _loadTypename.
	 *
	 * @param   string  $model   Model
	 * @param   string  $prefix  Prefix
	 *
	 * @return  string
	 */
	public static function _loadTypename($model, $prefix)
	{
		static $typenames = array();

		$modelkey = $prefix . $model;

		if (array_key_exists($modelkey, $typenames))
		{
			return $typenames[$modelkey];
		}
		else
		{
			$model = F0FModel::getTmpInstance($model, $prefix);
			$items = $model->getItemList(true);

			if (count($items))
			{
				$t = array();

				foreach ($items as $item)
				{
					$t[$item->id] = JText::_($item->name);
				}

				$typenames[$modelkey] = $t;

				return $typenames[$modelkey];
			}
		}

		return null;
	}

	/**
	 * isOptional.
	 *
	 * @param   array  $attribs  Attribs
	 *
	 * @return  bool
	 */
	public static function isOptional($attribs)
	{
		$required = ((array_key_exists('required', $attribs)) && ($attribs['required'] = 'required'));

		return (!$required);
	}

	/**
	 * addDefaultSelectOption.
	 *
	 * @param   array  $attribs   Attribs
	 * @param   array  &$options  Params
	 *
	 * @return  void
	 */
	public static function addDefaultSelectOption($attribs, &$options)
	{
		if (self::isOptional($attribs))
		{
			$options[] = JHTML::_('select.option', null, '- ' . JText::_('JSELECT') . ' -');
		}
	}

	/**
	 * imagePickerControl
	 *
	 * @param   string  $label            Param
	 * @param   string  $desc             Param
	 * @param   array   $options          The key that is selected
	 * @param   string  $name             The name for the field
	 * @param   array   $attribs          Additional HTML attributes for the <select> tag*
	 * @param   string  $selected         The key that is selected
	 * @param   string  $idTag            The name for the field
	 * @param   string  $extensionmainjs  Param
	 *
	 * @return  string  HTML for the select list.
	 */
	public static function imagePickerListControl($label, $desc, $options, $name, $attribs, $selected, $idTag, $extensionmainjs = null)
	{
		static $initialized = false;

		if (!$initialized)
		{
			$initialized = true;
			JHtml::stylesheet('lib_extly/image-picker.css', false, true);

			if ($extensionmainjs)
			{
				$dependencies = array();

				if (EXTLY_J25)
				{
					$dependencies['image-picker'] = array(DependencyManager::EXTLY_J25_JQUERY);
				}

				$file = 'media/lib_extly/js/utils/image-picker.min';
				$paths = array('image-picker' => $file);
				Extly::addAppDependency($extensionmainjs, $dependencies, $paths);
			}
			else
			{
				JHtml::script('lib_extly/utils/image-picker.min.js', false, true);
			}
		}

		if (!$idTag)
		{
			$idTag = self::generateIdTag();
		}

		Extly::addPostRequireScript(
		"jQuery('#{$idTag}').imagepicker({
show_label: true,
selected: function() {
	this.trigger('liszt:updated');
}
});"
				);

		$base_attrs = array();
		$base_attrs['id'] = $idTag;
		$base_attrs['option.attr'] = 'data-img-src';
		$base_attrs['list.translate'] = false;
		$base_attrs['option.key'] = 'value';
		$base_attrs['option.text'] = 'text';
		$base_attrs['list.select'] = $selected;

		$attribs = array_merge($base_attrs, $attribs);

		$control = self::genericlist($options, $name, $attribs);

		return EHtml::genericControl($label, $desc, $name, $control);
	}

	/**
	 * minuteList.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $id        The id for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function minuteList($selected = null, $id = 'minute2', $attribs = array())
	{
		$options = array();
		$options[] = JHTML::_('select.option', '*', 'Every 1\'');
		$options[] = JHTML::_('select.option', '0,5,10,15,20,25,30,35,40,45,50,55', 'Every 5\'');
		$options[] = JHTML::_('select.option', '0,10,20,30,40,50', 'Every 10\'');
		$options[] = JHTML::_('select.option', '0,15,30,45', 'Every 15\'');
		$options[] = JHTML::_('select.option', '0', '0');
		$options[] = JHTML::_('select.option', '1', '1');
		$options[] = JHTML::_('select.option', '2', '2');
		$options[] = JHTML::_('select.option', '3', '3');
		$options[] = JHTML::_('select.option', '4', '4');
		$options[] = JHTML::_('select.option', '5', '5');
		$options[] = JHTML::_('select.option', '6', '6');
		$options[] = JHTML::_('select.option', '7', '7');
		$options[] = JHTML::_('select.option', '8', '8');
		$options[] = JHTML::_('select.option', '9', '9');
		$options[] = JHTML::_('select.option', '10', '10');
		$options[] = JHTML::_('select.option', '11', '11');
		$options[] = JHTML::_('select.option', '12', '12');
		$options[] = JHTML::_('select.option', '13', '13');
		$options[] = JHTML::_('select.option', '14', '14');
		$options[] = JHTML::_('select.option', '15', '15');
		$options[] = JHTML::_('select.option', '16', '16');
		$options[] = JHTML::_('select.option', '17', '17');
		$options[] = JHTML::_('select.option', '18', '18');
		$options[] = JHTML::_('select.option', '19', '19');
		$options[] = JHTML::_('select.option', '20', '20');
		$options[] = JHTML::_('select.option', '21', '21');
		$options[] = JHTML::_('select.option', '22', '22');
		$options[] = JHTML::_('select.option', '23', '23');
		$options[] = JHTML::_('select.option', '24', '24');
		$options[] = JHTML::_('select.option', '25', '25');
		$options[] = JHTML::_('select.option', '26', '26');
		$options[] = JHTML::_('select.option', '27', '27');
		$options[] = JHTML::_('select.option', '28', '28');
		$options[] = JHTML::_('select.option', '29', '29');
		$options[] = JHTML::_('select.option', '30', '30');
		$options[] = JHTML::_('select.option', '31', '31');
		$options[] = JHTML::_('select.option', '32', '32');
		$options[] = JHTML::_('select.option', '33', '33');
		$options[] = JHTML::_('select.option', '34', '34');
		$options[] = JHTML::_('select.option', '35', '35');
		$options[] = JHTML::_('select.option', '36', '36');
		$options[] = JHTML::_('select.option', '37', '37');
		$options[] = JHTML::_('select.option', '38', '38');
		$options[] = JHTML::_('select.option', '39', '39');
		$options[] = JHTML::_('select.option', '40', '40');
		$options[] = JHTML::_('select.option', '41', '41');
		$options[] = JHTML::_('select.option', '42', '42');
		$options[] = JHTML::_('select.option', '43', '43');
		$options[] = JHTML::_('select.option', '44', '44');
		$options[] = JHTML::_('select.option', '45', '45');
		$options[] = JHTML::_('select.option', '46', '46');
		$options[] = JHTML::_('select.option', '47', '47');
		$options[] = JHTML::_('select.option', '48', '48');
		$options[] = JHTML::_('select.option', '49', '49');
		$options[] = JHTML::_('select.option', '50', '50');
		$options[] = JHTML::_('select.option', '51', '51');
		$options[] = JHTML::_('select.option', '52', '52');
		$options[] = JHTML::_('select.option', '53', '53');
		$options[] = JHTML::_('select.option', '54', '54');
		$options[] = JHTML::_('select.option', '55', '55');
		$options[] = JHTML::_('select.option', '56', '56');
		$options[] = JHTML::_('select.option', '57', '57');
		$options[] = JHTML::_('select.option', '58', '58');
		$options[] = JHTML::_('select.option', '59', '59');

		return self::genericlist($options, $id, $attribs, 'value', 'text', $selected, $id);
	}

	/**
	 * hourList.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $id        The id for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function hourList($selected = null, $id = 'hour2', $attribs = array())
	{
		$options = array();
		$options[] = JHTML::_('select.option', '*', 'Every 1h');
		$options[] = JHTML::_('select.option', '0,4,8,12,16,20', 'Every 4h');
		$options[] = JHTML::_('select.option', '0,6,12,18', 'Every 6h');
		$options[] = JHTML::_('select.option', '0', 'Midnight');
		$options[] = JHTML::_('select.option', '1', '1 AM');
		$options[] = JHTML::_('select.option', '2', '2 AM');
		$options[] = JHTML::_('select.option', '3', '3 AM');
		$options[] = JHTML::_('select.option', '4', '4 AM');
		$options[] = JHTML::_('select.option', '5', '5 AM');
		$options[] = JHTML::_('select.option', '6', '6 AM');
		$options[] = JHTML::_('select.option', '7', '7 AM');
		$options[] = JHTML::_('select.option', '8', '8 AM');
		$options[] = JHTML::_('select.option', '9', '9 AM');
		$options[] = JHTML::_('select.option', '10', '10 AM');
		$options[] = JHTML::_('select.option', '11', '11 AM');
		$options[] = JHTML::_('select.option', '12', '12 PM/Noon');
		$options[] = JHTML::_('select.option', '13', '1 PM');
		$options[] = JHTML::_('select.option', '14', '2 PM');
		$options[] = JHTML::_('select.option', '15', '3 PM');
		$options[] = JHTML::_('select.option', '16', '4 PM');
		$options[] = JHTML::_('select.option', '17', '5 PM');
		$options[] = JHTML::_('select.option', '18', '6 PM');
		$options[] = JHTML::_('select.option', '19', '7 PM');
		$options[] = JHTML::_('select.option', '20', '8 PM');
		$options[] = JHTML::_('select.option', '21', '9 PM');
		$options[] = JHTML::_('select.option', '22', '10 PM');
		$options[] = JHTML::_('select.option', '23', '11 PM');

		return self::genericlist($options, $id, $attribs, 'value', 'text', $selected, $id);
	}

	/**
	 * dayList.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $id        The id for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function dayList($selected = null, $id = 'day2', $attribs = array())
	{
		$options = array();
		$options[] = JHTML::_('select.option', '*', 'Every Day');
		$options[] = JHTML::_('select.option', '1', '1');
		$options[] = JHTML::_('select.option', '2', '2');
		$options[] = JHTML::_('select.option', '3', '3');
		$options[] = JHTML::_('select.option', '4', '4');
		$options[] = JHTML::_('select.option', '5', '5');
		$options[] = JHTML::_('select.option', '6', '6');
		$options[] = JHTML::_('select.option', '7', '7');
		$options[] = JHTML::_('select.option', '8', '8');
		$options[] = JHTML::_('select.option', '9', '9');
		$options[] = JHTML::_('select.option', '10', '10');
		$options[] = JHTML::_('select.option', '11', '11');
		$options[] = JHTML::_('select.option', '12', '12');
		$options[] = JHTML::_('select.option', '13', '13');
		$options[] = JHTML::_('select.option', '14', '14');
		$options[] = JHTML::_('select.option', '15', '15');
		$options[] = JHTML::_('select.option', '16', '16');
		$options[] = JHTML::_('select.option', '17', '17');
		$options[] = JHTML::_('select.option', '18', '18');
		$options[] = JHTML::_('select.option', '19', '19');
		$options[] = JHTML::_('select.option', '20', '20');
		$options[] = JHTML::_('select.option', '21', '21');
		$options[] = JHTML::_('select.option', '22', '22');
		$options[] = JHTML::_('select.option', '23', '23');
		$options[] = JHTML::_('select.option', '24', '24');
		$options[] = JHTML::_('select.option', '25', '25');
		$options[] = JHTML::_('select.option', '26', '26');
		$options[] = JHTML::_('select.option', '27', '27');
		$options[] = JHTML::_('select.option', '28', '28');
		$options[] = JHTML::_('select.option', '29', '29');
		$options[] = JHTML::_('select.option', '30', '30');
		$options[] = JHTML::_('select.option', '31', '31');

		return self::genericlist($options, $id, $attribs, 'value', 'text', $selected, $id);
	}

	/**
	 * month2.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $id        The id for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function monthList($selected = null, $id = 'month2', $attribs = array())
	{
		$options = array();
		$options[] = JHTML::_('select.option', '*', 'Every Month');
		$options[] = JHTML::_('select.option', '1', JText::_('JANUARY'));
		$options[] = JHTML::_('select.option', '2', JText::_('FEBRUARY'));
		$options[] = JHTML::_('select.option', '3', JText::_('MARCH'));
		$options[] = JHTML::_('select.option', '4', JText::_('APRIL'));
		$options[] = JHTML::_('select.option', '5', JText::_('MAY'));
		$options[] = JHTML::_('select.option', '6', JText::_('JUNE'));
		$options[] = JHTML::_('select.option', '7', JText::_('JULY'));
		$options[] = JHTML::_('select.option', '8', JText::_('AUGUST'));
		$options[] = JHTML::_('select.option', '9', JText::_('SEPTEMBER'));
		$options[] = JHTML::_('select.option', '10', JText::_('OCTOBER'));
		$options[] = JHTML::_('select.option', '11', JText::_('NOVEMBER'));
		$options[] = JHTML::_('select.option', '12', JText::_('DECEMBER'));

		return self::genericlist($options, $id, $attribs, 'value', 'text', $selected, $id);
	}

	/**
	 * weekday2.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $id        The id for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function weekdayList($selected = null, $id = 'weekday2', $attribs = array())
	{
		$options = array();
		$options[] = JHTML::_('select.option', '*', 'Every Weekday');
		$options[] = JHTML::_('select.option', '0', JText::_('SUNDAY'));
		$options[] = JHTML::_('select.option', '1', JText::_('MONDAY'));
		$options[] = JHTML::_('select.option', '2', JText::_('TUESDAY'));
		$options[] = JHTML::_('select.option', '3', JText::_('WEDNESDAY'));
		$options[] = JHTML::_('select.option', '4', JText::_('THURSDAY'));
		$options[] = JHTML::_('select.option', '5', JText::_('FRIDAY'));
		$options[] = JHTML::_('select.option', '6', JText::_('SATURDAY'));

		return self::genericlist($options, $id, $attribs, 'value', 'text', $selected, $id);
	}

	/**
	 * userSelect
	 *
	 * @param   string  $selected  Value
	 * @param   string  $name      The name for the field
	 * @param   array   $idTag     Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML
	 */
	public static function userSelect($selected, $name, $idTag = null)
	{
		if (!$idTag)
		{
			$idTag = EHtml::generateIdTag();
		}

		$username = '';
		$userid = null;

		$user = F0FPlatform::getInstance()->getUser($selected);

		if ($user)
		{
			$username = $user->name;
			$userid = $user->id;
		}

		$bogusNameTag = EHtml::generateIdTag();

		$control = '<input type="hidden" name="' . $name . '" id="' . $idTag . '" value="' . $userid . '" />';
		$control .= EHtml::readonlyText($username, $bogusNameTag, $idTag . '_username');

		$isAdmin = JFactory::getApplication()->isAdmin();

		if ($isAdmin)
		{
			$control .= ' <a class="btn modal" id="' . $idTag . '_userselect" href="index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' .
				$idTag . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><i class="xticon xticon-search"></i></a>';
		}

		return $control;
	}

	/**
	 * userGroupListControl
	 *
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 * @param   string  $selected  The key that is selected
	 * @param   string  $label     Label
	 * @param   string  $desc      Description
	 * @param   string  $idTag     The name for the field
	 *
	 * @return  string  HTML
	 */
	public static function userGroupListControl($name, $attribs, $selected, $label, $desc, $idTag = null)
	{
		$control = self::userGroupList($name, $attribs, $selected, $idTag);

		return EHtml::genericControl($label, $desc, $name, $control);
	}

	/**
	 * customGenericList.
	 *
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 * @param   string  $selected  The key that is selected
	 * @param   string  $idTag     The name for the field
	 *
	 * @return  string  HTML
	 */
	public static function userGroupList($name, $attribs, $selected, $idTag = null)
	{
		if (!$idTag)
		{
			$idTag = EHtml::generateIdTag();
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id, a.title');
		$query->from('#__usergroups AS a');
		$query->group('a.id, a.title');
		$query->order('a.id ASC');
		$query->order($query->qn('title') . ' ASC');

		// Get the options.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		if (count($items))
		{
			foreach ($items as $item)
			{
				$options[] = JHTML::_('select.option', $item->id, $item->title);
			}
		}

		return self::customGenericList($options, $name, $attribs, $selected, $idTag);
	}

	/**
	 * menuitemlist.
	 *
	 * @param   string  $selected       The key that is selected
	 * @param   string  $name           The name for the field
	 * @param   array   $attribs        Additional HTML attributes for the <select> tag
	 * @param   array   $config         Config
	 * @param   string  $idTag          Param
	 * @param   string  $jselect_label  String
	 *
	 * @return  string  HTML
	 */
	public static function menuitemlist($selected = null, $name = 'fieldlist', $attribs = array(), $config = null, $idTag = 'fieldlist', $jselect_label = 'JSELECT')
	{
		$custom = '';

		if (!array_key_exists('size', $attribs))
		{
			$attribs['size'] = 7;
		}

		$ngModel = null;
		$ngChange = null;

		if (array_key_exists('ng-model', $attribs))
		{
			$ngModel = ' ng-model="' . $attribs['ng-model'] . '" ';
			unset($attribs['ng-model']);
		}

		if (array_key_exists('ng-change', $attribs))
		{
			$ngChange = ' ng-change="' . $attribs['ng-change'] . '" ';
			unset($attribs['ng-change']);
		}

		foreach ($attribs as $key => $value)
		{
			$custom .= ' ' . $key . '="' . $value . '" ';
		}

		if (EXTLY_J3)
		{
			$menuitem = JFormHelper::loadFieldType('Menuitem', false);
			$element = new SimpleXMLElement('<field name="' . $name . '" type="menuitem" default="" ' . $custom . '><option value="">' . JText::_($jselect_label) . '</option></field>');
			$menuitem->setup($element, $selected);

			$select = $menuitem->input;

			if ($ngModel)
			{
				$select = str_replace('<select', '<select' . $ngModel, $select);
			}

			if ($ngChange)
			{
				$select = str_replace('<select', '<select' . $ngChange, $select);
			}

			return $select;
		}
		else
		{
			$control = "<input type=\"text\" name=\"{$name}\" id=\"{$idTag}\" value=\"{$selected}\"{$custom}/>";

			if ($ngModel)
			{
				$control = str_replace('<input', '<input' . $ngModel, $control);
			}

			if ($ngChange)
			{
				$control = str_replace('<input', '<input' . $ngChange, $control);
			}

			return $control;
		}
	}
}
