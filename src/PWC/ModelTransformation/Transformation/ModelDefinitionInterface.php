<?php

namespace PWC\ModelTransformation\Transformation;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
interface ModelDefinitionInterface
{

    /**
     * Assert that current model node is instance of class or type.
     * If given type is array or class implements Traversable, automaticly
     * assert that node is a collection.
     * By default node is instance of stdClass.
     *
     * @param string $fqcnOrType FQCN or type name
     *
     * @return ModelDefinitionInterface
     */
    public function isInstanceOf($fqcnOrType);

    /**
     * Assert that current model node is collection.
     * Declaring that node is a collection, automaticly assert that
     * is instance of array, if isInstanceOf() not called.
     *
     * @return ModelDefinitionInterface
     */
    public function isCollection();

    /**
     * Assert that current model node has property (if is an object) or key (if is an array or map structure).
     *
     * @param string $propertyOrKey Property or key.
     *
     * @return ModelDefinitionInterface Child node.
     */
    public function hasChild($propertyOrKey = null);

    /**
     * Go back to parent node.
     *
     * @return ModelDefinitionInterface Parent node.
     */
    public function parent();
}
