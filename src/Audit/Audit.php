<?php
/**
 * AuditCard Class Class
 *
 * @author    Dean Haines
 * @copyright Dean Haines, 2017, UK
 * @license   Proprietary See LICENSE.md
 */

namespace vbpupil\Audit;


use DateTime;

/**
 * Class Audit
 */
class Audit
{

    /**
     *
     */
    const AUDIT_MSG = [
        'debitAccountFail' => 'ABORTED DEBIT, Insufficient funds',
        'debitAccount' => 'Account debited',
        'getBalance' => 'Balance requested',
        'setBalance' => 'Balance set',
        'setCardName' => 'Card name set',
        'setCardType' => 'Card type set',
        'setExpDate' => 'Expiry date set',
        'setValidDate' => 'Valid from date set',
        'creditAccount' => 'Account credited'
    ];

    /**
     * @param array $trans
     */
    public static function setAudit($audit = array(), $trans = array())
    {
        $trans['time'] = (new DateTime('now'));
        $audit[] = $trans;

        return $audit;
    }


}