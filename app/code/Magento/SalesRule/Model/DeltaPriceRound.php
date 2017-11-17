<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRule\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Round price and save rounding operation delta.
 */
class DeltaPriceRound
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var float[]
     */
    private $roundingDeltas;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Round price based on previous rounding operation delta.
     *
     * @param float $price
     * @param string $type
     * @return float
     */
    public function round($price, $type)
    {
        if ($price) {
            // initialize the delta to a small number to avoid non-deterministic behavior with rounding of 0.5
            $delta = isset($this->roundingDeltas[$type]) ? $this->roundingDeltas[$type] : 0.000001;
            $price += $delta;
            $roundPrice = $this->priceCurrency->round($price);
            $this->roundingDeltas[$type] = $price - $roundPrice;
            $price = $roundPrice;
        }

        return $price;
    }

    /**
     * Reset all deltas.
     *
     * @return void
     */
    public function resetAll()
    {
        $this->roundingDeltas = [];
    }

    /**
     * Reset deltas by type.
     *
     * @param string $type
     * @return void
     */
    public function reset(string $type)
    {
        if (isset($this->roundingDeltas[$type])) {
            unset($this->roundingDeltas[$type]);
        }
    }
}
