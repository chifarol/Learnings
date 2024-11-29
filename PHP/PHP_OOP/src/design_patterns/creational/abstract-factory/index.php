<?php

// assuming you're working on football leagues each having 3 sub divisions

//                     Division1   Division2   Division3 
// Premier League      Man utd     hull city   dicmklds
// LaLiga              Barcelona   primiera    cklmsd
// Bundesliga          Dortmund    eddxmk      cdpod
// Seria A             AC Milan    cdk         cdcdmk

// OR you have a furniture company

//                     Chair       Sofa        Table 
// Victorian           whatever    whatever    whatever
// Arty                whatever    whatever    whatever
// Futuristic          whatever    whatever    whatever


// They work by having an interface that implements the "left-column" as methods that in turn return the "right-column" as instances


interface LeagueInterface
{
    public function getDivision1(): LeagueDiv1Interface;
    public function getDivision2(): LeagueDiv2Interface;
    public function getDivision3(): LeagueDiv3Interface;
}
interface LeagueDiv1Interface
{
    public function getDiv1Characteristics(): string;
}
interface LeagueDiv2Interface
{
    public function getDiv2Characteristics(): string;
}
interface LeagueDiv3Interface
{
    public function getDiv3Characteristics(): string;
}



class PremierLeague implements LeagueInterface
{
    public function getDivision1(): LeagueDiv1Interface
    {
        return new PremierLeagueDiv1();
    }
    public function getDivision2(): LeagueDiv2Interface
    {
        return new PremierLeagueDiv2();
    }
    public function getDivision3(): LeagueDiv3Interface
    {
        return new PremierLeagueDiv3();
    }
}
class PremierLeagueDiv1 implements LeagueDiv1Interface
{
    public function getDiv1Characteristics(): string
    {
        return "division 1 something";
    }
}
class PremierLeagueDiv2 implements LeagueDiv2Interface
{
    public function getDiv2Characteristics(): string
    {
        return "division 2 something";
    }
}
class PremierLeagueDiv3 implements LeagueDiv3Interface
{
    public function getDiv3Characteristics(): string
    {
        return "divison 3 something";
    }
}
class LeagueSelector
{
    public function __construct(private string $category)
    {

    }

    public function getLeague(): LeagueInterface
    {
        if ($this->category === "premier") {

            return new PremierLeague();
        } else if ($this->category === "serie-a") {
            // return new SerieA();

        } else if ($this->category === "bundes-liga") {

            // return new BundesLiga();
        } else {
            return new PremierLeague();
        }
    }
    public function someOperation(): string
    {
        // Call the factory method to create a Payment object.
        $league = $this->getLeague();
        // Now, use the Payment.
        $result = "PaymentMethod: The same paymentMethod's code has just worked with " .
            $league->getDivision1();

        return $result;
    }
}




function clientCode(LeagueSelector $leagueSelector)
{
    // ...
    echo "Client: I'm not aware of the paymentMethod's class, but it still works.\n"
        . $leagueSelector->someOperation();
    // ...
}


echo "<pre>";


echo "League: Launched premier league.\n";
echo ((new LeagueSelector("premier"))->getLeague()->getDivision1()->getDiv1Characteristics());
echo "\n\n";



echo "</pre>";