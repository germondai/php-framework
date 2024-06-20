<?php

declare(strict_types=1);

namespace Api\Controller;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Utils\Helper;

class Entity extends Api
{
    private array $tNames = [
        'users' => 'Uživatelé',
        'articles' => 'Články',
        'books' => 'Knihy',
        'events' => 'Kurzy',
        'medias' => 'Media',
    ];
    private array $disableds = ['id', 'created_at', 'updated_at', 'deleted_at'];
    private array $secrets = ['password'];

    private int $page = 1;
    private int $perPage = 10;

    public function run(): void
    {
        $page = $this->params['page'] ?? '';
        $perPage = $this->params['per_page'] ?? $this->params['perPage'] ?? $this->params['perpage'] ?? '';
        $page = ctype_digit($page) ? (int) $page : null;
        $perPage = ctype_digit($perPage) ? (int) $perPage : null;
        $this->page = $page ?? $this->page;
        $this->perPage = $perPage ?? $this->perPage;

        unset($this->params['page'], $this->params['per_page'], $this->params['perPage'], $this->params['perpage']);

        $route = str_replace(Helper::getLinkPath(), '', $_SERVER['REDIRECT_URL']);
        $params = explode('/', $route);
        $entityId = $params[0] ?? false;
        $entityClass = null;
        $id = $params[1] ?? false;

        if ($entityId && $entityId !== 'schema') {
            if ($entityId === 'model')
                parent::run();

            $entityClass = $this->findClassByTableName($entityId);

            $id = $id && ctype_digit($id) ? (int) $id : $id;
            $id ? $this->{strtolower($this->method)}($entityClass, $id)
                : $this->{strtolower($this->method)}($entityClass);

            return;
        }

        if (!empty($id))
            $entityClass = $this->findClassByTableName($id);
        $this->schema($entityClass ?? null);
    }

    private function schema(string $entityClass = null)
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();

        if (!empty($entityClass))
            $this->respond($this->getTables(true, $entityClass));

