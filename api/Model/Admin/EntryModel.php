<?php

declare(strict_types=1);

namespace Api\Model\Admin;

use Api\ApiController;
use Doctrine\ORM\Mapping\ClassMetadata;

class EntryModel extends ApiController
{
    private function findClassByTableName(string $tableName): string
    {
        $tableToEntityClassMap = [];

        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata)
            if ($metadata instanceof ClassMetadata)
                $tableToEntityClassMap[$metadata->getTableName()] = $metadata->getName();

        return $tableToEntityClassMap[$tableName];
    }

    public function actionGetAll()
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();
        $this->requireParams(['entityId']);

        $eId = $this->params['entityId'] ?? false;
        $eClass = $this->findClassByTableName($eId);

        $entries = $this->em->createQueryBuilder()
            ->select('e')
            ->from($eClass, 'e')
            ->getQuery()
            ->getArrayResult();

        $secrets = ['password'];

        foreach ($entries as &$entry) {
            foreach ($entry as $k => &$v) {
                if (in_array($k, $secrets))
                    $v = 'SECRET';
                elseif ($v instanceof \DateTime)
                    $v = $v->format('j. n. Y - H:i:s');
            }
        }

        return $entries;
    }

    public function actionGet()
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();
        $this->requireParams(['entityId', 'id']);

        $eId = $this->params['entityId'] ?? false;
        $eClass = $this->findClassByTableName($eId);
        $id = $this->params['id'] ?? false;

        $entry = $this->em->createQueryBuilder()
            ->select('e')
            ->from($eClass, 'e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $entry;
    }
}
