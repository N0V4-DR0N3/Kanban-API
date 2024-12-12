<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;
use ReflectionException;

abstract class TestCase extends BaseTestCase
{
    //

    /**
     * Call protected/private method of a class.
     *
     * @param object $object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method
     *
     * @return mixed Method return.
     * @throws ReflectionException
     */
    protected function invokeMethod(object &$object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new ReflectionClass(get_class($object));

        $method = $reflection->getMethod($methodName);

        $method->setAccessible(accessible: true);

        return $method->invokeArgs(object: $object, args: $parameters);
    }

    /**
     * Get protected/private attribute of a class.
     *
     * @param object $object Instantiated object that we will get attribute from.
     * @param string $attributeName Attribute name to get
     *
     * @return mixed Attribute value.
     * @throws ReflectionException
     */
    protected function invokeAttribute(object &$object, string $attributeName): mixed
    {
        $reflection = new ReflectionClass(get_class($object));

        $attribute = $reflection->getProperty($attributeName);

        $attribute->setAccessible(accessible: true);

        return $attribute->getValue(object: $object);
    }
}
