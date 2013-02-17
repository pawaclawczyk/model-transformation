<?php

namespace PWC\ModelTransformation;

use PWC\ModelTransformation\RuleSetInterface;
use PWC\ModelTransformation\Rule;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class RuleSet implements RuleSetInterface, \Iterator
{

    private $rules;
    private $_index;

    public function __construct($transformationRuleArray = null)
    {
        $this->rules = array();
        if (null !== $transformationRuleArray) {
            $this->parseRuleArray($transformationRuleArray);
        }
    }

    /**
     * @inheritdoc
     */
    public function addRule()
    {
        $rule = new Rule($this);
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

    private function parseRuleArray($transformationRuleArray)
    {
        foreach ($transformationRuleArray as $sourceProperty => $targetProperty) {
            $this->addRule()->addSourceProperty($sourceProperty)->setTargetProperty($targetProperty);
        }
    }

}
