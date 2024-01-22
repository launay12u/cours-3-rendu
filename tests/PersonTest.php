<?php

namespace Tests;

use App\Entity\Person;
use App\Entity\Wallet;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    protected Person $person;

    public function setUp(): void
    {
        $this->person = new Person('John Doe', 'USD');
    }

    public function testGetName(): void
    {
        $this->assertEquals('John Doe', $this->person->getName());
    }

    public function testSetName(): void
    {
        $this->person->setName('Jane Doe');
        $this->assertEquals('Jane Doe', $this->person->getName());
    }

    public function testGetWallet(): void
    {
        $this->assertInstanceOf(Wallet::class, $this->person->getWallet());
    }

    public function testSetWallet(): void
    {
        $newWallet = new Wallet('EUR');
        $this->person->setWallet($newWallet);
        $this->assertEquals($newWallet, $this->person->getWallet());
    }

    public function testHasFund(): void
    {
        $this->assertFalse($this->person->hasFund());
        $this->person->getWallet()->addFund(100);
        $this->assertTrue($this->person->hasFund());
    }

    public function testTransfertFund(): void
    {
        $person2 = new Person('Jane Doe', 'USD');
        $this->person->getWallet()->addFund(100);
        $this->person->transfertFund(50, $person2);
        $this->assertEquals(50, $this->person->getWallet()->getBalance());
        $this->assertEquals(50, $person2->getWallet()->getBalance());
    }

    public function testTransfertFundMoreThanBalance(): void
    {
        $person2 = new Person('Jane Doe', 'USD');
        $this->expectException(\Exception::class);
        $this->person->transfertFund(100, $person2);
    }

    public function testTransfertFundDifferentCurrencies(): void
    {
        $person2 = new Person('Jane Doe', 'EUR');
        $this->person->getWallet()->addFund(100);
        $this->expectException(\Exception::class);
        $this->person->transfertFund(50, $person2);
    }

    public function testDivideWallet(): void
    {
        $person2 = new Person('Jane Doe', 'USD');
        $person3 = new Person('Bob Doe', 'USD');
        $this->person->getWallet()->addFund(100);
        $this->person->divideWallet([$person2, $person3]);
        $this->assertEquals(0.0, $this->person->getWallet()->getBalance());
        $this->assertEquals(50, $person2->getWallet()->getBalance());
        $this->assertEquals(50, $person3->getWallet()->getBalance());
    }

    public function testBuyProduct(): void
    {
        $product = $this->createMock(\App\Entity\Product::class);
        $product->method('listCurrencies')->willReturn(['USD']);
        $product->method('getPrice')->willReturn(50.0);
        $this->person->getWallet()->addFund(100);
        $this->person->buyProduct($product);
        $this->assertEquals(50, $this->person->getWallet()->getBalance());
    }

    public function testBuyProductWithDifferentCurrency(): void
    {
        $product = $this->createMock(\App\Entity\Product::class);
        $product->method('listCurrencies')->willReturn(['EUR']);
        $product->method('getPrice')->willReturn(50.0);
        $this->person->getWallet()->addFund(100);
        $this->expectException(\Exception::class);
        $this->person->buyProduct($product);
    }
}
