<?php

namespace PWC\ModelTransformation\Transformation;

/**
 * Description of ModelDefinition
 *
 * @author Paweł A. Wacławczyk <pawel.waclawczyk@netteam.pl>
 */
class ModelDefinition implements ModelDefinitionInterface
{

    public function isCollection()
    {
        return $this;
    }

    public function isInstanceOf($fqcnOrType)
    {
        return $this;
    }

    public function hasChild($propertyOrKey = null)
    {
        return $this;
    }

    public function getParent()
    {
        return $this;
    }

    public function getValue($model, $path)
    {

    }

    public function setValue($model, $path, $value)
    {

    }

}
