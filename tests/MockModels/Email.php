<?php
namespace Nikapps\PsonTests\MockModels;

use Nikapps\Pson\Annotations\PsonType;
use Nikapps\Pson\Annotations\SerializedName;
use Nikapps\Pson\Traits\ClassHelper;

class Email
{

    use ClassHelper;

    /**
     * @PsonType("string")
     */
    public $email;

    /**
     * @SerializedName("type")
     */
    public $type;
} 