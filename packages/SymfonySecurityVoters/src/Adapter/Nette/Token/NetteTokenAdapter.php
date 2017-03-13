<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Adapter\Nette\Token;

use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symplify\SymfonySecurityVoters\Exception\MissingAttributeException;
use Symplify\SymfonySecurityVoters\Exception\NotImplementedException;

final class NetteTokenAdapter implements TokenInterface
{
    /**
     * @var User
     */
    private $user;

    public function __toString(): void
    {
        throw new NotImplementedException;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->user->getRoles();
    }

    public function getCredentials(): ?IIdentity
    {
        return $this->user->getIdentity();
    }

    /**
     * @return User|string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User|string $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function isAuthenticated(): bool
    {
        return $this->user->isLoggedIn();
    }

    /**
     * @param string $name
     */
    public function hasAttribute($name): bool
    {
        return isset($this->getAttributes()[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        if ($this->hasAttribute($name)) {
            return $this->getAttributes()[$name];
        }

        throw new MissingAttributeException(sprintf(
            'Attribute "%s" was not found. Pick one of: %s.',
            $name,
            implode(', ', array_keys($this->getAttributes()))
        ));
    }

    /**
     * @return mixed[]
     */
    public function getAttributes(): array
    {
        /** @var Identity $identity */
        $identity = $this->user->getIdentity();

        if (! is_array($identity->getData())) {
            return [$identity->getData()];
        }

        return $identity->getData();
    }

    /**
     * @param bool $isAuthenticated
     */
    public function setAuthenticated($isAuthenticated): void
    {
        throw new NotImplementedException;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): void
    {
        throw new NotImplementedException;
    }

    public function eraseCredentials(): void
    {
        throw new NotImplementedException;
    }

    /**
     * @param mixed[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        throw new NotImplementedException;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value): void
    {
        throw new NotImplementedException;
    }

    public function serialize(): void
    {
        throw new NotImplementedException;
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        throw new NotImplementedException;
    }
}
