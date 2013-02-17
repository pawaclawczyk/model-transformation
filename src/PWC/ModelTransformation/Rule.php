<?php

namespace PWC\ModelTransformation;

use PWC\ModelTransformation\RuleInterface;
use PWC\ModelTransformation\RuleSetInterface;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class Rule implements RuleInterface
{

    private $transformationRuleSet;
    private $sourceProperties;
    private $filters;
    private $targetProperty;

    public function __construct(RuleSetInterface $transformationRuleSet)
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
