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
     * Moves billing from payment to shipping step.
     *
     * @param array $jsLayout
     * @return array
     */
    private function moveBilling($jsLayout)
    {
        return $this->arrayManager->move(
            'components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods/children/billing-address-form',
            'components/checkout/children/steps/children/shipping-step/children/billing-address-form',
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
     * @param LayoutProcessor $layoutProcessor
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(LayoutProcessor $layoutProcessor, $jsLayout)
    {
        $jsLayout = $this->moveSummary($jsLayout);
        $jsLayout = $this->moveDiscount($jsLayout);
        //$jsLayout = $this->moveBilling($jsLayout);
        $jsLayout = $this->addPlaceOrder($jsLayout);
        return $jsLayout;
    }
}