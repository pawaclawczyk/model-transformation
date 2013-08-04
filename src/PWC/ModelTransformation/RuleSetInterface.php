<?php

namespace PWC\ModelTransformation;

/**
 * Set of rules used for transformation process.
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
interface RuleSetInterface
{

    /**
     * Add new transformation rule.
     *
     * @param string|array          $sourcePaths
     * @param string                $targetPath
     * @param string|callback|array $filters
     *
     * @return RuleSetInterface
     */
    public function addRule($sourcePaths, $targetPath, $filters = array());

    /**
     * Find transformation rule for source or target path.
     *
     * @param string $property Source or target path
     *
     * @return RuleInterface
     */
    public function findRule($path);
}
