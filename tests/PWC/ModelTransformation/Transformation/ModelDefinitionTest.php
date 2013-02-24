<?php

namespace PWC\ModelTransformation\Tests\Transformation;

use PWC\ModelTransformation\Transformation\ModelDefinition;

/**
 * Description of ModelDefinitionTest
 *
 * @author Paweł A. Wacławczyk <pawel.waclawczyk@netteam.pl>
 */
class ModelDefinitionTest extends \PHPUnit_Framework_TestCase
{

    private $model;
    private $modelDefinition;

    public function setUp()
    {
        parent::setUp();

        $this->model = new MainClass();
        $this->model->publicProperty = 'publicValue';
        $this->model->setPrivateProperty('privateValue');
        $this->model->setSubObject(new SubClass());
        $this->model->getSubObject()->publicProperty = 'subPublicProperty';
        $this->model->getSubObject()->setPrivateProperty('subPrivateProperty');

        $this->modelDefinition = new ModelDefinition();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->model = null;
    }

    public function testSimpleObjectReadingWithEmptyModelDefinition()
    {
        $this->assertEquals('publicValue', $this->modelDefinition->getValue($this->model, 'publicProperty'));
        $this->assertEquals('privateValue', $this->modelDefinition->getValue($this->model, 'privateProperty'));
        $this->assertEquals('subPublicProperty', $this->modelDefinition->getValue($this->model, 'subObject.publicProperty'));
        $this->assertEquals('subPrivateProperty', $this->modelDefinition->getValue($this->model, 'subObject.privateProperty'));
    }

    public function testSimpleObjectReadingWithFullModelDefinition()
    {
        $this->modelDefinition->isInstanceOf('PWC\ModelTransformation\Tests\Transformation\MainClass')
                ->hasChild('publicProperty')->isInstanceOf('string')->getParent()
                ->hasChild('privateProperty')->isInstanceOf('string')->getParent()
                ->hasChild('subObject')->isInstanceOf('PWC\ModelTransformation\Tests\Transformation\SubClass')
                ->hasChild('publicProperty')->isInstanceOf('string')->getParent()
                ->hasChild('privateProperty')->isInstanceOf('string');

        $this->assertEquals('publicValue', $this->modelDefinition->getValue($this->model, 'publicProperty'));
        $this->assertEquals('privateValue', $this->modelDefinition->getValue($this->model, 'privateProperty'));
        $this->assertEquals('subPublicProperty', $this->modelDefinition->getValue($this->model, 'subObject.publicProperty'));
        $this->assertEquals('subPrivateProperty', $this->modelDefinition->getValue($this->model, 'subObject.privateProperty'));
    }

    /**
     * @expectedException PWC\ModelTransformation\Transformation\Exception\TypeMismatchException
     */
    public function testSimpleObjectReadingWithFullModelDefinitionAndExpectTypeMismatchException()
    {
        $this->model->setSubObject(new OtherSubClass());

        $this->modelDefinition->isInstanceOf('PWC\ModelTransformation\Tests\Transformation\MainClass')
                ->hasChild('publicProperty')->isInstanceOf('string')->getParent()
                ->hasChild('privateProperty')->isInstanceOf('string')->getParent()
                ->hasChild('subObject')->isInstanceOf('PWC\ModelTransformation\Tests\Transformation\SubClass')
                ->hasChild('publicProperty')->isInstanceOf('string')->getParent()
                ->hasChild('privateProperty')->isInstanceOf('string');

        $this->modelDefinition->getValue($this->model, 'subObject.publicProperty');
    }

}

class MainClass
{

    public $publicProperty;
    private $privateProperty;
    private $subObject;

    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    public function setPrivateProperty($value)
    {
        $this->privateProperty = $value;

        return $this;
    }

    /**
     * @return SubClass
     */
    public function getSubObject()
    {
        return $this->subObject;
    }

    public function setSubObject($object)
    {
        $this->subObject = $object;

        return $this;
    }

}

class SubClass
{

    public $publicProperty;
    private $privateProperty;

    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    public function setPrivateProperty($value)
    {
        $this->privateProperty = $value;

        return $this;
    }

}

class OtherSubClass
{

    public $publicProperty;

}
