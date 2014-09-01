<?php

namespace Babesk\Proxies\__CG__\Babesk\ORM;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class SchbasBooks extends \Babesk\ORM\SchbasBooks implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'id', 'title', 'author', 'publisher', 'isbn', 'class', 'bundle', 'price', 'exemplars', 'subject', 'selfpayingUsers');
        }

        return array('__isInitialized__', 'id', 'title', 'author', 'publisher', 'isbn', 'class', 'bundle', 'price', 'exemplars', 'subject', 'selfpayingUsers');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (SchbasBooks $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle($title)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTitle', array($title));

        return parent::setTitle($title);
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTitle', array());

        return parent::getTitle();
    }

    /**
     * {@inheritDoc}
     */
    public function setAuthor($author)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAuthor', array($author));

        return parent::setAuthor($author);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthor()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAuthor', array());

        return parent::getAuthor();
    }

    /**
     * {@inheritDoc}
     */
    public function setPublisher($publisher)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPublisher', array($publisher));

        return parent::setPublisher($publisher);
    }

    /**
     * {@inheritDoc}
     */
    public function getPublisher()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPublisher', array());

        return parent::getPublisher();
    }

    /**
     * {@inheritDoc}
     */
    public function setIsbn($isbn)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsbn', array($isbn));

        return parent::setIsbn($isbn);
    }

    /**
     * {@inheritDoc}
     */
    public function getIsbn()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIsbn', array());

        return parent::getIsbn();
    }

    /**
     * {@inheritDoc}
     */
    public function setClass($class)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClass', array($class));

        return parent::setClass($class);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClass', array());

        return parent::getClass();
    }

    /**
     * {@inheritDoc}
     */
    public function setBundle($bundle)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBundle', array($bundle));

        return parent::setBundle($bundle);
    }

    /**
     * {@inheritDoc}
     */
    public function getBundle()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBundle', array());

        return parent::getBundle();
    }

    /**
     * {@inheritDoc}
     */
    public function setPrice($price)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPrice', array($price));

        return parent::setPrice($price);
    }

    /**
     * {@inheritDoc}
     */
    public function getPrice()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPrice', array());

        return parent::getPrice();
    }

    /**
     * {@inheritDoc}
     */
    public function addExemplar(\Babesk\ORM\SchbasInventory $exemplars)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addExemplar', array($exemplars));

        return parent::addExemplar($exemplars);
    }

    /**
     * {@inheritDoc}
     */
    public function removeExemplar(\Babesk\ORM\SchbasInventory $exemplars)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeExemplar', array($exemplars));

        return parent::removeExemplar($exemplars);
    }

    /**
     * {@inheritDoc}
     */
    public function getExemplars()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getExemplars', array());

        return parent::getExemplars();
    }

    /**
     * {@inheritDoc}
     */
    public function setSubject(\Babesk\ORM\SystemSchoolSubjects $subject = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSubject', array($subject));

        return parent::setSubject($subject);
    }

    /**
     * {@inheritDoc}
     */
    public function getSubject()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSubject', array());

        return parent::getSubject();
    }

    /**
     * {@inheritDoc}
     */
    public function addSelfpayingUser(\Babesk\ORM\SystemUsers $selfpayingUsers)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addSelfpayingUser', array($selfpayingUsers));

        return parent::addSelfpayingUser($selfpayingUsers);
    }

    /**
     * {@inheritDoc}
     */
    public function removeSelfpayingUser(\Babesk\ORM\SystemUsers $selfpayingUsers)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeSelfpayingUser', array($selfpayingUsers));

        return parent::removeSelfpayingUser($selfpayingUsers);
    }

    /**
     * {@inheritDoc}
     */
    public function getSelfpayingUsers()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSelfpayingUsers', array());

        return parent::getSelfpayingUsers();
    }

}
