<?php

namespace Rubic\CleanCheckoutOnestep\Plugin;

use Magento\Checkout\Block\Checkout\TotalsProcessor;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * @author Daniel Sloof <daniel@wearejh.com>
 */
class SortTotalsPlugin
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    /**
     * Since we moved the summary, we need to update this totals processor.
     *
     * @param TotalsProcessor $totalsProcessor
     * @param callable $proceed
     * @param array $jsLayout
     * @return array
     */
    public function aroundProcess(TotalsProcessor $totalsProcessor, callable $proceed, $jsLayout)
    {
        $totalsPath = 'components/checkout/children/steps/children/summary-step/children/summary/children/totals';
        return $this->arrayManager->set(
            $totalsPath,
            $jsLayout,
            $totalsProcessor->sortTotals($this->arrayManager->get($totalsPath, $jsLayout))
        );
    }
}