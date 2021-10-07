<?php

namespace Facade\Conceptual;

class Facade {
    protected $subsystem1;

    protected $subsystem2;

    public function __construct(Subsystem1 $subsystem1 = null, Subsystem2 $subsystem2 = null){
        $this->subsystem1 = $subsystem1 ?: new Subsystem1();
        $this->subsystem2 = $subsystem2 ?: new Subsystem2();
    }

    public function operation(): string {
        $result = "Facade initializes subsystem:<br>";
        $result .= $this->subsystem1->operation1();
        $result .= $this->subsystem2->operation1();
        $result .= 'Facade orders subsystems to perfrom actions:<br>';
        $result .=  $this->subsystem1->operationN();
        $result .= $this->subsystem2->operationZ();

        return $result;
    }
}

class Subsystem1 {
    public function operation1(): string {
        return 'Subsystem1: Ready!<br>';
    }

    public function operationN(): string {
        return 'Subsystem1: Go!<br>';
    }
}

class Subsystem2 {
    public function operation1(): string {
        return 'Subsystem2: Ready!<br>';
    }

    public function operationZ(): string {
        return 'Subsystem2: Fire!<br>';
    }
}

function clientCode(Facade $facade) {
    echo $facade->operation();
}

$subsystem1 = new Subsystem1();
$subsystem2 = new Subsystem2();

$facade = new Facade($subsystem1, $subsystem2);

clientCode($facade);