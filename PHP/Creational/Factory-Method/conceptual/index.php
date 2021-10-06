<?php
namespace NikhilJoshua;

abstract class Creator
{
    abstract public function factoryMethod(): Product;

    public function someOperation(): string
    {
        $product = $this->factoryMethod();
        $result = "Creator: The same creator's code has just worked with " . $product->operation();

        return $result;
    }
}

class ConcreteCreator1 extends Creator {
    public function factoryMethod(): Product
    {
        return new ConcreteProduct1();
    }
}

class ConcreteCreator2 extends Creator {
    public function factoryMethod(): Product
    {
        return new ConcreteProduct2();
    }
}

interface Product {
    public function operation(): string;
}

class ConcreteProduct1 implements Product {
    public function operation(): string
    {
        return "{Result of the ConcreteProduct1}";
    }
}

class ConcreteProduct2 implements Product {
    public function operation(): string
    {
        return "{Result of ConcreteProduct2}";
    }
}

function clientCode(Creator $creator) {
    echo "Client: I'm not aware of the creator's class, but it still works.<br>" . $creator->someOperation();
}

echo "App: Launched with the ConcreteCreator1.<br>";
clientCode(new ConcreteCreator1());
echo "<br><br>";

echo "App: Launched with the ConcreteCreator2.<br>";
clientCode(new ConcreteCreator2());
