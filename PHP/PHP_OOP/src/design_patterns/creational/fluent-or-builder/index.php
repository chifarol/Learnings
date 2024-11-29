<?php

// when you want to construct complex objects step by step
// e.g addWater("3 litres")->addMaggi(2)->addPepper("small")->addSalt("medium")
// doesn’t allow other objects to access the product while it’s being built.
// helps us avoid creating a subclass* for every possible configuration of an object
// also helps us avoid constructor with lots of parameters like new House(2, 4, 2, null, true, null) and new House(2, 4, 2, null, true, null)

$fluentClass = new class () {
    public function doSomething(): static
    {
        // do something
        return $this;
    }
    public function doSomethingElse(): static
    {
        //do something else
        return $this;
    }
};

//fluent usage
$fluentClass->doSomething()
    ->doSomethingElse()
    ->doSomething()->doSomethingElse()
;
