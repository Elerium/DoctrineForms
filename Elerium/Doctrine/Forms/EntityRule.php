<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms;

use Elerium;
use Nette;

class EntityRule extends Nette\Object
{
	/** @var string */
	private $operation;

	/** @var mixed */
	private $value;

	/** @var string */
	private $message;

	/**
	 * @param string $operation
	 * @param mixed|NULL $value
	 * @param string|NULL $message
	 */
	public function __construct($operation, $value = NULL, $message = NULL)
	{
		$this->operation = $operation;
		$this->value = $value;
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getOperation()
	{
		return $this->operation;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
}