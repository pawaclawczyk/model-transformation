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
    public function getParent();

    /**
     * Fetch value(s) from instance of model from given path.
     *
     * @param mixed  $model Instance of model.
     * @param string $path  Path to the value(s).
     *
     * @return mixed
     */
    public function getValue($model, $path);

    /**
     * Put value(s) to instance of model into given path.
     *
     * @param mixed  $model Instance of model.
     * @param string $path  Path to the value(s).
     * @param mixed  $value Value(s) to set.
     *
     * @return ModelDefinitionInterface
     */
    public function setValue($model, $path, $value);
}
