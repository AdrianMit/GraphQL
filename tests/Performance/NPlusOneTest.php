<?php

namespace Dreamlabs\Tests\Performance;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class NPlusOneTest extends TestCase
{

    private function getDataForPosts()
    {
        /**
         * We could make a DB request here, as a simplified version:
         * SELECT * FROM posts p LEFT JOIN authors a ON (a.id = p.author_id) LIMIT 10, 10
         */
        $authors = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Alex'],
            ['id' => 3, 'name' => 'Mike'],
        ];
        $posts   = [];
        for ($i = 0; $i < 10; $i++) {
            $posts[] = [
                'id'     => $i + 1,
                'title'  => sprintf('Post title $%s', $i),
                'author' => $authors[$i % 3]
            ];
        }

        return $posts;
    }

    public function testHigherResolver(): void
    {
        $authorType = new ObjectType([
            'name'   => 'Author',
            'fields' => [
                'id'   => new IdType(),
                'name' => new StringType(),
            ]
        ]);

        $postType = new ObjectType([
            'name'   => 'Post',
            'fields' => [
                'id'     => new IntType(),
                'title'  => new StringType(),
                'author' => $authorType,
            ]
        ]);

        $processor = new Processor(new Schema([
            'query' => new ObjectType([
                'fields' => [
                    'posts' => [
                        'type'    => new ListType($postType),
                        'resolve' => fn($source, $args, $info) => $this->getDataForPosts()
                    ]
                ]
            ])
        ]));

        $data = $processor->processPayload('{ posts { id, title, author { id, name } } }')->getResponseData();
        $this->assertNotEmpty($data['data']['posts'][0]['author']);
    }

}
