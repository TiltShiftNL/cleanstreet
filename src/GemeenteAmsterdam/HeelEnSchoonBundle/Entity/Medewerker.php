<?php
namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Medewerker implements AdvancedUserInterface
{
    /**
     * @var string
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=255)
     */
    private $naam;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotNull
     * @Assert\Type("boolean")
     */
    private $actief;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $admin;

    /**
     * Non mapped!
     * @var string
     */
    public $plainPassword;

    public function __construct()
    {
        $this->actief = true;
        $this->admin = false;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set naam
     *
     * @param string $naam
     *
     * @return Medewerker
     */
    public function setNaam($naam)
    {
        $this->naam = $naam;

        return $this;
    }

    /**
     * Get naam
     *
     * @return string
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * Set actief
     *
     * @param boolean $actief
     *
     * @return Medewerker
     */
    public function setActief($actief)
    {
        $this->actief = $actief;

        return $this;
    }

    /**
     * Get actief
     *
     * @return boolean
     */
    public function getActief()
    {
        return $this->actief;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getNaam();
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getRoles()
     */
    public function getRoles()
    {
        if ($this->isAdmin()) {
            return ['ROLE_USER', 'ROLE_ADMIN'];
        }
        return ['ROLE_USER'];
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getPassword()
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getSalt()
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getUsername()
     */
    public function getUsername()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::eraseCredentials()
     */
    public function eraseCredentials()
    {
        //
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->getActief();
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @Assert\Callback
     */
    public function isPasswordSet(ExecutionContextInterface $context)
    {
        if ($this->password === '' || $this->password === null) {
            $context
                ->buildViolation('Voer een wachtwoord in')
                ->atPath('plainPassword')
                ->addViolation();
        }
    }
}
