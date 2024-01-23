<?php

namespace Tests;

use App\Entity\Person;
use App\Entity\Wallet;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;
use Faker\Factory;

class PersonTest extends TestCase
{
    protected $faker;
    protected function setUp(): void
    {
        //je crée pas une personne ici car je veux les créer avec des noms que j'aime bien
        parent::setUp();
        $this->faker = Factory::create();
    }
    //pas obligatoire ici car le constructeur est trés simple et tester via Getter et setter
    public function test__construct() : void {
        $name = $this->faker->name;
        $person1 = new Person($name, 'USD');
        $person = new Person('Henri Despre', 'EUR');

        $this->assertInstanceOf(Person::class, $person);
        $this->assertNotEmpty($person);
        $this->assertNotNull($person->wallet);
        $this->assertEquals('EUR', $person->wallet->getCurrency());
        $this->assertNotEquals($person1->name, $person->name);
    }
    public function testGetName() : void{
        $name = $this->faker->name;
        $person = new Person($name, 'USD');

        $this->assertEquals($name, $person->getName());
        $this->assertIsString($person->getName());
        $this->assertNotEmpty($person->getName());
        $this->assertNotEquals('test', $person->getName());
    }
    public function testSetName() : void {
        $person = new Person('Ulysse Despre', 'USD');
        $newName = $this->faker->name;
        $nameDuplicate = $person->getName();
        $person->setName($newName);

        $this->assertEquals($newName, $person->getName());
        $this->assertIsString($person->getName());
        $this->assertEquals($nameDuplicate, "Ulysse Despre");
        $this->assertNotEquals($nameDuplicate, $person->getName());
    }

    public function testGetWallet() : void {
        $person = new Person('Julie despre', 'EUR');
        $wallet = $person->getWallet();

        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals($wallet, $person->getWallet());
        $this->assertNotNull($person->getWallet());
    }
    public function testSetWallet() : void {
        $person = new Person('Vincent Despre', 'USD');
        $result1 = $person->wallet;
        $wallet = new Wallet ('EUR');
        $wallet->setBalance(500);
        $person->setWallet($wallet);
        $result2= $person->wallet;

        $this->assertNotEquals($result1, $result2);
        $this->assertEquals($wallet, $person->getWallet());
        $this->assertNotNull($person->getWallet());
        $this->assertInstanceOf(Wallet::class, $result2);
       $this->assertEquals(500, $result2->getBalance());
        $this->assertNotEquals('USD', $person->getWallet()->getCurrency());
    }

    public function testHasFund(): void {
        $person = new Person('Vincent Despre', 'USD');

        $this->assertIsBool($person->hasFund());
        $this->assertFalse($person->hasFund());

        // Modifier le solde du portefeuille à zéro
        $person->getWallet()->setBalance(100000000);
        $this->assertTrue($person->hasFund());
    }

    public function testTransfertFund() : void {
        $person1 = new Person('Vincent Despre', 'USD');
        $person2 = new Person('Julie Despre', 'USD');

        $person1->getWallet()->setBalance(1000);
        $person2->getWallet()->setBalance(0);

        $this->assertEquals(1000, $person1->getWallet()->getBalance());
        $this->assertEquals(0, $person2->getWallet()->getBalance());

        $amount = $this->faker->randomFloat(3, 1, 1000);

        $person1->transfertFund($amount, $person2);

        $this->assertEquals($person1->getWallet()->getBalance(), 1000 - $amount);
        $this->assertEquals($person2->getWallet()->getBalance(), $amount);
    }
    public function testTransfertFundNoFund() : void {
        $person1 = new Person('Vincent Despre', 'USD');
        $person2 = new Person('Julie Despre', 'USD');

        $this->assertEquals(0, $person1->getWallet()->getBalance());
        $this->assertEquals(0, $person2->getWallet()->getBalance());

        $amount = $this->faker->randomFloat(3, 1, 1000);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        $person1->transfertFund($amount, $person2);

        $this->assertEquals(0, $person1->getWallet()->getBalance());
        $this->assertEquals(0, $person2->getWallet()->getBalance());
    }

