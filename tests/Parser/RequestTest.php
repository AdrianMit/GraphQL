<?php

namespace Dreamlabs\Tests\Parser;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Request;
use Dreamlabs\GraphQL\Parser\Ast\Fragment;
use Dreamlabs\GraphQL\Parser\Location;

class RequestTest extends TestCase
{

    public function testMethods(): void
    {
        $fragment1     = new Fragment('fragmentName1', 'test', [], [], new Location(1,1));
        $fragment2     = new Fragment('fragmentName2', 'test', [], [], new Location(1,1));
        $queriesData   = ['query1', 'query2'];
        $mutationsData = ['mutation1', 'mutation2'];
        $fragmentsData = [$fragment1];
        $variables     = [
            'page' => 2
        ];

        $request = new Request([
            'queries'   => $queriesData,
            'mutations' => $mutationsData,
            'fragments' => $fragmentsData,
        ]);
        $request->setVariables($variables);

        $this->assertEquals($queriesData, $request->getQueries());
        $this->assertEquals($mutationsData, $request->getMutations());
        $this->assertEquals($fragmentsData, $request->getFragments());
        $this->assertEquals($variables, $request->getVariables());

        $this->assertTrue($request->hasFragments());
        $this->assertTrue($request->hasMutations());
        $this->assertTrue($request->hasQueries());

        $this->assertTrue($request->hasVariable('page'));
        $this->assertEquals(2, $request->getVariable('page'));

        $request->addFragment($fragment2);
        $this->assertEquals($fragment2, $request->getFragment('fragmentName2'));
        $this->assertNull($request->getFragment('unknown fragment'));
    }

    public function testSetVariableParseJson(): void
    {
        $variables = '{"foo": "bar"}';
        $expectedVariableArray = [ 'foo' => 'bar' ];

        $request = new Request([], $variables);
        $this->assertEquals($expectedVariableArray, $request->getVariables());

        $request = new Request();
        $request->setVariables($variables);
        $this->assertEquals($expectedVariableArray, $request->getVariables());
    }

}
