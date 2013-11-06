<?php

namespace Dope\Form;

use Dope\Entity\Definition;

class Entity extends _Base
{
	/**
	 * @var \Dope\Entity
	 */
	protected $entity;
	
	/**
	 * @var \Doctrine\ORM\Mapping\ClassMetadata
	 */
	protected $entityClassMetadata;
	
	public function init()
	{
		parent::init();
		
		$this->setDecorators(array(
			'FormElements',
			array('ContentPane', array('region'=>'center')),
			'Buttons',
			'EntityFormContainer'
		));
	}
	
	public function toInlineForm()
	{
		$this->setDecorators(array(
			'FormElements',
			array('ContentPane', array('region'=>'center')),
			array('Buttons', array('placement'=>'append')),
			'EntityForm'
		));
		return $this;
	}
	
	public static function factory($entity, $name=null)
	{
		$class = new static();
		
		if ($name) {
			$class->setName($name);
		}
		
		$class->setEntity($entity);
		$class->configure();
		
		return $class;
	}
	
	public function hasEntity()
	{
		return (bool) $this->getEntity();
	}
	
	/**
	 * @return \Dope\Entity
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	
	public function setEntity(\Dope\Entity $entity=null)
	{
		$this->entity = $entity;
		return $this;
	}

	protected function getDefinition()
	{
		if ($this->hasEntity()) {
			return new Definition($this->getEntity());
		}
		
		if ($this->hasController()) {
			return new Definition($this->getController()->getModelClassName());
		}
		
		throw new \Exception("No way to determine entityName");
	}
	
	public function preConfigure()
	{
		parent::preConfigure();
		
		/* Should we populate the default values from the entity? */
		$getDefaultValuesFromEntity = ($this->hasController() AND $this->hasEntity());
		
		/* Loop through fields and create form */  
		foreach ($this->getDefinition()->getFields() as $name => $field) {
			/* Add Element */
			$this->addElement($field->type, $name, array_merge(array(
				'label' => $field->label ?: ucfirst(str_replace('_', ' ', $name)),
				'required' => $field->required,
				'multiOptions' => $field->options
			), $field->params));
			
			if ($getDefaultValuesFromEntity) {
				$element = $this->getElement($name);
				$value = $this->getEntity()->{$name};

				if ($value instanceof \DateTime) {
					switch ($this->getEntityClassMetadata()->getTypeOfColumn($name)) {
						case 'time':
							$element->setValue($value->format('H:i:s'));
							break;
						case 'date':
							$element->setValue($value->format('Y-m-d'));
							break;
						case 'datetime':
						default:
							$element->setValue($value->format('Y-m-d H:m:i'));
							break;
					}
				}
				elseif ($value instanceof \Dope\Entity) {
					$element->setValue($value->id);
				}
				else {
					$element->setValue($value);
				}
			}
			
			$group = $this->getDefinition()->getGroup($name);
			if ($group) {
				/* Create Display group */
				if (! $this->getDisplayGroup($group->name)) {
					$this->addDisplayGroup(
						array($name),
						$group->name,
						array('legend' => $group->label)
					);
					
					$this->setDecorators(array(
						'FormElements',
						array('ContentPane', array('region'=>'center')),
						'Quicklinks',
						'Buttons',
						'EntityFormContainer'
					));
					
					if ($group->order) {
						$this->getDisplayGroup($group->name)->setOrder($group->order);
					}
				}
				else {
					$this->getDisplayGroup($group->name)->addElement($this->getElement($name));
				}
			}
		}
		
		/* Method */
		$this->setMethod($this->hasEntity() ? 'put' : 'post');
		
		if ($this->hasController()) {
			$request = $this->getController()->getRequest();
			
			/* Action URL */
			$this->setAction($this->getController()->getHelper('url')->direct(
				$this->hasEntity() ? $this->getEntity()->id : null,
				$request->getControllerName(),
				$this->getController()->getFrontController()->getDefaultModule()
			));
			
			/* Relations */
			foreach ($this->getController()->getEntityRepository()->getAssociationMappings() as $alias => $mapping) {
				$isToManyAssociation = in_array($mapping['type'], array(
					\Doctrine\ORM\Mapping\ClassMetadata::ONE_TO_MANY,
					\Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_MANY
				));
				
				if ($this->hasElement($alias)) {
					continue; // skip if form already has element by this name
				}
				
				$this->addElement('hidden', $alias, array(
					'decorators' => array('DijitElement')
				));
				
				if ($getDefaultValuesFromEntity AND $this->getEntity()->{$alias}) {
					if ($isToManyAssociation) {
    				    $ids = array();
    				    foreach ($this->getEntity()->{$alias} as $_entity) {
    				        $ids[] = $_entity->id;
    				    }
    					$this->getElement($alias)->setValue(
    						join(',', $ids)
    					);
    				}
					elseif (isset($this->getEntity()->{$alias}->id)) {
				        $this->getElement($alias)->setValue(
			                $this->getEntity()->{$alias}->id
				        );
					}
				}
			}
		}
		
