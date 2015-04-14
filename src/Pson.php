<?php
namespace Nikapps\Pson;

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Nikapps\Pson\Annotations\Expose;
use Nikapps\Pson\Annotations\PsonType;
use Nikapps\Pson\Annotations\SerializedName;
use Nikapps\Pson\Annotations\Transient;
use Nikapps\Pson\Exception\NotFoundJsonKeyException;
use ReflectionClass;
use ReflectionObject;

class Pson
{

    /**
     * true: if json key is not exist throw exceptions
     *
     * @var boolean
     */
    protected $strict = false;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @param Reader|null $reader
     */
    function __construct(Reader $reader = null)
    {
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/PsonType.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/SerializedName.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/Transient.php');

        if (!$reader) {
            $this->annotationReader = new AnnotationReader();
        } else {
            $this->annotationReader = $reader;
        }

    }

    /**
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }

    /**
     * @param AnnotationReader $annotationReader
     */
    public function setAnnotationReader($annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }


    /**
     * @return boolean
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * @param boolean $strict
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;
    }

    /**
     * @param object $classInstance
     * @param bool $returnArray = false
     * @return string|array
     */
    public function toJson($classInstance, $returnArray = false)
    {

        if (!$classInstance) {
            if ($returnArray) {
                return [];
            } else {
                return json_encode([]);
            }
        }

        $reflectionClass = new ReflectionObject($classInstance);

        $output = [];

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $property) {

            if ($this->isTransient($property)) {
                continue;
            }

            $keyName = $this->getJsonKeyName($property);

            $property->setAccessible(true);

            $psonType = $this->getType($property);

            if (!is_null($psonType) && !$psonType->isTypeScalar()) {
                //pson type is defined
                if ($psonType->isArray()) {
                    $jsonValue = $this->convertObjectsToJson(
                        $property,
                        $classInstance
                    );
                } else {
                    $jsonValue = $this->convertSingleObjectToJson(
                        $property,
                        $classInstance
                    );
                }
            } else {
                //pson type is not defined
                $jsonValue = $property->getValue($classInstance);
            }

            $output[$keyName] = $jsonValue;
        }

        if ($returnArray) {
            return $output;
        } else {
            return json_encode($output);
        }

    }

    /**
     * @param string $class
     * @param string|array $json
     * @throws NotFoundJsonKeyException
     * @return object
     */
    public function fromJson($class, $json)
    {
        // convert to array
        if (!is_array($json)) {
            $json = json_decode($json, 1);
        }

        $reflectionClass = new ReflectionClass($class);
        $classInstance = $reflectionClass->newInstanceWithoutConstructor();

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $property) {

            // if property should be ignored
            if ($this->isTransient($property)) {
                continue;
            }

            $jsonKey = $this->getJsonKeyName($property);

            if ($this->isStrict() && !isset($json[$jsonKey])) {
                throw new NotFoundJsonKeyException("key $jsonKey is not exist");
            }

            $jsonValue = $this->getJsonValue($json, $jsonKey);

            $psonType = $this->getType($property);

            if (!is_null($psonType) && !$psonType->isTypeScalar()) {
                //if pson type is defined
                if ($psonType->isArray()) {
                    $value = $this->createArrayOfObjects(
                        $psonType,
                        $jsonValue
                    );
                } else {
                    $value = $this->createSingleObject(
                        $psonType,
                        $jsonValue
                    );
                }
            } else {
                //pson type is not defined
                $value = $jsonValue;
            }

            $property->setAccessible(true);
            $property->setValue($classInstance, $value);
        }

        return $classInstance;
    }

    /**
     * is Transient property? (for ignoring)
     *
     * @param \ReflectionProperty $property
     * @return bool
     */
    protected function isTransient(\ReflectionProperty $property)
    {
        /** @var Transient $transient */
        $transient = $this->annotationReader->getPropertyAnnotation(
            $property,
            'Nikapps\Pson\Annotations\Transient'
        );

        return !!$transient;
    }

    /**
     * get json key based on SerializedName() or property name
     *
     * @param \ReflectionProperty $property
     * @return string
     */
    protected function getJsonKeyName(\ReflectionProperty $property)
    {

        /** @var SerializedName $serializedName */
        $serializedName = $this->annotationReader->getPropertyAnnotation(
            $property,
            'Nikapps\Pson\Annotations\SerializedName'
        );


        if (!is_null($serializedName)) {
            $jsonKey = $serializedName->key;
        } else {

            $jsonKey = $property->getName();
        }

        return $jsonKey;
    }

    /**
     * get json value
     *
     * @param array $json
     * @param string $jsonKey
     * @return mixed
     */
    protected function getJsonValue($json, $jsonKey)
    {

        if (isset($json[$jsonKey])) {
            $jsonValue = $json[$jsonKey];
        } else {
            $jsonValue = null;
        }

        return $jsonValue;
    }

    /**
     * get pson type from property
     *
     * @param \ReflectionProperty $property
     * @return PsonType
     */
    protected function getType(\ReflectionProperty $property)
    {
        /** @var PsonType $psonType */
        $psonType = $this->annotationReader->getPropertyAnnotation(
            $property,
            'Nikapps\Pson\Annotations\PsonType'
        );

        return $psonType;
    }

    /**
     * create array of objects
     *
     * @param PsonType $psonType
     * @param array $jsonValue
     * @throws NotFoundJsonKeyException
     * @return object[]
     */
    protected function createArrayOfObjects(PsonType $psonType, $jsonValue)
    {
        $objArray = [];

        if (!is_array($jsonValue)) {
            return $objArray;
        }

        foreach ($jsonValue as $value) {

            $childClassObj = $this->fromJson(
                $psonType->getClassType(),
                $value
            );

            $objArray[] = $childClassObj;
        }

        return $objArray;
    }

    /**
     * create single object
     *
     * @param PsonType $psonType
     * @param $jsonValue
     * @return object
     * @throws NotFoundJsonKeyException
     */
    protected function createSingleObject(PsonType $psonType, $jsonValue)
    {

        $singleObj = $this->fromJson(
            $psonType->getClassType(),
            $jsonValue
        );

        return $singleObj;
    }

    /**
     * convert objects to json array
     *
     * @param \ReflectionProperty $property
     * @param $classInstance
     * @return array
     */
    protected function convertObjectsToJson(
        \ReflectionProperty $property,
        $classInstance
    ) {

        $values = $property->getValue($classInstance);

        $jsonArray = [];

        if (!count($values)) {
            return $jsonArray;
        }

        foreach ($values as $value) {
            $jsonArray[] = $this->toJson(
                $value,
                true
            );
        }

        return $jsonArray;
    }

    /**
     * convert single object to json
     *
     * @param \ReflectionProperty $property
     * @param $classInstance
     * @return array|string
     */
    protected function convertSingleObjectToJson(
        \ReflectionProperty $property,
        $classInstance
    ) {

        $json = $this->toJson(
            $property->getValue($classInstance),
            true
        );

        return $json;

    }


} 