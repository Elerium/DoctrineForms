<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms\Controls;

use Elerium;
use Nette;
use Doctrine\Common\Persistence\Mapping\ClassMetadata,
	Doctrine\Common\Collections\Collection;

trait AssociationSelectBox
{
	/** @var \Doctrine\Common\Persistence\Mapping\ClassMetadata */
	private $classMetadata;

	/** @var string */
	private $field;

	/** @var array */
	protected $entities;

	/** @var array */
	private $keys = array();

	/** @var int */
	private $currentKey = 0;

	/**
	 * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $classMetadata
	 * @param string $field
	 * @param \Doctrine\Common\Collections\Collection|array $entities
	 * @throws \Elerium\InvalidArgumentException
	 */
	protected function initialize(ClassMetadata $classMetadata, $field, $entities)
	{
		$this->classMetadata = $classMetadata;

		if(!$this->classMetadata->hasField($field))
		{
			throw new Elerium\InvalidArgumentException("Entity '{$this->classMetadata->getName()}' has no field named '{$field}'.");
		}

		$this->field = $field;

		if(is_array($entities))
		{
			$this->setItems($entities);
		}
		elseif($entities instanceof Collection)
		{
			$this->setCollectionItems($entities);
		}
		elseif($entities !== NULL)
		{
			throw new Elerium\InvalidArgumentException("Expected items type is array or collection, " . gettype($entities) . " given.");
		}
	}

	/**
	 * @param array $items
	 * @param bool $useKeys
	 * @throws \Elerium\InvalidArgumentException
	 */
	public function setItems(array $items, $useKeys = TRUE)
	{
		$this->invalidateKeys();

		$expectedClass = $this->classMetadata->getName();

		$values = array();
		foreach($items as $entity)
		{
			if(!$entity instanceof $expectedClass)
			{
				throw new Elerium\InvalidArgumentException("Expected item class is '$expectedClass', " . (is_object($entity) ?  "'" . get_class($entity) . "'" : gettype($entity)) . " given.");
			}

			$key = $this->getItemKey($entity, $useKeys);
			$value = $this->classMetadata->getFieldValue($entity, $this->field);

			$values[$key] = $value;
			$this->entities[$key] = $entity;
		}

		parent::setItems($values, $useKeys);
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection $collection
	 * @param bool $useKeys
	 */
	public function setCollectionItems(Collection $collection, $useKeys = TRUE)
	{
		$this->setItems($collection->toArray(), $useKeys);
	}

	/**
	 * @param object $entity
	 * @param bool $useKeys
	 * @return mixed
	 */
	private function getItemKey($entity, $useKeys)
	{
		$value = $this->classMetadata->getFieldValue($entity, $this->field);

		if($useKeys)
		{
			if(!isset($this->keys[$value]))
			{
				$this->keys[$value] = $this->currentKey++;
			}

			return $this->keys[$value];
		}

		return $value;
	}

	private function invalidateKeys()
	{
		$this->keys = array();
		$this->currentKey = 0;
	}
}