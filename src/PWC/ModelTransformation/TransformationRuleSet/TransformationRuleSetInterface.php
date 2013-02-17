<?php

namespace PWC\ModelTransformation\TransformationRuleSet;

/**
 * Set of rules used for transformation process.
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
interface TransformationRuleSetInterface
{

    public function addRule();

    public function findRule($property);
}
