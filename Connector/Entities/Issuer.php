<?php
namespace iDEALConnector\Entities;

use InvalidArgumentException;
/**
 * The Issuer class specific to the directoryResponse.
 */
class Issuer
{
    private $id;
    private $name;

    /**
     * @param string $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        
        if(!is_string($id))
            throw new InvalidArgumentException("Parameter 'id' must be of type string.");
        
        if(!is_string($name))
            throw new InvalidArgumentException("Parameter 'name' must be of type string.");
        
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
