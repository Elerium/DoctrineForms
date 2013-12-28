<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms;

use Elerium;
use Nette;
use Doctrine\ORM\EntityManager,
	Nette\ComponentModel\IComponent,
	Doctrine\ORM\Proxy\Proxy,
	Doctrine\Common\Annotations\Reader;

trait EntityContainer
{
	/** @var object */
	private $entity;

	/** @var array */
	private $defaults = array();

	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;

	/** @var \Doctrine\Common\Annotations\Reader */
	protected $reader;

	/** @var \Elerium\Doctrine\EntityMapper */
	private $entityMapper;

	/** @var \Doctrine\ORM\Mapping\ClassMetadata */
	private $classMetadata;

	/** @var \Elerium\Doctrine\Forms\EntityRules */
	private $entityRules;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @return \Elerium\Doctrine\Forms\EntityContainer
	 */
	protected function setEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		return $this;
	}

	/**
	 * @param \Doctrine\Common\Annotations\Reader $reader
	 * @return \Elerium\Doctrine\Forms\EntityContainer
	 */
	protected function setReader(Reader $reader)
	{
		$this->reader = $reader;
		return $this;
	}

	/**
	 * @param object $entity
	 * @return \Elerium\Doctrine\Forms\EntityContainer
	 * @throws \Elerium\InvalidArgumentException
	 */
	protected function setEntity($entity)
	{
		if(!is_object($entity))
		{
			throw new Elerium\InvalidArgumentException("Entity must be object, " . gettype($entity) . " given.");
		}

		if($this->entity !== NULL)
		{
			if($entity instanceof Proxy || $this->entity instanceof Proxy)
			{
				if(!$this->entity instanceof Proxy && get_parent_class($entity) !== get_class($this->entity))
				{
					throw new Elerium\InvalidArgumentException("Expected proxy is subclass of '" . get_class($this->entity) . "', subclass of '" . get_parent_class($entity) . "' given.");
				}
				elseif($this->entity instanceof Proxy && get_class($entity) !== get_class($this->entity) && get_parent_class($this->entity) !== get_class($entity))
				{
					throw new Elerium\InvalidArgumentException("Expected proxy is subclass of '" . get_parent_class($this->entity) . "', subclass of '" . get_parent_class($entity) . "' given.");
				}
			}
			elseif(is_subclass_of($entity, get_class($this->entity)))
			{
				$this->classMetadata = NULL; // Invalidate metadata
				$this->entityRules = NULL; // Invalidate rules
			}
			elseif(get_class($entity) !== get_class($this->entity))
			{
				throw new Elerium\InvalidArgumentException("Expected entity class is '" . get_class($this->entity) . "', '" . get_class($entity) . "' given.");
			}
		}

		$this->entity = $entity;
		return $this;
	}

	/**
	 * @param string $className
	 * @throws \Elerium\InvalidArgumentException
	 */
	protected function setEntityClass($className)
	{
		if(!is_string($className))
		{
			throw new Elerium\InvalidArgumentException("Entity class must be string, " . gettype($className) . " given.");
		}
		elseif(!class_exists($className))
		{
			throw new Elerium\InvalidArgumentException("Entity class '$className' does not exist.");
		}

		$this->setEntity($this->getEntityManager()->getClassMetadata($className)->newInstance());
	}

	/**
	 * {@inheritDoc}
	 */
	public function addComponent(IComponent $component, $name, $insertBefore = NULL)
	{
		if($this->getClassMetadata()->hasField($name))
		{
			foreach($this->getEntityRules()->getRulesForField($name) as $rule)
			{
				$component->addRule($rule->getOperation(), $rule->getMessage(), $rule->getValue());
			}
		}

		parent::addComponent($component, $name, $insertBefore);
	}

	/**
	 * @param string $name
	 * @return \Elerium\Doctrine\Forms\Container
	 * @throws \Elerium\InvalidArgumentException
	 */
	public function addAssociation($name)
	{
		if(!$this->getClassMetadata()->hasAssociation($name) || !$this->getClassMetadata()->isSingleValuedAssociation($name))
		{
			if($this->getClassMetadata()->hasField($name) || $this->getClassMetadata()->hasAssociation($name))
			{
				$msg = $this->getClassMetadata()->isCollectionValuedAssociation($name) ? 'is not single valued association field' : 'is not association field';
			}
			else
			{
				$msg = 'does not exist';
			}

			throw new Elerium\InvalidArgumentException("Field '$name' in entity '" . get_class($this->entity) . "' $msg.");
		}

		if(($association = $this->getClassMetadata()->getFieldValue($this->entity, $name)) === NULL)
		{
			$association = $this->getEntityManager()->getClassMetadata($this->getClassMetadata()->getAssociationTargetClass($name))->newInstance();
		}

		$container = new Container($this->getEntityManager(), $association, $this->reader);
		$container->currentGroup = $this->currentGroup;

		return $this[$name] = $container;
	}

	/**
	 * @param string $name
	 * @param string $label
	 * @param string $field
	 * @param \Doctrine\Common\Collections\Collection|array $entities
	 * @param int $size
	 * @return \Elerium\Doctrine\Forms\Controls\CollectionSelectBox|\Elerium\Doctrine\Forms\Controls\SingleSelectBox
	 */
	public function addAssociationSelect($name, $label, $field, $entities = NULL, $size = NULL)
	{
		$targetMetadata = $this->getEntityManager()->getClassMetadata($this->getClassMetadata()->getAssociationTargetClass($name));

		if($this->classMetadata->isSingleValuedAssociation($name))
		{
			return $this[$name] = new Controls\SingleSelectBox($targetMetadata, $field, $entities, $label, $size);
		}
		elseif($this->classMetadata->isCollectionValuedAssociation($name))
		{
			return $this[$name] = new Controls\CollectionSelectBox($targetMetadata, $field, $entities, $label, $size);
		}
	}

	/**
	 * @param object $entity
	 * @return \Elerium\Doctrine\Forms\EntityContainer
	 */
	public function setEntityDefaults($entity)
	{
		$form = $this->getForm(FALSE);
		if(!$form || !$form->isAnchored() || !$form->isSubmitted())
		{
			$this->setEntityValues($entity);
		}

		return $this;
	}

	/**
	 * @param object $entity
	 * @return \Elerium\Doctrine\Forms\EntityContainer
	 */
	public function setEntityValues($entity)
	{
		$this->setEntity($entity);

		$values = $this->getEntityMapper()->getValues($entity);
		foreach($this->getComponents() as $name => $control)
		{
			if($control instanceof IEntityControl || $control instanceof Container)
			{
				unset($values[$name]);
				$value = $this->getClassMetadata()->getFieldValue($entity, $name);

				if($control instanceof IEntityControl)
				{
					$control->setEntityValue($value);
				}
				elseif($control instanceof Container)
				{
					$control->setEntityDefaults($value);
				}
			}
		}

		$this->setValues($values);

		return $this;
	}

	/**
	 * @{inheritDoc}
	 */
	public function setValues($values, $erase = FALSE)
	{
		$this->defaults = array_merge($this->defaults, $values instanceof \Traversable ? iterator_to_array($values) : $values);

		parent::setValues($values, $erase);
	}

	/**
	 * @return object
	 */
	public function getEntityValues()
	{
		$values = $this->getValues(TRUE);

		foreach($this->getControls() as $name => $control)
		{
			if(!$this->getClassMetadata()->hasField($name) && !$this->getClassMetadata()->hasAssociation($name))
			{
				unset($values[$name]);
			}

			if($control instanceof IEntityControl)
			{
				$values[$name] = $control->getEntityValue();
			}
		}

		return $this->getEntityMapper()->setValues($this->entity, $values);
	}

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 * @return \Elerium\Doctrine\EntityMapper
	 */
	protected function getEntityMapper()
	{
		if($this->entityMapper === NULL)
		{
			$this->entityMapper = new Elerium\Doctrine\EntityMapper($this->getEntityManager()->getMetadataFactory());
		}

		return $this->entityMapper;
	}

	/**
	 * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
	 */
	public function getClassMetadata()
	{
		if($this->classMetadata === NULL)
		{
			$this->classMetadata = $this->getEntityManager()->getClassMetadata(get_class($this->entity));
		}

		return $this->classMetadata;
	}

	/**
	 * @return \Elerium\Doctrine\Forms\EntityRules
	 */
	protected function getEntityRules()
	{
		if($this->entityRules === NULL)
		{
			$this->entityRules = new EntityRules($this->getClassMetadata(), $this->reader);
		}

		return $this->entityRules;
	}

	/**
	 * @param string $field
	 * @throws \Elerium\InvalidArgumentException
	 * @return mixed
	 */
	public function getDefaultValue($field)
	{
		if(!isset($this[$field]))
		{
			throw new Elerium\InvalidArgumentException("Can't get default value for '$field' because field does not exist.");
		}
		elseif(!isset($this->defaults[$field]))
		{
			return NULL;
		}

		return $this->defaults[$field];
	}
}