<?php
/**
 * @author    Dean Haines
 * @copyright Dean Haines, 2018, UK
 * @license   Proprietary See LICENSE.md
 */

namespace vbpupil\Calculate;


Interface PaymentCardInterestInterface
{
    public function calculate($credit, $debit, $apr);
}