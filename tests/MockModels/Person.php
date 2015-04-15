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
     * @SerializedName("full_name")
     * @var string
     */
    protected $name;

    /**
     * email
     *
     * @SerializedName("emails")
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
     * @PsonType("integer");
     * @var int
     */
    protected $age;

    /**
     * @Transient
     * @var mixed
     */
    protected $internalObj = 'internal';


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
     * @return Email[]
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @param Email[] $emails
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    /**
     * @return Phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param Phone $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getInternalObj()
    {
        return $this->internalObj;
    }

    /**
     * @param mixed $internalObj
     */
    public function setInternalObj($internalObj)
    {
        $this->internalObj = $internalObj;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

} 