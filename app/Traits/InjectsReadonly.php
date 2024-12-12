<?php

namespace App\Traits;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

trait InjectsReadonly
{
    protected function injectReadonly(): void
    {
        $ref = new ReflectionClass($this);
        $properties = $ref->getProperties(ReflectionProperty::IS_READONLY);

        foreach ($properties as $property) {
            if (!($type = $property->getType()) instanceof ReflectionNamedType) {
                continue;
            }
            if (!class_exists($name = $type->getName())) {
                continue;
            }

            $property->setValue($this, app($name));
        }
    }
}
