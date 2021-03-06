<?php

namespace Dope\Entity\Search\Type;

use Dope\Entity\Search,
	Dope\Doctrine,
	Doctrine\ORM,
	Dope\Entity\Indexer\Analyzer;

class Query extends _Base
{
	public function preExecute()
	{
		$this->getSearch()->useLimit(false);
	}
	
	public function postExecute()
	{
		/*
		 * This (the next 300 lines) is our search implementation.
		 * If we keep this, we should move it to our own
		 * Searchable class and set Doctrine to use it.
		 */
		
		$modelIndexTable = 'index_' . $this->getSearch()->getEntityRepository()->getClassMetadata()->getTableName();
			
		$modelScoresById = array();
		$modelIdsByScore = array();
		$modelIds = array();
			
		/* Profile */
		$this->getDebug()->punch(__CLASS__, __LINE__);
		
		$columnsNames = method_exists($this->getSearch()->getEntityRepository(), 'getSearchColumnNames')
			? $this->getSearch()->getEntityRepository()->getSearchColumnNames()
			: $this->getSearch()->getEntityRepository()->getColumnNames();
			
		$_patternBunnies = '/\+?"([^"]+)"/';
			
		/* Parse terms */
			
		$termString = str_replace("'", '', $this->getSearch()->getData()->query); // remove '
			
		preg_match_all($_patternBunnies, $termString, $matches);
			
		$termsBunnies = $matches[1];
		$termsRequired = array();
		$termsExcluded = array();
			
		$terms = Analyzer::analyze($termString, null, false, false, true, true);
		
		for ($i=0; $i < count($terms); $i++) {
		    $terms[$i] = str_replace('*', '%', $terms[$i]);
		
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
		        Analyzer::analyze($_termBunny, null, true, false)
		    );
		}
			
		$terms = array_unique($terms);
		$termsRequired = array_unique($termsRequired);
		
		sort($terms);
		sort($termsRequired);
		sort($termsExcluded);
			
		$numTerms = count($terms);
		
		/* Debug */
		$this->getDebug()->log('TERMS All', $terms);
		$this->getDebug()->log('TERMS Required', $termsRequired);
		$this->getDebug()->log('TERMS Bunnies', $termsBunnies);
		$this->getDebug()->log('TERMS Excluded', $termsExcluded);
			
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
		
		/* Debug */
		$this->getDebug()->log("SELECT ids from $modelIndexTable", $_SQL);
			
		/*
		 * We need to increase "group_concat_max_len"
		 * to at least 10x 1024. It's default setting (1024)
		 * is way to low for us.
		 */
		$this->getSearch()->getEntityRepository()->getEntityManager()->getConnection()->executeQuery(
		    'SET SESSION group_concat_max_len = 10240;'
		);
		
		/* Profile */
	    $this->getDebug()->punch(__CLASS__, __LINE__, $_SQL);
		
		$indexRows = $this->getSearch()->getEntityRepository()->getEntityManager()->getConnection()->executeQuery($_SQL);
			
		/* Profile */
	    $this->getDebug()->punch(__CLASS__, __LINE__);
		
