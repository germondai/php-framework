<?php

declare(strict_types=1);

namespace Api\Model\Admin;

use Api\ApiController;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Utils\Helper;

class EntityModel extends ApiController
{
    private array $tNames = [
        'users' => 'Uživatelé',
        'articles' => 'Články',
        'books' => 'Knihy',
        'events' => 'Kurzy',
    ];

    /**
     * @param Column[] $columns
     */
    private function getColumns(array $columns): ?array
    {
        $cols = [];

        $disableds = ['id', 'created_at', 'updated_at', 'deleted_at'];

        foreach ($columns as $c)
            $cols[] = [
                'name' => $c->getName(),
                'type' => array_search($c->getType()::class, Type::getTypesMap()),
                'length' => $c->getLength(),
                'required' => $c->getNotnull(),
                'disabled' => in_array($c->getName(), $disableds),
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

    private function getTables(bool $columns = false, string $table = null)
    {
        $schM = $this->em->getConnection()->createSchemaManager();

        if ($table)
            return $this->getColumns($schM->listTableColumns($table));

        $tables = $schM->listTables();

        $tablesWcolumns = [];
        $tableNames = [];
        foreach ($tables as $t) {
            $tName = $t->getName();

            if (str_contains($tName, 'doctrine_'))
                continue;

            $tableNames[] = $tName;

            if ($columns)
                $tablesWcolumns[$tName][] = $this->getColumns($t->getColumns());
        }

        if ($columns)
            return $tablesWcolumns;

        return $tableNames;
    }

    public function actionGetAll()
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();

        $tables = $this->getTables();

        $output = [];
        foreach ($tables as $t) {
            $output[] = [
                'id' => $t,
                'name' => $this->tNames[$t] ?? $t,
                'columns' => []
            ];
        }

        return $output;
    }

    public function actionGet()
    {
        $this->allowMethods(['GET']);
        $this->requireParams(['entity']);
        $user = $this->verifyJWT();

        $entity = $this->params['entity'];

        return [
            'id' => $entity,
            'name' => $this->tNames[$entity] ?? $entity,
            'columns' => $this->getTables(true, $this->params['entity'])
        ];
    }
}