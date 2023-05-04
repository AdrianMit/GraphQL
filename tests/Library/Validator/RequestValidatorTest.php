<?php

namespace Dreamlabs\Tests\Library\Validator;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Request;
use Dreamlabs\GraphQL\Parser\Ast\Argument;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Dreamlabs\GraphQL\Parser\Ast\Field;
use Dreamlabs\GraphQL\Parser\Ast\Fragment;
use Dreamlabs\GraphQL\Parser\Ast\FragmentReference;
use Dreamlabs\GraphQL\Parser\Ast\Query;
use Dreamlabs\GraphQL\Parser\Location;
use Dreamlabs\GraphQL\Validator\RequestValidator\RequestValidator;

class RequestValidatorTest extends TestCase
{

    /**
     * @expectedException \Dreamlabs\GraphQL\Exception\Parser\InvalidRequestException
     * @dataProvider invalidRequestProvider
     */
    public function testInvalidRequests(Request $request): void
    {
        (new RequestValidator())->validate($request);
    }

    public function invalidRequestProvider()
    {
        $variable1 = (new Variable('test', 'Int', false, false, new Location(1, 1)))->setUsed(true);
        $variable2 = (new Variable('test2', 'Int', false, false, new Location(1, 1)))->setUsed(true);
        $variable3 = (new Variable('test3', 'Int', false, false, new Location(1, 1)))->setUsed(false);

        return [
            [
                new Request([
                    'queries'            => [
                        new Query('test', null, [], [
                            new FragmentReference('reference', new Location(1, 1))
                        ], [], new Location(1, 1))
                    ],
                    'fragmentReferences' => [
                        new FragmentReference('reference', new Location(1, 1))
                    ]
                ])
            ],
            [
                new Request([
                    'queries'            => [
                        new Query('test', null, [], [
                            new FragmentReference('reference', new Location(1, 1)),
                            new FragmentReference('reference2', new Location(1, 1)),
                        ], [], new Location(1, 1))
                    ],
                    'fragments'          => [
                        new Fragment('reference', 'TestType', [], [], new Location(1, 1))
                    ],
                    'fragmentReferences' => [
                        new FragmentReference('reference', new Location(1, 1)),
                        new FragmentReference('reference2', new Location(1, 1))
                    ]
                ])
            ],
            [
                new Request([
                    'queries'            => [
                        new Query('test', null, [], [
                            new FragmentReference('reference', new Location(1, 1)),
                        ], [], new Location(1, 1))
                    ],
                    'fragments'          => [
                        new Fragment('reference', 'TestType', [], [], new Location(1, 1)),
                        new Fragment('reference2', 'TestType', [], [], new Location(1, 1))
                    ],
                    'fragmentReferences' => [
                        new FragmentReference('reference', new Location(1, 1))
                    ]
                ])
            ],
            [
                new Request([
                    'queries'            => [
                        new Query('test', null,
                            [
                                new Argument('test', new VariableReference('test', new Location(1, 1)), new Location(1, 1))
                            ],
                            [
                                new Field('test', null, [], [], new Location(1, 1))
                            ],
                            [],
                            new Location(1, 1)
                        )
                    ],
                    'variableReferences' => [
                        new VariableReference('test', new Location(1, 1))
                    ]
                ], ['test' => 1])
            ],
            [
                new Request([
                    'queries'            => [
                        new Query('test', null, [
                            new Argument('test', new VariableReference('test', new Location(1, 1), $variable1), new Location(1, 1)),
                            new Argument('test2', new VariableReference('test2', new Location(1, 1), $variable2), new Location(1, 1)),
                        ], [
                            new Field('test', null, [], [], new Location(1, 1))
                        ], [], new Location(1,1))
                    ],
                    'variables'          => [
                        $variable1,
                        $variable2,
                        $variable3
                    ],
                    'variableReferences' => [
                        new VariableReference('test', new Location(1, 1), $variable1),
                        new VariableReference('test2', new Location(1, 1), $variable2)
                    ]
                ], ['test' => 1, 'test2' => 2])
            ]
        ];
    }

}
