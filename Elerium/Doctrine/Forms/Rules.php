<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms;

use Elerium;
use Nette;
use Nette\Forms\IControl;

class Rules extends Nette\Object
{

	/**
	 * @param \Nette\Forms\IControl $control
	 * @return bool
	 */
	public static function validateUnique(IControl $control)
	{
		$container = $control->getParent();
		if($container->getDefaultValue($control->name) == $control->getValue())
		{
			return TRUE;
		}

		return !(bool) $container->getEntityManager()->getRepository($container->getClassMetadata()->getName())->findOneBy(array($control->name => $control->value));
	}
}