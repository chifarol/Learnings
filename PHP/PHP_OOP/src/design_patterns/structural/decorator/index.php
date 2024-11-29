<?php
//  decorator pattern is an alternative way to inherit and modify or extend functionality without using object inheritance
//  The decorator class wraps a target class and acts as a simple proxy for *methods that you do not want to change*

//  a simple wrapper becomes a decorator when the wrapper implements the same interface as the wrapped object

//  decorators can be stacked up (e.g you can inherit a human body and stack a sweater for cold then a raincoat for rain).

//  This approach is quite similar conceptually to object inheritance. It is essentially the alternative strategy to use when you want to adopt composition instead of inheritance


//  EXAMPLE
//  implementing filter for different types of content, such as comments, forum posts or private messages



/**
 * The Component interface declares a filtering method that must be implemented
 * by all concrete components and decorators.
 */
interface FormatInputInterface
{
    public function formatText(string $text): string;
}

/**
 * The Concrete Component is a core element of decoration. It contains the
 * original text, as is, without any filtering or formatting.
 */
class TextInput implements FormatInputInterface
{
    public function formatText(string $text): string
    {
        return $text;
    }
}

/**
 * The base Decorator class doesn't contain any real filtering or formatting
 * logic. Its main purpose is to implement the basic decoration infrastructure:
 * a field for storing a wrapped component or another decorator and the basic
 * formatting method that delegates the work to the wrapped object. The real
 * formatting job is done by subclasses.
 */
class FormatTextDecorator implements FormatInputInterface
{

    public function __construct(protected FormatInputInterface $inputFormat)
    {

    }

    /**
     * Decorator delegates all work to a wrapped component.
     */
    public function formatText(string $text): string
    {
        return $this->inputFormat->formatText($text);
    }
}

/**
 * This Concrete Decorator strips out all HTML tags from the given text.
 */
class PlainTextFilter extends FormatTextDecorator
{
    public function formatText(string $text): string
    {
        $text = parent::formatText($text);
        return strip_tags($text);
    }
}

echo "<pre>";

// control Radio from remote controller
$text = <<<HERE
Please visit my <a href='http://www.iwillhackyou.com'>homepage</a>.
<script src="http://www.iwillhackyou.com/script.js">
  performXSSAttack();
</script>
HERE;

(new PlainTextFilter(new TextInput()))->formatText($text);
echo "\n";


echo "</pre>";