<?php

namespace PWC\ModelTransformation;

/**
 * Set of rules used for transformation process.
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
interface RuleSetInterface
{

    public function addRule();

    public function findRule($property);
}
