<?php

namespace Tests;

use App\Entity\Wallet;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    protected Wallet $wallet;

    public function setUp(): void
    {
        $this->wallet = new Wallet('USD');
    }

    public function testAddFund(): void
    {
        $this->wallet->addFund(100);
        $this->assertEquals(100, $this->wallet->getBalance());
    }

    public function testRemoveFund(): void
    {
        $this->wallet->addFund(100);
        $this->wallet->removeFund(50);
        $this->assertEquals(50, $this->wallet->getBalance());
    }

    public function testRemoveMoreThanBalance(): void
    {
        $this->wallet->addFund(50);
        $this->expectException(\Exception::class);
        $this->wallet->removeFund(100);
    }

    public function testInvalidCurrency(): void
    {
        $this->expectException(\Exception::class);
        $this->wallet->setCurrency('GBP');
    }

    public function testInvalidBalance(): void
    {
        $this->expectException(\Exception::class);
        $this->wallet->setBalance(-50);
    }

    /**
     * @throws \Exception
     */
    public function testRemoveFundsExceptionNotEnough(): void
    {
        $this->expectException(\Exception::class);
        $this->wallet->removeFund(1000);
    }

    public function testRemoveFundsExceptionNegativeFunds(): void
    {
        $this->expectException(\Exception::class);
        $this->wallet->removeFund(-1000);
    }


    public function testAddFundsException(): void
    {
        $this->expectException(\Exception::class);
        $this->wallet->addFund(-1000);
    }
}
