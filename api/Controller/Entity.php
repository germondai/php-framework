<?php

declare(strict_types=1);

namespace Api\Controller;

use Doctrine\ORM\AbstractQuery;
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
        // $user = $this->verifyJWT();

        if (!empty($entityClass))
            $this->respond($this->getTables(true, $entityClass));

        $this->respond($this->getTables());
    }

    private function get(string $entityClass = null, int $id = null)
    {
        $this->allowMethods(['GET']);

        if (!empty($entityClass)) {
            $cols = $this->getTables(true, $entityClass, true);
            $columnNames = array_column($cols, 'name');
            $columnCols = array_column($cols, 'col');

            function findColumnIndexByName($columns, $key)
            {
                foreach ($columns as $index => $column) {
                    if ($column['name'] === $key) {
                        return $index;
                    }
                }
                return false;
            }

            if (!empty($id)) {
                $entry = $this->em
                    ->getRepository($entityClass)
                    ->createQueryBuilder('e')
                    ->where('e.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
                    ->getArrayResult();

                foreach ($entry as &$e) {
                    foreach ($e as $key => $val) {
                        $index = findColumnIndexByName($cols, $key);

                        if ($index !== false)
                            unset($cols[$index]);
                        else
                            unset($e[$key]);
                    }

                    foreach ($cols as $c) {
                        if ($c['relation'] === 'multi') {
                            $ids = $this->em->getRepository($c['class'])->createQueryBuilder('c')
                                ->select('c.id')
                                ->where('c.' . $c['mappedBy'] . ' = :id')
                                ->setParameter('id', $e['id'])
                                ->getQuery()
                                ->getArrayResult();

                            foreach ($ids as &$a)
                                $a = $a['id'];

                            $e[$c['name']] = $ids;
                        } else {
                            $e[$c['name']] = $val;
                        }
                    }
                }

                if (!empty($entry))
                    $this->respond($this->process($entry)[0]);
                $this->throwError(404);
            }

            $query = $this->em->getRepository($entityClass)->createQueryBuilder('e');

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
                ->getArrayResult();

            $this->respond([
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'nextPage' => $nextPage,
                'previousPage' => $previousPage,
                'totalEntries' => $totalEntries,
                'result' => $this->process($records),
            ]);
        }

        $this->throwError();
    }

    private function post(string $entityClass = null)
    {
        $this->allowMethods(['POST']);
        // $user = $this->verifyJWT();

        if (!empty($entityClass)) {

            dump($entityClass);
            $this->respond('this is POST fn');
        }

        $this->throwError();
    }

    private function put(string $entityClass = null, int $id = null)
    {
        $this->allowMethods(['PUT']);
        // $user = $this->verifyJWT();

        if (!empty($entityClass) && !empty($id)) {

            dump($entityClass);
            dump($id);
            $this->respond('this is PUT fn');
        }

        $this->throwError();
    }

    private function patch(string $entityClass = null, int $id = null)
    {
        $this->allowMethods(['PATCH']);
        // $user = $this->verifyJWT();

        if (!empty($entityClass) && !empty($id)) {

            dump($entityClass);
            dump($id);
            $this->respond('this is PATCH fn');
        }

        $this->throwError();
    }

    private function delete(string $entityClass = null, int $id = null)
    {
        $this->allowMethods(['DELETE']);
        // $user = $this->verifyJWT();

        if (!empty($entityClass) && !empty($id)) {

            dump($entityClass);
            dump($id);
            $this->respond('this is DELETE fn');
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
                foreach ($m->fieldMappings as $field)
                    $cols[] = [
                        'name' => $field->fieldName,
                        'col' => $field->columnName,
                        'type' => $field->type,
                        'length' => $field->length,
                        'required' => !$field->nullable,
                        'disabled' => in_array($field->columnName, $this->disableds),
                    ];

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

                foreach ($cols as &$c)
                    unset($c['col']);

                if ($onlyColumns)
                    return $cols;
            }

            $tables[$name] = [
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
}
