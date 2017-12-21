<?php
/**
 * PaymentCard Class
 *
 * @author    Dean Haines
 * @copyright Dean Haines, 2017, UK
 * @license   Proprietary See LICENSE.md
 */

namespace Vbpupil\Card;

use Chippyash\Type\Number\FloatType;
use Chippyash\Type\String\StringType;
use DateTime;
use Exception;
use Vbpupil\Audit\Audit;


/**
 * Class PaymentCard
 */
class PaymentCard extends CardType
{

    /**
     * @var StringType
     */
    protected $cardName;

    /**
     * @var CardType
     */
    protected $cardType;

    /**
     * @var StringType
     */
    protected $owner;

    /**
     * @var DateTime
     */
    protected $expDate;

    /**
     * @var DateTime
     */
    protected $validDate;

    /**
     * @var
     */
    protected $audit;

    /**
     * @var FloatType
     */
    protected $balance;


    /**
     * PaymentCard constructor.
     */
    public function __construct()
    {
        if(!is_a($this->balance,'FloatType') ) {
            $this->setBalance(new FloatType(0.00));
        }
    }

    /**
     * @return FloatType
     */
    public function getBalance()
    {
        $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__], 'value'=>$this->balance->get()));
        return $this->balance;
    }

    /**
     * @param FloatType $balance
     * @return PaymentCard
     */
    public function setBalance(FloatType $balance)
    {
        $this->balance = $balance;
        $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__], 'value'=>$balance->get()));
        return $this;
    }


    /**
     * @return StringType
     */
    public function getCardName()
    {
        return $this->cardName;
    }

    /**
     * @param StringType $cardName
     * @return PaymentCard
     */
    public function setCardName(StringType $cardName)
    {
        $this->cardName = $cardName;
        $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__], 'value'=>$cardName->get()));
        return $this;
    }

    /**
     * @return CardType
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param CardType $cardType
     * @return $this|string
     */
    public function setCardType(CardType $cardType)
    {
        try {
            $this->cardType = $cardType;
            $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__], 'value'=>$cardType->getValue()));
            return $this;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return StringType
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param StringType $owner
     * @return $this
     */
    public function setOwner(StringType $owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpDate()
    {
        return $this->expDate;
    }

    /**
     * @param DateTime $expDate
     * @return $this
     */
    public function setExpDate(DateTime $expDate)
    {
        $this->expDate = $expDate;
        $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__], 'value'=>$expDate));
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidDate()
    {
        return $this->validDate;
    }

    /**
     * @param DateTime $validDate
     * @return $this
     */
    public function setValidDate(DateTime $validDate)
    {
        $this->validDate = $validDate;
        $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__],'value'=>$validDate));
        return $this;
    }

    /**
     * @param FloatType $value
     * @return PaymentCard
     */
    public function creditAccount(FloatType $value)
    {
        $this->balance = new FloatType(($this->balance->get() + $value->get()));
        $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__], 'value'=>$value->get()));
        $this->getBalance();
        return $this;
    }

    /**
     * @param FloatType $value
     * @throws PaymentCardException
     */
    public function debitAccount(FloatType $value)
    {
        if ($this->balance->get() - $value->get() < 0) {
            $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__.'Fail'], 'value'=>$value->get()));
            throw new PaymentCardException ('Insufficient funds to proceed.');
        }

        $this->balance = new FloatType(($this->balance->get() - $value->get()));
        $this->setAudit(array('msg'=>Audit::AUDIT_MSG[__FUNCTION__], 'value'=>$value->get()));
        $this->getBalance();
    }

    /**
     * @return string
     */
    public function getBalanceToString()
    {
        return "Your current Balance is {$this->balance->get()}";
    }

    /**
     * @return string
     */
    public function toString()
    {
        $response = "Valid date is {$this->getValidDate()->format('d-m-Y')}<br/>";
        $response .= "Expiry date is {$this->getExpDate()->format('d-m-Y')}<br />";
        $response .= $this->getBalanceToString();

        return $response;
    }

    /**
     * @param array $trans
     */
    protected function setAudit($trans = array())
    {
        $this->audit = Audit::setAudit($this->audit, $trans);
    }

    /**
     * @return mixed
     */
    public function getAudit()
    {
        return $this->audit;
    }
}