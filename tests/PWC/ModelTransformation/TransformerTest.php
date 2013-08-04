<?php

namespace PWC\ModelTransformation\Tests;

use PWC\ModelTransformation\Transformer;
use PWC\ModelTransformation\RuleSet;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class TransformerTest extends \PHPUnit_Framework_TestCase
{

    private $transformer;

    public function setUp()
    {
        parent::setUp();
        $this->transformer = new Transformer();
    }

    public function testObjectToObjectTransformation()
    {
        $sourceObject = new SourceClass('public', 'private', new SimpleClass('nested'));
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';
        $transformationRuleArray = array(
            'sourcePublicProperty' => 'targetPublicProperty',
            'sourcePrivateProperty' => 'targetPrivateProperty',
            'sourceObjectProperty.property' => 'targetObjectProperty.property',
        );

        $targetObject = $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray);

        $this->assertInstanceOf('PWC\ModelTransformation\Tests\TargetClass', $targetObject);
        $this->assertEquals('public', $targetObject->targetPublicProperty);
        $this->assertEquals('private', $targetObject->getTargetPrivateProperty());
        $this->assertEquals('nested', $targetObject->getTargetObjectProperty()->property);
    }

    public function testArrayToObjectTransformation()
    {
        $sourceArray = array(
            'property' => 'value',
            'nestedArray' => array(
                'nestedProperty' => 'nestedValue'
            ),
        );
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';
        $transformationRuleArray = array(
            '[property]' => 'targetPublicProperty',
            '[nestedArray][nestedProperty]' => 'targetObjectProperty.property',
        );

        $targetObject = $this->transformer->transform($sourceArray, $targetClass, $transformationRuleArray);

        $this->assertInstanceOf('PWC\ModelTransformation\Tests\TargetClass', $targetObject);
        $this->assertEquals('value', $targetObject->targetPublicProperty);
        $this->assertEquals('nestedValue', $targetObject->getTargetObjectProperty()->property);
    }

    /**
     * @expectedException Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException
     */
    public function testTryToGetFromNotExistingPropertyInSource()
    {
        $sourceObject = new SourceClass('public');
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';
        $transformationRuleArray = array(
            'nonExistingPublicProperty' => 'targetPublicProperty',
        );

        $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray);
    }

    /**
     * @expectedException Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException
     */
    public function testTryToSetToNotExistingPropertyInTarget()
    {
        $sourceObject = new SourceClass('public');
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';
        $transformationRuleArray = array(
            'sourcePublicProperty' => 'nonExistingPublicProperty',
        );

        $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToSetUnexpectedTypeInTargetProperty()
    {
        $sourceObject = new SourceClass(null, null, new OtherSimpleClass());
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';
        $transformationRuleArray = array(
            'sourceObjectProperty' => 'targetObjectProperty',
        );

        $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testTryToCreateInstanceOfNotExistingClass()
    {
        $sourceObject = new SourceClass();
        $targetClass = 'Not\Existing\Class';
        $transformationRuleArray = array(
        );

        $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray);
    }

    public function testForceTransformationEvenIfErrorOccuredWithAnyRule()
    {
        $sourceObject = new SourceClass('public', 'private', new OtherSimpleClass());
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';
        $transformationRuleArray = array(
            'notExistingProperty' => 'targetPrivateProperty',
            'sourcePrivateProperty' => 'notExistingProperty',
            'sourceObjectClass' => 'targetObjectClass',
            'sourcePublicProperty' => 'targetPublicProperty',
        );

        $targetObject = $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray, true);

        $this->assertEquals('public', $targetObject->targetPublicProperty);
        $this->assertNull($targetObject->getTargetPrivateProperty());
        $this->assertNull($targetObject->getTargetObjectProperty()->property);
    }

    public function testObjectToObjectTransformationWithInstantiatedTargetObject()
    {
        $sourceObject = new SourceClass('public');
        $targetObject = new TargetClass();
        $transformationRuleArray = array(
            'sourcePublicProperty' => 'targetPublicProperty'
        );

        $targetObject = $this->transformer->transform($sourceObject, $targetObject, $transformationRuleArray);

        $this->assertEquals('public', $targetObject->targetPublicProperty);
    }

    public function testObjectToObjectTransformationUsingRuleSet()
    {
        $sourceObject = new SourceClass('public', 'private', new SimpleClass('nested'));
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';
        $transformationRuleArray = new RuleSet();
        $transformationRuleArray
                ->addRule('sourcePublicProperty', 'targetPublicProperty')
                ->addRule('sourcePrivateProperty', 'targetPrivateProperty')
                ->addRule('sourceObjectProperty.property', 'targetObjectProperty.property');

        $targetObject = $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray);

        $this->assertInstanceOf('PWC\ModelTransformation\Tests\TargetClass', $targetObject);
        $this->assertEquals('public', $targetObject->targetPublicProperty);
        $this->assertEquals('private', $targetObject->getTargetPrivateProperty());
        $this->assertEquals('nested', $targetObject->getTargetObjectProperty()->property);
    }

    public function testObjectToObjectTransformationWithFilterNotUsingTargetValues()
    {
        $sourceObject = new SourceClass('John', 'Smith');
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';

        $filter = function ($targetPropertyValue, $name, $surname) {
                    return sprintf('%s %s', $name, $surname);
                };

        $transformationRuleArray = new RuleSet();
        $transformationRuleArray->addRule(array('sourcePublicProperty', 'sourcePrivateProperty'), 'targetPublicProperty', $filter);

        $targetObject = $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray);

        $this->assertEquals('John Smith', $targetObject->targetPublicProperty);
    }

    public function testObjectToObjectTransformationWithFilterUsingTargetValues()
    {
        $sourceObject = new SourceClass(5);
        $targetObject = new TargetClass();
        $targetObject->setTargetPrivateProperty(5);

        $filter = function ($targetPropertyValue, $sourcePropertyValue) {
                    return $targetPropertyValue + $sourcePropertyValue;
                };

        $transformationRuleArray = new RuleSet();
        $transformationRuleArray->addRule('sourcePublicProperty', 'targetPrivateProperty', $filter);

        $targetObject = $this->transformer->transform($sourceObject, $targetObject, $transformationRuleArray);

        $this->assertEquals(10, $targetObject->getTargetPrivateProperty());
    }

    public function testObjectToObjectTransformationWithFilterUsingExternalService()
    {
        $service = $this->getMock('PWC\ModelTransformation\Tests\Service');
        $service->expects($this->once())->method('get')->will($this->returnValue(10));

        $sourceObject = new SourceClass(5);
        $targetClass = 'PWC\ModelTransformation\Tests\TargetClass';

        $filter = function ($targetPropertyValue, $sourcePropertyValue) use ($service) {
                            return $sourcePropertyValue + $service->get();
                        };

        $transformationRuleArray = new RuleSet();
        $transformationRuleArray->addRule('sourcePublicProperty', 'targetPublicProperty', $filter);

        $targetObject = $this->transformer->transform($sourceObject, $targetClass, $transformationRuleArray);

        $this->assertEquals(15, $targetObject->targetPublicProperty);
    }

    public function testObjectToObjectTransformationWithGlobalRegisteredFilter()
    {
        $source = new SourceClass(5);
        $target = new TargetClass();

        $ruleSet = new RuleSet();
        $ruleSet->registerFilter('double', function ($target, $source) { return 2 * $source; });
        $ruleSet->addRule('sourcePublicProperty', 'targetPublicProperty', 'double');

        $targetObject = $this->transformer->transform($source, $target, $ruleSet);

        $this->assertEquals(10, $targetObject->targetPublicProperty);
    }

    public function testObjectToObjectTransformationWithGlobalPrependFilter()
    {
        $source = new SourceClass(5);
        $target = new TargetClass();

        $ruleSet = new RuleSet();
        $ruleSet->addFilter(function ($target, $source) { return 2 * $source; }, true);

        $ruleSet->addRule('sourcePublicProperty', 'targetPublicProperty', function ($target, $source) { return $source + 2; });

        $targetObject = $this->transformer->transform($source, $target, $ruleSet);

        $this->assertEquals(12, $targetObject->targetPublicProperty);
    }

    public function testObjectToObjectTransformationWithGlobalAppendFilter()
    {
        $source = new SourceClass(5);
        $target = new TargetClass();

        $ruleSet = new RuleSet();
        $ruleSet->addFilter(function ($target, $source) { return 2 * $source; });

        $ruleSet->addRule('sourcePublicProperty', 'targetPublicProperty', function ($target, $source) { return $source + 2; });

        $targetObject = $this->transformer->transform($source, $target, $ruleSet);

        $this->assertEquals(14, $targetObject->targetPublicProperty);
    }

}

