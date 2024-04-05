<?php

namespace App\Concerns\Modifiers;

use ReflectionClass;
use ReflectionProperty;

trait InjectsReadonly
{
    protected function injectReadonly(): void
    {
        $ref = new ReflectionClass($this);
        $properties = $ref->getProperties(ReflectionProperty::IS_READONLY);

        foreach ($properties as $property) {
            $name = $property->getType()->getName();

            if (class_exists($name)) {
                $property->setValue($this, app($name));
            }
        }
    }
}
