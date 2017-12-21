<?php
/**
 * CardType Class
 *
 * @author    Dean Haines
 * @copyright Dean Haines, 2017, UK
 * @license   Proprietary See LICENSE.md
 */

namespace Vbpupil\Card;


use MyCLabs\Enum\Enum;

/**
 * Class CardType
 *
 * @method CardType GIFT()
 * @method CardType DEBIT()
 * @method CardType CREDIT()
 * @method CardType CHARGE()
 * @method CardType ATM()
 * @method CardType STORE()
 * @method CardType FLEET()
 */
class CardType extends Enum
{

    const GIFT = 'gift';
    const DEBIT = 'debit';
    const CREDIT = 'credit';
    const CHARGE = 'charge';
    const ATM = 'atm';
    const STORE = 'store';
    const FLEET = 'fleet';

}