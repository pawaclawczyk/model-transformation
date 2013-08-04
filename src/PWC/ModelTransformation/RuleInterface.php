<?php

namespace PWC\ModelTransformation;

/**
 * Rule of transformation.
 * Contains source and target paths, and optional filter to apply.
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
interface RuleInterface
{

    /**
     * @return array Source paths.
     */
    public function getSourcePaths();

    /**
     * @return array Target path.
     */
    public function getTargetPath();

    /**
     * @return array Filter to apply on source data.
     */
    public function getFilters();
}
