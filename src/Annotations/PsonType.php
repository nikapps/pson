<?php
namespace Nikapps\Pson\Annotations;

/**
 * Class PsonType
 * @package Nikapps\Pson\Annotations
 * @Annotation
 * @Target({"PROPERTY"})
 */
class PsonType
{

    /**
     * @var string
     */
    public $type;

    protected $primitiveTypes = [
        'bool',
        'boolean',
        'int',
        'integer',
        'float',
        'double',
        'string',
        'null',
        'array',
        'object'
    ];

    /**
     * is array of objects or not
     *
     * example: FooObject[]
     *
     * @return bool
     */
    public function isArray()
    {
        return substr(trim($this->type), -2) == '[]';
    }

    /**
     * return class name
     *
     * example: \Namespace\Foo\Bar[] --> \Namespace\Foo\Bar
     *
     * @return string
     */
    public function getClassType()
    {
        if ($this->isArray()) {
            return substr(
                $this->type,
                0,
                strlen(trim($this->type)) - 2 //[]
            );
        } else {
            return trim($this->type);
        }
    }

    /**
     * finds whether type is a primitive
     *
     * @return bool
     */
    public function isPrimitiveType()
    {
        return in_array(strtolower($this->type), $this->primitiveTypes);
    }

    /**
     * whether array of primitives. (such as string[], int[], etc)
     *
     * @return bool
     */
    public function isArrayOfPrimitive(){
        return $this->isArray() && in_array(strtolower($this->getClassType()), $this->primitiveTypes);
    }

} 