<?php
namespace Nikapps\Pson\Annotations;

/**
 * Class SerializedName
 * @package Nikapps\Pson\Annotations
 * @Annotation
 * @Target({"PROPERTY"})
 */
class SerializedName
{

    /**
     * @var string
     */
    public $key;

} 