<?php
namespace Nikapps\PsonTests\unit;

use Nikapps\Pson\Pson;
use Nikapps\PsonTests\MockModels\AdminPerson;
use Nikapps\PsonTests\MockModels\Email;
use Nikapps\PsonTests\MockModels\Person;
use Nikapps\PsonTests\MockModels\Phone;

class PsonDeserializingTest extends \Codeception\TestCase\Test
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $personJson = '
        {
             "full_name": "Full Name",
             "emails": [
                {
                    "email": "email1@gmail.com",
                    "type": "home"
                },
                {
                    "email": "email2@gmail.com",
                    "type": "work"
                }
             ],
             "phone": {
                "number": "123456"
             }
        }
    ';

    protected $emptyArrayPersonJson = '
        {
             "full_name": "Full Name",
             "emails": [
             ],
             "phone": {
                "number": "123456"
             }
        }
    ';

    protected $missingObjectPersonJson = '
        {
             "full_name": "Full Name",
             "emails": [
             ]
        }
    ';

    protected $missingArrayObjectPersonJson = '
        {
             "full_name": "Full Name",

        }
    ';

    protected $scalarTypeConvertPerson = '
        {
             "age": 18.5
        }
    ';


    protected $adminPersonJson = '
        {
             "full_name": "Full Name",
             "emails": [
                {
                    "email": "email1@gmail.com",
                    "type": "home"
                },
                {
                    "email": "email2@gmail.com",
                    "type": "work"
                }
             ],
             "phone": {
                "number": "123456"
             },
             "person_role": "Administrator",
             "internalObj": "external"
        }
    ';

    protected $emailJson = '
        {
            "email": "email@gmail.com",
            "type": "work"
        }
    ';

    protected $missingEmailJson = '
        {
            "email": "email@gmail.com"
        }
    ';

    protected $phoneJson = '
        {
            "number": "1234"
        }
    ';

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // test deserializing public properties (without annotation)
    public function testDeserializingPublicPropertiesWithoutAnnotation()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Email $email
         */
        $email = $pson->fromJson(Email::getClass(), $this->emailJson);

        $I->assertTrue($email instanceof Email);
        $I->assertEquals($email->email, "email@gmail.com");
        $I->assertEquals($email->type, "work");
    }

    // test deserializing protected/private properties (without annotation)
    public function testDeserializingProtectedPropertiesWithoutAnnotation()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Phone $phone
         */
        $phone = $pson->fromJson(Phone::getClass(), $this->phoneJson);

        $I->assertTrue($phone instanceof Phone);
        $I->assertEquals($phone->getNumber(), "1234");
    }

    // test deserializing nested objects
    public function testDeserializingNestedObjects()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Person $person
         */
        $person = $pson->fromJson(Person::getClass(), $this->personJson);

        $I->assertTrue($person instanceof Person);
        $I->assertEquals($person->getName(), "Full Name");
        $I->assertEquals(count($person->getEmails()), 2);

        $email = $person->getEmails()[1];

        $I->assertTrue($email instanceof Email);
        $I->assertEquals($email->email, "email2@gmail.com");
        $I->assertEquals($email->type, "work");


        $I->assertTrue($person->getPhone() instanceof Phone);
        $I->assertEquals($person->getPhone()->getNumber(), "123456");


    }

    // test deserializing inheritance object
    public function testDeserializingInheritanceObject()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var AdminPerson $adminPerson
         */
        $adminPerson = $pson->fromJson(AdminPerson::getClass(), $this->adminPersonJson);

        $I->assertTrue($adminPerson instanceof AdminPerson);
        $I->assertEquals($adminPerson->getName(), "Full Name");
        $I->assertEquals(count($adminPerson->getEmails()), 2);

        $email = $adminPerson->getEmails()[1];

        $I->assertTrue($email instanceof Email);
        $I->assertEquals($email->email, "email2@gmail.com");
        $I->assertEquals($email->type, "work");


        $I->assertTrue($adminPerson->getPhone() instanceof Phone);
        $I->assertEquals($adminPerson->getPhone()->getNumber(), "123456");

        $I->assertEquals($adminPerson->getRole(), "Administrator");

    }

    // test deserializing with transient property
    public function testDeserializingWithTransientProperty()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var AdminPerson $adminPerson
         */
        $adminPerson = $pson->fromJson(AdminPerson::getClass(), $this->adminPersonJson);

        $I->assertEquals($adminPerson->getInternalObj(), "internal");

    }

    // test deserializing with missing json key
    public function testDeserializingWithMissingKey()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Email $email
         */
        $email = $pson->fromJson(Email::getClass(), $this->missingEmailJson);

        $I->assertTrue($email instanceof Email);
        $I->assertEquals($email->email, "email@gmail.com");
        $I->assertEquals($email->type, null);

    }

    /**
     * test deserializing with missing json key - strict mode
     */
    public function testDeserializingWithMissingKeyStrictMode()
    {

        $pson = new Pson();
        $pson->setStrict(true);

        \PHPUnit_Framework_TestCase::setExpectedException(
            '\Nikapps\Pson\Exception\NotFoundJsonKeyException'
        );

        /**
         * @var Email $email
         */
        $email = $pson->fromJson(Email::getClass(), $this->missingEmailJson);


    }

    // test deserializing empty array of objects
    public function testDeserializingEmptyArrayObjects()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Person $person
         */
        $person = $pson->fromJson(Person::getClass(), $this->emptyArrayPersonJson);

        $I->assertTrue(is_array($person->getEmails()));
        $I->assertEquals(count($person->getEmails()), 0);

    }

    // test deserializing missing object
    public function testDeserializingMissingObject()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Person $person
         */
        $person = $pson->fromJson(Person::getClass(), $this->missingObjectPersonJson);

        $I->assertTrue(is_null($person->getPhone()));

    }

    // test deserializing missing object
    public function testDeserializingMissingArrayObject()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Person $person
         */
        $person = $pson->fromJson(Person::getClass(), $this->missingArrayObjectPersonJson);

        $I->assertTrue(is_null($person->getEmails()));

    }

    // test deserializing with default value
    public function testDeserializingWithDefaultValue()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Person $person
         */
        $person = $pson->fromJson(Person::getClass(), $this->personJson);

        $I->assertEquals($person->getPhone()->getType(), "Other");

    }

    // test deserializing with default value
    public function testDeserializingWithScalarConvert()
    {
        $I = $this->tester;
        $pson = new Pson();

        /**
         * @var Person $person
         */
        $person = $pson->fromJson(Person::getClass(), $this->scalarTypeConvertPerson);

        $I->assertTrue(is_int($person->getAge()));
        $I->assertEquals($person->getAge(), 18);

    }

}