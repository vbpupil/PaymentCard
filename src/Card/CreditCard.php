<?php
/**
 * CreditCard Class
 *
 * @author    Dean Haines
 * @copyright Freetimers Communications Ltd, 2017, UK
 * @license   Proprietary See LICENSE.md
 */

namespace Vbpupil\Card;


use Chippyash\Type\Number\FloatType;
use DateTime;
use vbpupil\Audit\Audit;

/**
 * Class CreditCard
 */
class CreditCard extends PaymentCard
{

    /**
     * @var
     */
    protected $cardCharge;

    /**
     * @var
     */
    protected $creditLimit;

    /**
     * @var
     */
    protected $creditTrack;

    /**
     * @var
     */
    protected $apr;

    /**
     * CreditCard constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCreditLimit(new FloatType(100.00));
        $this->setApr(new FloatType(20.00));
        $this->setCardCharge(new FloatType(0));
    }

    /**
     * @param FloatType $value
     * @throws PaymentCardException
     */
    public function debitAccount(FloatType $value)
    {
        if (($this->creditLimit->get() - $this->balance->get()) - $value->get() < 0) {
            $this->setAudit(array('msg' => Audit::AUDIT_MSG[__FUNCTION__ . 'Fail'], 'value' => $value->get()));
            throw new PaymentCardException ('Insufficient credit limit to proceed.');
        }

        $this->balance = new FloatType(($this->balance->get() - $value->get()));

        $this->creditTracker(__FUNCTION__, $value->get());

        $this->setAudit(array('msg' => Audit::AUDIT_MSG[__FUNCTION__], 'value' => $value->get()));
        $this->getBalance();
    }

    /**
     * @param FloatType $value
     * @return $this|PaymentCard
     */
    public function creditAccount(FloatType $value)
    {
        $this->creditTracker(__FUNCTION__, $value->get());
        parent::creditAccount($value);

        return $this;
    }

    /**
     * Track when credit was borrowed so that we can calculate fee owed
     *
     * @param $action
     * @param $value
     */
    protected function creditTracker($action, $value)
    {
        switch ($action){
            case 'debitAccount':
                $this->creditTrack['debit'][] = array('date'=>new DateTime('now'), 'value'=> $value);
                break;
            case 'creditAccount':
                $this->creditTrack['credit'][] = array('date'=>new DateTime('now'), 'value'=> $value);
                break;
        }
    }


    /**
     *
     */
    public function calculateInterest()
    {

    }

    /**
     * @return mixed
     */
    public function getCreditLimit()
    {
        return $this->creditLimit;
    }

    /**
     * @param FloatType $value
     * @return $this
     */
    public function setCreditLimit(FloatType $value)
    {
        $this->setAudit(array('msg' => 'Account credited', 'value' => $value->get()));
        $this->creditLimit = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getApr()
    {
        return $this->apr;
    }

    /**
     * @param FloatType $apr
     * @return CreditCard
     */
    public function setApr(FloatType $apr)
    {
        $this->apr = $apr;
        return $this;
    }


    /**
     * @return string
     */
    public function getCreditLimitToString()
    {
        return "Your current Limit is {$this->creditLimit->get()}";
    }

    /**
     * @return string
     */
    public function toString()
    {
        $response = "Valid date is {$this->getValidDate()->format('d-m-Y')}<br/>";
        $response .= "Expiry date is {$this->getExpDate()->format('d-m-Y')}<br />";
        $response .= $this->getCreditLimitToString() . "<br/>";
        $response .= $this->getBalanceToString();

        return $response;
    }

    /**
     * @return mixed
     */
    public function getCardCharge()
    {
        return $this->cardCharge->get();
    }

    /**
     * Amount you will be charged for paying with this credit card
     * note - this is no longer allowed in the uk as of 01/01/2018
     *
     * @param FloatType $cardCharge
     */
    public function setCardCharge(FloatType $cardCharge)
    {
        $this->cardCharge = $cardCharge;
    }


}