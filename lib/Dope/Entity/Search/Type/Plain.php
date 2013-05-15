<?php

namespace Dope\Entity\Search\Type;

use Dope\Entity\Search,
	Dope\Log,
	Doctrine\ORM\Query;

class Plain extends _Base
{
	public function preExecute()
	{
		
	}
	
	public function postExecute()
	{
		//$this->getSearch()->getProfiler()->punch('search pre return');
			
		Log::console("PRE RETURN SQL");
		Log::console($this->getSearch()->getQueryBuilder()->getDQL());
		
		//$this->getSearch()->debug($this->getQueryBuilder()->getSqlQuery(), "PRE RETURN SQL");
			
		switch($this->getSearch()->getMode()) {
			
			/* Count */
		    case Search::MODE_COUNT_ONLY:
		        $this->getSearch()->setCount(count($this->getSearch()->getQueryBuilder()
			        ->select($this->getSearch()->getTableAlias() . '.id')
			        ->getQuery()
			        ->setMaxResults(null)
			        ->setFirstResult(null)
			        ->getArrayResult()
		        ));
		        break;
		
		    /* Normal */
		    default:
		    case Search::MODE_NORMAL:
		        $this->getSearch()->setRecords($this->getSearch()->getQueryBuilder()->getQuery()
			        ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
			        ->getResult(Query::HYDRATE_ARRAY)
			    );
		        $this->getSearch()->setCount(count($this->getSearch()->getRecords()));
		        break;
		    
		    /* Pagination */
		    case Search::MODE_WITH_PAGINATION:
		        $this->getSearch()->setRecords($this->getSearch()->getQueryBuilder()->getQuery()
			        ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
			        ->getResult(Query::HYDRATE_ARRAY)
			    );
		        $this->getSearch()->setIds($this->getSearch()->getArrayFromColumn('id', $this->getSearch()->getQueryBuilder()
	                ->select($this->getSearch()->getTableAlias() . '.id')
	                ->getQuery()
	                ->setMaxResults(null)
	                ->setFirstResult(null)
	                ->getArrayResult()
		        ));
		        $this->getSearch()->setCount(count($this->getSearch()->getIds()));
		        break;
		}
	}
}