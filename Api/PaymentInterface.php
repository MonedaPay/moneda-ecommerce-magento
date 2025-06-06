<?php
/**
 * Created by Qoliber
 *
 * @category    MonedaPay
 * @package     MonedaPay_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace MonedaPay\MonedaPay\Api;

use MonedaPay\MonedaPay\Api\Response\PlaceOrderInterface;
use MonedaPay\MonedaPayLib\Model\Response\AggregatedOrderStatusResponseInterface;
use MonedaPay\MonedaPayLib\Model\Response\OrderInfoResponseInterface;

interface PaymentInterface
{
    /**
     * Place Order
     *
     * @return \MonedaPay\MonedaPay\Api\Response\PlaceOrderInterface
     */
    public function placeOrder(): PlaceOrderInterface;

    /**
     * Order Info
     *
     * @return \MonedaPay\MonedaPayLib\Model\Response\OrderInfoResponseInterface
     */
    public function orderInfo(): OrderInfoResponseInterface;

    /**
     * Update Order Status
     *
     * @return \MonedaPay\MonedaPayLib\Model\Response\AggregatedOrderStatusResponseInterface
     */
    public function updateOrderStatus(): AggregatedOrderStatusResponseInterface;
}
