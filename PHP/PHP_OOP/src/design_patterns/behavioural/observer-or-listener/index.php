<?php
//  The observer pattern describes a scenario where a "subject" class actively informs one or more "observer" classes when certain events happen.

//  it lets you define a *subscription* mechanism to notify multiple objects about any events that happen to the object they’re observing

//  This pattern is composed of an object called Observable (or Subject orPublisher) and many other objects called Observers (or Subscribers).

//  PHP has several built-in interfaces (SplSubject, SplObserver) that can be used to make your implementations of the Observer pattern compatible with the rest of the PHP code.


//  Here's what the PHP SplSubject interface looks like:
//  interface SplSubject
//  *     {
//  *           // Attach an observer to the subject.
//  *         public function attach(SplObserver $observer);
//  *
//  *           // Detach an observer from the subject.
//  *         public function detach(SplObserver $observer);
//  *
//  *           // Notify all observers about an event.
//  *         public function notify();
//  *     }

//  interface SplObserver
//  *     {
//  *         public function update(SplSubject $subject);
//  *     }



use SplSubject;
use SplObjectStorage;
use SplObserver;

/**
 * User implements the observed object (called Subject), it maintains a list of observers and sends notifications to
 * them in case changes are made on the User object
 */
class User implements SplSubject
{
    private SplObjectStorage $observers;
    private $email;

    public function __construct()
    {
        $this->observers = new SplObjectStorage();
    }

    public function attach(SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }

    public function detach(SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    public function changeEmail(string $email): void
    {
        $this->email = $email;
        $this->notify();
    }

    public function notify(): void
    {
        /** @var SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}


class UserObserver implements SplObserver
{
    /**
     * @var SplSubject[]
     */
    private array $changedUsers = [];

    /**
     * It is called by the Subject, usually by SplSubject::notify()
     */
    public function update(SplSubject $subject): void
    {
        $this->changedUsers[] = clone $subject;
    }

    /**
     * @return SplSubject[]
     */
    public function getChangedUsers(): array
    {
        return $this->changedUsers;
    }
}







$observer = new UserObserver();

$user = new User();
$user->attach($observer);

$user->changeEmail('foo@bar.com');
