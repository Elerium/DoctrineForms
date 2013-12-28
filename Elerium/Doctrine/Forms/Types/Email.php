<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms\Types;

use Elerium;
use Nette;
use Doctrine\DBAL\Types\StringType,
	Elerium\Doctrine\Forms\IFormType,
	Elerium\Doctrine\Forms\Form;

class Email extends StringType implements IFormType
{
	const NAME = 'email';

	/**
	 * @return string
	 */
	public function getName()
	{
		return self::NAME;
	}

	/**
	 * @return string
	 */
	public function getFormType()
	{
		return Form::EMAIL;
	}
}