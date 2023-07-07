<?php

namespace Zitro\Blog\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllUsers(): array
    {
        return $this->findAll();
    }
}