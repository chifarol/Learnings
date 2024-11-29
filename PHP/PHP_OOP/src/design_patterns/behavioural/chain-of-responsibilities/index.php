<?php
//  This is a behavioral design pattern that lets you pass requests along a chain of handlers. 
//  Upon receiving a request, each handler decides either to process the request or to pass it to the next handler in the chain.

//  This pattern is usually used by "middlewares" 


//  The most common type of middleware is Single Pass Middleware. With this type every middleware callable receives two parameters: an input to be checked and a callback to call the next middleware in the chain

//  Examples

//  You need to run a chain of checks like authentication, authorization, form validation before acting on a request. The request can break at any stage if criteria is not met



/**
 * The classic CoR pattern declares a single role for objects that make up a
 * chain, which is a Handler. In our example, let's differentiate between
 * middleware and a final application's handler, which is executed when a
 * request gets through all the middleware objects.
 *
 * The base Middleware class declares an interface for linking middleware
 * objects into a chain.
 */
abstract class Middleware
{
    /**
     * @var Middleware
     */
    private $next;

    /**
     * This method can be used to build a "chain* of middleware objects.
     */
    public function setNext(Middleware $next): Middleware
    {
        $this->next = $next;

        return $this;
    }


    /**
     * Subclasses must override this method to provide their own checks. A
     * subclass can fall back to the parent implementation if it can't process a
     * request.
     */
    public function runChecks(User $user): bool
    {
        if (!$this->next) {
            return true;
        }

        return $this->next->runChecks($user);
    }
}


/**
 * This Concrete Middleware checks whether a user associated with the request
 * has sufficient balance.
 */
class SufficientBalanceMiddleware extends Middleware
{
    public function runChecks(User $user): bool
    {
        if ($user->getWalletBalance() > 0) {
            echo "User has sufficient wallet balance\n";
            // return true;
            return parent::runChecks($user);
        }
        echo "User has insufficient wallet balance\n";

        return false;
    }
}
/**
 * This Concrete Middleware checks whether a user associated with the request
 * is an admin.
 */
class IsAdminMiddleware extends Middleware
{
    public function runChecks(User $user): bool
    {
        if ($user->getEmail() === "admin@example.com") {
            echo "User is admin\n";
            return parent::runChecks($user);
        }
        echo "User is not admin!\n";

        return false;
    }
}
/**
 * This Concrete Middleware checks whether a user associated with the request
 * is an admin.
 */
class UserExistsMiddleware extends Middleware
{
    public function runChecks(User $user): bool
    {
        if ($user->getEmail()) {
            echo "User exists\n";
            return parent::runChecks($user);
        }
        echo "User does not exist!\n";

        return false;
    }
}


class User
{

    public function __construct(private $email, private $walletBalance)
    {

    }
    public function getWalletBalance()
    {
        return $this->walletBalance;
    }
    public function getEmail()
    {
        return $this->email;
    }
}


class AccessExclusiveFeature
{

    /**
     * @var array
     */
    private $middleWareArr = [];

    /**
     * The client can configure the server with a chain of middleware objects.
     */
    public function setMiddleware(array $middleWareArr): void
    {
        $this->middleWareArr = $middleWareArr;
    }
    public function compileMiddlewares(): Middleware
    {
        return array_reduce($this->middleWareArr, function ($acc, $curr) {
            if ($acc) {
                return ($curr->setNext($acc));
                // $currMiddleWare->setNext(  $pastMiddleWare->setNext($prevMiddleWare) )
            } else {
                return $curr;
            }

        });
    }

    /**
     * The server gets the email and password from the client and sends the
     * authorization request to the middleware.
     */
    public function getFeature(User $user): bool
    {
        if ($this->compileMiddlewares()->runChecks($user)) {
            echo "AccessExclusiveFeature: Authorization has been successful!\n";
            // Do something useful for authorized users.
            return true;
        }
        echo "AccessExclusiveFeature: Authorization not successful!\n";
        return false;
    }
}

echo "<pre>";
$user = new User("admin@example.com", 100);
$middleWareArr = [new SufficientBalanceMiddleware(), new IsAdminMiddleware(), new UserExistsMiddleware()];
$getExclusiveFeature = new AccessExclusiveFeature();

// $sufficientBalance = new SufficientBalanceMiddleware();
// $isAdmin = new IsAdminMiddleware();
// $userExists = new UserExistsMiddleware();
// $middleWare = $sufficientBalance->setNext($isAdmin->setNext($userExists));

$getExclusiveFeature->setMiddleware($middleWareArr);
$getExclusiveFeature->getFeature($user);


echo "</pre>";