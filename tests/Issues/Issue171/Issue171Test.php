<?php
namespace Dreamlabs\Tests\Issues\Issue171;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;

class Issue171Test extends TestCase
{
    public function testItSetsDeprecationReasonToNullByDefault(): void
    {
        $schema = new Issue171Schema();
        $processor = new Processor($schema);

        $processor->processPayload($this->getIntrospectionQuery(), []);
        $resp = $processor->getResponseData();

        $enumTypes = array_filter($resp['data']['__schema']['types'], fn($type): bool => $type['kind'] === 'ENUM');

        foreach ($enumTypes as $enumType) {
            foreach ($enumType['enumValues'] as $value) {
                $this->assertFalse($value['isDeprecated']);
                $this->assertNull($value['deprecationReason'], "deprecationReason should have been null");
            }
        }
    }

    private function getIntrospectionQuery()
    {
        return  <<<TEXT
query IntrospectionQuery {
                __schema {
                    queryType { name }
                    mutationType { name }
                    types {
                        ...FullType
                    }
                    directives {
                        name
                        description
                        args {
                            ...InputValue
                        }
                        onOperation
                        onFragment
                        onField
                    }
                }
            }

            fragment FullType on __Type {
                kind
                name
                description
                fields {
                    name
                    description
                    args {
                        ...InputValue
                    }
                    type {
                        ...TypeRef
                    }
                    isDeprecated
                    deprecationReason
                }
                inputFields {
                    ...InputValue
                }
                interfaces {
                    ...TypeRef
                }
                enumValues {
                    name
                    description
                    isDeprecated
                    deprecationReason
                }
                possibleTypes {
                    ...TypeRef
                }
            }

            fragment InputValue on __InputValue {
                name
                description
                type { ...TypeRef }
                defaultValue
            }

            fragment TypeRef on __Type {
                kind
                name
                ofType {
                    kind
                    name
                    ofType {
                        kind
                        name
                        ofType {
                            kind
                            name
                        }
                    }
                }
            }
TEXT;
    }
}
