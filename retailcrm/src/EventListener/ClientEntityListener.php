<?php


namespace App\EventListener;

use App\Entity\Client;
use App\Entity\ClientСollectionInterface;
use App\Entity\Log;
use App\Service\LogService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use ReflectionClass;

class ClientEntityListener
{
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $changes = [
            'insert' => [],
            'update' => [],
            'delete' => []
        ];
        $client = null;

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Client) {
                $this->setCreateAndUpdateDates($entity, $em);
            }
            if ($entity instanceof ClientСollectionInterface) {
                $changes['insert'][] = $this->getChanges($entity, $em);
                $client = $this->getClientByCollectionItem($entity, $em);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Client) {
                $changes['update'][] = $this->getChanges($entity, $em);
                if (!$client) {
                    $client = $entity;
                }
            }
            if ($entity instanceof ClientСollectionInterface) {
                $changes['update'][] = $this->getChanges($entity, $em);
                if (!$client) {
                    $client = $this->getClientByCollectionItem($entity, $em);
                }
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof ClientСollectionInterface) {
                $changes['delete'][] = $this->getChanges($entity, $em);
                if (!$client) {
                    $client = $this->getClientByCollectionItem($entity, $em);
                }
            }
        }

        if ($client) {
            $this->logChanges($client, $changes, $em);
        }
    }

    private function setCreateAndUpdateDates(Client $client, EntityManager $entityManager)
    {
        $unitOfWork = $entityManager->getUnitOfWork();
        $date = new DateTime();
        $client->setCreateDate($date);
        $client->setUpdateDate($date);
        $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(get_class($client)), $client);
    }

    private function getChanges($entity, EntityManager $entityManager): array
    {
        $changes = $entityManager->getUnitOfWork()->getEntityChangeSet($entity);
        if (isset($changes['id'])) {
            unset($changes['id']);
        }
        $reflect = new ReflectionClass($entity);
        $shortName = $reflect->getShortName();
        return [$shortName => $changes];
    }

    private function getClientByCollectionItem(ClientСollectionInterface $entity, EntityManager $entityManager)
    {
        $unitOfWork = $entityManager->getUnitOfWork();
        $client = null;
        if ($entity->getClient() instanceof Client) {
            $client = $entity->getClient();
        } else {
            $changeSet = $unitOfWork->getEntityChangeSet($entity);
            $client = $changeSet['client'][0];
        }
        if ($client) {
            return $client;
        } else {
            return null;
        }
    }

    private function logChanges(Client $client, array $changes, EntityManager $entityManager)
    {
        $unitOfWork = $entityManager->getUnitOfWork();
        $date = new DateTime();
        $log = new Log();
        $log->setClient($client);
        $log->setData($changes);
        $log->setDate($date);

        $entityManager->persist($log);
        $unitOfWork->computeChangeSet($entityManager->getClassMetadata(get_class($log)), $log);

        $client->setUpdateDate($date);
        $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(get_class($client)), $client);
    }
}
