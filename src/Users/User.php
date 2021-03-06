<?php

namespace Users;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\Strings;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @method string getPassword()
 * @method setPassword(string)
 */
class User
{

	use Identifier;
	use MagicAccessors;

	/**
	 * @ORM\Column(type="string", options={"comment":"User's email"})
	 * @var string
	 */
	private $email;

	/**
	 * @ORM\Column(type="string", options={"comment":"User's password"})
	 * @var string
	 */
	protected $password;

	/**
	 * @ORM\ManyToMany(targetEntity="Role", cascade={"persist"})
	 * @ORM\JoinTable(
	 *        joinColumns={@ORM\JoinColumn(name="user_id", onDelete="cascade")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="role")}
	 * )
	 * @var \Users\Role[]|ArrayCollection
	 */
	protected $roles;

	/**
	 * @ORM\Column(type="datetime", options={"comment":"Date of user account creation"})
	 * @var \DateTime
	 */
	private $createdAt;

	public function __construct($email)
	{
		$this->setEmail($email);
		$this->roles = new ArrayCollection();
		$this->createdAt = new \DateTime();
	}

	public function clearRoles()
	{
		$this->roles->clear();
	}

	public function setEmail($email)
	{
		$this->email = Strings::lower($email);
		return $this;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

}
