<?php
/**
 * Created by PhpStorm.
 * User: mounter
 * Date: 8/18/16
 * Time: 2:17 PM
 */

namespace Dreamlabs\Tests\Performance;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class LoadTest extends TestCase
{

    public function testLoad10k()
    {
        $time = microtime(true);
        $postType = new ObjectType([
            'name'   => 'Post',
            'fields' => [
                'id'      => new IdType(),
                'title'   => new StringType(),
                'authors' => [
                    'type' => new ListType(new ObjectType([
                        'name'   => 'Author',
                        'fields' => [
                            'name' => new StringType()
                        ]
                    ]))
                ],
            ]
        ]);

        $data = [];
        for ($i = 1; $i <= 10000; ++$i) {
            $authors = [];
            while (count($authors) < random_int(1, 4)) {
                $authors[] = [
                    'name' => 'Author ' . substr(md5(time()), 0, 4)
                ];
            }
            $data[] = [
                'id'      => $i,
                'title'   => 'Title of ' . $i,
                'authors' => $authors,
            ];
        }

        $p = new Processor(new Schema([
            'query' => new ObjectType([
                'name' => 'RootQuery',
                'fields' => [
                    'posts' => [
                        'type' => new ListType($postType),
                        'resolve' => fn(): array => $data
                    ]
                ],
            ]),
        ]));
        return true;
        $p->processPayload('{ posts { id, title, authors { name } } }');
        $res = $p->getResponseData();
        echo "Count: " . (is_countable($res['data']['posts']) ? count($res['data']['posts']) : 0) . "\n";
        printf("Test Time: %04f\n", microtime(true) - $time);
    }

}
