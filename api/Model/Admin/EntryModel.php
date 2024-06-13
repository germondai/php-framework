<?php

declare(strict_types=1);

namespace Api\Model\Admin;

use Api\ApiController;
use Api\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadata;

class EntryModel extends ApiController
{
    private array $secrets = ['password'];

    private function process(array $entries)
    {
        foreach ($entries as &$entry) {
            foreach ($entry as $k => &$v) {
                if (in_array($k, $this->secrets))
                    $v = 'SECRET';
                elseif ($v instanceof \DateTime)
                    $v = $v->format('j. n. Y - H:i:s');
            }
        }

        return $entries;
    }

    private function findClassByTableName(string $tableName): string
    {
        $tableToEntityClassMap = [];

        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata)
            if ($metadata instanceof ClassMetadata)
                $tableToEntityClassMap[$metadata->getTableName()] = $metadata->getName();

        return $tableToEntityClassMap[$tableName];
    }

    public function findWithRelations(string $class, int $id = null)
    {
        $metadata = $this->em->getClassMetadata($class);
        $qb = $this->em->createQueryBuilder();
        $qb = $qb->select('a')->from($class, 'a');
        if ($id)
            $qb = $qb->where('a.id = :id')->setParameter('id', $id);
        $qb = $qb->getQuery()->getResult();

        $extractedResults = [];
        foreach ($qb as $result) {
            $extractedResult = [];
            foreach ($metadata->associationMappings as $alias => $mapping) {
                $relationName = $mapping['fieldName'];
                $relation = $result->{'get' . ucfirst($relationName)}();
                if ($relation instanceof Collection) {
                    // ! Uncomment to Map IDs of each Entry in Collections
                    // $extractedResult[$relationName] = [];
                    // foreach ($relation as $s)
                    //     $extractedResult[$relationName][] = $s->getId();
                } else
                    $extractedResult[$relationName . 'Id'] = $relation->getId();
            }

            $entityArray = [];
            foreach ($metadata->getFieldNames() as $fieldName)
                $entityArray[$fieldName] = $result->{'get' . ucfirst($fieldName)}();

            $extractedResults[] = array_merge($entityArray, $extractedResult);
        }

        return $extractedResults;
    }

    public function actionGetAll()
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();
        $this->requireParams(['entityId']);

        $eId = $this->params['entityId'] ?? false;
        $eClass = $this->findClassByTableName($eId);

        $entries = $this->findWithRelations($eClass);
        return $this->process($entries);
    }

    public function actionGet()
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();
        $this->requireParams(['entityId', 'id']);

        $eId = $this->params['entityId'] ?? false;
        $eClass = $this->findClassByTableName($eId);
        $id = $this->params['id'] ?? false;

        $entry = $this->findWithRelations($eClass, !empty($id) ? (int) $id : -1);
        return $this->process($entry)[0];
    }

    public function actionCreate()
    {
        $this->allowMethods(['POST']);
        $user = $this->verifyJWT();
        $this->requireParams(['entityId', 'id']);

        $eId = $this->params['entityId'] ?? false;
        $eClass = $this->findClassByTableName($eId);
        $id = $this->params['id'] ?? false;

        // $new = new $eClass();

        // Sample data
        $data = [
            'class' => 'Api\Entity\Article', // Note the namespace correction if needed
            'title' => 'Title',
            'content' => 'Content',
            'user_id' => 3
        ];

        // Extract class name and user ID
        $className = $data['class'];
        $userId = $data['user_id'];

        // Find the User entity
        $user = $this->em->getRepository(User::class)->find($userId);
        if (!$user)
            $this->throwError(400, 'No user found for id ' . $userId);


        // Create a new instance of the Article entity
        if (!class_exists($className))
            throw new \Exception('Class ' . $className . ' does not exist');

        $entity = new $className();

        // dump($entity);

        // Set the properties dynamically
        foreach ($data as $property => $value) {
            if ($property !== 'class' && $property !== 'user_id') {
                $setter = 'set' . ucfirst($property);
                if (method_exists($entity, $setter)) {
                    $entity->$setter($value);
                }
            }
        }

        // dump($entity);

        // Set the User relationship
        $entity->setUser($user);
        // dumpe($entity);

        // Persist and flush
        $this->em->persist($entity);
        $this->em->flush();

        return;
    }
}
