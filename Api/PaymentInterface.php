<?php
/**
 * Created by Qoliber
 *
 * @category    Ari10
 * @package     Ari10_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Ari10\MonedaPay\Api;

use Ari10\MonedaPay\Api\Response\PlaceOrderInterface;
use Ari10\MonedaPayLib\Model\Response\AggregatedOrderStatusResponseInterface;
use Ari10\MonedaPayLib\Model\Response\OrderInfoResponseInterface;

interface PaymentInterface
{
    /**
     * Place Order
     *
     * @return \Ari10\MonedaPay\Api\Response\PlaceOrderInterface
     */
    public function placeOrder(): PlaceOrderInterface;

    /**
     * Order Info
     *
     * @return \Ari10\MonedaPayLib\Model\Response\OrderInfoResponseInterface
     */
    public function orderInfo(): OrderInfoResponseInterface;

    /**
     * Update Order Status
     *
     * @return \Ari10\MonedaPayLib\Model\Response\AggregatedOrderStatusResponseInterface
     */
    public function updateOrderStatus(): AggregatedOrderStatusResponseInterface;
}
