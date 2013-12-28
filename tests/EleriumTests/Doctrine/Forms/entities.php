<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine\Forms\Entity;

/**
 * @Entity
 */
class Simple
{
	/**
	 * @param int $id
	 * @param string $string
	 */
	public function __construct($id = NULL, $string = NULL)
	{
		$this->id = $id;
		$this->string = $string;
	}

	/**
	 * @Id
	 * @Column(type="integer")
	 */
	public $id;

	/**
	 * @Column(type="string")
	 */
	public $string;
}

/**
 * @Entity
 */
class Person
{

	/**
	 * @Id
	 * @Column(type="integer")
	 */
	public $id;

	/**
	 * @Column(type="string")
	 */
	public $name;

	/**
	 * @ManyToOne(targetEntity="Person", inversedBy="childrens")
	 * @JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	public $parent;

	/**
	 * @OneToMany(targetEntity="Person", mappedBy="parent")
	 */
	public $childrens;

	/**
	 * @param int $id
	 * @param string $name
	 */
	public function __construct($id = NULL, $name = NULL)
	{
		$this->id = $id;
		$this->name = $name;
	}
}

/**
 * @Entity
 */
class PersonDetail
{
	/**
	 * @Id
	 * @Column(type="integer")
	 * @Form(messages={"type"="Wrong type!"})
	 */
	public $id;

	/**
	 * @Column(type="string", nullable=FALSE, length=64)
	 * @Form(messages={"length"="Max length must be %d!"})
	 */
	public $name;

	/** @Column(type="integer", nullable=TRUE) */
	public $age;

	/** @Column(type="decimal", nullable=TRUE) */
	public $height;

	/** @Column(type="email", nullable=TRUE) */
	public $email;
}
