<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms\Mapping;

use Elerium;
use Nette;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Form extends Annotation
{
	public $messages = array();
}