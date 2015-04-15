<?php


use Nikapps\Pson\Pson;

class PsonSerializerTest extends \Codeception\TestCase\Test
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // test serializing object with private properties
    public function testSerializingWithPublicProperties()
    {
        $I = $this->tester;

        $email = new \Nikapps\PsonTests\MockModels\Email();
        $email->email = 'email@example.com';
        $email->type = 'home';

        $pson = new Pson();

        $json = $pson->toJson($email);

        $expectedJson = '
            {"email":"email@example.com","type":"home"}
        ';
        $I->assertEquals($json, trim($expectedJson));
    }

    // test serializing object with public properties
    public function testSerializingWithPrivateProperties()
    {
        $I = $this->tester;

        $phone = new \Nikapps\PsonTests\MockModels\Phone();
        $phone->setNumber("123456");
        $phone->setType('home');

        $pson = new Pson();

        $json = $pson->toJson($phone);

        $expectedJson = '
            {"number":"123456","type":"home"}
        ';
        $I->assertEquals($json, trim($expectedJson));
    }

    // test serializing object with nested objects
    public function testSerializingWithNestedObjects()
    {
        $I = $this->tester;

        $phone = new \Nikapps\PsonTests\MockModels\Phone();
        $phone->setNumber("123456");
        $phone->setType('home');

        $email1 = new \Nikapps\PsonTests\MockModels\Email();
        $email1->email = 'email@example.com';
        $email1->type = 'home';

        $email2 = new \Nikapps\PsonTests\MockModels\Email();
        $email2->email = 'email2@example.com';
        $email2->type = 'work';

        $person = new \Nikapps\PsonTests\MockModels\Person();
        $person->setAge(20);
        $person->setName('cool man!');
        $person->setPhone($phone);
        $person->setEmails([
            $email1,
            $email2
        ]);

        $pson = new Pson();

        $json = json_decode($pson->toJson($person));

        $I->assertEquals($json->full_name, $person->getName());
        $I->assertEquals($json->age, $person->getAge());
        $I->assertEquals($json->phone->number, $person->getPhone()->getNumber());
        $I->assertEquals($json->phone->type, $person->getPhone()->getType());
        $I->assertEquals(count($json->emails), 2);
        $I->assertEquals(
            $json->emails[0]->email,
            $person->getEmails()[0]->email
        );
        $I->assertEquals(
            $json->emails[1]->type,
            $person->getEmails()[1]->type
        );

    }

    // test serializing object with type conversion
    public function testSerializingWithTypeConversion()
    {
        $I = $this->tester;

        $email = new \Nikapps\PsonTests\MockModels\Email();
        $email->email = 123456;
        $email->type = 'home';

        $pson = new Pson();

        $json = json_decode($pson->toJson($email));

        $I->assertTrue(is_string($json->email));
        $I->assertEquals($json->email, '123456');
    }

    // test serializing object with with transient property
    public function testSerializingTransientProperty()
    {
        $I = $this->tester;

        $person = new \Nikapps\PsonTests\MockModels\Person();

        $pson = new Pson();

        $json = json_decode($pson->toJson($person));

        $I->assertFalse(isset($json->internalObj));
    }

    // test serializing object with with null properties
    public function testSerializingNullProperties()
    {
        $I = $this->tester;

        $person = new \Nikapps\PsonTests\MockModels\Person();
        $person->setName("Cool Man!");
        $person->setAge(null);

        $pson = new Pson();

        $json = json_decode($pson->toJson($person));

        $I->assertFalse(isset($json->phone));
        $I->assertFalse(isset($json->emails));
        $I->assertFalse(isset($json->age));
        $I->assertTrue(isset($json->full_name));
    }


}