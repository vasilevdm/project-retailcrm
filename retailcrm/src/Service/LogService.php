<?php


namespace App\Service;


use Doctrine\ORM\Event\OnFlushEventArgs;

class LogService
{
    public $em;
    public $uow;
    public $entity;

    public function set(OnFlushEventArgs $eventArgs) {
        $this->em = $eventArgs->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();
        $this->entity = $this->uow->getScheduledEntityInsertions();
    }
}
