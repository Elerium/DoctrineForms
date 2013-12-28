<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms\Controls;

use Elerium;
use Nette;
use Nette\Forms\Controls\MultiSelectBox,
	Elerium\Doctrine\Forms\IEntityControl,
	Doctrine\Common\Persistence\Mapping\ClassMetadata,
	Doctrine\Common\Collections\Collection;

class CollectionSelectBox extends MultiSelectBox implements IEntityControl
{
	use AssociationSelectBox;

	/**
	 * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $classMetadata
	 * @param string $field
	 * @param Doctrine\Common\Collections\Collection|array $entities
	 * @param string $label
	 * @param int $size
	 */
	public function __construct(ClassMetadata $classMetadata, $field, $entities, $label = NULL, $size = NULL)
	{
		$this->initialize($classMetadata, $field, $entities);

		parent::__construct($label, NULL, $size);
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection|array $entities
	 * @throws \Elerium\InvalidArgumentException
	 */
	public function setEntityValue($entities)
	{
		if(is_object($entities) && !$entities instanceof Collection)
		{
			$entities = array($entities);
		}

		if(!$entities instanceof Collection && !is_array($entities))
		{
			throw new Elerium\InvalidArgumentException("Expected value is array or collection, " . gettype($entities) . " given.");
		}

		$keys = array();
		foreach($entities as $entity)
		{
			if(!is_object($entity))
			{
				throw new Elerium\InvalidArgumentException('Values can contains only entities, ' . gettype($entity) . ' given.');
			}

			$keys[] = $this->getItemKey($entity, $this->areKeysUsed());
		}

		parent::setValue($keys);
	}

	/**
	 * @return array
	 */
	public function getEntityValue()
	{
		return array_values(array_intersect_key($this->entities, array_flip(parent::getValue())));
	}
}