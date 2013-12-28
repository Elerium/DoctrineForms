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
$parent = $form->addAssociationSelect('parent', NULL, 'name');
$childrens = $form->addAssociationSelect('childrens', NULL, 'name');

$parent->setItems($entities, TRUE);
$childrens->setItems($entities, TRUE);

Assert::same(file_get_contents(__DIR__ . '/Form.components.select.expected1'), $form->__toString());

$parent->setItems($entities, FALSE);
$childrens->setItems($entities, FALSE);

Assert::same(file_get_contents(__DIR__ . '/Form.components.select.expected2'), $form->__toString());