class SourceClass
{

    public $sourcePublicProperty;
    private $sourcePrivateProperty;
    private $sourceObjectProperty;

    public function __construct($sourcePublicPropertyValue = null, $sourcePrivatePropertyValue = null, $sourceObjectProperty = null)
    {
        $this->sourcePublicProperty = $sourcePublicPropertyValue;
        $this->sourcePrivateProperty = $sourcePrivatePropertyValue;
        $this->sourceObjectProperty = $sourceObjectProperty;
    }

    public function getSourcePrivateProperty()
    {
        return $this->sourcePrivateProperty;
    }

    public function setSourceObjectProperty($sourceObject)
    {
        $this->sourceObjectProperty = $sourceObject;

        return $this;
    }

    public function getSourceObjectProperty()
    {
        return $this->sourceObjectProperty;
    }

}

class TargetClass
{

    public $targetPublicProperty;
    private $targetPrivateProperty;
    private $targetObjectProperty;

    public function __construct()
    {
        $this->targetObjectProperty = new SimpleClass();
    }

    public function setTargetPrivateProperty($targetPrivatePropertyValue)
    {
        $this->targetPrivateProperty = $targetPrivatePropertyValue;

        return $this;
    }

    public function getTargetPrivateProperty()
    {
        return $this->targetPrivateProperty;
    }

    public function setTargetObjectProperty(SimpleClass $object)
    {
        $this->targetObjectProperty = $object;

        return $this;
    }

    public function getTargetObjectProperty()
    {
        return $this->targetObjectProperty;
    }

}

class SimpleClass
{

    public $property;

    public function __construct($propertyValue = null)
    {
        $this->property = $propertyValue;
    }

}

class OtherSimpleClass
{

}

interface Service
{

    public function get();
}
