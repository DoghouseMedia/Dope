<?php

namespace Dope\Doctrine\ORM;

use Doctrine\ORM\Mapping\UniqueConstraint,
    Dope\Entity\Search,
    Dope\Entity\Definition,
    Dope\Controller\Data,
    Dope\Config\Helper as Config;

class EntityRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * @var \Dope\Entity\Search\Table\Aliases
	 */
	protected $tableAliases;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	protected $select;
	
	/**
	 * @var \Dope\Entity\Search
	 */
	protected $search;
	
	/**
	 * @var bool
	 */
	protected $usePagination = false;
	
	/**
	 * @var \Dope\Entity\Definition
	 */
	protected $definition = null;
	
	/**
	 * @return \Dope\Entity\Definition
	 */
	public function getDefinition()
	{
	    if (! $this->definition instanceof Definition) {
	        $this->definition = new Definition($this->getClassName());
	    }
	
	    return $this->definition;
	}
	
	/**
	 * @return \Dope\Entity
	 */
	public function newInstance()
	{
		$className = $this->getClassName();
		return new $className();
	}
	
	public function hasSubClasses()
	{
		return (bool) $this->getSubClasses();
	}
	
	public function getSubClasses()
	{
		$subClasses = array_values($this->getClassMetadata()->discriminatorMap);
		
		array_splice(
			$subClasses,
			array_search($this->getClassName(), $subClasses),
			1
		);
		
		for($i=0; $i < count($subClasses); $i++) {
			$subParentClasses = \Dope\Doctrine::getEntityManager()
				->getClassMetadata($subClasses[$i])->parentClasses;
			
			if (! in_array($this->getClassName(), $subParentClasses)) {
				array_splice($subClasses, $i, 1);
				$i--;
			}
		}
		
		\Dope\Log::console($subClasses);
		
		return $subClasses;
	}
	
	public function getSubClassTables()
	{
		return array_map(function($subClass) {
			return \Dope\Doctrine::getRepository($subClass);
		}, $this->getSubClasses());
	}
	
	public function hasColumn($columnName)
	{
		return in_array($columnName, $this->getColumnNames());
	}
	
	public function getModelAlias($className=null)
	{
		$className = $className ?: $this->getClassMetadata()->rootEntityName;
		
		return strtolower(str_replace(
			$this->getClassMetadata()->namespace . '\\',
			'',
			$className
		));
	}
	
	public function getModelKey()
	{
		return $this->getModelAlias($this->getClassName());
	}
	
	public function getAssociationMappings()
	{
		return $this->getClassMetadata()->getAssociationMappings();
	}

	public function getTableAliases()
	{
		if (! $this->tableAliases instanceof Search\Table\Aliases) {
			$this->tableAliases = new Search\Table\Aliases();
		}
	
		return $this->tableAliases;
	}
	
	public function isColumnUnique($columnName) {
		foreach($this->getIndexes() as $indexName => $indexData) {
			$isTypeUnique = isset($indexData['type']) && ($indexData['type'] == 'unique');
			$isColInIndex = isset($indexData['fields']) && in_array($columnName, array_keys($indexData['fields']));
			
			if ($isTypeUnique AND $isColInIndex) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getSelect()
	{
		return $this->select;
	}
	
	/**
	 * @return \Dope\Entity\Search
	 */
	public function getSearch()
	{
		return $this->search;
	}
	
	public function usePagination($usePagination=null)
	{
		if (is_bool($usePagination)) {
			$this->usePagination = $usePagination;
		}
		
		return $this->usePagination;
	}
	
	/**
	 * 
	 */
	public function useFileTypeField()
	{
		return true;
	}

	/**
	 * Get column weight factor (for search)
	 * 
	 * This should obviously be overriden when models need to specify weighting for fields
	 * 
	 * @param string $columnName
	 * @param string $focusPresetName
	 * @return int column weight factor
	 */
	public function getColumnWeightFactor($columnName, $focusPresetName=false)
    {
    	return 1;
    }
    
    /**
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    protected function getClassMetadata()
    {
    	return parent::getClassMetadata();
    }
    
    public function getColumnNames(array $fieldNames = null)
    {
    	return array_merge(
    		$this->getClassMetadata()->getColumnNames($fieldNames),
    		$this->getClassMetadata()->getAssociationNames()
    	);
    }

    public function getIrrelevantColumnNames()
    {
    	return array(
    		'editable',
    		'deleted'
    	);
    }
	
    /**
     * Returns default sort (eg. "field_name DESC") or false
     */
    public function getDefaultSort()
    {
    	return false;
    }
    
	/**
	 * Retrieve form/input filter
	 * 
	 * @todo Somehow it doesn't feel right having this here...
	 * 
	 * @return \Dope\Form\Entity
	 */
	public function getForm(array $options=array(), $prefix = null, $alias=null, $default='\Dope\Form\Entity', $depth=0)
	{	
		if (is_null($prefix)) {
			$prefix = '\\' . Config::getOption('appnamespace') . '\Form\Entity';
		}

		$alias = $alias ?: $this->getModelAlias($this->getClassName());
		
		$form = null;
		$inflector = new \Zend_Filter_Word_UnderscoreToCamelCase();
		$formclass = $prefix . '\\' . $inflector->filter($alias);

		/*
		 * Yuck, we need to turn off error reporting since 
		 * trying to autoload will spit out Warnings!
		 * 
		 * @todo There are very few models that don't have a form,
		 * so creating a default form might be a better solution.
		 */
		$errorReporting = error_reporting(0);
		if (\Zend_Loader_Autoloader::autoload($formclass)) {
			$form = new $formclass();
		}
		error_reporting($errorReporting);
		
		if (! $form instanceof \Dope\Form\_Base) {
			if (isset($this->getClassMetadata()->parentClasses[$depth])) {
				$alias = $this->getModelAlias($this->getClassMetadata()->parentClasses[$depth]);
				return $this->getForm($options, $prefix, $alias, $default, $depth+1);
			}
			
			$form = new $default();
		}
		
		$form->setOptions($options);
		
		return $form;
	}
	
	/**
	 * Get count of search but no results, just the count
	 * 
	 * @param \Dope\Controller\Data $data
	 * @return integer count
	 */
	public function searchCount(Data $data)
	{
		return $this->search($data, \Dope\Entity\Search::SEARCH_COUNT_ONLY);
	}
	
	/**
	 * Search
	 * 
	 * @return array
	 */
	public function search(Data $data, $mode=\Dope\Entity\Search::SEARCH_WITH_PAGINATION)
	{
		$hasQuery = (bool) $data->query;
		$useDefaultSort = ! (bool) $hasQuery;

		/*
		 * This should be refactored somehow and not set this directly here.
		 * Either do it in constructor, or have a setSearch() method.
		 * 
		 * @todo Refactor
		 */
		$this->search = new Search($this, $data);
		
		$this->select = $this->getSearch()->filter(false, $useDefaultSort);

		\Dope\Log::console('SELECT after filter');
		\Dope\Log::console($this->select->getDQL());
		
		/* Apply terms */
		if ($hasQuery) {
			/*
			 * This (the next 300 lines) is our search implementation.
			 * If we keep this, we should move it to our own
			 * Searchable class and set Doctrine to use it.
			 */
			
			$modelIndexTable = 'index_' . $this->getClassMetadata()->getTableName();
			
			$modelScoresById = array();
			$modelIdsByScore = array();
			$modelIds = array();
			
			$this->getSearch()->getProfiler()->punch('search query pre terms loop');

			$columnsNames = method_exists($this, 'getSearchColumnNames') ?
				$this->getSearchColumnNames() :
				$this->getColumnNames();
					
			$_patternBunnies = '/\+?"([^"]+)"/';
			
			/* Parse terms */
			
			$termString = str_replace("'", '', $data->query); // remove '
			
			preg_match_all($_patternBunnies, $termString, $matches);				
			
			$termsBunnies = $matches[1];
			$termsRequired = array();
			$termsExcluded = array();
			
			$terms = \Dope\Entity\Indexer\Analyzer::analyze($termString, null, false, false, true, true);

			for ($i=0; $i < count($terms); $i++) {
				$terms[$i] = str_replace ( '*', '%', $terms[$i] );
				
				if ($terms[$i][0] == '+' AND $terms[$i][1] != '"') {
					$termsRequired[] = substr($terms[$i], 1);
				}
				elseif ($terms[$i][0] == '-' AND $terms[$i][1] != '"') {
					$termsExcluded[] = substr($terms[$i], 1);
				}
				
				$terms[$i] = preg_replace('/[^\w%]/mis', '', $terms[$i]);
			}
			
			foreach($termsBunnies as $_termBunny) {
				$termsRequired = array_merge($termsRequired,
					\Dope\Entity\Indexer\Analyzer::analyze($_termBunny, null, true, false)
				);
			}
			
			$terms = array_unique($terms);
			$termsRequired = array_unique($termsRequired);

			sort($terms);
			sort($termsRequired);
			sort($termsExcluded);
			
			$numTerms = count($terms);
		
			$this->debug($terms, 'TERMS All');
			$this->debug($termsRequired, 'TERMS Required');
			$this->debug($termsBunnies, 'TERMS Bunnies');
			$this->debug($termsExcluded, 'TERMS Excluded');
			
			$_SQL_keyword_like_array = array();
			$_SQL_keyword_exact_array = array();
			
			foreach($terms as $term) {				
				if (strpos($term, '%') !== false) {
					$_SQL_keyword_like_array[] = "keyword LIKE '$term'";
				}
				else {
					$_SQL_keyword_exact_array[] = $term;
				}
			}
			
			$_SQL_keyword = "keyword IN ('" . join("','", $_SQL_keyword_exact_array) . "')";
			if (count($_SQL_keyword_like_array)) {
				$_SQL_keyword .= ' OR (' . join(' OR ', $_SQL_keyword_like_array) . ')';
			}
			
			$_SQL = "SELECT "; 
			$_SQL.=	"id, ";
			$_SQL.=	"COUNT(DISTINCT keyword) AS keyword_count, ";
			$_SQL.=	"GROUP_CONCAT(DISTINCT keyword ORDER BY keyword SEPARATOR ' ') AS keywords, ";
			$_SQL.=	"GROUP_CONCAT(CONCAT(keyword,'-',field,'-',position) ORDER BY field, position SEPARATOR '|') AS extra ";
			$_SQL.=	"FROM $modelIndexTable ";
			$_SQL.=	"WHERE ($_SQL_keyword) "; 
			$_SQL.=	"GROUP BY id ";
			
			$_hasHaving = false;
			if (count($termsRequired)) {
				$_SQL.=	"HAVING keywords LIKE '%" . join('%', $termsRequired) . "%' ";
				$_hasHaving = true;
			}
			if (count($termsExcluded)) {
				$_excludeSql = array();
				foreach($termsExcluded as $termExcluded) {
					$_excludeSql[] = "keywords NOT LIKE '%" . $termExcluded . "%' ";
				}
				
				$_SQL.= $_hasHaving ? 'AND' : 'HAVING';
				$_SQL.= '(' . join(' AND ', $_excludeSql) . ')';
			}
			
			$_SQL.=	"ORDER BY keyword_count DESC ";

			//$this->debug($_SQL, "SELECT ids from $modelIndexTable");
			
			/*
			 * We need to increase "group_concat_max_len"
			 * to at least 10x 1024. It's default setting (1024)
			 * is way to low for us.
			 */
			$this->getEntityManager()->getConnection()->executeQuery('
			    SET SESSION group_concat_max_len = 10240;
			');
			
			$indexRows = $this->getEntityManager()->getConnection()->executeQuery($_SQL);
			//$indexRows = $this->getEntityManager()->createNativeQuery($_SQL);
			//$indexRows->setFetchMode(\PDO::FETCH_ASSOC);
			//print_r($indexRows);
			//die;
			
			foreach($indexRows as $indexRow) {
				$modelScoresById[$indexRow['id']] = 0;
				$extraParts = explode('|', $indexRow['extra']);
				$matchedBunnies = array();
				
				$_byField = array();
				$_adjacencies = array();
				
				foreach($extraParts as $extraPart) {
					$_parts = explode('-', $extraPart);
					
					if (count($_parts) !== 3) continue;
					
					list($keyword, $field, $position) = $_parts;
					
					if (!isset($_byField[$field])) $_byField[$field] = array();
					
					$_byField[$field][$position] = $keyword;
				}

				foreach($_byField as $field => $_data) {
				    $modelScoresById[$indexRow['id']] += min(10, count($_data)) * $this->getColumnWeightFactor($field, $data->query_focus);
				    
				    foreach($termsBunnies as $termsBunny) {
						$_terms = \Dope\Entity\Indexer\Analyzer::analyze($termsBunny, null, true, false);
						if (!isset($_terms[0])) continue;
						
						$positions = array_keys($_data, $_terms[0]);

						foreach($positions as $position) {
							for($i=1, $_termsCount = count($_terms); $i < $_termsCount; $i++) {
								if (!isset($_data[$position+$i])) continue 2; // try next pos
								if ($_data[$position+$i] != $_terms[$i]) continue 2; // try next pos
								
								if ($i + 1 == $_termsCount) {
									$matchedBunnies[] = $termsBunny;
								}
							}
						}
					}
				}
				
				$modelScoresById[$indexRow['id']] *= ($indexRow['keyword_count'] / max($numTerms, 1));
				$modelScoresById[$indexRow['id']] *= max(array(count($matchedBunnies) / max($numTerms, 1), 1));
				
				$uniqueMatchedBunnies = array_unique($matchedBunnies);
				$uniqueTermsBunnies = array_unique($termsBunnies);
				
				if (count($uniqueMatchedBunnies) != count($uniqueTermsBunnies)) {
					unset($modelScoresById[$indexRow['id']]);
					
					$this->debug($indexRow['id'], "Bunny discard");
					$this->debug($matchedBunnies, "Bunny matches");
					$this->debug($termsBunnies, "Bunny terms");
				}
			}
			
			arsort($modelScoresById, SORT_NUMERIC);
			
			if (count($modelScoresById)) {
				$minScore = current($modelScoresById) / 100 * 5;
				
				$numValidResults = 0;
				foreach($modelScoresById as $id => $score) {
					if ($numValidResults >= 100 AND $score < $minScore) {
						unset($modelScoresById[$id]);
					}
					$numValidResults++;
				}
			}
			
			//$this->debug($modelScoresById, 'Model scores by id');
			
			$modelIds = array_keys($modelScoresById);
			
			if (count($modelIds)) {
				//$this->getSearch()->getProfiler()->punch('search query pre result search');
				//$this->debug($modelIds, 'Model IDs');
				
				/* Get params */
				$params = $this->select->getParameters();
				
				/* Define temporary table name */
				$tmpTableName = uniqid('temp_search_' . time() . '_' . rand(111,999), true);
				
				//$this->getSearch()->getProfiler()->punch('search query pre result search create tmp table pre');
				
				/* Create temporary table */
				$this->getEntityManager()->getConnection()->executeQuery("
					CREATE TEMPORARY TABLE `$tmpTableName` (
						`score` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						`id` INT UNSIGNED NOT NULL DEFAULT 0
					)
				");
				
				//$this->getSearch()->getProfiler()->punch('search query pre result search create tmp table post');
				
				/* Insert into temporary table */
				$this->getEntityManager()->getConnection()->executeQuery("
					INSERT INTO `$tmpTableName` (id) VALUES(" . join('),(', $modelIds) . ")
				");
				
				//$this->getSearch()->getProfiler()->punch('search query pre result search insert tmp table');
				
				/*
				 * Ensure there's a WHERE clause
				 * 
				 * Due to our dodgy preg_replace() calls below, we need to make sure
				 * there's a WHERE clause in our query, otherwise, the replace will fail.
				 * 
				 * @todo Clean all this up
				 */
				$this->select->andWhere('1=1');
				
				/* Build & Run search query */
				$SQL_DATA = $this->select
					->getQuery()
					->getSQL();
				
				//$this->getSearch()->getProfiler()->punch('search query pre result search insert tmp table 2');
				
				preg_match('/^SELECT .*? FROM [^\s]*? (\w+)/mis', $SQL_DATA, $matches);
				$modelTableAlias = $matches[1];
				
				if ($data->select) {
					$_selects = array();
					foreach(explode(',', $data->select) as $column) {
						$_selects[] = $modelTableAlias . '.' . $column;
					}
					$SQL_DATA = preg_replace('/^SELECT .*? FROM ([^\s]*?) (\w+)/mis', 'SELECT DISTINCT ' . join(',', $_selects) . ' FROM \\1 \\2',$SQL_DATA);
				}
				else {
					$SQL_DATA = preg_replace('/^SELECT .*? FROM ([^\s]*?) (\w+)/mis', 'SELECT DISTINCT `\\2`.* FROM \\1 \\2',$SQL_DATA);
				}
				
				$SQL_DATA = preg_replace('/ WHERE /', " RIGHT JOIN `$tmpTableName` `tmps` ON `tmps`.`id` = `$modelTableAlias`.`id` WHERE (`tmps`.`id` IS NOT NULL) AND ", $SQL_DATA, 1);
				
				if (! $this->getSearch()->getSort()->hasUserSort()) {
					if (strpos($SQL_DATA, 'ORDER BY')) {
						$SQL_DATA = preg_replace('/ ORDER BY /', ' ORDER BY `tmps`.`score`,', $SQL_DATA);
					} else {
						$SQL_DATA .= ' ORDER BY `tmps`.`score` ';
					}
				}
				
				$SQL_IDS = $SQL_DATA; // copy data to id query before setting limit
				
				$SQL_IDS = preg_replace('/^SELECT .*? FROM ([^\s]*?) (\w+)/mis', 'SELECT DISTINCT `\\2`.id FROM \\1 \\2',$SQL_IDS);
				
				if ($data->list_count) {
					$SQL_DATA .= ' LIMIT ' . $data->list_start . ',' . $data->list_count;
				}
				
				//$this->getSearch()->getProfiler()->punch('search query pre result search insert tmp table 3');
				//$this->debug($SQL_DATA, 'SELECT data after query');
				//$this->debug($SQL_IDS, 'SELECT ids after query');
				//$this->debug($params['where'], 'PARAMS WHERE');
				//$this->getSearch()->getProfiler()->punch('search query pre result search pre execution');

				$paramsWhere = isset($params['where']) ? $params['where'] : '';
				
				if ($mode != Search::SEARCH_COUNT_ONLY) { // make sure we are NOT in 'count only' mode
					$RES_DATA = \Dope\Doctrine::getEntityManager()
						->getConnection()
						->executeQuery($SQL_DATA, array($paramsWhere));

					$RES_IDS = \Dope\Doctrine::getEntityManager()
						->getConnection()
						->executeQuery($SQL_IDS, array($paramsWhere));
				}
				
				//$this->getSearch()->getProfiler()->punch('search query pre result search post execution');
	
				if ($mode == Search::SEARCH_COUNT_ONLY) {
					/* Build & Run search count query */
					$this->select->select('COUNT(id)');
					$this->select->getQuery()->setMaxResults(false)->setFirstResult(false); // remove limit/offset to get total count
					
					//$this->getSearch()->getProfiler()->punch('search query pre result search post execution 2');
					
					$SQL_COUNT = $this->select->getDQL();
					$SQL_COUNT = str_replace(' WHERE ', " RIGHT JOIN `$tmpTableName` `tmps` ON `tmps`.`id` = `$modelTableAlias`.`id` WHERE ",$SQL_COUNT);
					
					//$this->getSearch()->getProfiler()->punch('search query pre result search post execution 3');
					//$this->debug($SQL_COUNT,'SELECT count after query');
					//$this->getSearch()->getProfiler()->punch('search query pre result search pre execution count');
					
					$RES_COUNT = \Dope\Doctrine::getEntityManager()
						->getConnection()
						->executeQuery($SQL_COUNT, array($paramsWhere));
				}
				
				//$this->getSearch()->getProfiler()->punch('search query pre result search post execution count');
				
				/* Drop temporary table */
				\Dope\Doctrine::getEntityManager()->getConnection()->executeQuery("
					DROP TABLE `$tmpTableName`
				");
				
				//$this->getSearch()->getProfiler()->punch('search query pre result search post tmp drop');
				
				if ($mode == Search::SEARCH_COUNT_ONLY) {
					$FINAL_COUNT = $RES_COUNT->fetchColumn();
				}
				else {
					$FINAL_DATA_ARRAY = $RES_DATA->fetchAll(\Doctrine\ORM\Query::HYDRATE_ARRAY);
					
					//$this->getSearch()->getProfiler()->punch('search query post fetch all data');
					
					$FINAL_ALL_IDS = $this->getArrayFromColumn(0,
						$RES_IDS->fetchAll(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR)
					);
					
					$FINAL_COUNT = count($FINAL_ALL_IDS);
					
					//$this->getSearch()->getProfiler()->punch('search query post fetch all ids');
					
					foreach($FINAL_DATA_ARRAY as $i => $row) {
						$FINAL_DATA_ARRAY[$i]['score'] = $modelScoresById[$row['id']];
					}
				}
			}
			else {
				$FINAL_DATA_ARRAY = array();
				$FINAL_COUNT = 0;
				$FINAL_ALL_IDS = array();
			}
		}
		else { // $data->query is -not- a string
			//$this->getSearch()->getProfiler()->punch('search pre return');
			
			$this->select = $this->getSearch()->limit($this->select, $data->list_count, $data->list_start);
			
			\Dope\Log::console("PRE RETURN SQL");
			\Dope\Log::console($this->select->getDQL());
			//$this->debug($this->select->getSqlQuery(), "PRE RETURN SQL");
			
			switch($mode) {
				case Search::SEARCH_COUNT_ONLY:
					$FINAL_COUNT = count($this->select
						->select($this->getSearch()->getTableAlias() . '.id')
						->getQuery()
						->setMaxResults(null)
						->setFirstResult(null)
						->getArrayResult()
					);
					break;

				default:
				case Search::SEARCH_NORMAL:
					$FINAL_DATA_ARRAY = $this->select->getQuery()
						->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
						->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
					$FINAL_COUNT = count($FINAL_DATA_ARRAY);
					$FINAL_ALL_IDS=array();
					break;
					
				case Search::SEARCH_WITH_PAGINATION:
					$FINAL_DATA_ARRAY = $this->select
					    ->getQuery()
						->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
						->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
					$FINAL_ALL_IDS = $this->getArrayFromColumn('id', $this->select
						->select($this->getSearch()->getTableAlias() . '.id')
						->getQuery()
						->setMaxResults(null)
						->setFirstResult(null)
						->getArrayResult()
					);
					$FINAL_COUNT = count($FINAL_ALL_IDS);
					break;
			}
		}
		
		if ($mode == Search::SEARCH_COUNT_ONLY) {
			return $FINAL_COUNT; 
		}
		else {
			//$this->getSearch()->getProfiler()->punch('search pre populate relations');

			/* Populate relations */
			$FINAL_DATA_ARRAY = $this->populateRelations($FINAL_DATA_ARRAY);
			
			/* Populate toString field */
			$FINAL_DATA_ARRAY = $this->populateToString($FINAL_DATA_ARRAY);
			
			//$this->getSearch()->getProfiler()->punch('search pre return 2');
			//$this->debug($FINAL_DATA_ARRAY, "FINAL DATA ARRAY");
			
			return array(
				$FINAL_DATA_ARRAY,
				$FINAL_COUNT,
				$FINAL_ALL_IDS
			);
		}
	}
	
	/**
	 * This code is stolen/duplicated from Dope\Entity::__toString().
	 * @todo Clean up
	 * 
	 * @param array $collection
	 * @return array $collection
	 */
	protected function populateToString(array $collection)
	{
		if (!count($collection)) return $collection;
		
		$definition = new \Dope\Entity\Definition($this->getClassName());

		if (count($definition->getToStringColumnNames())) {
			foreach ($collection as &$record) {
				$values = array();
				foreach ($definition->getToStringColumnNames() as $columnName) {
					if (isset($record[$columnName])) {
						$values[] = $record[$columnName];
					}
				}
				$record['__toString'] = (string) join(' ', $values);
			}
		}
		else {
			foreach ($collection as &$record) {
				$record['__toString'] = ucfirst($this->getModelKey()) . ' ' . $record['id'];
			}
		}
		
		return $collection;
	}
	
	protected function populateRelations(array $collection)
	{
		if (!count($collection)) return $collection;
		
		$md = $this->getClassMetadata();
		foreach ($collection as &$record) {
			foreach($md->getAssociationMappings() as $alias => $mapping) {
// 				if (! isset($record[$alias])) {
// 					continue;
// 				}
				
				\Dope\Log::console($alias); //. ': ' . $record[$alias]);
				\Dope\Log::console($mapping);
				
// 				if (isset($mapping['joinColumns'][0]['name']) AND isset($record[$mapping['joinColumns'][0]['name']])) {
// 					$record[$alias] = (string) \Dope\Doctrine::getRepository($mapping['targetEntity'])
// 						->find($record[$mapping['joinColumns'][0]['name']]);
// 				} else {
// 					$record[$alias] = '';
// 				}
						
				switch ($mapping['type']) {
					case \Doctrine\ORM\Mapping\ClassMetadata::ONE_TO_ONE:
					case \Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_ONE:
 						if (isset($mapping['joinColumns'][0]['name']) AND 
 						    isset($record[$mapping['joinColumns'][0]['name']])
 						) {
							$record[$alias] = (string) \Dope\Doctrine::getRepository($mapping['targetEntity'])
								->find($record[$mapping['joinColumns'][0]['name']]);
						} else {
							$record[$alias] = '';
						}
						break;
				
					case \Doctrine\ORM\Mapping\ClassMetadata::ONE_TO_MANY:
					//case \Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_MANY:
					    $record[$alias] = array();
					    
				        $_entities = \Dope\Doctrine::getRepository($mapping['targetEntity'])->findBy(array(
				            $mapping['mappedBy'] => $record['id']
				        ));
				        
				        foreach ($_entities as $_entity) {
				            $record[$alias][] = $_entity->id;
				        }
					    break;
				}
			}
		}
// 		foreach(array_keys($collection[0]) as $key) {
// 			foreach ($collection as &$record) {
// 				try {
// 					/*
// 					 * Relations
// 					 * 
// 					 * Find key that match 'fk_*_id' and load the string representation of related record
// 					 */
// 					if (preg_match('/^fk_(\w*?)_id$/', $key, $matches)) {
// 						$relatedId = $record[$key];
// 						$targetEntity = $this->getForeignKeyRelationEntity($key);
// 						$record[$matches[1]] = (string) \Dope\Doctrine::getRepository($targetEntity)
// 							->find($relatedId);
// 					}
					
// 					if ($key[0] === strtoupper($key[0]) AND isset($record[$key][0]) AND is_array($record[$key][0])) {
// 						$relatedId = $record[$key][0]['id'];
// 						$relatedTableName = strtolower($key);
// 						$targetEntity = 'Snowwhite\\Entity\\' . $key; // @todo Remove hard-coded class prefix
						
// 						$record[$relatedTableName] = \Dope\Doctrine::getRepository($targetEntity)
// 							->find($relatedId);
// 					}
// 				}
// 				catch(\Exception $e) {
// 					// ignore
// 				}
// 			}
// 		}
				
		return $collection;
	}
	
	public function getForeignKeyRelationEntity($key)
	{
		foreach($this->getAssociationMappings() as $alias => $mapping) {
			if (isset($mapping['joinColumns']) AND $mapping['joinColumns'][0]['name'] == $key) {
				return $mapping['targetEntity'];
			}
		}
		
		return false;
	}
	
	protected function debug($object, $title=null)
	{
	    /*
	     * @todo Encapsulate this in \Dope\Env
	     */
	    
// 		$showDebug = (
// 			\APPLICATION_ENV != '' AND
// 			!in_array(\APPLICATION_ENV, array('staging','development','production'))
// 		);
		
// 		if ($showDebug) {
// 			if (! $this->logger instanceof \Zend_Log) {
// 				$this->logger = new \Zend_Log();
//         		$this->logger->addWriter(new \Zend_Log_Writer_Firebug());
// 			}
			
// 			if ($title) {
// 				$this->logger->log('----- ' . $title . ' -----', \Zend_Log::INFO);
// 			}
			
// 			$this->logger->log(
// 				is_string($object) ? addslashes($object) : $object,
// 				\Zend_Log::INFO
// 			);
			
// 			return true;
// 		}
		
// 		return false;
	}
	
	protected function getArrayFromColumn($columnName, array $rows)
	{
		$result = array();
		
		foreach($rows as $row) {
			$result[] = $row[$columnName];
		}
		
		return $result;
	}
}
