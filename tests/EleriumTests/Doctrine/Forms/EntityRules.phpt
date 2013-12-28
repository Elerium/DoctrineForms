<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine\Forms;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Elerium;
use EleriumTests\Mocks;
use Tester\Assert;
use Elerium\Doctrine\Forms,
	Doctrine\DBAL\Types\Type,
	Elerium\Doctrine\Forms\Types\Email,
	Elerium\Doctrine\Forms\EntityRule;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../Mocks/EntityManager.php';
require_once __DIR__ . '/entities.php';

$entityManager = new Mocks\EntityManager;

$reader = new SimpleAnnotationReader;
require_once ELERIUM_DIR . '/Doctrine/Forms/Mapping/Form.php';
$reader->addNamespace('Elerium\Doctrine\Forms\Mapping'); // Require to be loaded

Type::addType(Email::NAME, 'Elerium\Doctrine\Forms\Types\Email');
$entityRules = new Forms\EntityRules($entityManager->getClassMetadata(get_class(new Entity\PersonDetail)), $reader);

Assert::equal(array(
	'id' => array(
		new EntityRule(':integer', NULL, 'Wrong type!'),
		new EntityRule(':filled', NULL, NULL)
	),
	'name' => array(
		new EntityRule(':maxLength', 64, 'Max length must be %d!'),
		new EntityRule(':filled', NULL, NULL)
	),
	'age' => array(
		new EntityRule(':integer', NULL, NULL)
	),
	'height' => array(
		new EntityRule(':float', NULL, NULL)
	),
	'email' => array(
		new EntityRule(':email', NULL, NULL)
	)
), $entityRules->getAllRules());