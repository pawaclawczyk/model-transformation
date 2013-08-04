<?php

namespace PWC\ModelTransformation;

use PWC\ModelTransformation\RuleSetInterface;
use PWC\ModelTransformation\Rule;

/**
 * {@inheritdoc}
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class RuleSet implements RuleSetInterface, \Iterator
{

    private $rules;
    private $_index;

    /**
     * @param array $transformationMap Primitive set of rules, given as associative array.
     */
    public function __construct($transformationMap = null)
    {
        $this->rules = array();
        if (null !== $transformationMap) {
            $this->parseRuleArray($transformationMap);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addRule($sourcePaths, $targetPath, $filters = array())
    {
        $rule = new Rule($sourcePaths, $targetPath, $filters);
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function findRule($path)
    {
        foreach ($this->rules as $rule) {
            if (in_array($path, $rule->getSourcePaths()) || $path === $rule->getTargetPath()) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->rules[$this->_index];
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->_index;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->_index++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->_index = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->rules[$this->_index]);
    }

    private function parseRuleArray($transformationMap)
    {
        foreach ($transformationMap as $sourcePath => $targetPath) {
            $this->addRule($sourcePath, $targetPath);
        }
    }

}
