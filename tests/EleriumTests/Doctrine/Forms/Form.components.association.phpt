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

$form = new Forms\Form($entityManager, new Entity\Person);

Assert::throws(function() use ($form) {
	$form->addAssociation('unknown');
}, 'Elerium\InvalidArgumentException', "Field 'unknown' in entity 'EleriumTests\\Doctrine\\Forms\\Entity\\Person' does not exist.");

Assert::throws(function() use ($form) {
	$form->addAssociation('name');
}, 'Elerium\InvalidArgumentException', "Field 'name' in entity 'EleriumTests\\Doctrine\\Forms\\Entity\\Person' is not association field.");

Assert::throws(function() use ($form) {
	$form->addAssociation('childrens');
}, 'Elerium\InvalidArgumentException', "Field 'childrens' in entity 'EleriumTests\\Doctrine\\Forms\\Entity\\Person' is not single valued association field.");

$form->addText('id');
$form->addText('name');
$parent = $form->addAssociation('parent');
$parent->addText('id');
$parent->addText('name');
$parent2 = $parent->addAssociation('parent');
$parent2->addText('id');
$parent2->addText('name');

Assert::true($parent instanceof Forms\Container);
Assert::true($parent2 instanceof Forms\Container);
Assert::same(file_get_contents(__DIR__ . '/Form.components.association.expected'), $form->__toString());