<?php
/**
 * @author    Dean Haines
 * @copyright Dean Haines, 2018, UK
 * @license   Proprietary See LICENSE.md
 */

namespace vbpupil\Calculate;


use Chippyash\Type\Number\FloatType;
use DateTime;
use Vbpupil\Calculate\PaymentCardInterestInterface;

class CalculateCreditCardInterest implements PaymentCardInterestInterface
{

    protected $totalInterestChargedForTheMonth = 0;

    protected $apr;

    public function __construct()
    {
        $this->setApr(new FloatType(11.5));
    }

    /**
     * @param FloatType $apr
     */
    public
    function setApr(FloatType $apr)
    {
        $this->apr = $apr;
        return $this;
    }

    public function calculate($credit, $debit, $apr)
    {
        $paidIn = 0;
        $paidOut = array();
        $counter = 0;
        $daysInThisMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $daysInThisYear = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
        $this->setApr(new FloatType($apr));

        //GET A TOTAL FOR WHATS BEEN PAID IN
        foreach ($credit AS $d) {
            $paidIn += $d['value'];
        }

        //SORT DEBIT INTO DATE ORDER
        usort($debit, function ($a, $b) {
            if ($a['date'] == $b['date']) {
                return 0;
            }
            return ($a['date'] < $b['date']) ? -1 : 1;
        });


        foreach ($debit as $d) {
            $paidOut[$counter]['loanTime'] = $d['date']->diff(new DateTime('now'));
            $paidOut[$counter]['amount'] = $d['value'];
            $paidOut[$counter]['date'] = $d['date'];

            if ($paidIn > 0) {
                $paidOut[$counter]['amount'] -= $paidIn;
                $paidIn = ($paidIn - $d['value']);
            }

            //(AMOUNT BORROWED * NUMBER OF DAYS BORROWED FOR) / BY THE MONTH
            $paidOut[$counter]['dailyBalance'] = ($paidOut[$counter]['amount'] * $paidOut[$counter]['loanTime']->days) / $daysInThisMonth;

            //TOTAL AMOUNT YOU WILL BE CHARGED FOR BORROWING
            $paidOut[$counter]['interestCharged'] = number_format(($paidOut[$counter]['dailyBalance'] * (($this->getApr() / $daysInThisYear) / 100) * $daysInThisMonth), 2, '.', '');

            $this->totalInterestChargedForTheMonth += $paidOut[$counter]['interestCharged'];

            $counter++;
        }

        $paidOut['totalInterestChargedForTheMonth'] = $this->totalInterestChargedForTheMonth;

        return $paidOut;
    }

    /**
     * @return float
     */
    public
    function getApr()
    {
        return $this->apr->get();
    }

}