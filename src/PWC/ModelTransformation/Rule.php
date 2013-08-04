<?php

namespace PWC\ModelTransformation;

use PWC\ModelTransformation\RuleInterface;

/**
 * {@inheritdoc}
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class Rule implements RuleInterface
{

    private $sourcePaths;
    private $targetProperty;
    private $filters;

    public function __construct($sourcePaths, $targetPath, $filters = array())
    {
        $this->sourcePaths = (array) $sourcePaths;
        $this->targetProperty = $targetPath;
        $this->filters = (array) $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourcePaths()
    {
        return $this->sourcePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetPath()
    {
        return $this->targetProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

}
