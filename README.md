# GraphQL
[![Build Status](https://travis-ci.org/Youshido/GraphQL.svg?branch=master)](http://travis-ci.org/Youshido/GraphQL)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Youshido/GraphQL/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Youshido/GraphQL/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Youshido/GraphQL/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Youshido/GraphQL/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8b8ab2a2-32fb-4298-a986-b75ca523c7c9/mini.png)](https://insight.sensiolabs.com/projects/8b8ab2a2-32fb-4298-a986-b75ca523c7c9)

This is a clean PHP realization of the GraphQL protocol based on the working draft of the specification located on https://github.com/facebook/graphql.

GraphQL is a modern replacement of the REST API approach. It advanced in many ways and has fundamental improvements over the old not-so-good REST:

 - self-checks embedded on the ground level of your backend architecture
 - reusable API for different client versions and devices – no need in "/v1" and "/v2" anymore
 - a complete new level of distinguishing the backend and the frontend logic
 - easily generated documentation and incredibly easy way to explore API for other developers
 - once your architecture is complete – simple changes on the client does not require you to change API

It could be hard to believe, but give it a try and you'll be rewarded with much better architecture and so much easier to support code.

*Current package is and will be trying to be kept up with the latest revision of the GraphQL specification which is now of April 2016.*



## Table of Contents

* [Getting Started](#getting-started)
* [Installation](#installation)
* [Example – Creating Blog Schema](#example--creating-blog-schema)
  * [Inline approach](#inline-approach)
  * [Object Oriented approach](#object-oriented-approach)
  * [Choosing approach for your project](#choosing-approach-for-your-project)
* [Query Documents](#query-documents)
* [Type System](#type-system)
  * [Scalar Types](#scalar-types)
  * [Objects](#objects)
  * [Interfaces](#interfaces)
  * [Enums](#enums)
  * [Unions](#unions)
  * [Lists](#lists)
  * [Input Objects](#input-objects)
  * [Non-Null](#non-null)
* [Building your schema](#building-your-schema)
  * [Mutation helper class](#mutation-helper-class)
* [Useful information](#useful-information)
  * [GraphiQL tool](#graphiql-tool)

## Getting Started

You should be better off starting with some examples, and "Star Wars" become a somewhat "Hello world" example for the GraphQL frameworks.
We have that too and if you looking for just that – go directly by this link – [Star Wars example](https://github.com/Youshido/GraphQL/Tests/StarWars).
On the other hand based on the feedback we prepared a step-by-step for those who want to get up to speed fast.

### Installation

You should simply install this package using the composer. If you're not familiar with it you should check out the [manual](https://getcomposer.org/doc/00-intro.md).
Add the following package to your `composer.json`:

 ```
 {
     "require": {
         "youshido/graphql": "*"
     }
 }
 ```
After you have created the `composer.json` simply run `composer update`.
Alternatively you can do the following sequence:
```sh
mkdir graphql-test && cd graphql-test
composer init
composer require youshido/graphql
```

After the successful message, Let's check if everything is good by setting up a simple schema that will return current time.
*(you can find this example in the examples directory – [01_sandbox](https://github.com/Youshido/GraphQL/examples/01_sandbox))*

```php
<?php
namespace Sandbox;

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

require_once 'vendor/autoload.php';

$processor = new Processor();
$processor->setSchema(new Schema([
    'query' => new ObjectType([
        'name' => 'RootQueryType',
        'fields' => [
            'currentTime' => [
                'type' => new StringType(),
                'resolve' => function() {
                    return date('Y-m-d H:ia');
                }
            ]
        ]
    ])
]));

$res = $processor->processRequest('{ currentTime }')->getResponseData();
print_r($res);
```

If everything was set up correctly – you should see response with your current time:
 ```js
 {
    data: { currentTime: "2016-05-01 19:27pm" }
 }
 ```

If not – check that you have the latest composer version and that you've created your test file in the same directory you have `vendor` folder in.
You can always use a script from `examples` folder. Simply run `php vendor/youshido/GraphQL/examples/01_sandbox/index.php`.

## Example – Creating Blog Schema

For our learning example we'll architect a GraphQL Schema for the Blog.

We'll keep it simple so our Blog will have Users who write Posts and leave Comments.
Here's an example of the query that returns title and summary of the latest Post:
 ```
 latestPost {
     title,
     summary
 }
 ```
As you can see, GraphQL query is a simple text query structured very much similar to the json or yaml format.

Supposedly our server should reply with a response structured like following:
 ```js
 {
    data: {
        latestPost: {
            title: "This is a post title",
            summary: "This is a post summary"
        }
    }
 }
 ```

Let's go ahead and create a backend that can handle that.

### Creating Post schema

We believe you'll be using our package along with your favorite framework (we have a Symfony version [here](http://github.com/Youshido/GraphqlBundle)).
But for the purpose of the current example we'll keep it as plain php code.
*(you can check out the complete example by the following link https://github.com/Youshido/GraphQL/examples/02_Blog )*

We'll take a quick look on different approaches you can use to define your schema.
Even though inline approach might seem to be easier and faster we strongly recommend to use an object based because it will give you more flexibility and freedom as your project grows.

#### Inline approach

So we'll start by creating the Post type. For now we'll have only two fields – title and summary:

**inline-schema.php**
```php
<?php
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

// creating a root query structure
$rootQueryType = new ObjectType([
    // name for the root query type doesn't matter, by a convention it's RootQueryType
    'name'   => 'RootQueryType',
    'fields' => [
        'latestPost' => new ObjectType([ // our Post type will be extended from the generic ObjectType
            'name'    => 'Post', // name of our type – "Post"
            'fields'  => [
                'title'   => new StringType(),  // defining the "title" field, type - String
                'summary' => new StringType(),  // defining the "summary" field, type is also String
            ],
            'resolve' => function () {          // this is a resolve function
                return [                        // for now it will return a static array with data
                    "title"   => "New approach in API has been revealed",
                    "summary" => "This post will describe a new approach to create and maintain APIs",
                ];
            }
        ])
    ]
]);
```

Let's create an endpoint to work with our schema so we can actually test everything we do. it will eventually be able to handle requests from the client.

**index.php**
```php
<?php

namespace BlogTest;

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;

require_once __DIR__ . '/vendor/autoload.php';
$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);

require_once __DIR__ . '/inline-schema.php';       // including our schema

$processor = new Processor();
$processor->setSchema(new Schema([
    'query' => $rootQueryType
]));
$payload = '{ latestPost { title, summary } }';
$response = $processor->processRequest($payload, [])->getResponseData();

print_r($response);
```

To check if everything is working well, simply execute it – `php index.php`
You should see a result similar to the one described in the previous section:
 ```js
 {
    data: {
        latestPost: {
            title: "New approach in API has been revealed",
            summary: "This post will describe a new approach to create and maintain APIs"
        }
    }
 }
 ```

As you can see our request was set to retrieve two fields, title and summary. You can try to play with the code by removing one field from the request or by changing the resolve function.

#### Object oriented approach

From now on we'll be focusing on the Object oriented approach, but you can find full examples of both in the examples folder – (https://github.com/Youshido/GraphQL/examples/02_Blog).

Let's create a folder for our Schema:
```sh
mkdir Schema
```

Using your editor create a file `Schema/PostType.php` and put the following content there:
```php
<?php
namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PostType extends AbstractObjectType   // extending abstract Object type
{

    public function build(TypeConfigInterface $config)  // implementing an abstract function where you build your type
    {
        $config->addField('title', new StringType())        // adding title field of type String
               ->addField('summary', new StringType());     // adding summary field of type String
    }

    public function resolve($value = null, $args = [])  // implementing resolve function
    {
        return [
            "title"   => "New approach in API has been revealed",
            "summary" => "This post will describe a new approach to create and maintain APIs",
        ];
    }

    public function getName()
    {
        return "Post";  // important to use the real name here, it will be used later
    }

}
```

In order to make it work we need to update our `index.php` as well:
```php
<?php

namespace BlogTest;

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';       // including PostType definition

$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);
// adding a field to our query schema
$rootQueryType->getConfig()->addField('latestPost', new PostType());

$processor = new Processor();
$processor->setSchema(new Schema([
    'query' => $rootQueryType
]));
$payload = '{ latestPost { title, summary } }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n"
```

Once again, let's make sure everything is working properly by running `php index.php`. You should see the same response you saw for the inline approach.

### Choosing approach for your project

We would recommend to stick to object oriented approach for the several reasons that matter the most for the GraphQL specifically (also valid as general statements):
 - it makes your Types reusable
 - abilities to refactor your schema using IDEs
 - autocomplete to help you avoid typos
 - much easier to navigate through your Schema

 The only reason we keep the inline approach is to let you bootstrap and explore your ideas. With the inline approach you can be fast and agile in creating GraphQL schema to test your frontend or mobile client, create a mock-data server and so on.

> **User valid Names**
> We highly recommend to get familiar with GraphQL [specification](https://facebook.github.io/graphql/#sec-Language.Query-Document), but important thing for now is
> to remember that valid identifier in GraphQL should follow the pattern `/[_A-Za-z][_0-9A-Za-z]*/`.
> That means any identifier can has only latin letter, underscore, or digit but can't start with digit.
> *Names are case sensitive*

We'll continue on our Blog Schema throughout our exploration of all the details of GraphQL.

## Query Documents

Query Document in terms of GraphQL describes a complete request received by GraphQL service.
It contains list of *Operations* and *Fragments*. Both are fully supported by our PHP library.
There are two types of *Operations* in GraphQL:
- *Query* – a read only request that is not supposed to do any changes on the server
- *Mutation* – a request that changes data on the server followed by a data fetch

You've already seen `latestPost` and `currentTime` queries in our examples above, so let's define a simple Mutation to provide an API to like the Post.
Before we jump into writing the code let's think about it.
Any Operation, in our case a mutation, needs to have a response type. Here's an example of the request sent and expected result:

*request*
```
mutation {
  likePost(id: 5)
}
```
*response*
```js
{
  data: { likePost: 3 }
}
```

In real life you'll more likely have a response of type `Post` for such mutation, but we're going to create a simple example above keep it inside the `index.php`:

```php
<?php

namespace BlogTest;

use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';       // including PostType definition

$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
    'fields' => [
        'latestPost' => new PostType()
    ]
]);

$rootMutationType =  new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
        // here's our likePost mutation
        'likePost' => [                   
            // we specify the output type – simple Int, since it doesn't have a structure
            // mutation name will be used as a key for the result
            'type'    => new IntType(),   
            // we set the argument for our mutation, in our case it's an Int
            // with a composition of NonNull
            'args'    => [
                'id' => new NonNullType(new IntType())
            ],
            // simple resolve function that always returns 2
            'resolve' => function () {
                return 2;
            },
        ]
    ]
]);

$processor = new Processor();

$processor->setSchema(new Schema([
    'query'    => $rootQueryType,
    'mutation' => $rootMutationType,
]));
$payload  = 'mutation { likePost(id:5) }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n";

```

Let's make it a little bit real. We'll add a "likeCount" field to our `PostType`:
```php
<?php
// add it after the last ->addField in your build function
  ->addField('likeCount', new IntType())
//
```

And now let's change our mutation type from the `IntType` to the `PostType` and also change the `resolve` function to be complaint with the the new type we set:
```php
<?php
$rootMutationType =  new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
        'likePost' => [                   
            'type'    => new PostType(),   
            'args'    => [
                'id' => new NonNullType(new IntType())
            ],
            'resolve' => function ($value, $args) {
                // adding like count code goes here
                return [
                    'title' => 'Title for the post #' . $args['id'],
                    'summary' => 'We can now get a richer response from the mutation',
                    'likeCount' => 3
                ];
            },
        ]
    ]
]);
```

As you can see we're repeating ourselves with the resolve function. Since we already have one that can return `PostType` structure, we can utilize it by using the 3rd argument of the `resolve` function – it's output type:
```php
$rootMutationType =  new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
        'likePost' => [                   
            'type'    => new PostType(),   
            'args'    => [
                'id' => new NonNullType(new IntType())
            ],
            'resolve' => function ($value, $args, $type) {
                // adding like count code goes here
                return $type->resolve($value, $args);
            },
        ]
    ]
]);
```

Now when you have a basic understanding of how queries and mutations are structured, let's move on to the details of the GraphQL type system and PHP-specific features of GraphQL service.

## Type System

*Type* is a atom of definition in GraphQL Schema. Every field, object, or argument has a type. That obviously means GraphQL is a strongly typed language.
Types are defined specific for the application, in our case we'll have types like `Post`, `User`, `Comment` and so on.
GraphQL has variety of build in types that are used to build your custom types.

### Scalar Types

List of currently supported types:
- Int
- Float
- String
- Boolean
- Id (serialized as String per [spec](https://facebook.github.io/graphql/#sec-ID))

We also implemented some extended types that we're considering to be scalar:
- Timestamp
- Date
- DateTime
- DateTimeTz

You can define a new Scalar type by extending the `AbstractScalarType` class although you'll end up working with more complex types.
> usage of scalar types you'll see in the combination with other types along the way

### Objects

Every domain in your business logic will be either extended from the `AbstractObjectType` or created as an instance of `ObjectType` class. In our blog example we used `ObjectType` to create an inline `Post` type and in the object oriented example we extended the `AbstractObjectType` to create a `PostType` class.

Let's take a deeper look on the structure of the object type, especially on their fields
*inline object creation*
```php
<?php

$postType = new ObjectType([
  // you have to specify a string name
  'name'    => 'Post',
  // fields is an array of the array structure
  'fields'  => [
      // here you have a complex field with a lot of options
      'title'   => [
          'type'              => new StringType(),                    // string type
          'description'       => 'This field contains a post title',  // description
          'isDeprecated'      => true,                                // marked as deprecated
          'deprecationReason' => 'field title is now deprecated',     // explain the reason
          'args'              => [
              'truncated' => new BooleanType()                        // add an optional argument
          ],
          'resolve'           => function ($value, $args) {
              // used argument to modify a field value
              return (!empty($args['truncated'])) ? explode(' ', $value)[0] . '...' : $value;
          }
      ],
      // if field just has a type, you can use a short declaration syntax like this
      'summary' => new StringType(),  
      'likeCount' => new IntType(),
  ],
   // arguments for the whole query
  'args'    => [
      'id' => new IntType()
  ],
  // resolve function for the query
  'resolve' => function ($value, $args, $type) {
      return [
          'title'   => 'Title for the latest Post',
          'summary' => 'Post summary',
          'likeCount' => 2,
      ];
  },
])
```

And in comparison, take a look at the Object oriented version with all the same fields:
```php
<?php

class PostType extends AbstractObjectType
{

    public function getName()
    {
        return "Post";
    }

    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('title', new NonNullType(new StringType()), [
                'description'       => 'This field contains a post title',
                'isDeprecated'      => true,
                'deprecationReason' => 'field title is now deprecated',
                'args'              => [
                    'truncated' => new BooleanType()
                ],
                'resolve'           => function ($value, $args) {
                    return (!empty($args['truncated'])) ? explode(' ', $value)[0] . '...' : $value;
                }
            ])
            ->addField('summary', new StringType())
            ->addField('likeCount', new IntType());
        $config->addArgument('id', new IntType());
    }

    public function resolve($value = null, $args = [])
    {
        return [
            "title"     => "Title for the latest Post",
            "summary"   => "Post summary",
            "likeCount" => 2
        ];
    }

}

```

Once again, it's not that a big difference between two approaches but having a separate class for the Type will gives you a lot of freedom and adds some flexibility into the project.

### Interfaces

GraphQL supports `Interfaces`. You can define Interface and use it as a list item or to make sure that specific objects conform to your interfaces.
Let's create a `ContentBlockInterface` that will represent something that we can have a `title` and a `summary` from.
```php
<?php
/**
* ContentBlockInterface.php
*/

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractInterfaceType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ContentBlockInterface extends AbstractInterfaceType
{

    public function build(TypeConfigInterface $config)
    {
        $config->addField('title', new NonNullType(new StringType()));
        $config->addField('summary', new StringType());
    }

    public function resolveType($object) {
        // since there's only one type implementing this interface we can return it's type
        return new PostType();
    }

}
```
Most often you'll use only `build` function of the interface to define fields and/or arguments that need to be implemented.
In order to add this Interface to the `PostType` we have to override the `getInterfaces` method:
```php
<?php
/**
* PostType.php
*/

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PostType extends AbstractObjectType
{

    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('title', new StringType())
            ->addField('summary', new StringType())
            ->addField('likeCount', new IntType());
        $config->addArgument('id', new IntType());
    }

    public function getInterfaces()
    {
        return [new ContentBlockInterface()];
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        return [
            "title"     => "Post title from the PostType class",
            "summary"   => "This new GraphQL library for PHP works really well",
            "likeCount" => 2
        ];
    }

}

```
As you might have noticed there's no `getName` method for both Interface and Type classes – that's a simplified approach available when you want to have your name exactly the same as the class name.

If you run the script as it is right now, you'll get an error:
```js
{"errors":[{"message":"Implementation of ContentBlockInterface is invalid for the field title"}]}
```
That's because the field definition inside the `PostType` is different from the one described in the `ContentBlockInterface`.
To fix it we have to declare fields with the same names and same types. We already have `title` but it's a nullable field so we got to change it by adding a non-null wrapper – `new NonNullType(new StringType())`.
You can check the result by executing our test script, you should got the usual response.

### Enums

GraphQL Enums are a variant on the Scalar type, which represents one of a predefined values.
Enums serialize as a string: the name of the represented value but can be associated with a numeric(as example) value.

To show you how Enum works we're going to create a new class - `PostStatus`:
```php
<?php
/**
 * PostStatus.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\Object\AbstractEnumType;

class PostStatus extends AbstractEnumType
{
    public function getValues()
    {
        return [
            [
                'value' => 0,
                'name'  => 'DRAFT',
            ],
            [
                'value' => 1,
                'name'  => 'PUBLISHED',
            ]
        ];
    }

}
```
Now when you have this class created you can add a status field to our `PostType`:
```php
<?php
// add field to the build function of the PostType class
->addField('status', new PostStatus())

// and resolve a value in resolve function
return [
    "title"     => "Post title from the PostType class",
    "summary"   => "This new GraphQL library for PHP works really well",
    "status"    => 1,
    "likeCount" => 2
];

```

Now you can call the `status` field in your request:
```php
$payload  = '{ latestPost { title, status, likeCount } }';
```
You should get a result similar to the following:
```js
{"data":{"latestPost":{"title":"Post title from the PostType class","status":"PUBLISHED","likeCount":2}}}
```

### Unions

GraphQL Unions represent an object type that could be resolved as one of a specified GraphQL Object types.
To get you an idea of what this is we'll create a new query field that will return a list of unions.

Imaging that you have a page and you need to get all content blocks for this page. Let content block be either `Post` or `Banner`.
We'll need to create a `BannerType`:
```php
<?php
/**
 * BannerType.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class BannerType extends AbstractObjectType
{
    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('title', new StringType())
            ->addField('imageLink', new StringType());
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        return [
            'title' => 'Banner 1',
            'imageLink' => 'banner1.jpg'
        ];
    }
}
```
Now, we're going to create a `ContentBlockUnion` that will represent a `UnionType`:
```php
<?php
/**
 * ContentBlockUnion.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\Object\AbstractUnionType;

class ContentBlockUnion extends AbstractUnionType
{
    public function getTypes()
    {
        return [new PostType(), new BannerType()];
    }

    public function resolveType($object)
    {
        return empty($object['id']) ? null : (strpos($object['id'], 'post') !== false ? new PostType() : new BannerType());
    }
}
```

We're also going to create a simple `DataProvider` that will give us test data for the demonstration:
```php
<?php
/**
 * DataProvider.php
 */

namespace Examples\Blog\Schema;

class DataProvider
{
    public static function getPost($id)
    {
        return [
            "id"        => "post-" . $id,
            "title"     => "Post " . $id . " title",
            "summary"   => "This new GraphQL library for PHP works really well",
            "status"    => 1,
            "likeCount" => 2
        ];
    }

    public static function getBanner($id)
    {
        return [
            'id'        => "banner-" . $id,
            'title'     => "Banner " . $id,
            'imageLink' => "banner" . $id . ".jpg"
        ];
    }
}
```

Now, we're ready to update our Schema and include `ContentBlockUnion` into it.
As we're getting our schema bigger we'd like to extract it to a separate file:
```php
<?php
/**
 * BlogSchema.php
 */

namespace Examples\Blog\Schema;


use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Type\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Type\ListType\ListType;

class BlogSchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addFields([
            'latestPost'           => new PostType(),
            'randomBanner'         => [
                'type'    => new BannerType(),
                'resolve' => function () {
                    return DataProvider::getBanner(rand(1, 10));
                }
            ],
            'pageContentUnion'     => [
                'type'    => new ListType(new ContentBlockUnion()),
                'resolve' => function () {
                    return [DataProvider::getPost(1), DataProvider::getBanner(1)];
                }
            ]
        ]);
        $config->getMutation()->addFields([
            'likePost' => new LikePost()
        ]);
    }

}
```
Having this separate schema file you should update your `index.php` to look like this:
```php
<?php

namespace BlogTest;

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;

require_once __DIR__ . '/schema-bootstrap.php';
/** @var Schema $schema */

$processor = new Processor();

$processor->setSchema($schema);
$payload  = '{ pageContentUnion { ... on Post { title } ... on Banner { title, imageLink } } }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n";

```
Due to the GraphQL syntax you have to specify fields for each type of object you're getting in the union request.
If everything was done right you should see the following response in the console:
```js
{"data":{"pageContentUnion":[
    {"title":"Post 1 title","summary":"This new GraphQL library for PHP works really well"},
    {"title":"Banner 1","imageLink":"banner1.jpg"}
]}}
```
Also, you might want to check out how to use [GraphiQL tool](#graphiql-tool) to get a visual representation of what you're doing here.

### Lists

As you've seen in the previous example `Lists` are used to create a separate type – list of any items that have GraphQL type.
List type can also be created using Interface which gives you a flexibility in defining your schema.
Let's go ahead and add that type of field to out BlogSchema:
```php
<?php
/**
 * BlogSchema.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Type\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Type\ListType\ListType;

class BlogSchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addFields([
            'latestPost'           => new PostType(),
            'randomBanner'         => [
                'type'    => new BannerType(),
                'resolve' => function () {
                    return DataProvider::getBanner(rand(1, 10));
                }
            ],
            'pageContentUnion'     => [
                'type'    => new ListType(new ContentBlockUnion()),
                'resolve' => function () {
                    return [DataProvider::getPost(1), DataProvider::getBanner(1)];
                }
            ],
            'pageContentInterfaced' => [
                'type'    => new ListType(new ContentBlockInterface()),
                'resolve' => function () {
                    return [DataProvider::getPost(2), DataProvider::getBanner(3)];
                }
            ]
        ]);
        $config->getMutation()->addFields([
            'likePost' => new LikePost()
        ]);
    }

}
```
We added a list of `ContentBlockInterface` as a type of `pageContentInterfaced` field and returning a Post and a Banner in resolve function.
Now, our payload will be very simple:
```php
<?php
$payload  = '{ pageContentInterface { title} }';
```
Be aware, because our `BannerType` doesn't implement interface we would get an error:
```js
{ "errors": [ "message": "Type Banner does not implement ContentBlockInterface" } ]}
```
To fix this we just need to implement the interface by implementing `getInterfaces` method and adding the proper field definitions to our `BannerType`:

Let's implement our `ContentBlockInterface` in the `BannerType`:
```php
<?php
/**
 * BannerType.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class BannerType extends AbstractObjectType
{
    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('title', new NonNullType(new StringType()))
            ->addField('summary', new StringType())
            ->addField('imageLink', new StringType());
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        return DataProvider::getBanner(1);
    }

    public function getInterfaces()
    {
        return [new ContentBlockInterface()];
    }
}
```
Send the request again and you'll get a nice response with titles of the both Post and Banner:
```js
{"data":{"pageContentInterface":[{"title":"Post 2 title"},{"title":"Banner 3"}]}}
```

### Input Objects
So far we've been working mostly on the request that does not require you to send any kind of data, but in real life you'll have a lot of requests – mutations where you'll be sending different kind of form – login, registration, create post and other data to the server.
In order to properly handle and validate that data GraphQL type system provides you an `InputType`. By default all the `Scalar` types are input but if you want to have a single more complicated input type you need to extend an `InputObjectType`.

Let's go ahead and create a `PostInputType` that could be used to create a new Post in our system.
```php
<?php
/**
 * PostInputType.php
 */

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Config\InputTypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractInputObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PostInputType extends AbstractInputObjectType
{

    public function build(InputTypeConfigInterface $config)
    {
        $config
            ->addField('title', new NonNullType(new StringType()))
            ->addField('summary', new StringType());
    }


}
```

This `InputType` could be used to create a new mutation (we can do it in the `BlogSchema::build` for testing):
```php
<?php
$config->getMutation()->addFields([
    'likePost'   => new LikePost(),
    'createPost' => [
        'type'   => new PostType(),
        'args' => [
            'post'   => new PostInputType(),
            'author' => new StringType()
        ],
        'resolve' => function($value, $args, $type) {
            return DataProvider::getPost(10);
        }
    ]
]);
```

Try to execute the following mutation so you can see the result:
```
mutation {
  createPost(author: "Alex", post: {title: "helpp", summary: "help2" }) {
    title
  }
}
```
> The best way to see the result of your queries/mutations and to inspect the Schema is to use a [GraphiQL tool](#graphiql-tool)

### Non Null

`NonNullType` is really simple to use – consider it as a wrapper that can insure that your field / argument is required and being passed to the resolve function.
We have used this type many times already so we'll just show you two methods that might be useful in your resolve functions:
- `getNullableType()`
- `getNamedType()`

These two can return you a type that was wrapped up in the `NonNullType` so you can get it's fields, arguments or name.

## Building your schema

It's always a good idea to give your heads up about any possible errors as soon as possible, better on the development stage.
For this purpose specifically we made a lot of Abstract classes that will force you to implement the right methods to reduce amount of errors or, if you're lucky enough – to have none of them.
If you want to implement a new type consider extending the following classes:
* AbstractType
* AbstractScalarType
* AbstractObjectType
* AbstractMutationObjectType
* AbstractInputObjectType
* AbstractInterfaceType
* AbstractEnumType
* AbstractListType
* AbstractUnionType
* AbstractSchemaType

### Mutation helper class
Usually you can create a mutation buy extending `AbstractObjectType` or by creating a new field of `ObjectType` inside your `Schema::build` method.
It is crucial for the class to have a `getType` method returning the actual OutputType of your mutation.
There's a class called `AbstractMutationObjectType` that will help you to not forget about OutputType by forcing you to implement a method `getOutputType` that will eventually be used by internal `getType` method.

## Useful information

We tried to put together some of the useful links and references that might help you to quicker become a better GraphQL developer

### GraphiQL Tool
To improve our testing experience even more we suggest to start using GraphiQL client, that's included in our examples. It's a JavaScript GraphQL Schema Explorer.
To use it – run the `server.sh` from the `examples/02_blog/` folder and open the `examples/GraphiQL/index.html` file in your browser.
You'll see a nice looking editor that has an autocomplete function and contains all information about your current Schema on the right side in the Docs sidebar.
