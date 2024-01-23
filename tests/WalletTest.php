<?php

namespace Tests;

use App\Entity\Wallet;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    protected $faker;

    protected function setUp(): void
    {
        //j'utilise pas autant que j'aurais pu le setUp
        parent::setUp();
        $this->faker = Factory::create();
    }
    public function testConstruct(): void {
        $wallet = new Wallet('USD');
        $this->assertEquals(0, $wallet->getBalance());
        $this->assertEquals('USD', $wallet->getCurrency());
        $this->assertNotEmpty($wallet);

    }
    public function testGetBalance(): void {
        $balance = $this->faker->randomFloat(2, 0, 1000);
        $wallet = new Wallet('USD');
        $wallet->setBalance($balance);

        $this->assertEquals($balance, $wallet->getBalance());
        $this->assertIsFloat($wallet->getBalance());
    }

    public function testGetCurrency(): void {
        $currency = $this->faker->randomElement(Wallet::AVAILABLE_CURRENCY);
        $wallet = new Wallet($currency);

        $this->assertEquals($currency, $wallet->getCurrency());
        $this->assertIsString($wallet->getCurrency());
    }

    public function testSetBalance(): void {
        $wallet = new Wallet('USD');

        $balance = $this->faker->randomFloat(2, 0, 1000);
        $wallet->setBalance($balance);

        $this->assertEquals($balance, $wallet->getBalance());

        // Test balance négative
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid balance');
        $wallet->setBalance(-10);
    }

    public function testSetCurrency() : void {
        $wallet = new Wallet('USD');
        $this->assertEquals('USD', $wallet->getCurrency());

        $wallet->setCurrency('EUR');
        $this->assertEquals('EUR', $wallet->getCurrency());
    }

    public function testSetCurrencyException(): void {
        $wallet = new Wallet('USD');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        $wallet->setCurrency('CAD');

        $this->assertTrue($wallet->getCurrency() === 'USD');
        $this->assertFalse($wallet->getCurrency() === 'CAD');

    }


    public function testRemoveFund(): void {
        $wallet = new Wallet('USD');
        $initialBalance = $this->faker->randomFloat(2, 10, 1000);
        $wallet->setBalance($initialBalance);

        $amountToRemove = $this->faker->randomFloat(2, 1, 100);
        $wallet->removeFund($amountToRemove);

        $expectedBalance = $initialBalance - $amountToRemove;
        $this->assertEquals($expectedBalance, $wallet->getBalance());
    }

        public function testRemoveInvalidAmount(): void {
            $wallet = new Wallet('USD');
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Insufficient funds');
            $wallet->setBalance(50);
            $wallet->removeFund(100);

            $this->assertTrue(50, wallet->getBalance());
            $this->assertIsFloat($wallet->getBalance());
        }
    public function testRemoveNegAmount(): void {
        $wallet = new Wallet('EUR');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        $wallet->setBalance(50);
        $wallet->removeFund(-10);

        $this->assertTrue(50, wallet->getBalance());
        $this->assertIsFloat($wallet->getBalance());
    }
    public function testRemoveFundNeg(): void {
        $wallet = new Wallet('USD');
        $wallet->setBalance(100);
        $this->expectException(\Exception::class);
        $wallet->removeFund(-60);
    }

    public function testAddFund()
    {
        $wallet = new Wallet('USD');
        $initialBalance = $this->faker->randomFloat(2, 0, 1000);
        $wallet->setBalance($initialBalance);

        $amountToAdd = $this->faker->randomFloat(2, 1, 1000);
        $wallet->addFund($amountToAdd);

        $expectedBalance = $initialBalance + $amountToAdd;
        $this->assertEquals($expectedBalance, $wallet->getBalance());

    }
    public function testAddFundNegative(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(100);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        $wallet->addFund(-60);

        $this->assertTrue(100, wallet->getBalance());
    }
}
