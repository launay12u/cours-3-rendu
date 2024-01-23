<?php

namespace Tests;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;
use Faker\Factory;

class ProductTest extends TestCase
{
    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }
    //pas obligatoire
    public function testProductConstructor(): void
    {
        $product = new Product('soupe', ['EUR' => 10], 'food');
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('soupe', $product->getName());
        $this->assertEquals(['EUR' => 10], $product->getPrices());
        $this->assertEquals('food', $product->getType());
    }

    public function testGetName(): void {
        $product = new Product("Mario Wonder", ["EUR" => 49, "USD" => 50], "other");
        $this->assertEquals("Mario Wonder", $product->getName());
        $this->assertIsString($product->getName());
        $this->assertNotEmpty($product->getName());
    }
    public function testGetPrices(): void {
        $product = new Product("Mario Wonder", ["EUR" => 49, "USD" => 50], "other");
        $this->assertEquals(["EUR" => 49, "USD" => 50], $product->getPrices());
    }
    public function testGetPriceInvCurrency(): void {
        $product = new Product('pitaya', ['EUR' => 10], 'food');
        $this->expectException(\Exception::class);
        $product->getPrice('CAD');
    }

    public function testGetPriceWrongCurrency(): void {
        $product = new Product('aubergine', ['EUR' => 15], 'food');
        $this->expectException(\Exception::class);
        $product->getPrice('USD');

        $this->assertTrue(15,$product->getPrice('EUR'));
    }

    public function testGetType(): void {
        $product = new Product("souris rose", ["EUR" => 20, "USD" => 30], "tech");
        $this->assertEquals("tech", $product->getType());
        $this->assertIsString($product->getType());
    }
    public function testGetTVA(): void {
        //tva pour produit alimentaire 10%
        $foodProduct = new Product('pomme', ['USD' => 1], 'food');
        $this->assertEquals(0.1, $foodProduct->getTVA());

        //tva pour produit non alimentaire 20%
        $techProduct = new Product('Portable', ['USD' => 1000], 'tech');
        $this->assertEquals(0.2, $techProduct->getTVA());
    }

    public function testListCurrencies(): void {
        $product = new Product('Petit Ecureil', ['USD' => 15, 'EUR' => 12], 'other');
        $expectedCurrencies = ['USD', 'EUR'];

        $this->assertEquals($expectedCurrencies, $product->listCurrencies());
    }

    public function testGetPrice(): void {
        $product = new Product('Headphones', ['USD' => 50, 'EUR' => 40], 'tech');

        $prixDollard = $product->getPrice('USD');
        $this->assertEquals(50, $prixDollard);

        // Test device non conform
        $this->expectException(\Exception::class);
        $product->getPrice('CAD');

        $this->assertEquals(40, $product->getPrice('EUR'));
        $this->assertFalse(40, $product->getPrice('CAD'));
    }

    public function testSetPrices(): void {
        $product = new Product('Ipad', [], 'tech');

        $validPrices = ['USD' => 200, 'EUR' => 180];
        $product->setPrices($validPrices);
        $this->assertEquals($validPrices, $product->getPrices());

        // prix négatif
        $invalidPrices = ['USD' => -10, 'EUR' => 180];
        $product->setPrices($invalidPrices);
        $this->assertEquals(["USD" => 200, "EUR" => 180], $product->getPrices());

        //device pas enregistrer comme possible
        $invalidPrices = ['USD' => 276, 'CAD' => 170];
        $product->setPrices($invalidPrices);
        $this->assertEquals(["USD" => 276, "EUR" => 180], $product->getPrices());
    }
    public function testSetType(): void {
        $product = new Product("huile olive", ["EUR" => 25, "USD" => 30], "food");
        $product->setType("other");
        $this->assertEquals("other", $product->getType());
    }

    public function testSetTypeWrong(): void {
        $product = new Product("Mug", ["EUR" => 25, "USD" => 35], "tech");
        $this->expectException(\Exception::class);
        $product->setType("tasse");
    }
}
