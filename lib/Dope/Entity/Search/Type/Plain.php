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
		/* Profile */
		$this->getDebug()->punch(__CLASS__, __LINE__);

		/* Debug */
		$this->getDebug()->log('PRE RETURN DQL', $this->getSearch()->getQueryBuilder()->getDQL());
		$this->getDebug()->log('PRE RETURN SQL', $this->getSearch()->getQueryBuilder()->getQuery()->getSQL());
			
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