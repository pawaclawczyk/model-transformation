<?php

namespace PWC\ModelTransformation\Tests;

use PWC\ModelTransformation\RuleSet;

/**
 *
 * @author Paweł A. Wacławczyk <p.a.waclawczyk@gmail.com>
 */
class RuleSetTest extends \PHPUnit_Framework_TestCase
{

    private $transformationRuleSet;

    protected function setUp()
    {
        $this->transformationRuleSet = new RuleSet();
    }

    protected function tearDown()
    {
        $this->transformationRuleSet = null;
    }

    public function testBuildingRuleSet()
    {
        $this->transformationRuleSet
                ->addRule(array('source.property', 'source.another.property'), 'target.property', array(function () {
                        return 1;
                    }, function () {
                        return 2;
                    }))
                ->addRule('source.other.rule', 'target.other.rule');

        $this->assertInstanceOf('\Iterator', $this->transformationRuleSet);

        $this->transformationRuleSet->rewind();
        $transformationRule = $this->transformationRuleSet->current();

        $this->assertEquals(array('source.property', 'source.another.property'), $transformationRule->getSourcePaths());
        $this->assertEquals(array(function () {
                return 1;
            }, function () {
                return 2;
            }), $transformationRule->getFilters());
        $this->assertEquals('target.property', $transformationRule->getTargetPath());

        $this->transformationRuleSet->next();
        $transformationRule = $this->transformationRuleSet->current();

        $this->assertEquals(array('source.other.rule'), $transformationRule->getSourcePaths());
        $this->assertEquals(array(), $transformationRule->getFilters());
        $this->assertEquals('target.other.rule', $transformationRule->getTargetPath());
    }

    public function testBuildingRuleSetFromRuleArray()
    {
        $transformationRuleArray = array(
            'first.property' => 'first',
            'second.property' => 'second',
        );

        $transformationRuleSet = new RuleSet($transformationRuleArray);

        $transformationRuleSet->rewind();
        $transformationRule = $transformationRuleSet->current();

        $this->assertEquals(array('first.property'), $transformationRule->getSourcePaths());
        $this->assertEquals(array(), $transformationRule->getFilters());
        $this->assertEquals('first', $transformationRule->getTargetPath());

        $transformationRuleSet->next();
        $transformationRule = $transformationRuleSet->current();

        $this->assertEquals(array('second.property'), $transformationRule->getSourcePaths());
        $this->assertEquals(array(), $transformationRule->getFilters());
        $this->assertEquals('second', $transformationRule->getTargetPath());
    }

    public function testFindingRuleByProperty()
    {
        $transformationRuleSet = new RuleSet();
        $transformationRuleSet
                ->addRule('propertyA', 'targetA')
                ->addRule(array('propertyB', 'propertyC'), 'targetB');

        $transformationRuleSet->rewind();
        $transformationRule = $transformationRuleSet->current();

        $this->assertEquals($transformationRule, $transformationRuleSet->findRule('targetA'));
        $this->assertEquals($transformationRule, $transformationRuleSet->findRule('propertyA'));

        $transformationRuleSet->next();
        $transformationRule = $transformationRuleSet->current();

        $this->assertEquals($transformationRule, $transformationRuleSet->findRule('propertyB'));

        $this->assertNull($transformationRuleSet->findRule('NotExistingDataPath'));
    }

    public function testIteratorMethods()
    {
        $transformationRuleSet = new RuleSet();
        $transformationRuleSet
                ->addRule('propertyA', 'targetA')
                ->addRule('propertyB', 'targetB');

        $index = 0;
        foreach ($transformationRuleSet as $key => $rule) {
            $this->assertEquals($index, $key);
            $this->assertInstanceOf('PWC\ModelTransformation\RuleInterface', $rule);

            $index++;
        }
    }

    public function testUsingGlobalRegisteredFilter()
    {
        $alias = 'return_one';
        $filter = function () {
                    return 1;
                };

        $this->transformationRuleSet->registerFilter($alias, $filter);
        $this->transformationRuleSet->addRule('sourcePath', 'targetPath', 'return_one');

        $ruleFilters = $this->transformationRuleSet->findRule('sourcePath')->getFilters();
        $this->assertEquals($filter, $ruleFilters[0]);
    }

    public function testUsingGlobalRegisteredFilterCombinedWithInlineDefinedFilter()
    {
        $alias = 'return_one';
        $filter = function () {
                    return 1;
                };

        $this->transformationRuleSet->registerFilter($alias, $filter);

        $otherFilter = function () {
                    return 2;
                };
        $this->transformationRuleSet->addRule('sourcePath', 'targetPath', array($filter, 'return_one'));
        $ruleFilters = $this->transformationRuleSet->findRule('sourcePath')->getFilters();
        $this->assertEquals($otherFilter, $ruleFilters[0]);
        $this->assertEquals($filter, $ruleFilters[1]);
    }

    public function testAddingFilterForAllRules()
    {
        $filterAppend = function () {
                    return 1;
                };

        $filterPrepend = function () {
                    return 2;
                };

        $otherFilter = function () {
                    return 3;
                };

        $this->transformationRuleSet->addFilter($filterPrepend, true);
        $this->transformationRuleSet->addFilter($filterAppend);

        $this->transformationRuleSet->addRule('sourcePath', 'targetPath', $otherFilter);
        $this->transformationRuleSet->addRule('otherSourcePath', 'otherTargetPath');

        $ruleFilters = $this->transformationRuleSet->findRule('sourcePath')->getFilters();
        $this->assertEquals($filterPrepend, $ruleFilters[0]);
        $this->assertEquals($otherFilter, $ruleFilters[1]);
        $this->assertEquals($filterAppend, $ruleFilters[2]);

        $ruleFilters = $this->transformationRuleSet->findRule('otherSourcePath')->getFilters();
        $this->assertEquals($filterPrepend, $ruleFilters[0]);
        $this->assertEquals($filterAppend, $ruleFilters[1]);
    }

    public function testAddingGlobalRegisteredFilterForAllRules()
    {
        $filter = function () {
                    return 1;
                };
        $alias = 'filter_one';

        $this->transformationRuleSet->registerFilter($alias, $filter);
        $this->transformationRuleSet->addFilter($alias);

        $this->transformationRuleSet->addRule('sourcePath', 'targetPath');

        $ruleFilters = $this->transformationRuleSet->findRule('sourcePath')->getFilters();

        $this->assertEquals($filter, $ruleFilters[0]);
    }

}
