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
	Doctrine\DBAL\Types\Type,
	Elerium\Doctrine\Forms\Types\Email,
	Doctrine\Common\Annotations\SimpleAnnotationReader;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../Mocks/EntityManager.php';
require_once __DIR__ . '/entities.php';

$reader = new SimpleAnnotationReader;
require_once ELERIUM_DIR . '/Doctrine/Forms/Mapping/Form.php';
$reader->addNamespace('Elerium\Doctrine\Forms\Mapping'); // Require to be loaded

Type::addType(Email::NAME, 'Elerium\Doctrine\Forms\Types\Email');

$form = new Forms\Form(new Mocks\EntityManager, new Entity\PersonDetail, $reader);
$form->addText('id')->addRule(Forms\Form::UNIQUE, 'ID already exist');
$form->addText('name');
$form->addText('age');
$form->addText('height');
$form->addText('email');

Assert::same(file_get_contents(__DIR__ . '/Form.rules.expected'), $form->__toString());