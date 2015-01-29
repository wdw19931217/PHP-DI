<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace DI\Test\IntegrationTest\Definitions;

use DI\ContainerBuilder;

/**
 * Test environment variable definitions
 *
 * @coversNothing
 */
class EnvironmentVariableDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvironmentVariable()
    {
        $expectedValue = getenv('USER');
        if (! $expectedValue) {
            $this->markTestSkipped(
                'This test relies on the presence of the USER environment variable.'
            );
        }

        $builder = new ContainerBuilder();
        $builder->addDefinitions(array(
            'var' => \DI\env('USER'),
        ));
        $container = $builder->build();

        $this->assertEquals($expectedValue, $container->get('var'));
    }

    /**
     * @expectedException \DI\Definition\Exception\DefinitionException
     * @expectedExceptionMessage The environment variable 'PHP_DI_DO_NOT_DEFINE_THIS' has not been defined
     */
    public function testUndefinedEnvironmentVariable()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(array(
            'var' => \DI\env('PHP_DI_DO_NOT_DEFINE_THIS'),
        ));
        $container = $builder->build();

        $container->get('var');
    }

    public function testOptionalEnvironmentVariable()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(array(
            'var' => \DI\env('PHP_DI_DO_NOT_DEFINE_THIS', '<default>'),
        ));
        $container = $builder->build();

        $this->assertEquals('<default>', $container->get('var'));
    }

    public function testOptionalEnvironmentVariableWithNullDefault()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(array(
            'var' => \DI\env('PHP_DI_DO_NOT_DEFINE_THIS', null),
        ));
        $container = $builder->build();

        $this->assertNull($container->get('var'));
    }

    public function testOptionalEnvironmentVariableWithLinkedDefaultValue()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(array(
            'var' => \DI\env('PHP_DI_DO_NOT_DEFINE_THIS', \DI\link('foo')),
            'foo' => 'bar',
        ));
        $container = $builder->build();

        $this->assertEquals('bar', $container->get('var'));
    }
}
