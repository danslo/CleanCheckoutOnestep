<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
use Magento\Framework\Component\ComponentRegistrar;
use Rubic\CleanCheckout\Theme\ThemeRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Rubic_CleanCheckoutOnestep',
    __DIR__
);

ThemeRegistrar::register('Rubic_CleanCheckoutOnestep', 'Onestep Theme');