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

class Form extends Nette\Application\UI\Form
{
	const UNIQUE = 'Elerium\Doctrine\Forms\Rules::validateUnique';

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

	public static function register()
	{
		Nette\Forms\Container::extensionMethod('addEntity', function(IContainer $container, $name, EntityManager $entityManager, $entity, Reader $reader = NULL) {
			return $container[$name] = new Container($entityManager, $entity, $reader);
		});
	}
}