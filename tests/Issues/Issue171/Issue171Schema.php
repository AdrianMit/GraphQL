<?php
namespace Dreamlabs\Tests\Issues\Issue171;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\Enum\AbstractEnumType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;

class Issue171Schema extends AbstractSchema
{
    public function build(SchemaConfig $config): void
    {
        $config->getQuery()->addField(
            'plan',
            [
                'type' => new PlanType(),
            ]
        );
    }
}

class PlanType extends AbstractObjectType
{
    public function build(ObjectTypeConfig $config): void
    {
        $config->addField('kpi_status', [
            'type' => new KpiStatusType(),
        ]);
    }
}

class KpiStatusType extends AbstractEnumType
{
    public function getValues()
    {
        return [
            [
                'name'              => 'BAD',
                'value'             => 'Bad',
            ],
            [
                'name'              => 'GOOD',
                'value'             => 'Good',
            ],
            [
                'name'              => 'WARNING',
                'value'             => 'Warning',
            ]
        ];
    }
}
