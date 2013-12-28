<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine\Forms;

use Elerium;
use EleriumTests\Mocks;
use Tester\Assert;
use Elerium\Doctrine\Forms,
	Doctrine\Common\Collections\ArrayCollection,
	Nette\ArrayHash;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../Mocks/EntityManager.php';
require_once __DIR__ . '/entities.php';

$entityManager = new Mocks\EntityManager;

$entities = array(
	new Entity\Person(1, 'John'),
	new Entity\Person(2, 'Jane'),
	new Entity\Person(3, 'Bill')
);

$form = new Forms\Form($entityManager, new Entity\Person);
$form->addAssociationSelect('parent', NULL, 'name', $entities);
$form->addAssociationSelect('childrens', NULL, 'name', $entities);

$defaults = new Entity\Person;
$defaults->parent = new Entity\Person(2, 'Jane');
$defaults->childrens = new ArrayCollection(array(new Entity\Person(1, 'John'), new Entity\Person(3, 'Bill')));
$form->setEntityDefaults($defaults);

$expected = new Entity\Person;
$expected->parent = new Entity\Person(2, 'Jane');
$expected->childrens = new ArrayCollection(array(new Entity\Person(1, 'John'), new Entity\Person(3, 'Bill')));
Assert::equal($expected, $form->getEntityValues());

$expected = array(
	'parent' => 1,
	'childrens' => array(0, 2)
);

Assert::equal($expected, $form->getValues(TRUE));
Assert::equal(ArrayHash::from($expected, FALSE), $form->getValues());