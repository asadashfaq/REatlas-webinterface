<?php

class DbQuery
{
	/**
	 * @var array list of data to build the query
	 */
	protected $query = array(
		'select' => array(),
		'from' => 	'',
		'join' => 	array(),
		'where' => 	array(),
		'group' => 	array(),
		'having' => array(),
		'order' => 	array(),
		'limit' => 	array('offset' => 0, 'limit' => 0),
	);

	/**
	 * Add fields in query selection
	 *
	 * @param string $fields List of fields to concat to other fields
	 * @return DbQuery
	 */
	public function select($fields)
	{
		if (!empty($fields))
			$this->query['select'][] = $fields;
		return $this;
	}

	/**
	 * Set table for FROM clause
	 *
	 * @param string $table Table name
	 * @return DbQuery
	 */
	public function from($table, $alias = null)
	{
		if (!empty($table))
			$this->query['from'] = '`'._DB_PREFIX_.$table.'`'.($alias ? ' '.$alias : '');
		return $this;
	}

	/**
	 * Add JOIN clause
	 * 	E.g. $this->join('RIGHT JOIN '._DB_PREFIX_.'product p ON ...');
	 *
	 * @param string $join Complete string
	 * @return DbQuery
	 */
	public function join($join)
	{
		if (!empty($join))
			$this->query['join'][] = $join;

		return $this;
	}

	/**
	 * Add LEFT JOIN clause
	 *
	 * @param string $table Table name (without prefix)
	 * @param string $alias Table alias
	 * @param string $on ON clause
	 */
	public function leftJoin($table, $alias = null, $on = null)
	{
		return $this->join('LEFT JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' `'.pSQL($alias).'`' : '').($on ? ' ON '.$on : ''));
	}

	/**
	 * Add INNER JOIN clause
	 * 	E.g. $this->innerJoin('product p ON ...')
	 *
	 * @param string $table Table name (without prefix)
	 * @param string $alias Table alias
	 * @param string $on ON clause
	 */
	public function innerJoin($table, $alias = null, $on = null)
	{
		return $this->join('INNER JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' '.pSQL($alias) : '').($on ? ' ON '.$on : ''));
	}

	/**
	 * Add LEFT OUTER JOIN clause
	 *
	 * @param string $table Table name (without prefix)
	 * @param string $alias Table alias
	 * @param string $on ON clause
	 */
	public function leftOuterJoin($table, $alias = null, $on = null)
	{
		return $this->join('LEFT OUTER JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' '.pSQL($alias) : '').($on ? ' ON '.$on : ''));
	}

	/**
	 * Add NATURAL JOIN clause
	 *
	 * @param string $table Table name (without prefix)
	 * @param string $alias Table alias
	 */
	public function naturalJoin($table, $alias = null)
	{
		return $this->join('NATURAL JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' '.pSQL($alias) : ''));
	}

	/**
	 * Add a restriction in WHERE clause (each restriction will be separated by AND statement)
	 *
	 * @param string $restriction
	 * @return DbQuery
	 */
	public function where($restriction)
	{
		if (!empty($restriction))
			$this->query['where'][] = $restriction;

		return $this;
	}

	/**
	 * Add a restriction in HAVING clause (each restriction will be separated by AND statement)
	 *
	 * @param string $restriction
	 * @return DbQuery
	 */
	public function having($restriction)
	{
		if (!empty($restriction))
			$this->query['having'][] = $restriction;

		return $this;
	}

	/**
	 * Add an ORDER B restriction
	 *
	 * @param string $fields List of fields to sort. E.g. $this->order('myField, b.mySecondField DESC')
	 * @return DbQuery
	 */
	public function orderBy($fields)
	{
		if (!empty($fields))
			$this->query['order'][] = $fields;

		return $this;
	}

	/**
	 * Add a GROUP BY restriction
	 *
	 * @param string $fields List of fields to sort. E.g. $this->group('myField, b.mySecondField DESC')
	 * @return DbQuery
	 */
	public function groupBy($fields)
	{
		if (!empty($fields))
			$this->query['group'][] = $fields;

		return $this;
	}

	/**
	 * Limit results in query
	 *
	 * @param string $fields List of fields to sort. E.g. $this->order('myField, b.mySecondField DESC')
	 * @return DbQuery
	 */
	public function limit($limit, $offset = 0)
	{
		$offset = (int)$offset;
		if ($offset < 0)
			$offset = 0;

		$this->query['limit'] = array(
			'offset' => $offset,
			'limit' =>	(int)$limit,
		);
		return $this;
	}

	/**
	 * Generate and get the query
	 *
	 * @return string
	 */
	public function build()
	{
		$sql = 'SELECT '.((($this->query['select'])) ? implode(",\n", $this->query['select']) : '*')."\n";

		if (!$this->query['from'])
			die('DbQuery->build() missing from clause');
		$sql .= 'FROM '.$this->query['from']."\n";

		if ($this->query['join'])
			$sql .= implode("\n", $this->query['join'])."\n";

		if ($this->query['where'])
			$sql .= 'WHERE ('.implode(') AND (', $this->query['where']).")\n";

		if ($this->query['group'])
			$sql .= 'GROUP BY '.implode(', ', $this->query['group'])."\n";

		if ($this->query['having'])
			$sql .= 'HAVING ('.implode(') AND (', $this->query['having']).")\n";

		if ($this->query['order'])
			$sql .= 'ORDER BY '.implode(', ', $this->query['order'])."\n";

		if ($this->query['limit']['limit'])
		{
			$limit = $this->query['limit'];
			$sql .= 'LIMIT '.(($limit['offset']) ? $limit['offset'].', '.$limit['limit'] : $limit['limit']);
		}

		return $sql;
	}

	public function __toString()
	{
		return $this->build();
	}
}