		foreach ($indexRows as $indexRow) {
		    $modelScoresById[$indexRow['id']] = 0;
		    $extraParts = explode('|', $indexRow['extra']);
		    $matchedBunnies = array();
		
		    $_byField = array();
		    $_adjacencies = array();
		
		    foreach ($extraParts as $extraPart) {
		        $_parts = explode('-', $extraPart);
		        	
		        if (count($_parts) !== 3) {
		        	continue;
		        }
		        	
		        list($keyword, $field, $position) = $_parts;
		        	
		        if (! isset($_byField[$field])) {
		        	$_byField[$field] = array();
		        }
		        	
		        $_byField[$field][$position] = $keyword;
		    }
		
		    foreach ($_byField as $field => $_data) {
		        $modelScoresById[$indexRow['id']] += min(10, count($_data)) * $this->getSearch()->getColumnWeightFactor($field, $this->getSearch()->getData()->query_focus);
		
		        foreach ($termsBunnies as $termsBunny) {
		            $_terms = Analyzer::analyze($termsBunny, null, true, false);
		            if (!isset($_terms[0])) continue;
		
		            $positions = array_keys($_data, $_terms[0]);
		
		            foreach ($positions as $position) {
		                for ($i=1, $_termsCount = count($_terms); $i < $_termsCount; $i++) {
		                    if (! isset($_data[$position+$i])) {
		                    	continue 2; // try next pos
		                    }
		                    
		                    if ($_data[$position+$i] != $_terms[$i]) {
		                    	continue 2; // try next pos
		                    }
		
		                    if ($i + 1 == $_termsCount) {
		                        $matchedBunnies[] = $termsBunny;
		                    }
		                }
		            }
		        }
		    }
		
		    $modelScoresById[$indexRow['id']] *= $indexRow['keyword_count'] / max($numTerms, 1);
		    $modelScoresById[$indexRow['id']] *= max(array(count($matchedBunnies) / max($numTerms, 1), 1));
		
		    $uniqueMatchedBunnies = array_unique($matchedBunnies);
		    $uniqueTermsBunnies = array_unique($termsBunnies);
		
		    if (count($uniqueMatchedBunnies) != count($uniqueTermsBunnies)) {
		        unset($modelScoresById[$indexRow['id']]);
		        	
		        /* Debug */
		        $this->getDebug()->log("Bunny discard", $indexRow['id']);
		        $this->getDebug()->log("Bunny matches", $matchedBunnies);
		        $this->getDebug()->log("Bunny terms", $termsBunnies);
		    }
		}
			
		arsort($modelScoresById, SORT_NUMERIC);
			
		if (count($modelScoresById)) {
		    $minScore = current($modelScoresById) / 100 * 5;
		
		    $numValidResults = 0;
		    foreach ($modelScoresById as $id => $score) {
		        if ($numValidResults >= 100 AND $score < $minScore) {
		            unset($modelScoresById[$id]);
		        }
		        $numValidResults++;
		    }
		}
		
		$modelIds = array_keys($modelScoresById);
			
