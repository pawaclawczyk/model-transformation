<?php

namespace PWC\ModelTransformation;

use PWC\ModelTransformation\TransformerInterface;
use PWC\ModelTransformation\RuleInterface;
use PWC\ModelTransformation\RuleSet;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

/**
 * Description of Transformer
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class Transformer implements TransformerInterface
{
    private $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::getPropertyAccessor();
    }

    /**
     * @inheritdoc
     */
    public function transform($source, $target, $transformationRuleSet, $continueOnErrors = false)
    {
        if (is_string($target)) {
            $target = $this->createTargetInstance($target);
        }

        if (is_array($transformationRuleSet)) {
            $transformationRuleSet = new RuleSet($transformationRuleSet);
        }

        foreach ($transformationRuleSet as $transformationRule) {
            try {
                $this->transformSingleRule($source, $target, $transformationRule);
            } catch (NoSuchPropertyException $e) {
                if (!$continueOnErrors) {
                    throw $e;
                }
            } catch (\Exception $e) {
                if (!$continueOnErrors) {
                    throw new \InvalidArgumentException($e->getMessage());
                }
            }
        }

        return $target;
    }

    private function transformSingleRule($source, $target, RuleInterface $transformationRule)
    {
        $sourceValue = $this->getSourceValue($source, $transformationRule);
        $filteredValue = $this->filter($sourceValue, $target, $transformationRule);
        $this->setTargetValue($filteredValue, $target, $transformationRule);
    }

    private function getSourceValue($source, RuleInterface $transformationRule)
    {
        $extractFromArray = (1 === count($transformationRule->getSourceProperties()));
        $sourceValue = array();

        foreach ($transformationRule->getSourceProperties() as $sourceProperty) {
            $sourceValue[] = $this->propertyAccessor->getValue($source, $sourceProperty);
        }

        if ($extractFromArray) {
            $sourceValue = reset($sourceValue);
        }

        return $sourceValue;
    }

    private function filter($sourceValue, $target, RuleInterface $transformationRule)
    {
        $targetValue = $this->propertyAccessor->getValue($target, $transformationRule->getTargetProperty());
        $value = $sourceValue;

        foreach ($transformationRule->getFilters() as $filter) {
            $params = (is_array($value)) ? $value : array($value);
            array_unshift($params, $targetValue);

            $value = call_user_func_array($filter, $params);
        }

        return $value;
    }

    private function setTargetValue($value, $target, RuleInterface $transformationRule)
    {
        $this->propertyAccessor->setValue($target, $transformationRule->getTargetProperty(), $value);
    }

    private function createTargetInstance($class)
    {
        if (!class_exists($class)) {
            throw new \BadMethodCallException(sprintf('Class %s not found.\nCannot create instance of class %s.', $class, $class));
        }

        return new $class();
    }
}
