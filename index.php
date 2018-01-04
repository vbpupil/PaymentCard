<?php

use Chippyash\Type\Number\FloatType;
use Chippyash\Type\String\StringType;
use Vbpupil\Calculate\CalculateCreditCardInterest;
use Vbpupil\Card\CardType;
use Vbpupil\Card\CreditCard;

include 'vendor/autoload.php';

$card = new CreditCard();

$card
->setCardName(new StringType('Shop Gift Card'))
->setCardType(new CardType('credit'))
->setOwner(new StringType('Mr D Schwartz'))
->setValidDate(new DateTime('1/12/17', new DateTimeZone('Europe/London')))
->setExpDate(new DateTime('01/12/18', new DateTimeZone('Europe/London')))

->creditAccount(new FloatType(10.00), new DateTime('now'))
->creditAccount(new FloatType(15.00), new DateTime('now'))

->debitAccount(new FloatType(500.00), new DateTime('2017-09-02'))
->debitAccount(new FloatType(80.00), new DateTime('2017-04-24'))
->debitAccount(new FloatType(200.00), new DateTime('now'))

->calculateInterest( new CalculateCreditCardInterest());

dump($card);