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

    private $filters;
    private $prependFilters;
    private $appendFilters;
    private $rules;
    private $_index;

    /**
     * @param array $transformationMap Primitive set of rules, given as associative array.
     */
    public function __construct($transformationMap = null)
    {
        $this->filters = array();
        $this->prependFilters = array();
        $this->appendFilters = array();
        $this->rules = array();
        if (null !== $transformationMap) {
            $this->parseRuleArray($transformationMap);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerFilter($alias, $callback)
    {
        $this->filters[$alias] = $callback;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter($callback, $prepend = false)
    {
        if (is_string($callback)) {
            $callback = $this->getFilter($callback);
        }

        if ($prepend) {
            $this->prependFilters[] = $callback;
        } else {
            $this->appendFilters[] = $callback;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRule($sourcePaths, $targetPath, $filters = array())
    {
        $filters = $this->prepareFilters($filters);

        $rule = new Rule($sourcePaths, $targetPath, $filters);
        $this->rules[] = $rule;

        return $this;
    }

    private function getFilter($alias)
    {
        return $this->filters[$alias];
    }

    private function prepareFilters($filters)
    {
        $filters = (array) $filters;

        foreach ($this->prependFilters as $filter) {
            array_unshift($filters, $filter);
        }

        foreach ($filters as $key => $filter) {
            if (is_string($filter)) {
                $filters[$key] = $this->getFilter($filter);
            }
        }

        foreach ($this->appendFilters as $filter) {
            array_push($filters, $filter);
        }

        return $filters;
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
