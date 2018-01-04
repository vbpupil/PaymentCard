<?php
/**
 * @author    Dean Haines
 * @copyright Dean Haines, 2018, UK
 * @license   Proprietary See LICENSE.md
 */

namespace vbpupil\test\Unit;

use Chippyash\Type\Number\FloatType;
use Chippyash\Type\String\StringType;
use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Vbpupil\Card\CardType;
use Vbpupil\Card\PaymentCard;


class PaymentCardTest extends TestCase
{

    /*
     *
     */
    protected $sut;

    protected $audit;

    /**
     *
     */
    public function setUp()
    {
        $this->sut = new PaymentCard();

        $this->sut
            ->setCardName(new StringType('Debit Card'))
            ->setCardType(new CardType('debit'))
            ->setOwner(new StringType('Mr D Haines'))
            ->setValidDate(new DateTime('1/12/17', new DateTimeZone('Europe/London')))
            ->setExpDate(new DateTime('01/12/18', new DateTimeZone('Europe/London')));

        $this->getAudit();
    }

    /**
     *
     */
    public function testWeHaveABalanceOfZeroAndThatAuditIsBeingRecorded()
    {
        $this->assertEquals('0', $this->sut->getBalance());
        $this->assertEquals('Balance set', $this->audit[0]['msg']);
        $this->assertEquals('0', $this->audit[0]['value']);
    }

    public function getAudit()
    {
        $this->audit = $this->sut->getAudit();
    }

    public function getLastAuditItem()
    {
        $this->audit = $this->sut->getLastAuditItem();
    }

    public function testCeditAccountAndBalance()
    {
        $this->sut->creditAccount(new FloatType(10.00), new DateTime('now'));

        $this->getLastAuditItem();

        $this->assertEquals('10.00', $this->sut->getBalance());
        $this->assertEquals('Balance requested', $this->audit['msg']);
        $this->assertEquals('10.00', $this->audit['value']);
    }

    public function testDebitAccountAndBalance()
    {
        $this->sut->creditAccount(new FloatType(10.00), new DateTime('now'));
        $this->sut->debitAccount(new FloatType(5.00), new DateTime('now'));

        $this->getLastAuditItem();

        $this->assertEquals('5.00', $this->sut->getBalance());
        $this->assertEquals('Balance requested', $this->audit['msg']);
        $this->assertEquals('5.00', $this->audit['value']);
    }

    public function testBalanceToString()
    {
        $this->sut->creditAccount(new FloatType(10.00), new DateTime('now'));
        $this->assertEquals('Your current Balance is 10', $this->sut->getBalanceToString());
    }
}
