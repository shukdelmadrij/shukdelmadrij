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

/**
 * EDbProxyHelper
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class EDbProxyHelper
{
	private $bulk_tables;

	private $bulk_tables_pk_field;

	private $bulk_inserts;

	private $bulk_updates;

	private $bulk_updates_pks;

	private $bulk_updates_pks_flushed;

	const BUFFER_LIMIT = 255;

	/**
	 * getInstance
	 *
	 * @return  object
	 */
	public static function getInstance()
	{
		$instance = null;

		if (!$instance)
		{
			$instance = new EDbProxyHelper;
			$instance->initialize();
		}

		return $instance;
	}

	/**
	 * initialize
	 *
	 * @return	void
	 */
	private function initialize()
	{
		$this->bulk_tables = array();
		$this->bulk_tables_pk_field = array();
		$this->bulk_inserts = array();
		$this->bulk_updates = array();
		$this->bulk_updates_pks = array();
		$this->bulk_updates_pks_flushed = array();
	}

	/**
	 * addInsert
	 *
	 * @param   string  $table   Param
	 * @param   array   $values  Param
	 *
	 * @return	void
	 */
	public function addInsert($table, $values)
	{
		$this->bulk_tables[$table] = $table;
		$insertSet = '(' . implode(',', $values) . ')';
		$this->bulk_inserts[$table][] = $insertSet;

		if (count($this->bulk_inserts[$table]) > self::BUFFER_LIMIT)
		{
			$this->flushInserts();
		}
	}

	/**
	 * flushInserts
	 *
	 * @return	void
	 */
	public function flushInserts()
	{
		$db = JFactory::getDbo();

		foreach ($this->bulk_tables as $table)
		{
			// No inserts
			if (!array_key_exists($table, $this->bulk_inserts))
			{
				continue;
			}

			$tableq = $db->qn($table);

			$insertSet = $this->bulk_inserts[$table];
			$values = implode(',', $insertSet);
			$query = 'INSERT INTO ' . $tableq . ' VALUES ' . $values . ';';

			$db->setQuery($query);
			$db->execute();

			if ($error = $db->getErrorMsg())
			{
				throw new Exception($error);
			}
		}

		$this->bulk_inserts = array();
	}

	/**
	 * addUpdate
	 *
	 * @param   string  $table     Param
	 * @param   string  $pk_field  Param
	 * @param   string  $pk_id     Param
	 * @param   array   $values    Param
	 *
	 * @return	void
	 */
	public function addUpdate($table, $pk_field, $pk_id, $values)
	{
		$this->bulk_tables[$table] = $table;
		$this->bulk_tables_pk_field[$table] = $pk_field;

		if ((!array_key_exists($table, $this->bulk_updates_pks)) || (!array_key_exists($pk_id, $this->bulk_updates_pks[$table])))
		{
			$this->bulk_updates_pks[$table][$pk_id] = $pk_id;

			foreach ($values as $field => $value)
			{
				$this->bulk_updates[$table][$field][$pk_id] = $value;
			}

			if (count($this->bulk_updates[$table][$field]) > self::BUFFER_LIMIT)
			{
				$this->flushUpdates();
			}
		}
	}

	/**
	 * flushUpdates
	 *
	 * @return	void
	 */
	public function flushUpdates()
	{
		$db = JFactory::getDbo();

		foreach ($this->bulk_tables as $table)
		{
			// No updates
			if (!array_key_exists($table, $this->bulk_updates))
			{
				continue;
			}

			$tableq = $db->qn($table);
			$pk_field = $this->bulk_tables_pk_field[$table];

			$fieldSet = $this->bulk_updates[$table];
			$pkSet = $this->bulk_updates_pks[$table];
			$pkSet = array_diff($pkSet, $this->bulk_updates_pks_flushed);

			$this->bulk_updates_pks_flushed = array_merge($this->bulk_updates_pks_flushed, $pkSet);

			$assignment = $this->_buildAssignmentExpression($db, $pk_field, $fieldSet);
			$condition = $this->_buildConditionExpression($db, $pk_field, $pkSet);
			$query = 'UPDATE ' . $tableq . ' SET ' . $assignment . ' WHERE ' . $condition;

			$db->setQuery($query);
			$db->execute();

			if ($error = $db->getErrorMsg())
			{
				throw new Exception($error);
			}
		}

		$this->bulk_updates = array();
	}

	/**
	 * _buildAssignmentExpression
	 *
	 * @param   object  &$db        Param
	 * @param   string  $pk_field   Param
	 * @param   array   &$fieldSet  Param
	 *
	 * @return	string
	 */
	private function _buildAssignmentExpression(&$db, $pk_field, &$fieldSet)
	{
		$expression = array();
		$pk_field = $db->qn($pk_field);

		foreach ($fieldSet as $field => $values)
		{
			$assign = array();
			$field_q = $db->qn($field);

			foreach ($values as $pk => $v)
			{
				$assign[] = 'WHEN ' . $pk . ' THEN ' . $v;
			}

			$assign_expr = implode(' ', $assign);
			$expression[] = $field_q . ' = CASE ' . $pk_field . ' ' . $assign_expr . ' END';
		}

		$final_expr = implode(' ', $expression);

		return $final_expr;
	}

	/**
	 * _buildConditionExpression
	 *
	 * @param   object  &$db       Param
	 * @param   string  $pk_field  Param
	 * @param   array   &$pkSet    Param
	 *
	 * @return	string
	 */
	private function _buildConditionExpression(&$db, $pk_field, &$pkSet)
	{
		$pk_field = $db->qn($pk_field);
		$condition = $pk_field . ' IN (' . implode(',', $pkSet) . ')';

		return $condition;
	}
}
