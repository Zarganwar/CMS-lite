<?php

namespace Navigation;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * @ORM\Entity
 *
 * @method setName(string $name)
 * @method string getName()
 * @method setIdentifier(string $identifier)
 */
class Navigation
{

	use Identifier;
	use MagicAccessors;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @ORM\ManyToMany(targetEntity="NavigationItem", mappedBy="navigations", cascade={"persist"})
	 * @var NavigationItem[]|\Doctrine\Common\Collections\ArrayCollection
	 */
	protected $items;

	/**
	 * @ORM\Column(type="string", unique=TRUE)
	 */
	protected $identifier;

}
