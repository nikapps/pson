<?php
namespace Nikapps\PsonTests\MockModels;

use Nikapps\Pson\Annotations\Transient;
use Nikapps\Pson\Annotations\PsonType;
use Nikapps\Pson\Annotations\SerializedName;
use Nikapps\Pson\Traits\ClassHelper;

class Person
{

    use ClassHelper;

    /**
     * name
     *
     * @SerializedName("FULL_NAME")
     * @var string
     */
    protected $name;

    /**
     * email
     *
     * @SerializedName("EMAILS")
     * @PsonType("Nikapps\PsonTests\MockModels\Email[]")
     * @var Email[]
     */
    protected $emails;

    /**
     * @PsonType("Nikapps\PsonTests\MockModels\Phone")
     * @var Phone
     */
    protected $phone;

    /**
     * @return string
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @param string $emails
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }



} 