<?php

declare(strict_types=1);

namespace Api\Controller;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
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

    public function run(): void
    {
        $route = str_replace(Helper::getLinkPath(), '', $_SERVER['REDIRECT_URL']);
        $params = explode('/', $route);
        $entityId = $params[0] ?? false;
        $id = $params[1] ?? false;

        if ($entityId && $entityId !== 'schema') {
            $id = $id && ctype_digit($id) ? (int) $id : $id;
            $id ? $this->{strtolower($this->method)}($entityId, $id)
                : $this->{strtolower($this->method)}($entityId);
            return;
        }

        $this->schema($id ? $id : null);
    }

    private function schema(string $entityId = null)
    {
        $this->allowMethods(['GET']);
        // $user = $this->verifyJWT();

        if (!empty($entityId))
            $this->respond($this->getTables(true, $entityId));

        $this->respond($this->getTables());
    }

    private function get(string $entityId = null, int $id = null)
    {
        $this->allowMethods(['GET']);

        if (!empty($entityId)) {

            dump($entityId);
            dump($id);
            $this->respond('this is GET fn');
        }

        $this->throwError();
    }

    private function post(string $entityId = null)
    {
        $this->allowMethods(['POST']);
        // $user = $this->verifyJWT();

        if (!empty($entityId)) {

            dump($entityId);
            $this->respond('this is POST fn');
        }

        $this->throwError();
    }

    private function put(string $entityId = null, int $id = null)
    {
        $this->allowMethods(['PUT']);
        // $user = $this->verifyJWT();

        if (!empty($entityId) && !empty($id)) {

            dump($entityId);
            dump($id);
            $this->respond('this is PUT fn');
        }

        $this->throwError();
    }

    private function patch(string $entityId = null, int $id = null)
    {
        $this->allowMethods(['PATCH']);
        // $user = $this->verifyJWT();

        if (!empty($entityId) && !empty($id)) {

            dump($entityId);
            dump($id);
            $this->respond('this is PATCH fn');
        }

        $this->throwError();
    }

    private function delete(string $entityId = null, int $id = null)
    {
        $this->allowMethods(['DELETE']);
        // $user = $this->verifyJWT();

        if (!empty($entityId) && !empty($id)) {

            dump($entityId);
            dump($id);
            $this->respond('this is DELETE fn');
        }

        $this->throwError();
    }

    private function getTables(bool $columns = false, string $table = null)
    {
        $schM = $this->em->getConnection()->createSchemaManager();

        $tables = $schM->listTables();

        if ($table)
            $tables = [$schM->introspectTable($table)];

        $tableNames = [];
        foreach ($tables as $t) {
            $tName = $t->getName();

            if (str_contains($tName, 'doctrine_'))
                continue;

            $tableNames[$tName] = [
                'id' => $tName,
                // 'class' => $this->findClassByTableName($tName),
                'schema' => 'schema/' . $tName,
            ];

            if ($columns)
                $tableNames[$tName]['columns'] = $this->getColumns($t->getColumns());
        }

        return $tableNames;
    }

    /**
     * @param Column[] $columns
     */
    private function getColumns(array $columns): ?array
    {
        $cols = [];

        foreach ($columns as $c)
            $cols[] = [
                'name' => $c->getName(),
                'type' => array_search($c->getType()::class, Type::getTypesMap()),
                'length' => $c->getLength(),
                'required' => $c->getNotnull(),
                'disabled' => in_array($c->getName(), $this->disableds),
            ];

        usort($cols, function ($a, $b) {
            if ($a['name'] === 'id')
                return -1;
            if ($b['name'] === 'id')
                return 1;
            if (substr($a['name'], -3) === '_id' && substr($b['name'], -3) !== '_id')
                return -1;
            if (substr($a['name'], -3) !== '_id' && substr($b['name'], -3) === '_id')
                return 1;
            if (substr($a['name'], -3) === '_at' && substr($b['name'], -3) !== '_at')
                return 1;
            if (substr($a['name'], -3) !== '_at' && substr($b['name'], -3) === '_at')
                return -1;
            return 0;
        });

        foreach ($cols as &$c)
            $c['name'] = Helper::snakeToCamel($c['name']);

        return $cols;
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
