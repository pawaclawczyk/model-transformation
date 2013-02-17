<?php

namespace PWC\ModelTransformation\Tests\TranasformationRuleSet;

use PWC\ModelTransformation\TransformationRuleSet\TransformationRuleSet;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class TransformationRuleSetTest extends \PHPUnit_Framework_TestCase
{

    private $transformationRuleSet;

    public function setUp()
    {
        parent::setUp();
        $this->transformationRuleSet = new TransformationRuleSet();
    }

    public function testBuildingTransformationRuleSet()
    {
        $this->transformationRuleSet
                ->addRule()
                ->setSourceProperties(array('source.property', 'source.another.property'))
                ->setFilters(array(function () {
                        return 1;
                    }))->addFilter(function () {
                            return 2;
                        })
                ->setTargetProperty('target.property')
                ->addRule()
                ->addSourceProperty('source.other.rule')
                ->setTargetProperty('target.other.rule');

        $this->assertInstanceOf('\Iterator', $this->transformationRuleSet);

        $this->transformationRuleSet->rewind();
        $transformationRule = $this->transformationRuleSet->current();

        $this->assertEquals(array('source.property', 'source.another.property'), $transformationRule->getSourceProperties());
        $this->assertEquals(array(function () {
                return 1;
            }, function () {
                return 2;
            }), $transformationRule->getFilters());
        $this->assertEquals('target.property', $transformationRule->getTargetProperty());

        $this->transformationRuleSet->next();
        $transformationRule = $this->transformationRuleSet->current();

        $this->assertEquals(array('source.other.rule'), $transformationRule->getSourceProperties());
        $this->assertEquals(array(), $transformationRule->getFilters());
        $this->assertEquals('target.other.rule', $transformationRule->getTargetProperty());
    }

    public function testBuildingTransformationRuleSetFromTransformationRuleArray()
    {
        $transformationRuleArray = array(
            'first.property' => 'first',
            'second.property' => 'second',
        );

        $transformationRuleSet = new TransformationRuleSet($transformationRuleArray);

        $transformationRuleSet->rewind();
        $transformationRule = $transformationRuleSet->current();

        $this->assertEquals(array('first.property'), $transformationRule->getSourceProperties());
        $this->assertEquals(array(), $transformationRule->getFilters());
        $this->assertEquals('first', $transformationRule->getTargetProperty());

        $transformationRuleSet->next();
        $transformationRule = $transformationRuleSet->current();

        $this->assertEquals(array('second.property'), $transformationRule->getSourceProperties());
        $this->assertEquals(array(), $transformationRule->getFilters());
        $this->assertEquals('second', $transformationRule->getTargetProperty());
    }

    public function testFindingRuleByProperty()
    {
        $transformationRuleSet = new TransformationRuleSet();
        $transformationRuleSet
                ->addRule()->addSourceProperty('propertyA')->setTargetProperty('targetA')
                ->addRule()->setSourceProperties(array('propertyB', 'propertyC'))->setTargetProperty('targetB');

        $transformationRuleSet->rewind();
        $transformationRule = $transformationRuleSet->current();

        $this->assertEquals($transformationRule, $transformationRuleSet->findRule('targetA'));
        $this->assertEquals($transformationRule, $transformationRuleSet->findRule('propertyA'));

        $transformationRuleSet->next();
        $transformationRule = $transformationRuleSet->current();

        $this->assertEquals($transformationRule, $transformationRuleSet->findRule('propertyB'));
    }

}
