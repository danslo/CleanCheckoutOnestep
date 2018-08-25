<?php

namespace Rubic\CleanCheckoutOnestep\Plugin;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\PaymentMethodManagementInterface;

/**
 * @author Daniel Sloof <daniel@wearejh.com>
 */
class AllPaymentMethodsPlugin
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @param CheckoutSession $checkoutSession
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        PaymentMethodManagementInterface $paymentMethodManagement
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->paymentMethodManagement = $paymentMethodManagement;
    }

    /**
     * Magento normally only returns a list here for virtual quotes.
     *
     * Since we always show the payment method block, just return all applicable methods.
     *
     * @return array
     */
    private function getPaymentMethods()
    {
        $paymentMethods = [];
        $quote = $this->checkoutSession->getQuote();
        foreach ($this->paymentMethodManagement->getList($quote->getId()) as $paymentMethod) {
            $paymentMethods[] = [
                'code' => $paymentMethod->getCode(),
                'title' => $paymentMethod->getTitle()
            ];
        }
        return $paymentMethods;
    }

    /**
     * @param DefaultConfigProvider $defaultConfigProvider
     * @param array $config
     * @return array
     */
    public function afterGetConfig(DefaultConfigProvider $defaultConfigProvider, $config)
    {
        $config['paymentMethods'] = $this->getPaymentMethods();
        return $config;
    }
}