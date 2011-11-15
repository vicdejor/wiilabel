<?php
/**
 * CXMLModel class file.
 *
 * @author Krzysztof Wójcicki
 * @link http://www.kwojcicki.pl/
 * @copyright Copyright &copy; 2010 Krzysztof Wójcicki
 * @license http://www.kwojcicki.pl
 */

/**
 * CXMLModel is the base class for classes representing xml data.
 * It provides easy access to nodes and attributes 
 * 
 * @author Krzysztof Wójcicki <qiang.xue@gmail.com>
 * @version $Id: CXMLModel.php 1 2010-04-29 $
 */
class CXMLModel extends CModel
{
	/**
	 * combined file and class names => model
	 */
	private static $_models=array();

	/**
	 * Model id (key for $_models array)
	 */
	private $model;
	
	/**
	 * Dom document loaded from xml
	 */
	private $domDocument;
	
	/**
	 * Xpath object for dom document
	 */
	private $domXpath;
	
	/**
	 * Actual DOM node
	 */
	private $domNode;
	
	/**
	 * Path to the xml file
	 */
	private $file;
	
	/**
	 * XPath used to select nodes
	 */
	private $path;
	
	/**
	 * Applied filter
	 */
	private $filter;

	/**
	 * True if data has changed
	 */
	private $__hasChanges;
	
	/**
	 * Constructor.
	 * @param bool internal.
	 */
	public function __construct($internal = false)
	{
		if ($internal)
			return;

		$this->domDocument = new DomDocument(1.0, 'utf-8');
		$this->domXpath = new DOMXPath($this->domDocument);
		$this->domNode = $this->domDocument->createElement(get_class($this));
		
		if (!$this->domDocument->hasChildNodes())
			$this->domDocument->appendChild($this->domDocument->createElement('Root'));
		$this->domDocument->firstChild->appendChild($this->domNode);
		
		/// make it detached
		$this->domDocument = null;
		$this->domXpath = null;
		
		$this->__hasChanges = false;
		
		$this->init();
		$this->afterConstruct();
	}

	/**
	 * If model is loaded from file and was changed it will be saved 
	 */
	public function __destruct()
	{
		if ($this->file != null && self::$_models[$this->model] === $this && $this->__hasChanges)
			$this->domDocument->save($this->file);
	}
	
	public function getDomDocument()
	{
		return $this->domDocument;
	}
	
	/**
	 * Returns dom node
	 * @return unknown_type
	 */
	public function getDomNode()
	{
		return $this->domNode;
	}
	
	/**
	 * Dumps model as xml string
	 */
	public function toXml()
	{
		return $this->domDocument->saveXML();
	}
	
	/**
	 * Attach record to specified model
	 * @param $model
	 * @return true on success, false otherwise
	 */
	public function attach($model = null)
	{
		if ($this->domDocument != null || $this->domXpath != null)
			return false;
			
		if ($model === null)
		{
			$tmp = get_class($this) . '::';
			foreach (array_keys(self::$_models) as $k)
			{
				if (strpos($k, $tmp) === 0)
				{
					$model = $k;
					break;
				}
			}
			if ($model === null)
				return false;
		}
		if (is_string($model) && !isset(self::$_models[$model]))
		{
			return false;
		}
		if (is_string($model))
		{
			$model = self::$_models[$model];
		}
		
		$this->model = $model->model;
		$this->domDocument = $model->domDocument;
		$this->domXpath = $model->domXpath;
		$this->domNode = $this->domDocument->importNode($this->domNode, true); // make clone to detach from old document
		
		$this->domDocument->firstChild->appendChild($this->domNode);
		
		return true;
	}
	
	/**
	 * Detach record from model
	 * @return unknown_type
	 * @return true on success, false otherwise
	 */
	public function detach()
	{
		if ($this->domDocument == null || $this->domXpath == null)
			return false;
		if (!isset(self::$_models[$this->model]))
			return false;
		
		$this->domDocument->firstChild->removeChild($this->domNode);
		
		$this->domXpath = null;
		$this->domDocument = null;
		$this->domNode = $this->domNode->cloneNode(true); // make clone to detach from old document
		
		return true;
	}
	
	
	/**
	 * Initializes this model.
	 * This method is invoked when an AR instance is newly created and has
	 * its {@link scenario} set.
	 * You may override this method to provide code that is needed to initialize the model (e.g. setting
	 * initial property values.)
	 * @since 1.0.8
	 */
	public function init()
	{
	}

