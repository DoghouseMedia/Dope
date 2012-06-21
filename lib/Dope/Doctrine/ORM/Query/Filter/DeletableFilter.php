<?php

namespace Dope\Doctrine\ORM\Query\Filter;
use Doctrine\ORM\Mapping\ClassMetaData,
Doctrine\ORM\Query\Filter\SQLFilter;

class DeletableFilter extends SQLFilter
{
	public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
	{
		if (! $targetEntity->hasField('deleted')) {
			return '';
		}
		
		$mapping = $targetEntity->getFieldMapping('deleted');
		if ($mapping['type'] != 'boolean') {
			return '';
		}
		
		return $targetTableAlias.'.deleted != 1';
	}
}