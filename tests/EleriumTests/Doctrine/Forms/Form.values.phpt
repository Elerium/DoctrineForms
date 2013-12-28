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
	$form->setEntityDefaults(NULL);
}, 'Elerium\InvalidArgumentException', "Entity must be object, NULL given.");

Assert::throws(function() use ($form) {
	$form->setEntityDefaults('EleriumTests\Doctrine\Forms\Entity\Person');
}, 'Elerium\InvalidArgumentException', "Entity must be object, string given.");

Assert::throws(function() use ($form) {
	$form->setEntityDefaults(1);
}, 'Elerium\InvalidArgumentException', "Entity must be object, integer given.");

Assert::throws(function() use ($form) {
	$form->setEntityDefaults(array());
}, 'Elerium\InvalidArgumentException', "Entity must be object, array given.");

Assert::throws(function() use ($form) {
	$form->setEntityDefaults(new Entity\Simple);
}, 'Elerium\InvalidArgumentException', "Expected entity class is 'EleriumTests\\Doctrine\\Forms\\Entity\\Person', 'EleriumTests\\Doctrine\\Forms\\Entity\\Simple' given.");

/**
 * @Entity
 */
class Child extends Entity\Person
{
}

// --------------- Subclass ---------------

$form = new Forms\Form($entityManager, new Entity\Person);
$form->setEntityDefaults(new Entity\Person);
$form->setEntityDefaults(new Child);

// --------------- Proxies ----------------

$personProxy = $entityManager->getProxyFactory()->getProxy(get_class(new Entity\Person), 1);
$personProxy->__isInitialized__ = TRUE;
$childProxy = $entityManager->getProxyFactory()->getProxy(get_class(new Child), 1);
$childProxy->__isInitialized__ = TRUE;

$form = new Forms\Form($entityManager, new Entity\Person);

Assert::throws(function() use ($form, $childProxy) {
	$form->setEntityDefaults($childProxy);
}, 'Elerium\InvalidArgumentException', "Expected proxy is subclass of 'EleriumTests\\Doctrine\\Forms\\Entity\\Person', subclass of 'EleriumTests\\Doctrine\\Forms\\Child' given.");

$form->setEntityDefaults($personProxy);
Assert::throws(function() use ($form, $childProxy) {
	$form->setEntityDefaults($childProxy);
}, 'Elerium\InvalidArgumentException', "Expected proxy is subclass of 'EleriumTests\\Doctrine\\Forms\\Entity\\Person', subclass of 'EleriumTests\\Doctrine\\Forms\\Child' given.");

$form->setEntityDefaults($personProxy);
$form->setEntityDefaults(new Entity\Person);