	public function __clone()
	{
		$className = get_class($this);
		
		$model = new $className(true);
		$model->model = $this->model;
		$model->domDocument = $this->domDocument;
		$model->domXpath = $this->domXpath;
		$model->domNode = $this->domNode;
		$model->file = $this->file;
		$model->path = $this->path;
		
		return $model;
	}
	
	/**
	 * PHP getter magic method.
	 * This method is overridden so that Model attributes can be accessed like properties.
	 * @param string property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name)
	{
		$xpath = './' . $name;
		if ($this->filter !== null && strpos($this->filter, $xpath) == 0)
			$xpath = $this->filter;
		$result = $this->xpathQuery($xpath, $this->domNode);
		
		if ($result->length > 0)
		{
			return $this->instantiate($result->item(0));
		}
		
		return null;
	}

	/**
	 * PHP setter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string property name
	 * @param mixed property value
	 */
	public function __set($name, $value)
	{
		self::$_models[$this->model]->__hasChanges = true;
		
		$xpath = './' . $name;
		if ($this->filter !== null && strpos($this->filter, $xpath) == 0)
			$xpath = $this->filter;
		$result = $this->xpathQuery($xpath, $this->domNode);
		if ($result->length > 0)
		{
			if ($result->length == 1)
			{
				return $this->instantiate($result->item(0));
			}
			
			$records = array();
			for ($i = 0; $i < $result->length; ++$i)
				$records[] = $this->instantiate($result->item($i));
			
			return $records;
		}
		
		return null;
	}

	/**
	 * Checks if a property value is null.
	 * This method overrides the parent implementation by checking
	 * if the named attribute is null or not.
	 * @param string the property name or the event name
	 * @return boolean whether the property value is null
	 * @since 1.0.1
	 */
	public function __isset($name)
	{
		if ($this->xpathQuery('./' . $name, $this->domNode)->length > 0)
			return true;
			
		return parent::__isset($name);
	}

	/**
	 * Sets a component property to be null.
	 * This method overrides the parent implementation by clearing
	 * the specified attribute value.
	 * @param string the property name or the event name
	 * @since 1.0.1
	 */
	public function __unset($name)
	{
		while ($this->xpathQuery('./' . $name, $this->domNode)->length > 0)
			$this->$name->detach();
		
		parent::__unset($name);
	}

	/**
	 * Returns the static model of the specified Model class.
	 * The model returned is a static instance of the Model class.
	 * It is provided for invoking class-level methods (something similar to static class methods.)
	 *
	 * EVERY derived Model class must override this method as follows,
	 * <pre>
	 * public static function model($xml, $className=__CLASS__)
	 * {
	 *     return parent::model($xml, $className);
	 * }
	 * </pre>
	 *
	 * @param string active xml file name or xml data
	 * @param string active record class name.
	 * @return CActiveRecord active record model instance.
	 */
	public static function model($xml, $className=__CLASS__)
	{
		$key = $className . '::' . (is_file($xml) ? $xml : md5($xml));
		
		if (isset(self::$_models[$key]))
		{
			return self::$_models[$key]->$className;
		}
		else
		{
			$model = self::$_models[$key] = new $className(true);
			$model->model = $key;
			$model->domDocument = new DomDocument(1.0, 'utf-8');
			if (is_file($xml))
				$model->domDocument->load($xml);
			else
				$model->domDocument->loadXML($xml);
				
			$model->domXpath = new DOMXPath($model->domDocument);
			$model->domNode = null;
			$model->file = is_file($xml) ? $xml : null;
			$model->path = '/*/';
			
			$result = $model->xpathQuery('/*');
			if ($result->length > 0)
				$model->domNode = $result->item(0);
			
			return $model->$className;
		}
	}

	/**
	 * Returns the list of all attribute names of the model.
	 * This would return all attribute names of the node associated with this Model class.
	 * @return array list of attribute names.
	 */
	public function attributeNames()
	{
		$result = array();
		foreach ($this->domNode->attributes as $key => $value)
			$result[] = $key;
			
		return $result;
	}

