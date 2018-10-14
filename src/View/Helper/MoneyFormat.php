<?php

namespace ConferenceTools\Attendance\View\Helper;

use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Zend\View\Helper\AbstractHelper;

class MoneyFormat extends AbstractHelper
{
    /**
     * @param bool $useNet
     * @return string
     */
    public function __invoke($money, $useNet = false)
    {
        if ($money instanceof Price) {
            if ($useNet) {
                $money = $money->getNet();
            } else {
                $money = $money->getGross();
            }
        }
        //$currencyFormat = $this->getView()->plugin('currencyFormat');

        //return $currencyFormat($money->getAmount() / 100, $money->getCurrency());

        return sprintf('£%.2f', $money->getAmount() / 100);
    }
}