		return $this; // chainable
	}
	
	public function postConfigure()
	{
		parent::postConfigure();
		$this->removeElement('submit');
	}
	
	/* ----- Mini Forms -----*/
	
// 	public function createMiniForm(Entity $_form, \Dope\Entity $entity, $subformName=null)
// 	{
// 		if ($subformName) {
// 			$form = $_form->getDisplayGroup($subformName);//->toQuickForm();
// 		} else {
// 			$form = $_form;
// 		}
		
// 		$entityKey = $entity->getEntityKey();
// 		$subName = $form->getName();
	
// 		$form->setName($entityKey . '-' . $subName);
// 		$form->populateByEntity($entity);
// 		$form->addClassName('related-subform');
	
// 		/* Attribs */
// 		$form->setAttrib('relatedname', $entityKey);
// 		$form->setAttrib('relatedsubname', $subName);
// 		$form->setAttrib('relatedformid', $this->getId() . '-' . $form->getName());
// 		$form->setAttrib('relatedformlink', $this->getView()->url(array(
// 			'controller' => $entityKey,
// 			'action' => 'edit',
// 			'subformname' => $subformName,
// 			'format' => 'form',
// 			'id' => $entity->id
// 		)));
			
// 		/* Add */
// 		//$this->addDisplayGroup($form->getElements(), $subformname);
	
// 		return $this;
// 	}
	
	/* ----- Data ----- */
	
	public function populateByEntity(\Dope\Entity $entity)
	{
		$md = \Dope\Doctrine::getEntityManager()->getClassMetadata(get_class($entity));
	
		$fieldValues = array_intersect_key($entity->toArray(), $this->getElements());
	
		\Dope\Log::console('populateByEntity');
		\Dope\Log::console(array_keys($this->getElements()));
		\Dope\Log::console($fieldValues);
		
		$this->populate($fieldValues);
	
		//$this->populate($record->toArray());
	
		// 		foreach($this->getSubForms() as $name => $subform) {
		// 			if (! $subform instanceof Core_Form_Subform_Multi) {
		// 				continue;
		// 			}
			
		// 			$alias = $subform->getRelationName();
			
		// 			$fkName = 'fk_' . $record->getEntityKey() . '_id';
			
		// 			foreach($record->$alias as $i => $_record) {
		// 				if (! $_record instanceof Core_Model) continue;
		// 				if ($_record->isDeleted()) continue;
	
		// 				$_form = clone $subform->getTemplateForm();
	
		// 				if (isset($_record->id) AND !$_form->hasElement('id')) {
		// 					$_form->addElement('hidden', 'id');
		// 					$_form->hide('id');
		// 				}
	
		// 				if (isset($_record->$fkName) AND !$_form->hasElement($fkName)) {
		// 					$_form->addElement('hidden', $fkName);
		// 					$_form->hide($fkName);
		// 				}
	
		// 				$_form->populate($_record->toArray());
		// 				$_form->addAttribs(array('id' => $_form->getName() . '-' . $i));
	
		// 				$subform->addSubform($_form, $i);
		// 			}
	
		// 			if ($subform->getTemplateElement()->getAttrib('form')->hasElement($fkName)) {
		// 				$subform->getTemplateElement()->getAttrib('form')->getElement($fkName)->setValue($this->id->getValue());
		// 			}
		// 		}
	
		return $this;
	}
	
	public function getEntityClassMetadata()
	{
		if (! $this->entityClassMetadata) {
			$this->entityClassMetadata = \Dope\Doctrine::getEntityManager()->getClassMetadata(
				get_class($this->getEntity())
    		);
		}
		
		return $this->entityClassMetadata;
	}
}