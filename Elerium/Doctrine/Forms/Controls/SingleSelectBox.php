<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms\Controls;

use Elerium;
use Nette;
use Nette\Forms\Controls\SelectBox,
	Elerium\Doctrine\Forms\IEntityControl,
	Doctrine\Common\Persistence\Mapping\ClassMetadata;

class SingleSelectBox extends SelectBox implements IEntityControl
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
	 * @param object $entity
	 * @throws \Elerium\InvalidArgumentException
	 */
	public function setEntityValue($entity)
	{
		if(!is_object($entity))
		{
			throw new Elerium\InvalidArgumentException('Expected value is entity, ' . gettype($entity) . ' given.');
		}

		parent::setValue($this->getItemKey($entity, $this->areKeysUsed()));
	}

	/**
	 * @return object|NULL
	 */
	public function getEntityValue()
	{
		if(($value = parent::getValue()) !== NULL)
		{
			return $this->entities[$value];
		}
	}
}