<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Timestampable\TimestampableListener;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findOrCreateFromAzure(AzureResourceOwner $owner, array $extraInfo = [])
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.azureId = :azureId')
            ->orWhere('u.email = :email')
            ->setParameters([
                'azureId' => $owner->getId(),
                'email' => $owner->getUpn()
            ])
            ->getQuery()
            ->getOneOrNullResult();

        $em = $this->getEntityManager();

        if ($user) {
            /** @var User $user */
            if (!$user->getAzureId()) {
                $user->setAzureId($owner->getId());
            }
            if (!$user->getIsActive()) {
                $user->setIsActive(true);
            }
            if (!empty($extraInfo)) {
                $user->setExtraInfo($extraInfo);
            }
            $em->persist($user);
            $em->flush();

            return $user;
        }

        $user = (new User())
            ->setAzureId($owner->getId())
            ->setEmail($owner->getUpn())
            ->setDisplayName($owner->getFirstName() . ' ' . $owner->getLastName())
            ->setLastLoginAt(new \DateTime())
        ;
        if (!empty($extraInfo)) {
            $user->setExtraInfo($extraInfo);
        }
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Update last login datetime
     *
     * @param User $user
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setLastLogin(User $user)
    {
        // Disable timestampable to avoid wrong updated date
        $this->disableTimestampable();
        $user->setLastLoginAt(new \DateTime());
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Temporary disable the event listeners of timestampable
     */
    private function disableTimestampable()
    {
        $evm = $this->_em->getEventManager();
        foreach ($evm->getListeners() as $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof TimestampableListener) {
                    $listenerInst = $listener;
                    $evm->removeEventListener(
                        ['prePersist','loadClassMetadata','onFlush'],
                        $listenerInst
                    );
                }
            }
        }
    }

    public function statUserByCountry()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id) as users', 'u.countryWorkplace as country')
            ->groupBy('u.countryWorkplace')
            ->orderBy('users', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function statLatestLogsIn(int $maxResults = 10)
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.lastLoginAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    public function getAdmins()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.groups', 'g')
            ->where('g.roles LIKE :role')
            ->setParameter('role', '%"ROLE_ADMIN"%')
            ->getQuery()
            ->getResult();
    }
}
