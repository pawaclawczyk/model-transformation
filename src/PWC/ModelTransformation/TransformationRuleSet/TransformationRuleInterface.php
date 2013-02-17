<?php

namespace PWC\ModelTransformation\TransformationRuleSet;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
interface TransformationRuleInterface
{

    public function addSourceProperty($property);

    public function setSourceProperties(array $properties);

    public function getSourceProperties();

    public function addFilter($callback);

    public function setFilters(array $filters);

    public function getFilters();

    public function setTargetProperty($property);

    public function getTargetProperty();

    public function addRule();
}
