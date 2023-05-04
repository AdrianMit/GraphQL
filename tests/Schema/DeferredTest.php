<?php

namespace Dreamlabs\Tests\Schema;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Execution\DeferredResolver;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

/**
 * Interface definition for a database service that will be mocked.
 */
interface DeferredDatabase
{

    /**
     * Retrieve a list of users by their id.
     *
     * @param string[] $ids
     *   The user ids.
     *
     * @return array
     *   A 2-dimensional array of user objects, keyed by their id.
     */
    public function query(array $ids);
}

/**
 * Buffers queries until they are actually required.
 */
class DeferredQueryBuffer
{

    protected $buffer = [];

    protected $results = [];

    public function __construct(protected DeferredDatabase $database)
    {
    }

    public function add(array $ids)
    {
        $key = md5(serialize($ids));
        $this->buffer[$key] = $ids;

        return fn() => $this->fetch($key);
    }

    protected function fetch($resultId)
    {
        if (!array_key_exists($resultId, $this->results)) {
            $query = array_unique(
              array_reduce($this->buffer, 'array_merge', [])
            );
            sort($query);
            $result = $this->database->query($query);
            foreach ($this->buffer as $index => $query) {
                foreach ($query as $id) {
                    $this->results[$index][$id] = $result[$id];
                }
                unset($this->buffer[$index]);
            }
        }

        return $this->results[$resultId];
    }
}

class DeferredUserType extends AbstractObjectType
{

    public function __construct(protected DeferredQueryBuffer $database)
    {
        parent::__construct();
    }


    public function build(ObjectTypeConfig $config): void
    {
        $config->addField(
          new Field(
            [
              'name' => 'name',
              'type' => new StringType(),
              'resolve' => fn($value) => $value['name'],
            ]
          )
        );

        $config->addField(
          new Field(
            [
              'name' => 'friends',
              'type' => new ListType(new DeferredUserType($this->database)),
              'resolve' => fn($value): DeferredResolver => new DeferredResolver(
                $this->database->add($value['friends'])
              ),
            ]
          )
        );

        $config->addField(
          new Field(
            [
              'name' => 'foes',
              'type' => new ListType(new DeferredUserType($this->database)),
              'resolve' => fn($value): DeferredResolver => new DeferredResolver(
                $this->database->add($value['foes'])
              ),
            ]
          )
        );
    }
}

class DeferredSchema extends AbstractSchema
{

    public function __construct(DeferredQueryBuffer $buffer)
    {
        $usersField = new Field(
          [
            'name' => 'users',
            'type' => new ListType(new DeferredUserType($buffer)),
            'resolve' => fn($value, $args): DeferredResolver => new DeferredResolver($buffer->add($args['ids'])),
          ]
        );

        $usersField->addArgument(
          'ids',
          [
            'type' => new ListType(new StringType()),
          ]
        );
        parent::__construct(
          [
            'query' => new ObjectType(
              [
                'name' => 'RootQuery',
                'fields' => [$usersField],
              ]
            ),
          ]
        );
    }


    public function build(SchemaConfig $config): void
    {
    }

}


/**
 * Test the deferred resolving under different circumstances.
 */
class DeferredTest extends TestCase
{

    /**
     * @var Processor
     */
    protected $processor;

    protected function query($query, DeferredQueryBuffer $buffer)
    {
        $processor = new Processor(new DeferredSchema($buffer));
        $processor->processPayload($query, []);

        return $processor->getResponseData();
    }


    /**
     * Test a simple single deferred field.
     */
    public function testSingleResolve(): void
    {
        $query = 'query {
          users(ids: ["a", "b"]) {
            name
          }
        }';
        $database = $this->prophesize(DeferredDatabase::class);

        $database->query(['a', 'b'])->willReturn(
          [
            'a' => ['id' => 'a', 'name' => 'User A'],
            'b' => ['id' => 'b', 'name' => 'User B'],
          ]
        )->shouldBeCalledTimes(1);

        $result = $this->query(
          $query,
          new DeferredQueryBuffer($database->reveal())
        );

        $database->checkProphecyMethodsPredictions();

