<?php
// Can be called a WRAPPER

// provides a simplified interface to a library, a framework, or any other complex set of classes (more like adding another layer of abtraction to make things appear simpler)
// provides convenient access to a particular part of the larger subsystem’s functionality
// because of the above a facade might provide limited functionality in comparison to working with the subsystem directly
// a facade is handy when you need to integrate your app with a sophisticated library that has dozens of features, but you just need a tiny bit of its functionality

// e.g a facade that provides access to language translation from chatGPT

//Try not to confuse the Facade pattern with Laravel facades, which are more of a magical way to access services statically as opposed to having explicit dependencies injected into class instances:

class Translate
{
    public function __construct(private string $text, private string $language)
    {
        # code...
    }
    public function getChatGPTTranslation()
    {
        return (new ChatGPT($this->text))->getTranslation($this->language);
        # code...
    }
}

class ChatGPT
{
    public function __construct(private string $text)
    {

    }
    public function getTranslation(string $language)
    {
        return "dksnksdnijisx";
        # code...
    }

    // nore methods
    // nore methods
    // nore methods
    // nore methods
    // nore methods
    // nore methods
    // nore methods
    // nore methods
}


$translator = new Translate('welcome', "fr");
echo $translator->getChatGPTTranslation();