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

Assert::throws(function() use ($entityManager) {
	new Forms\Form($entityManager, NULL);
}, 'Elerium\InvalidArgumentException', "Entity class must be string, NULL given.");

Assert::throws(function() use ($entityManager) {
	new Forms\Form($entityManager, 1);
}, 'Elerium\InvalidArgumentException', "Entity class must be string, integer given.");

Assert::throws(function() use ($entityManager) {
	new Forms\Form($entityManager, 'EleriumTests\Doctrine\Forms\Entity\InvalidEntity');
}, 'Elerium\InvalidArgumentException', "Entity class 'EleriumTests\\Doctrine\\Forms\\Entity\\InvalidEntity' does not exist.");

new Forms\Form($entityManager, 'EleriumTests\Doctrine\Forms\Entity\Person');
new Forms\Form($entityManager, new Entity\Person);