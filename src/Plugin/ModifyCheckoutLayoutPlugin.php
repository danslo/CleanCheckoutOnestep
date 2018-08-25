<?php

namespace Rubic\CleanCheckoutOnestep\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * @author Daniel Sloof <daniel@wearejh.com>
 */
class ModifyCheckoutLayoutPlugin
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
     * Moves the summary from the sidebar into our summary step.
     *
     * @param array $jsLayout
     * @return array
     */
    private function moveSummary($jsLayout)
    {
        return $this->arrayManager->move(
            'components/checkout/children/sidebar/children/summary',
            'components/checkout/children/steps/children/summary-step/children/summary',
            $jsLayout
        );
    }

    /**
     * Moves discount from payment to the summary step.
     *
     * @param array $jsLayout
     * @return array
     */
    private function moveDiscount($jsLayout)
    {
        return $this->arrayManager->move(
            'components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods/children/discount',
            'components/checkout/children/steps/children/summary-step/children/discount',
            $jsLayout
        );
    }

    /**
     * Adds the place order components to the summary.
     *
     * @param array $jsLayout
     * @return array
     */
    private function addPlaceOrder($jsLayout)
    {
        return $this->arrayManager->set(
            'components/checkout/children/steps/children/summary-step/children/placeOrder',
            $jsLayout,
            ['component' => 'Rubic_CleanCheckoutOnestep/js/view/place-order']
        );
    }

    /**
     * We will handle this in the initial (shipping) column, so we can remove from billing step.
     *
     * @param array $jsLayout
     * @return array
     */
    private function removeAddressFormsFromBillingStep($jsLayout)
    {
        return $this->arrayManager->remove(
            'components/checkout/children/steps/children/billing-step/children/payment/children/payments-list/children',
            $jsLayout
        );
    }

    /**
     * @param LayoutProcessor $layoutProcessor
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(LayoutProcessor $layoutProcessor, $jsLayout)
    {
        $jsLayout = $this->moveSummary($jsLayout);
        $jsLayout = $this->moveDiscount($jsLayout);
        $jsLayout = $this->addPlaceOrder($jsLayout);
        $jsLayout = $this->removeAddressFormsFromBillingStep($jsLayout);
        return $jsLayout;
    }
}