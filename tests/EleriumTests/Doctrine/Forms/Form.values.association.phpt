<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine\Forms;

use Elerium;
use EleriumTests\Mocks;
use Tester\Assert;
use Elerium\Doctrine\Forms;
use Nette\ArrayHash;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../Mocks/EntityManager.php';
require_once __DIR__ . '/entities.php';


$entityManager = new Mocks\EntityManager;

$form = new Forms\Form($entityManager, new Entity\Person);
$form->addText('id');
$form->addText('name');
$parent = $form->addAssociation('parent');
$parent->addText('id');
$parent->addText('name');
$parent2 = $parent->addAssociation('parent');
$parent2->addText('id');
$parent2->addText('name');

$defaults = new Entity\Person(1, 'John');
$defaults->parent = new Entity\Person(2, 'Jane');
$defaults->parent->parent = new Entity\Person(3, 'Bill');
$form->setEntityDefaults($defaults);

$expected = new Entity\Person('1', 'John');
$expected->parent = new Entity\Person('2', 'Jane');
$expected->parent->parent = new Entity\Person('3', 'Bill');
Assert::equal($expected, $form->getEntityValues());

$expected = array(
	'id' => '1',
	'name' => 'John',
	'parent' => array(
		'id' => '2',
		'name' => 'Jane',
		'parent' => array(
			'id' => '3',
			'name' => 'Bill'
		)
	)
);

Assert::equal($expected, $form->getValues(TRUE));
Assert::equal(ArrayHash::from($expected), $form->getValues());