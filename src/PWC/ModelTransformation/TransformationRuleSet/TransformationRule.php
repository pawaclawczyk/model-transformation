<?php

namespace PWC\ModelTransformation\TransformationRuleSet;

use PWC\ModelTransformation\TransformationRuleSet\TransformationRuleInterface;
use PWC\ModelTransformation\TransformationRuleSet\TransformationRuleSetInterface;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class TransformationRule implements TransformationRuleInterface
{

    private $transformationRuleSet;
    private $sourceProperties;
    private $filters;
    private $targetProperty;

    public function __construct(TransformationRuleSetInterface $transformationRuleSet)
    {
        $this->transformationRuleSet = $transformationRuleSet;
        $this->sourceProperties = array();
        $this->filters = array();
    }

    /**
     * @inheritdoc
     */
    public function addSourceProperty($property)
    {
        $this->sourceProperties[] = $property;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSourceProperties(array $properties)
    {
        $this->sourceProperties = $properties;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSourceProperties()
    {
        return $this->sourceProperties;
    }

    /**
     * @inheritdoc
     */
    public function addFilter($callback)
    {
        $this->filters[] = $callback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @inheritdoc
     */
    public function setTargetProperty($property)
    {
        $this->targetProperty = $property;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTargetProperty()
    {
        return $this->targetProperty;
    }

    /**
     * @inheritdoc
     */
    public function addRule()
    {
        return $this->transformationRuleSet->addRule();
    }

}