		if (count($modelIds)) {
		    /* Profile */
	    	$this->getDebug()->punch(__CLASS__, __LINE__);
		    //$this->getSearch()->debug($modelIds, 'Model IDs');
		
		    /* Get params */
		    $params = $this->getSearch()->getQueryBuilder()->getParameters();
		
		    /* Define temporary table name */
		    $tmpTableName = uniqid('temp_search_' . time() . '_' . rand(111,999), true);
		
		    /* Profile */
	    	$this->getDebug()->punch(__CLASS__, __LINE__);
		
		    /* Create temporary table */
		    $this->getSearch()->getEntityRepository()->getEntityManager()->getConnection()->executeQuery("
		        CREATE TEMPORARY TABLE `$tmpTableName` (
		            `score` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		            `id` INT UNSIGNED NOT NULL DEFAULT 0
		        )
		    ");
		
		    /* Profile */
	    	$this->getDebug()->punch(__CLASS__, __LINE__);
		
		    /* Insert into temporary table */
			$this->getSearch()->getEntityRepository()->getEntityManager()->getConnection()->executeQuery("
				INSERT INTO `$tmpTableName` (id) VALUES(" . join('),(', $modelIds) . ")
			");
		
		    /* Profile */
	    	$this->getDebug()->punch(__CLASS__, __LINE__);
		
		    /*
		     * Ensure there's a WHERE clause
		     *
		     * Due to our dodgy preg_replace() calls below, we need to make sure
		     * there's a WHERE clause in our query, otherwise, the replace will fail.
		     *
		     * @todo Clean all this up
		     */
		    $this->getSearch()->getQueryBuilder()->andWhere('1=1');
		
		    /* Build & Run search query */
		    $SQL_DATA = $this->getSearch()->getQueryBuilder()->getQuery()->getSQL();
		
		    /* Profile */
	    	$this->getDebug()->punch(__CLASS__, __LINE__);
		
		    preg_match('/^SELECT .*? FROM [^\s]*? (\w+)/mis', $SQL_DATA, $matches);
		    $modelTableAlias = $matches[1];
		
		    if ($this->getSearch()->getSelectedColumns()) {
		        $_selects = array();
		        foreach ($this->getSearch()->getSelectedColumns() as $column) {
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
		
		    if ($this->getSearch()->getData()->list_count) {
		        $SQL_DATA .= ' LIMIT ' . $this->getSearch()->getData()->list_start . ',' . $this->getSearch()->getData()->list_count;
		    }
		
		    /* Debug */
		    $this->getDebug()->log('SELECT data after query', $SQL_DATA);
		    $this->getDebug()->log('SELECT ids after query', $SQL_IDS);
		    $this->getDebug()->log('PARAMS WHERE', $params['where']);
		    
		    /* Profile */
	    	$this->getDebug()->punch(__CLASS__, __LINE__);
		
		    $paramsWhere = isset($params['where']) ? $params['where'] : '';
		
		    if ($this->getSearch()->getMode() == Search::MODE_COUNT_ONLY) {
		        /* Build & Run search count query */
		        $this->getSearch()->getQueryBuilder()->select('COUNT(id)');
		        $this->getSearch()->getQueryBuilder()->getQuery()->setMaxResults(false)->setFirstResult(false); // remove limit/offset to get total count
		        
		        /* Profile */
	    		$this->getDebug()->punch(__CLASS__, __LINE__);
		        
		        $SQL_COUNT = str_replace(
		        	' WHERE ',
		        	" RIGHT JOIN `$tmpTableName` `tmps` ON `tmps`.`id` = `$modelTableAlias`.`id` WHERE ",
		        	$this->getSearch()->getQueryBuilder()->getDQL()
		        );
		        
		        /* Debug */
		        $this->getDebug()->log('SELECT count after query', $SQL_COUNT);
		        
		        /* Profile */
	    		$this->getDebug()->punch(__CLASS__, __LINE__);
		        	
		        $RES_COUNT = \Dope\Doctrine::getEntityManager()
			        ->getConnection()
			        ->executeQuery($SQL_COUNT, array($paramsWhere));
		    }
		    else { // if we are NOT in 'count only' mode
		        $RES_DATA = \Dope\Doctrine::getEntityManager()
			        ->getConnection()
			        ->executeQuery($SQL_DATA, array($paramsWhere));
		        
		        $RES_IDS = \Dope\Doctrine::getEntityManager()
			        ->getConnection()
			        ->executeQuery($SQL_IDS, array($paramsWhere));
		    }
		
		    /* Profile */
	    	$this->getDebug()->punch(__CLASS__, __LINE__);
		
		    /* Drop temporary table */
		    $this->getSearch()->getEntityRepository()->getEntityManager()->getConnection()->executeQuery("
		        DROP TABLE `$tmpTableName`
		    ");
		
		    /* Profile */
		    $this->getDebug()->punch(__CLASS__, __LINE__);
			
		    if ($this->getSearch()->getMode() == Search::MODE_COUNT_ONLY) {
		    	$this->getSearch()->setCount($RES_COUNT->fetchColumn());
		    }
		    else {		    	
			    $this->getSearch()->setRecords(
			    	array_map(function($record) use ($modelScoresById) {
		    			$record['score'] = $modelScoresById[$record['id']];
		    			return $record;
		    		}, $RES_DATA->fetchAll(ORM\Query::HYDRATE_ARRAY))
			    );
			    
			    /* Profile */
	    		$this->getDebug()->punch(__CLASS__, __LINE__);
			    	
			    $this->getSearch()->setIds($this->getSearch()->getArrayFromColumn(0,
			    	$RES_IDS->fetchAll(ORM\Query::HYDRATE_SINGLE_SCALAR)
			    ));
			    	
			    $this->getSearch()->setCount(count($this->getSearch()->getIds()));
			    	
			    /* Profile */
	    		$this->getDebug()->punch(__CLASS__, __LINE__);
		    }
		}
	}
}