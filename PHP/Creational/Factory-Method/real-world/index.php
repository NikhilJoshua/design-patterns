<?php

abstract class SocialNetworkPoster {
    abstract public function getSocialNetwork(): SocialNetworkConnector;

    public function post($content): void {
        $network = $this->getSocialNetwork();
        $network->logIn();
        $network->createPost($content);
        $network->logout();
    }
}

class FacebookPoster extends SocialNetworkPoster {
    private $login, $password;

    public function __construct(string $login, string $password){
        $this->login = $login;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector
    {
        return new FacebookConnector($this->login, $this->password);
    }
}

class LinkedInPoster extends SocialNetworkPoster {
    private $email, $password;

    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector {
        return new LinkedInConnector($this->email, $this->password);
    }
}

interface SocialNetworkConnector {
    public function logIn(): void;
    public function logout(): void;
    public function createPost($content): void;
}

class FacebookConnector implements SocialNetworkConnector
{
    private $login, $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function logIn(): void
    {
        echo "Send HTTP API request to log in user $this->login with " . "password $this->password <br>";
    }

    public function logout(): void {
        echo "Send HTTP API request to log out user $this->login <br>";
    }

    public function createPost($content): void {
        echo "Send HTTP API request to create a post in Facebook timeline. <br>";
    }
}

class LinkedInConnector implements SocialNetworkConnector {
    private $email, $password;

    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function logIn(): void
    {
        echo "Send HTTP API Request to log in user $this->email with " . "Password $this->password <br>";
    }
    public function logOut(): void
    {
        echo "Send HTTP API request to log out user $this->email <br>";
    }

    public function createPost($content): void
    {
        echo "Send HTTP API requests to create a post in LinkedIn timeline.<br>";
    }


}
function clientCode(SocialNetworkPoster $creator) {
    $creator->post("Hello World");
    $creator->post("I had a large hamburger this morning");

}

echo 'Testing CC1: <br>';
clientCode(new FacebookPoster("nikhil_joshua", "***test***"));

echo '<br><br>';

echo 'Testing CC2: <br>';
clientCode(new LinkedInPoster('Nikhil_Joshua', '***Linked***'));
