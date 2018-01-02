<?php
/**
 * @author    Dean Haines
 * @copyright Dean Haines, 2018, UK
 * @license   Proprietary See LICENSE.md
 */

namespace vbpupil\Calculate;


use DateTime;
use Vbpupil\Calculate\PaymentCardInterestInterface;

class CalculateCreditCardPaymentCardInterest implements PaymentCardInterestInterface
{

    protected $totalInterestChargedForTheMonth = 0;

    public function calculate($credit, $debit, $apr)
    {
        $paidIn = 0;
        $paidOut = array();
        $counter = 0;

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
            $paidOut[$counter]['dailyBalance'] = ($paidOut[$counter]['amount'] * $paidOut[$counter]['loanTime']->days) / 30;

            //TOTAL AMOUNT YOU WILL BE CHARGED FOR BORROWING
            $paidOut[$counter]['interestCharged'] = number_format(($paidOut[$counter]['dailyBalance'] * (($apr / 365) / 100) * 30), 2, '.', '');

            $this->totalInterestChargedForTheMonth += $paidOut[$counter]['interestCharged'];

            $counter++;
        }

        $paidOut['totalInterestChargedForTheMonth'] = $this->totalInterestChargedForTheMonth;

        return $paidOut;
    }

}