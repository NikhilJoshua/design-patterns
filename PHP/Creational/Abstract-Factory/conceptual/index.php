<?php

namespace AbstractFactory;

interface AbstractFactory
{
    public function createProductA(): AbstractProductA;
    public function createProductB(): AbstractProductB;
}

class ConcreteFactory1 implements  AbstractFactory {
    public function createProductA(): AbstractProductA {
        return new ConcreteProductA1();
    }
    public function createProductB(): AbstractProductB
    {
        return new ConcreteProductB1();
    }
}

class ConcreteFactory2 implements AbstractFactory {
    public function createProductA(): AbstractProductA {
        return new ConcreteProductA2();
    }

    public function createProductB(): AbstractProductB {
        return new ConcreteProductB2();
    }
}

interface AbstractProductA {
    public function usefulFunctionA(): string;
}

class ConcreteProductA1 implements AbstractProductA
{
    public function usefulFunctionA(): string
    {
        return "The result of the product A1.";
    }
}

class ConcreteProductA2 implements AbstractProductA {
    public function usefulFunctionA(): string {
        return "The result of the product A2.";
    }
}

interface AbstractProductB {
    public function usefulFunctionB(): string;

    public function anotherUsefulFunctionB(AbstractProductA $collaborator): string;
}

class ConcreteProductB1 implements AbstractProductB {
    public function usefulFunctionB(): string {
        return "the result of the product B1. ";
    }

    public function anotherUsefulFunctionB(AbstractProductA $collaborator): string
    {
        $result = $collaborator->usefulFunctionA();

        return "The result of the B1 collaborating with the ({$result})";
    }
}

class ConcreteProductB2 implements AbstractProductB {
    public function usefulFunctionB(): string {
        return "the result of the product B2.";
    }

    public function anotherUsefulFunctionB(AbstractProductA $collaboarator): string {
        $result = $collaboarator->usefulFunctionA();

        return "the result of the B2 Collaborating with the ({$result})";
    }
}

function clientCode(AbstractFactory $factory) {
    $productA = $factory->createProductA();
    $productB = $factory->createProductB();

    echo $productB->usefulfunctionB() . '<br>';
    echo $productB->anotherUsefulFunctionB($productA) . '<br>';
}

echo 'Client: Testing client code with the first factory type:<br>';
clientCode(new ConcreteFactory1());

echo '<br>';

echo 'Client: testing same with 2nd factory:<br>';
clientCode(new ConcreteFactory2());