define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ], function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'moneda_pay',
                component: 'Ari10_MonedaPay/js/view/payment/method-renderer/moneda-pay-method'
            }
        );

        return Component.extend({});
    }
);
