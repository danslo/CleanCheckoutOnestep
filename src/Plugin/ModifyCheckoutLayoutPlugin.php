<?php

namespace Rubic\CleanCheckoutOnestep\Plugin;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\AttributeMapper;

/**
 * @author Daniel Sloof <daniel@wearejh.com>
 */
class ModifyCheckoutLayoutPlugin
{
    /**
     * @var AttributeMetadataDataProvider
     */
    public $attributeMetadataDataProvider;

    /**
     * @var AttributeMapper
     */
    public $attributeMapper;

    /**
     * @var AttributeMerger
     */
    public $merger;

    /**
     * @var CheckoutSession
     */
    public $checkoutSession;

    /**
     * @var null
     */
    public $quote = null;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param AttributeMapper $attributeMapper
     * @param AttributeMerger $merger
     * @param CheckoutSession $checkoutSession
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        AttributeMerger $merger,
        CheckoutSession $checkoutSession,
        ArrayManager $arrayManager
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->merger = $merger;
        $this->checkoutSession = $checkoutSession;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Moves the summary from the sidebar into our summary step.
     *
     * @param array $jsLayout
     * @return array
     */
    protected function moveSummary($jsLayout)
    {
        $jsLayout = $this->arrayManager->move(
            'components/checkout/children/sidebar/children/summary',
            'components/checkout/children/steps/children/summary-step/children/summary',
            $jsLayout
        );

        // move totals below items...
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['summary-step']['children']['summary']['children'])) {
            $children = $jsLayout['components']['checkout']['children']['steps']['children']['summary-step']['children']['summary']['children'];
            $sortOrder = 10;
            foreach ($children as $key => $child) {
                $jsLayout['components']['checkout']['children']['steps']['children']['summary-step']['children']['summary']['children'][$key]['sortOrder'] = $sortOrder;
                $sortOrder += 10;
            }
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['summary-step']['children']['summary']['children']['totals'])) {
                $jsLayout['components']['checkout']['children']['steps']['children']['summary-step']['children']['summary']['children']['totals']['sortOrder'] = $sortOrder +10;
            }

        }

        return $jsLayout;
    }

    protected function moveAgreements($jsLayout)
    {
        if ($this->arrayManager->exists('components/checkout/children/steps/children/billing-step/children/payment/children/payments-list/children/before-place-order/children/agreements', $jsLayout)) {
            $jsLayout = $this->arrayManager->move(
                'components/checkout/children/steps/children/billing-step/children/payment/children/payments-list/children/before-place-order/children/agreements',
                'components/checkout/children/steps/children/summary-step/children/agreements',
                $jsLayout
            );
            $jsLayout = $this->arrayManager->set('components/checkout/children/steps/children/summary-step/children/agreements/template', $jsLayout, 'Rubic_CleanCheckoutOnestep/checkout-agreements');
        }

        return $jsLayout;
    }

    protected function moveNewsletter($jsLayout)
    {
        if ($this->arrayManager->exists('components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods/children/newsletter', $jsLayout)) {
            $jsLayout = $this->arrayManager->move(
                'components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods/children/newsletter',
                'components/checkout/children/steps/children/summary-step/children/newsletter',
                $jsLayout
            );
        }

        return $jsLayout;
    }

    /**
     * Moves discount from payment to the summary step.
     *
     * @param array $jsLayout
     * @return array
     */
    protected function moveDiscount($jsLayout)
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
    protected function moveBilling($jsLayout)
    {
        if ($this->getQuote()->isVirtual()) {
            return $jsLayout;
        }

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset'])) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['placeholder'] = __('Street Address');
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][1]['placeholder'] = __('Street line 2');

            $elements = $this->getAddressAttributes();
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['billing-address'] = $this->getCustomBillingAddressComponent($elements);

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['billing-address']['children']['form-fields']['children']['street']['children'][0]['placeholder'] = __('Street Address');
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['billing-address']['children']['form-fields']['children']['street']['children'][1]['placeholder'] = __('Street line 2');
        }

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['billing-address-form']);
        }

        if ($billingAddressForms = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']) {
            foreach ($billingAddressForms as $billingAddressFormsKey => $billingAddressForm) {
                if ($billingAddressFormsKey != 'before-place-order') {
                    unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$billingAddressFormsKey]);
                }
            }
        }

        return $jsLayout;
    }

    /**
     * Get Quote
     *
     * @return \Magento\Quote\Model\Quote|null
     */
    public function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }

    /**
     * Get all visible address attribute
     *
     * @return array
     */
    protected function getAddressAttributes()
    {

        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );

        $elements = [];
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if ($attribute->getIsUserDefined()) {
                continue;
            }
            $elements[$code] = $this->attributeMapper->map($attribute);
            if (isset($elements[$code]['label'])) {
                $label = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }
        }
        return $elements;
    }

    /**
     * Prepare billing address field for shipping step for physical product
     *
     * @param $elements
     * @return array
     */
    protected function getCustomBillingAddressComponent($elements)
    {
        return [
            'component' => 'Rubic_CleanCheckoutOnestep/js/view/billing-address',
            'displayArea' => 'billing-address',
            'provider' => 'checkoutProvider',
            'deps' => ['checkoutProvider'],
            'dataScopePrefix' => 'billingAddress',
            'children' => [
                'form-fields' => [
                    'component' => 'uiComponent',
                    'displayArea' => 'additional-fieldsets',
                    'children' => $this->merger->merge(
                        $elements,
                        'checkoutProvider',
                        'billingAddress',
                        [
                            'country_id' => [
                                'sortOrder' => 115,
                            ],
                            'region' => [
                                'visible' => false,
                            ],
                            'region_id' => [
                                'component' => 'Magento_Ui/js/form/element/region',
                                'config' => [
                                    'template' => 'ui/form/field',
                                    'elementTmpl' => 'ui/form/element/select',
                                    'customEntry' => 'billingAddress.region',
                                ],
                                'validation' => [
                                    'required-entry' => true,
                                ],
                                'filterBy' => [
                                    'target' => '${ $.provider }:${ $.parentScope }.country_id',
                                    'field' => 'country_id',
                                ],
                            ],
                            'postcode' => [
                                'component' => 'Magento_Ui/js/form/element/post-code',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                            'company' => [
                                'validation' => [
                                    'min_text_length' => 0,
                                ],
                            ],
                            'fax' => [
                                'validation' => [
                                    'min_text_length' => 0,
                                ],
                            ],
                            'telephone' => [
                                'config' => [
                                    'tooltip' => [
                                        'description' => __('For delivery questions.'),
                                    ],
                                ],
                            ],
                        ]
                    ),
                ],
            ],
        ];
    }

    /**
     * Adds the place order components to the summary.
     *
     * @param array $jsLayout
     * @return array
     */
    protected function addPlaceOrder($jsLayout)
    {
        return $this->arrayManager->set(
            'components/checkout/children/steps/children/summary-step/children/placeOrder',
            $jsLayout,
            ['component' => 'Rubic_CleanCheckoutOnestep/js/view/place-order']
        );
    }

    protected function changeEmailStepTemplate($jsLayout)
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['email-step'])) {
            $jsLayout['components']['checkout']['children']['steps']['children']['email-step']['template'] = 'Rubic_CleanCheckoutOnestep/email';
        }

        return $jsLayout;
    }

    /**
     * @param LayoutProcessor $layoutProcessor
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(LayoutProcessor $layoutProcessor, $jsLayout)
    {
        $jsLayout = $this->changeEmailStepTemplate($jsLayout);
        $jsLayout = $this->moveSummary($jsLayout);
        $jsLayout = $this->moveDiscount($jsLayout);
        $jsLayout = $this->moveNewsletter($jsLayout);
        $jsLayout = $this->moveAgreements($jsLayout);
        $jsLayout = $this->moveBilling($jsLayout);
        $jsLayout = $this->addPlaceOrder($jsLayout);
        return $jsLayout;
    }
}
