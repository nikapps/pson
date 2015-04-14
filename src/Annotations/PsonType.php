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

    protected $scalarTypes = [
        'bool',
        'boolean',
        'int',
        'integer',
        'float',
        'double',
        'string',
        'number'
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
     * finds whether type is a scalar
     *
     * @return bool
     */
    public function isTypeScalar()
    {
        return in_array(strtolower($this->type), $this->scalarTypes);
    }


} 