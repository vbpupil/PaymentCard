<?php
/**
 * Freetimers Shortcode Class
 *
 * @author    Dean Haines
 * @copyright Freetimers Communications Ltd, 2018, UK
 * @license   Proprietary See LICENSE.md
 */

namespace vbpupil\Calculate;


Interface InterestInterface
{
    public function calculate($credit, $debit, $apr);
}