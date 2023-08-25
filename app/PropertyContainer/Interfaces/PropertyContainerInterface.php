<?php

namespace App\PropertyContainer\Interfaces;

/**
 * Interface PropertyContainerInterface
 */
interface PropertyContainerInterface
{
    /**
     * @param $propertyName
     * @param $value
     * @return mixed
     */
    function addProperty($propertyName, $value);

    /**
     * @param $propertyName
     * @return mixed
     */
    function deleteProperty($propertyName);

    /**
     * @param $propertyName
     * @param $value
     * @return mixed
     */
    function setProperty($propertyName, $value);

    /**
     * @param $propertyName
     * @return mixed
     */
    function getProperty($propertyName);
}