	/**
	 * @param string attribute name
	 * @return boolean whether this Model has the named attribute (node attribute).
	 */
	public function hasAttribute($name)
	{
		return $this->domNode->attributes->getNamedItem($name) != null;
	}

	/**
	 * Returns the named attribute value.
	 * @param string the attribute name
	 * @return mixed the attribute value. Null if the attribute is not set or does not exist.
	 * @see hasAttribute
	 */
	public function getAttribute($name)
	{
		return $this->domNode->attributes->getNamedItem($name) == null ? null : $this->domNode->attributes->getNamedItem($name)->nodeValue;
	}

	public function setAttribute($name, $value)
	{
		self::$_models[$this->model]->__hasChanges = true;
		
		$node = $this->domNode->attributes->getNamedItem($name);
		if ($node == null)
		{
			$node = $this->domNode->appendChild($this->domDocument->createAttribute($name));
		}
		if ($node != null)
		{
			$node->nodeValue = $value;
		}
		return $node != null;
	}

	public function getAttributes()
	{
		$result = array();
		foreach ($this->domNode->attributes as $key => $value)
			$result[$key] = $value;
			
		return $result;
	}

	
	public function length()
	{
		$length = 0;
		for ($node = $this->firstSibling(); $node != null; $node = $this->nextSibling($node))
			$length++;
		
		return $length;
	}
	
	public function setInnerXML($value)
	{
		$doc = DOMDocument::loadXML("<root>" . $value . "</root>");
		$node = $doc->firstChild->cloneNode(true); // clone root node
		
		while ($this->domNode->childNodes->length > 0)
			$this->domNode->removeChild($this->domNode->lastChild);
		
		for ($i = 0; $i < $node->children->length; ++$i)
			$this->domNode->appendChild($node->children->item($i));
	}
	
	public function innerXML()
	{
		if ($this->domNode->childNodes->length == 0)
			return null;
		
		$node = $this->domNode->nodeName;
		
		$value = $this->domDocument->saveXML($this->domNode);
		$value = preg_replace('/^<' . $node . '[^>]*>(.*)<\\/' . $node . '>$/sm', '\\1', $value);
		return $value;
	}
	
	public function outerXML()
	{
		if ($this->domNode->childNodes->length == 0)
			return null;
		
		$node = $this->domNode->nodeName;
		$value = $this->domDocument->saveXML($this->domNode);
		return $value;
	}
	
	public function setValue($value)
	{
		$this->setInnerXML($value);
	}
	
	/**
	 * Synonim for innerXML
	 * @return unknown_type
	 */
	public function value()
	{
		return $this->innerXML();
	}
	
	/**
	 * This event is raised after the record instance is created by new operator.
	 * @param CEvent the event parameter
	 * @since 1.0.2
	 */
	public function onAfterConstruct($event)
	{
		$this->raiseEvent('onAfterConstruct',$event);
	}

	public function onBeforeFind($event)
	{
		$this->raiseEvent('onBeforeFind',$event);
	}

	public function onAfterFind($event)
	{
		$this->raiseEvent('onAfterFind',$event);
	}


	/**
	 * This method is invoked after a record instance is created by new operator.
	 * The default implementation raises the {@link onAfterConstruct} event.
	 * You may override this method to do postprocessing after record creation.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterConstruct()
	{
		if($this->hasEventHandler('onAfterConstruct'))
			$this->onAfterConstruct(new CEvent($this));
	}

	protected function beforeFind()
	{
		if($this->hasEventHandler('onBeforeFind'))
			$this->onBeforeFind(new CEvent($this));
	}

	protected function afterFind()
	{
		if($this->hasEventHandler('onAfterFind'))
			$this->onAfterFind(new CEvent($this));
	}

	public function beforeFindInternal()
	{
		$this->beforeFind();
	}

	public function afterFindInternal()
	{
		$this->afterFind();
	}
	
	/**
	 * Compares this model with another one.
	 */
	public function equals($record)
	{
		return $this->domDocument===$record->domDocument && $this->domNode===$record->domNode && $this->path===$record->path;
	}