    public function testTransfertFundDiffDevice() : void {
        $person1 = new Person('Vincent Despre', 'USD');
        $person2 = new Person('Julie Despre', 'EUR');
        $person1->getWallet()->setBalance(100);
        $this->assertEquals(100, $person1->getWallet()->getBalance());
        $this->assertEquals(0, $person2->getWallet()->getBalance());

        $amount = $this->faker->randomFloat(3, 1, 1000);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t give money with different currencies');
        $person1->transfertFund($amount, $person2);

        $this->assertEquals(100, $person1->getWallet()->getBalance());
        $this->assertEquals(0, $person2->getWallet()->getBalance());
    }

    public function testDivideWalletInt(): void
    {
        $person1 = new Person('Vincent Despre', 'USD');
        $person1->getWallet()->setBalance(300);
        $person2 = new Person('Julie Depsre', 'USD');
        $person3 = new Person('Henri Despre', 'USD');
        $person4 = new Person('Ulusse Despre', 'USD');

        //test état des portefeilles avant le partage
        $this->assertEquals(300, $person1->getWallet()->getBalance());
        $this->assertEquals(0, $person2->getWallet()->getBalance());
        $this->assertEquals(0, $person3->getWallet()->getBalance());
        $this->assertEquals(0, $person4->getWallet()->getBalance());

        $person1->divideWallet([$person2, $person3, $person4]);
        $this->assertEquals(0, $person1->getWallet()->getBalance());
        $this->assertEquals(100, $person2->getWallet()->getBalance());
        $this->assertEquals(100, $person3->getWallet()->getBalance());
        $this->assertEquals(100, $person4->getWallet()->getBalance());
    }

    public function testDivideWalletFloat(): void
    {
        $person1 = new Person('Vincent Despre', 'USD');
        $person1->getWallet()->setBalance(10);
        $person2 = new Person('Julie Depsre', 'USD');
        $person3 = new Person('Henri Despre', 'USD');
        $person4 = new Person('Ulusse Despre', 'USD');

        //test état des portefeilles avant le partage
        $this->assertEquals(10, $person1->getWallet()->getBalance());
        $this->assertEquals(0, $person2->getWallet()->getBalance());
        $this->assertEquals(0, $person3->getWallet()->getBalance());
        $this->assertEquals(0, $person4->getWallet()->getBalance());

        $person1->divideWallet([$person2, $person3, $person4]);
        $this->assertEquals(0, $person1->getWallet()->getBalance());
        $this->assertEquals(3.34, $person2->getWallet()->getBalance());
        $this->assertEquals(3.33, $person3->getWallet()->getBalance());
        $this->assertEquals(3.33, $person4->getWallet()->getBalance());
    }

    public function testBuyProduct(): void
    {
        $person1 = new Person('Ulysse Despre', 'USD');
        $person2 = new Person('Henri Despre', 'EUR');
        $person1->getWallet()->setBalance(10);
        $person2->getWallet()->setBalance(10);

        $this->assertEquals(10, $person1->getWallet()->getBalance());
        $this->assertEquals(10, $person2->getWallet()->getBalance());

        $product = new Product('nutella', ['EUR' => 4, 'USD' => 3], 'food');
        $person1->buyProduct($product);
        $this->assertEquals(7, $person1->getWallet()->getBalance());
        $person1->buyProduct($product);
        $this->assertEquals(4, $person1->getWallet()->getBalance());

        $person2->buyProduct($product);
        $this->assertEquals(6, $person2->getWallet()->getBalance());
    }

    public function testBuyProductBadDevice(): void {
        $person1 = new Person('Vincent Despre', 'USD');
        $person1->getWallet()->setBalance(10);
        $product = new Product('Lion', ['EUR' => 1], 'food');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t buy product with this wallet currency');
        $person1->buyProduct($product);

        $this->assertEquals(10, $person1->getWallet()->getBalance());
    }

    public function testBuyProductNoFund(): void {
        $person1 = new Person('Vincent Despre', 'USD');
        $product = new Product('Reese', ['USD' => 1], 'food');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        $person1->buyProduct($product);

        $this->assertEquals(0, $person1->getWallet()->getBalance());
    }

}
