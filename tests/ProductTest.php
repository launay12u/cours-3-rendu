<?php

namespace Tests;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    protected Product $product;

    public function setUp(): void
    {
        $this->product = new Product('Product 1', ['USD' => 50.0], 'food');
    }

    public function testName(): void
    {
        $this->assertEquals('Product 1', $this->product->getName());
    }

    public function testCategory(): void
    {
        $this->assertEquals('food', $this->product->getType());
    }

    public function testSetName(): void
    {
        $this->product->setName('Product 2');
        $this->assertEquals('Product 2', $this->product->getName());
    }

    public function testCurrency(): void
    {
        $this->assertEquals(['USD' => 50.0], $this->product->getPrices());
    }

    public function testSetCurrency(): void
    {
        $this->product->setPrices(['EUR' => 45.0]);
        $this->assertEquals(['USD' => 50.0, 'EUR' => 45.0], $this->product->getPrices());
    }

    public function testPrice(): void
    {
        $this->assertEquals(50.0, $this->product->getPrice('USD'));
    }

    public function testSetPrice(): void
    {
        $this->product->setPrices(['USD' => 100.0]);
        $this->assertEquals(100.0, $this->product->getPrice('USD'));
    }

    public function testListCurrencies(): void
    {
        $this->product->setPrices(['USD' => 50.0, 'EUR' => 45.0]);
        $this->assertEquals(['USD', 'EUR'], $this->product->listCurrencies());
    }

    public function testInvalidType(): void
    {
        $this->expectException(\Exception::class);
        $this->product->setType('invalid');
    }

    public function testInvalidCurrency(): void
    {
        $this->product->setPrices(['GBP' => -50.0]);
        $this->assertEquals(['USD' => 50.0], $this->product->getPrices());
    }

    public function testInvalidPrice(): void
    {
        $this->product->setPrices(['USD' => -50.0]);
        $this->assertEquals(['USD' => 50.0], $this->product->getPrices());
    }

    public function testGetTVA(): void
    {
        $this->product->setPrices(['USD' => 50.0]);
        $this->assertEquals(0.1, $this->product->getTVA());

        //change type
        $this->product->setType('tech');
        $this->assertEquals(0.2, $this->product->getTVA());
    }

    public function testGetPriceException(): void
    {
        $this->expectException(\Exception::class);
        $this->product->getPrice('GBP');
    }

    public function testGetPriceExceptionNoCurrency(): void
    {
        $this->expectException(\Exception::class);
        $this->product->getPrice('EUR');
    }
}
