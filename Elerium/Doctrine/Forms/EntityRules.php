<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine\Forms;

use Elerium;
use Nette;
use Doctrine\ORM\Mapping\ClassMetadata,
	Doctrine\DBAL\Types\Type,
	Doctrine\Common\Annotations\Reader;

class EntityRules extends Nette\Object
{
	/* Doctrine rules */
	const TYPE = 'type';
	const LENGTH = 'length';
	const NULLABLE = 'nullable';

	/** @var \Doctrine\ORM\Mapping\ClassMetadata */
	protected $classMetadata;

	/** @var \Doctrine\Common\Annotations\Reader|NULL */
	protected $reader;

	/** @var array */
	public static $rules = array(
		self::TYPE => array(__CLASS__, 'getType'),
		self::LENGTH => array(__CLASS__, 'getMaxLength'),
		self::NULLABLE => array(__CLASS__, 'getNullable')
	);

	/** @var array */
	protected static $types = array(
		Type::INTEGER => Form::INTEGER,
		Type::SMALLINT => Form::INTEGER,
		Type::BIGINT => Form::INTEGER,
		Type::FLOAT => Form::FLOAT,
		Type::DECIMAL => Form::FLOAT
	);

	/**
	 * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
	 * @param \Doctrine\Common\Annotations\Reader|NULL $annotationReader
	 */
	public function __construct(ClassMetadata $classMetadata, Reader $reader = NULL)
	{
		$this->classMetadata = $classMetadata;
		$this->reader = $reader;
	}

	/**
	 * @return array
	 */
	public function getAllRules()
	{
		$rules = array();
		foreach($this->classMetadata->getFieldNames() as $field)
		{
			$rules[$field] = $this->getRulesForField($field);
		}

		return $rules;
	}

	/**
	 * @param string $field
	 * @return array
	 * @throws \Elerium\InvalidArgumentException
	 */
	public function getRulesForField($field)
	{
		if(!$this->classMetadata->hasField($field))
		{
			throw new \Elerium\InvalidArgumentException("Field '$field' does not exist in entity '{$this->classMetadata->getName()}'.");
		}

		if(isset($this->reader))
		{
			$annotations = $this->reader->getPropertyAnnotations(new \ReflectionProperty($this->classMetadata->getName(), $field));
		}
		else
		{
			$annotations = array();
		}

		$rules = array();
		foreach($this->classMetadata->getFieldMapping($field) as $rule => $value)
		{
			if(isset(self::$rules[$rule]) && is_callable(self::$rules[$rule]))
			{
				$rule = call_user_func_array(self::$rules[$rule], array($value, $annotations));

				if($rule !== NULL)
				{
					$rules[] = $rule;
				}
			}
		}

		return $rules;
	}

	/**
	 * @param string $value
	 * @param array $annotations
	 * @return \Elerium\Doctrine\Forms\EntityRule|NULL
	 */
	protected static function getType($value, array $annotations)
	{
		if(!isset(self::$types[$value]))
		{
			if(Type::getType($value) && ($type = Type::getType($value)) && $type instanceof IFormType)
			{
				self::$types[$value] = $type->getFormType();
			}
			else
			{
				return NULL;
			}
		}

		return new EntityRule(self::$types[$value], NULL, self::getMessage($annotations, self::TYPE));
	}

	/**
	 * @param int $value
	 * @param array $annotations
	 * @return \Elerium\Doctrine\Forms\EntityRule|NULL
	 */
	protected static function getMaxLength($value, array $annotations)
	{
		if($value !== NULL)
		{
			return new EntityRule(Form::MAX_LENGTH, $value, self::getMessage($annotations, self::LENGTH));
		}
	}

	/**
	 * @param bool $value
	 * @param array $annotations
	 * @return \Elerium\Doctrine\Forms\EntityRule|NULL
	 */
	protected static function getNullable($value, array $annotations)
	{
		if($value === FALSE)
		{
			return new EntityRule(Form::FILLED, NULL, self::getMessage($annotations, self::NULLABLE));
		}
	}

	/**
	 * @param array $annotations
	 * @param string $operation
	 * @return mixed
	 */
	private static function getMessage(array $annotations, $operation)
	{
		foreach($annotations as $annotation)
		{
			if($annotation instanceof Mapping\Form)
			{
				foreach($annotation->messages as $op => $message)
				{
					if($op === $operation)
					{
						return $message;
					}
				}
			}
		}
	}
}