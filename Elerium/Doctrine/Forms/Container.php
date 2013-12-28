<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms;

use Elerium;
use Nette;
use Doctrine\ORM\EntityManager,
	Nette\ComponentModel\IContainer,
	Doctrine\Common\Annotations\Reader;

class Container extends Nette\Forms\Container
{
	use EntityContainer;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param object $entity
	 * @param \Doctrine\Common\Annotations\Reader|NULL $reader
	 * @param \Nette\ComponentModel\IContainer $parent
	 * @param string $name
	 */
	public function __construct(EntityManager $entityManager, $entity, Reader $reader = NULL, IContainer $parent = NULL, $name = NULL)
	{
		$this->setEntityManager($entityManager);

		if(is_object($entity))
		{
			$this->setEntity($entity);
		}
		else
		{
			$this->setEntityClass($entity);
		}

		if($reader !== NULL)
		{
			$this->setReader($reader);
		}

		parent::__construct($parent, $name);
	}
}