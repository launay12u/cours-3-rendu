<?php

namespace Tests;

use App\Entity\Wallet;
use PHPUnit\Framework\TestCase;
use App\Entity\Person;
use App\Entity\Product;

class PersonTest extends TestCase
{

    public function testGetName(): void
    {
        $person=new Person('Prénom', 'EUR');
        $this->assertEquals('Prénom',$person->getName());
    }

    public function testSetName() : void
    {
        $person=new Person('Prénom', 'EUR');
        $person->setName('Emilie');
        $this->assertEquals('Emilie', $person->getName());
    }

    public function testGetWallet() :void {
        $person=new Person('Prénom', 'EUR');
        $this->assertInstanceOf(Wallet::class, $person->getWallet());
    }

    public function testSetWallet():void {
        $person=new Person('Prénom', 'EUR');
        $person->setWallet(new Wallet('USD'));
        $this->assertInstanceOf(Wallet::class, $person->getWallet());
    }

    public function testHasFund():void {
        $person=new Person('Prénom', 'EUR');
        $this->assertFalse($person->hasFund());

        //Le test ne fonctionne pas
    }

    public function testTransfertFund():void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t give money with different currencies');
        $person1=new Person('Lola', 'EUR');
        $person2=new Person('Emilie', 'EUR');
        $person3=new Person('John', 'USD');

        $person1->wallet->setBalance(100);
        $person1->transfertFund(40, $person2);
        $person1->transfertFund(20, $person3);
        // Lola avait 100€, elle en a transféré 40 à Lola. Elle devrait avoir 60€ sur son compte.
        $this->assertEquals(60, $person1->wallet->getBalance());
        // Emilie avait 0€, elle en a reçu 40 de Lola. Elle devrait avoir 40€ sur son compte.
        $this->assertEquals(40, $person2->wallet->getBalance());
        // John et Lola n'ont pas la même currency. Ils ne peuvent pas faire de transfert d'argent entre eux.
    }

    public function testDivideWallet():void {
        $person1=new Person('Lola', 'EUR');
        $person1->wallet->setBalance(33);
        $person2=new Person('Emilie', 'EUR');
        $person3=new Person('Jack', 'EUR');
        $person4=new Person('Richard', 'EUR');
        $persons=[$person2, $person3, $person4];

        // Lola est morte, on transfert donc tout son argent à ses enfants, divisé de façon égale. Son compte
        // détenait 33€, maintenant il contient normalement 0€ et celui de ses 3 enfants contiennent 11€ chacun.
        $person1->divideWallet($persons);
        $this->assertEquals(0, $person1->wallet->getBalance());
        $this->assertEquals(11, $person2->wallet->getBalance());
        $this->assertEquals(11, $person3->wallet->getBalance());
        $this->assertEquals(11, $person4->wallet->getBalance());
    }

    public function testBuyProduct():void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t buy product with this wallet currency');
        $person1=new Person('Lola', 'EUR');
        $person1->wallet->setBalance(200);
        $product1=new Product('Bague', ['USD'=>163.00, 'EUR'=>150.00], 'other');
        $product2=new Product('Pomme', ['USD'=>1], 'food');
        $person1->buyProduct($product1);
        $person1->buyProduct($product2);
        $this->assertEquals(50, $person1->wallet->getBalance());

    }
}
