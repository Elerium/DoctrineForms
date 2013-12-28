<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms;

use Elerium;
use Nette;
use Nette\Forms\IControl;

interface IEntityControl extends IControl
{
	/**
	 * @param object|array $entity
	 * @return object
	 */
	public function setEntityValue($entity);

	/**
	 * @return object
	 */
	public function getEntityValue();
}