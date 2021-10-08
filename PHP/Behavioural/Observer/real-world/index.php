<?php

namespace Observer\RealWorld;

class UserRepository implements \SplSubject {
    private $users = [];

    private $observers = [];

    public function __construct() {
        $this->observers['*'] = [];
    }

    private function initEventGroup(string $event = '*'): void {
        if(!isset($this->observers[$event])) {
            $this->observers[$event] = [];
        }
    }

    private function getEventObservers(string $event = '*'): array {
        $this->initEventGroup($event);
        $group = $this->observers[$event];
        $all = $this->observers['*'];

        return array_merge($group, $all);
    }

    public function attach(\SplObserver $observer, string $event ='*'): void {
        $this->initEventGroup($event);
        $this->observers[$event][] = $observer;
    }

    public function detach(\SplObserver $observer, string $event = '*'): void {
        foreach ($this->getEventObservers($event) as $key => $s) {
            if($s === $observer) {
                unset($this->observers[$event][$key]);
            }
        }
    }

    public function notify(string $event = '*', $data = null): void {
        echo 'UserRepository: Broadcasting the '. $event . 'event<br>';
        foreach ($this->getEventObservers($event) as $observer) {
            $observer->update($this, $event, $data);
        }
    }

    public function initialize($filename): void {
        echo 'UserRepository: Loading user records from a file.<br>';
        $this->notify('users: init', $filename);
    }

    public function createUser(array $data): User {
        echo 'UserRepository: Creating a user.<br>';

        $user = new User();
        $user->update($data);

        $id = bin2hex(openssl_random_pseudo_bytes(16));

        $user->update(['id' => $id] );
        $this->users[$id] = $user;

        $this->notify('users:created', $user);

        return $user;
    }

    public function updateUser(User $user, array $data): User {
        echo 'UserRepository: Updating a user.<br>';
        $id = $user->attributes['id'];
        if(!isset($this->users[$id])) {
            return null;
        }

        $user = $this->users[$id];
        $user->update($data);

        $this->notify('users:updated', $user);

        return $user;
    }

    public function deleteUser(User $user): void {
        echo 'UserRepository: Deleting a user<br>';

        $id = $user->attributes['id'];
        if(!isset($this->users[$id])) {
            return;
        }
        unset($this->users[$id]);
        $this->notify('users:deleted', $user);
    }
}

class User {
    public $attributes = [];

    public function update($data): void {
        $this->attributes = array_merge($this->attributes, $data);
    }
}

class Logger implements \SplObserver {
    private $filename;

    public function __construct($filename) {
        $this->filename = $filename;
        if(file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    public function update(\SplSubject $repository, string $event = null, $data= null): void {
        $entry = date('Y-m-d H:i:s') . ': ' . $event . ' with data ' . json_encode($data) . "\n";
        file_put_contents($this->filename, $entry, FILE_APPEND);

        echo 'Logger: I\'ve written '. $event .' entry to the log.'."\n";
    }
}

class OnboardingNotfication implements \SplObserver {
    private $adminEmail;

    public function __construct($adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function update(\SplSubject $repository, string $event = null, $data = null): void {
        echo 'OnboardingNotification: the notification has been emailed! <br>';
    }
}

$repository = new UserRepository();
$repository->attach(new Logger(__DIR__), '*');
$repository->attach(new OnboardingNotfication('1@example.com'), 'users:created');

$repository->initialize(__DIR__ .'/user.csv');

$user = $repository->createUser([
    'name' => 'Nikhil Joshua',
    'email' => 'nikhil.joshua@rtcamp.com',
]);

$repository->deleteUser($user);