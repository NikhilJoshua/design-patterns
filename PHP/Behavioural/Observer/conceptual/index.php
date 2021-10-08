<?php

namespace Observer\Conceptual;

class Subject implements \SplSubject
{
    public $state;

    private $observers;

    public function __construct(){
        $this->observers = new \SplObjectStorage();
    }

    public function attach(\SplObserver $observer): void {
        echo 'Subject: Attached on observer.<br>';
        $this->observers->attach($observer);
    }

    public function detach(\SplObserver $observer): void {
        $this->observers->detach($observer);
        echo 'Subject: Detached an observer<br>';
    }

    public function notify(): void {
        echo 'Subject: notifying observers...<br>';
        foreach($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function someBusinessLogic(): void {
        echo 'Subject: I\'m doing something important.<br>' ;
        $this->state = rand(0,10);
        echo 'Subject: My state has just changed to: ' . $this->state.'<br>';
        $this->notify();
    }
}

class ConcereteObserverA implements \SplObserver {
    public function update(\SplSubject $subject): Void {
        if($subject->state < 3) {
            echo 'ConcreteObserverA: Reacted to the event.<br>';
        }
    }
}

class ConcreteObserverB implements \SplObserver {
    public function update(\SplSubject $subject): void {
        if($subject->state == 0 || $subject->state >= 2) {
            echo 'ConcreteObserverB: Reacted to the event.<br>';
        }
    }
}

$subject = new Subject();

$o1 = new ConcereteObserverA();
$subject->attach($o1);

$o2 = new ConcreteObserverB();
$subject->attach($o2);

$subject->someBusinessLogic();
$subject->someBusinessLogic();

$subject->detach($o2);

$subject->someBusinessLogic();