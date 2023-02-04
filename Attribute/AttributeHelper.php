<?php

namespace Ict\ApiOneEndpoint\Attribute;

class AttributeHelper
{
    public function getAttr(object $object, string $attrClass): ?object
    {
        $reflectionClass = new \ReflectionClass($object);
        $attributes = $reflectionClass->getAttributes($attrClass);
        if(count($attributes) > 0) {
            $attr = reset($attributes);
            return $attr->newInstance();
        }

        return null;
    }
}