        $this->assertEquals(
          [
            'users' => [
              ['name' => 'User A'],
              ['name' => 'User B'],
            ],
          ],
          $result['data'],
          'Retrieved correct data.'
        );
    }

    /**
     * Test if multiple calls to the same field result in a single query.
     */
    public function testMultiResolve(): void
    {

        $query = 'query {
          a:users(ids: ["a"]) {
            name
          }
          b:users(ids: ["b"]) {
            name
          }
        }';

        $database = $this->prophesize(DeferredDatabase::class);

        $database->query(['a', 'b'])->willReturn(
          [
            'a' => ['id' => 'a', 'name' => 'User A'],
            'b' => ['id' => 'b', 'name' => 'User B'],
          ]
        )->shouldBeCalledTimes(1);

        $result = $this->query(
          $query,
          new DeferredQueryBuffer($database->reveal())
        );

        $database->checkProphecyMethodsPredictions();

        $this->assertEquals(
          [
            'a' => [
              ['name' => 'User A'],
            ],
            'b' => [
              ['name' => 'User B'],
            ],
          ],
          $result['data'],
          'Retrieved correct data.'
        );

    }

    /**
     * Test if recursive deferred resolvers work properly.
     */
    public function testRecursiveResolve(): void
    {

        $query = 'query {
          a:users(ids: ["a"]) {
            name
            friends {
              name
            }
          }
        }';

        $database = $this->prophesize(DeferredDatabase::class);

        $database->query(['a'])->willReturn(
          [
            'a' => ['id' => 'a', 'name' => 'User A', 'friends' => ['b', 'c']],
          ]
        )->shouldBeCalledTimes(1);

        $database->query(['b', 'c'])->willReturn(
          [
            'b' => ['id' => 'b', 'name' => 'User B'],
            'c' => ['id' => 'c', 'name' => 'User C'],
          ]
        );

        $result = $this->query(
          $query,
          new DeferredQueryBuffer($database->reveal())
        );

        $database->checkProphecyMethodsPredictions();

        $this->assertEquals(
          [
            'a' => [
              [
                'name' => 'User A',
                'friends' => [
                  ['name' => 'User B'],
                  ['name' => 'User C'],
                ],
              ],
            ],
          ],
          $result['data'],
          'Retrieved correct data.'
        );
    }

    /**
     * Test if multiple deferred resolvers are optimized into two queries.
     */
    public function testMultiRecursiveResolve(): void
    {

        $query = 'query {
          a:users(ids: ["a"]) {
            name
            friends {
              name
            }
            foes {
              name
            }
          }
          b:users(ids: ["b"]) {
            name
            friends {
              name
            }
            foes {
              name
            }
          }
        }';

        $database = $this->prophesize(DeferredDatabase::class);

        $database->query(['a', 'b'])->willReturn(
          [
            'a' => [
              'id' => 'a',
              'name' => 'User A',
              'friends' => ['b', 'c'],
              'foes' => ['d', 'e'],
            ],
            'b' => [
              'id' => 'b',
              'name' => 'User B',
              'friends' => ['a'],
              'foes' => ['c'],
            ],
          ]
        )->shouldBeCalledTimes(1);

        $database->query(['a', 'b', 'c', 'd', 'e'])->willReturn(
          [
            'a' => ['id' => 'a', 'name' => 'User A'],
            'b' => ['id' => 'b', 'name' => 'User B'],
            'c' => ['id' => 'c', 'name' => 'User C'],
            'd' => ['id' => 'd', 'name' => 'User D'],
            'e' => ['id' => 'e', 'name' => 'User E'],
          ]
        )->shouldBeCalledTimes(1);

        $result = $this->query(
          $query,
          new DeferredQueryBuffer($database->reveal())
        );

        $database->checkProphecyMethodsPredictions();

        $this->assertEquals(
          [
            'a' => [
              [
                'name' => 'User A',
                'friends' => [
                  ['name' => 'User B'],
                  ['name' => 'User C'],
                ],
                'foes' => [
                  ['name' => 'User D'],
                  ['name' => 'User E'],
                ],
              ],
            ],
            'b' => [
              [
                'name' => 'User B',
                'friends' => [
                  ['name' => 'User A'],
                ],
                'foes' => [
                  ['name' => 'User C'],
                ],
              ],
            ],
          ],
          $result['data'],
          'Retrieved data is correct.'
        );
    }
}
