<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine\Forms\Controls;

use Elerium;
use EleriumTests\Mocks;
use EleriumTests\Doctrine\Forms\Entity;
use Tester\Assert;
use Elerium\Doctrine\Forms\Controls,
	Doctrine\Common\Collections\ArrayCollection;

require_once __DIR__ . '/../../../../bootstrap.php';
require_once __DIR__ . '/../../../../Mocks/EntityManager.php';
require_once __DIR__ . '/../entities.php';

$entityManager = new Mocks\EntityManager;
$metadata = $entityManager->getClassMetadata(get_class(new Entity\Person));
$entities = array(
	new Entity\Person(1, 'John'),
	new Entity\Person(2, 'Jane'),
	new Entity\Person(3, 'Bill')
);

Assert::throws(function() use ($metadata, $entities) {
	new Controls\CollectionSelectBox($metadata, 'freewill', $entities);
}, 'Elerium\InvalidArgumentException', "Entity 'EleriumTests\\Doctrine\\Forms\\Entity\\Person' has no field named 'freewill'.");

Assert::throws(function() use ($metadata) {
	new Controls\CollectionSelectBox($metadata, 'name', 'John');
}, 'Elerium\InvalidArgumentException', "Expected items type is array or collection, string given.");

new Controls\CollectionSelectBox($metadata, 'name', NULL);
new Controls\CollectionSelectBox($metadata, 'name', new ArrayCollection($entities));

Assert::throws(function() use ($metadata) {
	new Controls\CollectionSelectBox($metadata, 'name', array(new Entity\Simple));
}, 'Elerium\InvalidArgumentException', "Expected item class is 'EleriumTests\\Doctrine\\Forms\\Entity\\Person', 'EleriumTests\\Doctrine\\Forms\\Entity\\Simple' given.");

$control = new Controls\CollectionSelectBox($metadata, 'name', $entities);

Assert::throws(function() use ($control) {
	$control->setEntityValue('John');
}, 'Elerium\InvalidArgumentException', 'Expected value is array or collection, string given.');

Assert::throws(function() use ($control) {
	$control->setEntityValue(new ArrayCollection(array('John')));
}, 'Elerium\InvalidArgumentException', 'Values can contains only entities, string given.');

$control->setEntityValue(array(new Entity\Person(1, 'John'), new Entity\Person(3, 'Bill')));
Assert::equal(array(new Entity\Person(1, 'John'), new Entity\Person(3, 'Bill')), $control->getEntityValue());