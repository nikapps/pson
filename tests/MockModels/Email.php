<?php
namespace Nikapps\PsonTests\MockModels;

use Nikapps\Pson\Annotations\PsonType;
use Nikapps\Pson\Annotations\SerializedName;

class Email
{

    /**
     * @PsonType("string")
     */
    public $email;

    /**
     * @SerializedName("TYPE")
     */
    public $type;
} 