	/**
	 * Creates an model instance.
	 */
	protected function instantiate(&$node)
	{
		$model = clone $this;
		$model->domNode = $node;
		$model->path .= '/' . $node->nodeName;
		
		return $model;
	}
	
	public function getIterator()
	{
		return new CXMLModelIterator($this);
	}
	
	public function moveFirst()
	{
		if ($this->domNode == null)
			return false;
		
		$this->domNode = $this->firstSibling();
		return true;
	}
	
	public function moveLast()
	{
		if ($this->domNode == null)
			return false;
		
		$this->domNode = $this->lastSibling();
		return true;
	}
	
	public function movePrevious()
	{
		if ($this->domNode == null)
			return false;
		
		$node = $this->previousSibling();
		if ($node == null)
			return false;
			
		$this->domNode = $node;
		return true;
	}
	
	public function moveNext()
	{
		if ($this->domNode == null)
			return false;
		
		$node = $this->nextSibling();
		if ($node == null)
			return false;
			
		$this->domNode = $node;
		return true;
	}
	
	public function moveTo($i)
	{
		$result = $this->moveFirst();
		while ($i-- > 0)
			$result = $result && $this->moveNext();
			
		return $result;
	}
	
	public function moveBy($i)
	{
		$result = true;
		if ($i > 0)
		{
			while ($i-- > 0)
				$result = $result && $this->moveNext();
		}
		else if ($i < 0)
		{
			while ($i++ < 0)
				$result = $result && $this->movePrevious();
		}
		return $result;
	}
	
	public function getFirst()
	{
		$clone = clone $this;
		if ($clone->moveFirst())
			return $clone;
			
		return null;
	}
	
	public function getLast()
	{
		$clone = clone $this;
		if ($clone->moveLast())
			return $clone;
			
		return null;
	}
	
	public function getPrevious()
	{
		$clone = clone $this;
		if ($clone->movePrevious())
			return $clone;
			
		return null;
	}
	
	public function getNext()
	{
		$clone = clone $this;
		if ($clone->moveNext())
			return $clone;
			
		return null;
	}
	
	public function get($i)
	{
		$clone = clone $this;
		if ($clone->moveTo($i))
			return $clone;
			
		return null;
	}
	
	/// utility methods
	protected function xpathQuery($query, &$node = null)
	{
		return $this->domXpath->evaluate($query, $node == null ? $this->domDocument : $node);
	}
	
	protected function nextSibling($node = null)
	{
		if ($this->domNode == null)
			return null;
	
		if ($node === null)
			$node = $this->domNode;
		
		do
		{
			$node = $node->nextSibling;
		}
		while ($node != null && $node->nodeName != $this->domNode->nodeName);
		
		return $node;
	}
	
	protected function previousSibling($node = null)
	{
		if ($this->domNode == null)
			return null;
	
		if ($node === null)
			$node = $this->domNode;
		
		do
		{
			$node = $node->previousSibling;
		}
		while ($node != null && $node->nodeName != $this->domNode->nodeName);
		
		return $node;
	}
	
	protected function firstSibling($node = null)
	{
		if ($this->domNode == null)
			return null;
		
		if ($node === null)
			$node = $this->domNode;
			
		while (($t = $this->previousSibling($node)) != null)
			$node = $t;
			
		return $node;
	}
	
	protected function lastSibling($node = null)
	{
		if ($this->domNode == null)
			return null;
		
		if ($node === null)
			$node = $this->domNode;
			
		while (($t = $this->nextSibling($node)) != null)
			$node = $t;
			
		return $node;
	}
}

class CXMLModelIterator implements Iterator
{
	private $model;
	private $valid;
	private $index;
	
	public function __construct($model)
	{
		$this->model = clone $model;
		$this->valid = ($model->getDomNode() != null);
	}

	public function rewind()
	{
		$this->model->moveFirst();
    }

    public function current()
	{
        return $this->model;
    }

    public function key()
	{
        return $this->index;
    }

    public function next()
	{
		$node = $this->model->getDomNode();
        
		$this->model->moveNext();
		$this->index++;
		
		if ($this->model->getDomNode()->isSameNode($node))
			$this->valid = false;
    }

    public function valid()
	{
        return $this->valid;
    }
}
