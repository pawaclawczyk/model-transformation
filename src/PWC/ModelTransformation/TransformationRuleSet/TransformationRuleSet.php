<?php

namespace PWC\ModelTransformation\TransformationRuleSet;

use PWC\ModelTransformation\TransformationRuleSet\TransformationRuleSetInterface;
use PWC\ModelTransformation\TransformationRuleSet\TransformationRule;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class TransformationRuleSet implements TransformationRuleSetInterface, \Iterator
{

    private $rules;
    private $_index;

    public function __construct($transformationRuleArray = null)
    {
        $this->rules = array();
        if (null !== $transformationRuleArray) {
            $this->parseTransformationRuleArray($transformationRuleArray);
        }
    }

    /**
     * @inheritdoc
     */
    public function addRule()
    {
        $rule = new TransformationRule($this);
        $this->rules[] = $rule;

        return $rule;
    }

    /**
     * @inheritdoc
     */
    public function findRule($property)
    {
        foreach ($this->rules as $rule) {
            if (in_array($property, $rule->getSourceProperties()) || $property === $rule->getTargetProperty()) {
                return $rule;
            }
        }

        return null;
    }

    public function current()
    {
        return $this->rules[$this->_index];
    }

    public function key()
    {
        return $this->_index;
    }

    public function next()
    {
        $this->_index++;
    }

    public function rewind()
    {
        $this->_index = 0;
    }

    public function valid()
    {
        return isset($this->rules[$this->_index]);
    }

    private function parseTransformationRuleArray($transformationRuleArray)
    {
        foreach ($transformationRuleArray as $sourceProperty => $targetProperty) {
            $this->addRule()->addSourceProperty($sourceProperty)->setTargetProperty($targetProperty);
        }
    }

}
