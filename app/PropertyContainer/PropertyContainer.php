<?php

declare(strict_types=1);

namespace App\PropertyContainer;

use App\PropertyContainer\Interfaces\PropertyContainerInterface;
use Exception;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;

class PropertyContainer implements PropertyContainerInterface
{
    private $propertyContainer = [];

    /**
     * @param $propertyName
     * @param $value
     * @return void
     * @throws Exception
     */
    public function addProperty($propertyName, $value): void
    {
        if (empty($propertyName)) {
            throw new Exception(__("exception.not_set_property"));
        }

        if (empty($value)) {
            throw new Exception(__("exception.not_set_value"));
        }

        $this->propertyContainer[$propertyName] = $value;
    }

    /**
     * @param $propertyName
     * @return void
     * @throws Exception
     */
    public function deleteProperty($propertyName): void
    {
        if (empty($propertyName)) {
            throw new Exception(__("exception.not_set_property"));
        }

        unset($this->propertyContainer[$propertyName]);
    }

    /**
     * @param $propertyName
     * @param $value
     * @return void
     * @throws Exception
     */
    public function setProperty($propertyName, $value): void
    {
        if (empty($propertyName)) {
            throw new Exception(__("exception.not_set_property"));
        }

        if (empty($value)) {
            throw new Exception(__("exception.not_set_value"));
        }

        if (!isset($this->propertyContainer[$propertyName])) {
            throw new Exception(__("exception.not_found_property"));
        }

        $this->propertyContainer[$propertyName] = $value;
    }

    /**
     * @param $propertyName
     * @return HigherOrderBuilderProxy|mixed
     * @throws Exception
     */
    public function getProperty($propertyName)
    {
        if (empty($propertyName)) {
            throw new Exception(__("exception.not_set_property"));
        }

        $property = $this->propertyContainer[$propertyName];

        if (empty($property)) {
            throw new Exception(__("exception.not_found_property"));
        }

        return $property->value;
    }
}
