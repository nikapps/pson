<?php
namespace Nikapps\PsonTests\MockModels;

use Nikapps\Pson\Annotations\SerializedName;

class AdminPerson extends Person
{

    /**
     * @SerializedName("person_role")
     * @var
     */
    private $role;

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }


} 