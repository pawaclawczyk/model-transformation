<?php

namespace PWC\ModelTransformation;

/**
 * Transform one model object into another.
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
interface TransformerInterface
{

    public function transform($source, $target, $transformationRuleSet, $continueOnErrors = false);

}
