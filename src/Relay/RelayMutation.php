<?php

namespace Dreamlabs\GraphQL\Relay;


use Exception;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Type\InputObject\InputObjectType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class RelayMutation
{

    /**
     * @param string                   $name
     *
     *
     * @throws \Exception
     */
    public static function buildMutation($name, array $args, AbstractObjectType|array $output, callable $resolveFunction): Field
    {
        if (!is_array($output) || (is_object($output) && !($output instanceof AbstractObjectType))) {
            throw new Exception('Output can be instance of AbstractObjectType or array of fields');
        }

        return new Field([
            'name'    => $name,
            'args'    => [
                new InputField([
                    'name' => 'input',
                    'type' => new NonNullType(new InputObjectType([
                        'name'   => ucfirst($name) . 'Input',
                        'fields' => array_merge(
                            $args,
                            [new InputField(['name' => 'clientMutationId', 'type' => new NonNullType(new StringType())])]
                        )
                    ]))
                ])
            ],
            'type'    => new ObjectType([
                'fields' => is_object($output) ? $output : array_merge(
                    $output,
                    [new Field(['name' => 'clientMutationId', 'type' => new NonNullType(new StringType())])]
                ),
                'name'   => ucfirst($name) . 'Payload'
            ]),
            'resolve' => function ($value, $args, ResolveInfo $info) use ($resolveFunction) {
                $resolveValue = $resolveFunction($value, $args['input'], $args, $info);

                if (is_object($resolveValue)) {
                    $resolveValue->clientMutationId = $args['input']['clientMutationId'];
                } elseif (is_array($resolveValue)) {
                    $resolveValue['clientMutationId'] = $args['input']['clientMutationId'];
                }

                return $resolveValue;
            }
        ]);
    }

}
