<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery;

use Mockery\ExpectationInterface;
use Mockery\Generator\CachingGenerator;
use Mockery\Generator\Generator;
use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\StringManipulation\Pass\RemoveDestructorPass;
use Mockery\Generator\StringManipulationGenerator;
use Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;
use Mockery\Generator\StringManipulation\Pass\MagicMethodTypeHintsPass;
use Mockery\Generator\StringManipulation\Pass\ClassNamePass;
use Mockery\Generator\StringManipulation\Pass\ClassPass;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;
use Mockery\Generator\StringManipulation\Pass\InterfacePass;
use Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass;
use Mockery\Generator\StringManipulation\Pass\RemoveBuiltinMethodsThatAreFinalPass;
use Mockery\Generator\StringManipulation\Pass\RemoveUnserializeForInternalSerializableClassesPass;
use Mockery\Loader\EvalLoader;
use Mockery\Loader\Loader;

class VCR
{
    private static $cassettes;

    public static function record($instance, array $options = [])
    {
        $builder = new MockConfigurationBuilder();
        $builder->addTarget($instance);
        $config = $builder->getMockConfiguration();

        $def = static::generator()->generate($config);

        static::loader()->load($def);

        $className = "\\".$def->getClassName();

        return new $className($instance, static::cassettes());
    }

    public static function loader()
    {
        return new EvalLoader();
    }

    public static function generator()
    {
        $generator = new StringManipulationGenerator(array(
            new CallTypeHintPass(),
            new MagicMethodTypeHintsPass(),
            new ClassPass(),
            new ClassNamePass(),
            new InterfacePass(),
            new MethodDefinitionPass(),
            new RemoveUnserializeForInternalSerializableClassesPass(),
            new RemoveBuiltinMethodsThatAreFinalPass(),
            new RemoveDestructorPass(),
        ), __DIR__.'/VCR/Recordable.php');

        return new CachingGenerator($generator);
    }

    public static function serializer()
    {
        return new VCR\PHPSerializer();
    }

    public static function storage()
    {
        return new VCR\Storage("/tmp/mockery_vcr");
    }

    public static function load($name)
    {
        static::cassettes()->push(new VCR\Cassette($name, static::storage(), static::serializer()));
    }

    public static function eject()
    {
        static::cassettes()->pop()->eject();
    }

    public static function ejectAll()
    {
        while (!static::cassettes()->isEmpty()) {
            static::cassettes()->pop()->eject();
        }
    }

    /**
     * @private
     */
    public static function cassettes()
    {
        if (!static::$cassettes) {
            static::$cassettes = new VCR\CassetteStack();
        }

        return static::$cassettes;
    }
}
