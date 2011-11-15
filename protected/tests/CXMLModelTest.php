<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../../extensions/CXMLModel.php';

class Environment extends CXMLModel
{
	public static function model($xml, $className=__CLASS__)
	{
	    return parent::model($xml, $className);
	}
}

class TestFunction extends CXMLModel
{
	public static function model($xml, $className=__CLASS__)
	{
	    return parent::model($xml, $className);
	}
}
 
class CXMLModelTest extends PHPUnit_Framework_TestCase
{
	private $xml = 
	'<?xml version="1.0" encoding="ISO-8859-1"?>
	<TestCase name="LinksTest" method="%s">
		<Environment>
			<QtVersion>4.6.2</QtVersion>
			<QTestVersion>4.6.2</QTestVersion>
		</Environment>
		<TestFunction name="initTestCase">
			<Incident type="pass" file="" line="0"/>
		</TestFunction>
		<TestFunction name="testValidLinks">
			<Incident type="pass" file="" line="0"/>
		</TestFunction>
		<TestFunction name="cleanupTestCase">
			<Incident type="fail" file="" line="0"/>
		</TestFunction>
	</TestCase>';

	private $anotherxml = 
	'<?xml version="1.0" encoding="ISO-8859-1"?>
	<AnyNodeName method="%s">
		<Environment id="I">
			<Node>1</Node>
			<Node>2</Node>
			<Node>3</Node>
			<Node>4</Node>
		</Environment>
		<Environment id="II">
			<Node>A</Node>
			<Node>B</Node>
			<Node>C</Node>
			<Node>D</Node>
			<Node>E</Node>
		</Environment>
	</AnyNodeName>';

    public function testCount()
    {
        $model = TestFunction::model(sprintf($this->xml, __FUNCTION__));
		
		$count = 0;
		foreach ($model as $row)
			$count++;
		$this->assertEquals($count, 3);
		$this->assertEquals($model->length(), 3);
    }
	
	public function testAttach()
    {
        $model = TestFunction::model(sprintf($this->xml, __FUNCTION__));
		
        $this->assertTrue($model != null);
        $this->assertTrue($model->getDomDocument() != null);
        
		$test = new TestFunction();
		$this->assertTrue($test->getDomDocument() == null);
		$this->assertTrue($test->attach($model));
		$this->assertTrue($test->getDomDocument() != null);
		
		$this->assertEquals(5, count(split('<TestFunction', $model->toXml()))); /// 5 parts after split -> 4 <TestFunction> tags
    }
	
	public function testDetach()
    {
	    $model = TestFunction::model(sprintf($this->xml, __FUNCTION__));
		
		$this->assertTrue($model != null);
        $this->assertTrue($model->getDomDocument() != null);
        
        $test = $model->getNext();
        $this->assertTrue($test->getDomDocument() != null);
		$this->assertTrue($test->detach());
		$this->assertTrue($test->getDomDocument() == null);
		
		$this->assertEquals(3, count(split('<TestFunction', $model->toXml()))); /// 3 parts after split -> 2 <TestFunction> tags
    }

	public function testGet()
	{
		$model = TestFunction::model(sprintf($this->xml, __FUNCTION__));
		
		$this->assertEquals('initTestCase', $model->getAttribute('name'));
		$this->assertEquals('pass', $model->Incident->getAttribute('type'));
		
		$model = Environment::model(sprintf($this->xml, __FUNCTION__));
		$this->assertEquals('4.6.2', $model->QtVersion->value());
		$this->assertEquals('4.6.2', $model->QTestVersion->value());
		
		$model = Environment::model(sprintf($this->anotherxml, __FUNCTION__));
		
		$this->assertEquals(2, $model->length());
		$this->assertEquals('I', $model->getAttribute("id"));
		
		/// moveNext/movePrevious/move... affects model
		$model->moveNext();
		$this->assertEquals('II', $model->getAttribute("id"));
		
		/// getNext/getPrevious/get.. don't
		$this->assertEquals('I', $model->getFirst()->getAttribute("id"));
		$this->assertEquals('II', $model->getAttribute("id"));
		
		$model->moveFirst();
		
		/// you can iterate over it
		$count = 0;
		$array = array();
		foreach ($model as $k => $v)
		{
			$array[$v->getAttribute("id")] = $k;
			$this->assertEquals('I', $model->getAttribute("id")); // and it will not affect '$model'
			$count++;
		}
		$this->assertTrue(array_key_exists('I', $array));
		$this->assertTrue(array_key_exists('II', $array));
		$this->assertEquals(2, $count);
		
		/// easy access to children
		$this->assertEquals(4, $model->Node->length());
		
		/// and to node value
		$this->assertEquals('1', $model->Node->value());
		$this->assertEquals('3', $model->Node->get(2)->value());
	}
}
?>