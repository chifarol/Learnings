<?php

//  adapter pattern is used when we need to make a class conform to an API that it does not naturally conform to
//  An adapter wraps one of the objects to hide the complexity of conversion happening behind the scenes
//  It can be likened to a middleman or a translator between 2 classes

//  EXAMPLE
//  you can wrap an object that operates in meters and kilometers with an adapter that converts all of the data to imperial units such as feet and miles
//  an adapter that converts XML-to-JSON
//  you can adapt square pegs to round holes.

class RoundPeg
{
    public function __construct(private float $radius)
    {

    }
    public function getRadius()
    {
        return $this->radius;
    }
}
class SquarePeg
{
    public function __construct(private float $width)
    {

    }
    public function getWidth()
    {
        return $this->width;
    }
}



class RoundHole
{
    public function __construct(private float $radius)
    {

    }
    public function getRadius()
    {
        return $this->radius;
    }
    public function shapeFits(RoundPeg $roundPeg)
    {
        $itFits = $this->radius >= $roundPeg->getRadius();
        echo "shape with radius $this->radius fits: " . ($itFits ? "true" : "false");
        return $itFits;
    }
}

class SquarePegAdapter extends RoundPeg
{
    private $radius;

    public function __construct(private SquarePeg $squarePeg)
    {

    }
    public function getRadius()
    {
        return ($this->squarePeg->getWidth() * sqrt(2) / 2);
    }
}


echo "<pre>";

$hole = new RoundHole(5);
$rpeg = new RoundPeg(5);
$hole->shapeFits($rpeg); // true
echo "\n";
$small_sqpeg = new SquarePeg(5);
$large_sqpeg = new SquarePeg(10);
// $hole->shapeFits($small_sqpeg); // this won't compile (incompatible types)

$small_sqpeg_adapter = new SquarePegAdapter($small_sqpeg);
$large_sqpeg_adapter = new SquarePegAdapter($large_sqpeg);
$hole->shapeFits($small_sqpeg_adapter); // true
$hole->shapeFits($large_sqpeg_adapter); // false
echo "</pre>";