        $this->respond($this->getTables());
    }

    private function get(string $entityClass = null, int $id = null)
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();

        if (!empty($entityClass)) {
            $query = $this->em->getRepository($entityClass)->createQueryBuilder('e');

            // show soft deleted - later add only for admin/fullAccess role
            if (!isset($this->params['all']))
                $query = $query->where('e.deletedAt IS NULL');

            if (!empty($id)) {
                $entry = $query
                    ->andWhere('e.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
                    ->getArrayResult();

                if (!empty($entry))
                    $this->respond($this->process($entityClass, $entry)[0]);
                $this->throwError(404);
            }

            $paginatior = new Paginator($query);

            $totalEntries = $paginatior->count();
            $page = $this->page;
            $perPage = $this->perPage;
            $totalPages = (int) ceil($totalEntries / $perPage);
            $nextPage = (($page < $totalPages) ? $page + 1 : $totalPages);
            $previousPage = (($page > 1) ? $page - 1 : 1);

            $records = $paginatior->getQuery()
                ->setFirstResult($perPage * ($page - 1))
                ->setMaxResults($perPage)
                ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
                ->getArrayResult();

            $this->respond([
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'nextPage' => $nextPage,
                'previousPage' => $previousPage,
                'totalEntries' => $totalEntries,
                'result' => $this->process($entityClass, $records),
            ]);
        }

        $this->throwError();
    }

    private function post(string $entityClass = null)
    {
        $this->allowMethods(['POST']);
        $user = $this->verifyJWT();

        if (!empty($entityClass)) {
            $cols = $this->getTables(true, $entityClass, true);

            $insertableCols = [];
            $requireParams = [];
            foreach ($cols as $c) {
                $dis = $c['disabled'] ?? false;
                $rel = $c['relation'] ?? false;
                $req = $c['required'] ?? true;
                if ($dis === true || $rel === 'multi' || $c['name'] === 'file')
                    continue;

                $insertableCols[] = $c;

                if ($req)
                    $requireParams[] = $c['name'];
            }

            $this->requireParams($requireParams);

            if ($entityClass === 'Api\Entity\Media') {
                if (empty($_FILES['file']))
                    $this->throwError();
                $entity = Helper::uploadFile($_FILES['file'], Helper::getBasePath() . 'public/uploads/', 80);
            }

            $entity = !empty($entity) ? $entity : new $entityClass();
            foreach ($insertableCols as $c) {
                $name = $c['name'];
                $value = $this->params[$name];
                $rel = $c['relation'] ?? false;
                $class = $c['class'] ?? false;

                if ($rel && $class)
                    $value = $this->em->getRepository($class)->findOneBy(['id' => $value]);

                $setter = 'set' . ucfirst($name);
                if (method_exists($entity, $setter))
                    $entity->$setter($value);
            }

            if ($entity) {
                $this->em->persist($entity);
                $this->em->flush();

                $this->respond(['success' => true]);
            }
        }

        $this->throwError();
    }

    private function put(string $entityClass = null, int $id = null)
    {
        $this->allowMethods(['PUT', 'PATCH']);
        $user = $this->verifyJWT();
        $this->patch($entityClass, $id);
    }

    private function patch(string $entityClass = null, int $id = null)
    {
        $this->allowMethods(['PATCH', 'PUT']);
        $user = $this->verifyJWT();

        if (!empty($entityClass) && !empty($id)) {
            $entity = $this->em->getRepository($entityClass)->findOneBy(['id' => $id]);

            foreach ($this->params as $key => $val) {
                $setter = 'set' . ucfirst($key);

                if (method_exists($entity, $setter)) {
                    $refClass = new \ReflectionClass($entity);
                    $params = $refClass->getMethod($setter)->getParameters();
                    $paramType = $params[0]->getType()->getName();

                    if (str_contains($paramType, 'Api\Entity\\'))
                        $val = $this->em->getRepository($paramType)->findOneBy(['id' => $val]);

                    $entity->$setter($val);
                } else
                    $this->throwError();
                // add error track or smth
            }

            if ($entity) {
                $this->em->flush();
                $this->respond(['success' => true]);
            }
        }

        $this->throwError();
    }

    private function delete(string $entityClass = null, int $id = null)
    {
        $this->allowMethods(['DELETE']);
        $user = $this->verifyJWT();

        if (!empty($entityClass) && !empty($id)) {
            $entity = $this->em->getRepository($entityClass)->findOneBy(['id' => $id]);

            // custom delete conditions
            if ($entity instanceof \Api\Entity\User)
                $entity->setDeletedAt(new \DateTime);
            elseif ($entity instanceof \Api\Entity\Media) {
                if (unlink($entity->getPath()))
                    $this->em->remove($entity);
            } else
                $this->em->remove($entity);

            if ($entity) {
                $this->em->flush();
                $this->respond(['success' => true]);
            }
        }

        $this->throwError();
    }

    private function getTables(bool $columns = false, string $class = null, bool $onlyColumns = false)
    {
        $mf = $this->em->getMetadataFactory();
        $metadata = !empty($class) ? [$mf->getMetadataFor($class)] : $mf->getAllMetadata();

        $tables = [];
        foreach ($metadata as $m) {
            $name = $m->table['name'];
            $tName = $this->tNames[$name] ?? $name;

            if ($name === 'base')
                continue;

            $cols = [];
            if ($columns) {
                function findType(string $name, string $type)
                {
                    $names = [
                        'email' => 'email',
                        'password' => 'password'
                    ];
                    if (in_array($name, $names))
                        return $names[$name];

                    $types = [
                        'string' => 'text',
                        'integer' => 'number',
                        'text' => 'textarea',
                        'datetime' => 'datetime-local',
                    ];
                    return $types[$type] ?? 'text';
                }

                foreach ($m->fieldMappings as $field) {
                    // hide specific cols
                    if ($name === 'medias' && in_array($field->fieldName, ['path', 'extension']))
                        continue;

                    $cols[] = [
                        'name' => $field->fieldName,
                        'col' => $field->columnName,
                        'type' => $field->type,
                        'form' => findType($field->fieldName, $field->type),
                        'length' => $field->length,
                        'required' => !$field->nullable,
                        'disabled' => in_array($field->columnName, $this->disableds),
                    ];
                }

                foreach ($m->associationMappings as $assoc)
                    $cols[] = [
                        'name' => $assoc->fieldName,
                        'col' => $assoc->joinColumns[0]->name ?? '',
                        'relation' => $assoc->isManyToOne() ? 'single' : 'multi',
                        'schema' => 'schema/' . $mf->getMetadataFor($assoc->targetEntity)->table['name'],
                        'class' => $assoc->targetEntity,
                        'mappedBy' => $assoc->mappedBy ?? null,
                        'inversedBy' => $assoc->inversedBy ?? null,
                    ];

                usort($cols, function ($a, $b) {
                    if ($a['col'] === 'id')
                        return -1;
                    if ($b['col'] === 'id')
                        return 1;
                    if (substr($a['col'], -3) === '_id' && substr($b['col'], -3) !== '_id')
                        return -1;
                    if (substr($a['col'], -3) !== '_id' && substr($b['col'], -3) === '_id')
                        return 1;
                    if (substr($a['col'], -3) === '_at' && substr($b['col'], -3) !== '_at')
                        return 1;
                    if (substr($a['col'], -3) !== '_at' && substr($b['col'], -3) === '_at')
                        return -1;
                    return 0;
                });

                if ($name === 'medias') {
                    foreach ($cols as &$c)
                        if (
                            !in_array($c['name'], ['title', 'alt', 'description', 'credit'])
                            && isset($c['disabled'])
                        )
                            $c['disabled'] = true;

                    $cols[] = [
                        'name' => 'file',
                        'col' => 'file',
                        'type' => 'file',
                        'form' => 'file',
                        'length' => null,
                        'required' => true,
                        'disabled' => false,
                    ];
                }

                foreach ($cols as &$c)
                    unset($c['col']);

                if ($onlyColumns)
                    return $cols;
            }

            $tables[] = [
                'id' => $name,
                'name' => $tName,
                'schema' => 'schema/' . $name,
                'columns' => $cols,
            ];
        }

        return $tables;
    }

    private function findClassByTableName(string $tableName): string
    {
        $tableToEntityClassMap = [];

        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata)
            if ($metadata instanceof ClassMetadata)
                $tableToEntityClassMap[$metadata->getTableName()] = $metadata->getName();

        return $tableToEntityClassMap[$tableName];
    }

    private function process(string $entityClass, array $entries)
    {
        $cols = $this->getTables(true, $entityClass, true);

        function findColumnIndexByName($columns, $key)
        {
            foreach ($columns as $index => $column)
                if ($column['name'] === $key)
                    return $index;

            return false;
        }

        foreach ($entries as &$e) {
            $tempCols = $cols;

            // entry value filters
            foreach ($e as $k => &$v) {
                if ($entityClass === 'Api\Entity\Media' && $k === 'size')
                    $v = Helper::formatBytes($v);

                if (in_array($k, $this->secrets))
                    $v = 'SECRET';
                elseif ($v instanceof \DateTime)
                    $v = $v->format('j. n. Y - H:i:s');
            }

            foreach ($e as $key => $val) {
                $index = findColumnIndexByName($tempCols, $key);

                if ($index !== false)
                    unset($tempCols[$index]);
                else
                    unset($e[$key]);
            }

            foreach ($tempCols as $c) {
                if (!empty($c['relation']) && $c['relation'] === 'multi') {
                    $ids = $this->em->getRepository($c['class'])->createQueryBuilder('c')
                        ->select('c.id')
                        ->where('c.' . $c['mappedBy'] . ' = :id')
                        ->setParameter('id', $e['id'])
                        ->getQuery()
                        ->getArrayResult();

                    foreach ($ids as &$a)
                        $a = $a['id'];

                    $e[$c['name']] = $ids;
                } else
                    $e[$c['name']] = $val;
            }
        }

        return $entries;
    }
}
