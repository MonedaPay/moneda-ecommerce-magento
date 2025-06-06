<?php
/**
 * Created by Qoliber
 *
 * @category    MonedaPay
 * @package     MonedaPay_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace MonedaPay\MonedaPay\Model\Services;

use MonedaPay\MonedaPay\Api\PaymentInterface;
use MonedaPay\MonedaPay\Api\Response\PlaceOrderInterface as Response;
use MonedaPay\MonedaPay\Logger\Logger;
use MonedaPay\MonedaPay\Model\Payment as MonedaPayPayment;
use MonedaPay\MonedaPayLib\Model\Response\AggregatedOrderStatusResponseInterface;
use MonedaPay\MonedaPayLib\Model\Response\OrderInfoResponseInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\OrderFactory;

class Payment implements PaymentInterface
{
    /**
     * @param \MonedaPay\MonedaPay\Api\Response\PlaceOrderInterface $response
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \MonedaPay\MonedaPay\Model\Payment $monedaPayPayment
     * @param \MonedaPay\MonedaPay\Logger\Logger $logger
     */
    public function __construct(
        private Response                $response,
        private CheckoutSession         $checkoutSession,
        private OrderFactory            $orderFactory,
        private MonedaPayPayment        $monedaPayPayment,
        private Logger                  $logger
    ) {
    }

    /**
     * Place Order
     *
     * @return \MonedaPay\MonedaPay\Api\Response\PlaceOrderInterface
     */
    public function placeOrder(): Response
    {
        $orderId = $this->checkoutSession->getLastRealOrderId();

        try {
            $order = $this->orderFactory->create()->loadByIncrementId($orderId);

            if (!$order->getIncrementId()) {
                $this->response->setStatus(false);

                return $this->response;
            }

            $createOrderLink = $this->monedaPayPayment->getLink($order);

            if (!$createOrderLink) {
                $this->response->setStatus(false);

                return $this->response;
            }

            $this->response->setStatus(true);
            $this->response->setPaymentUrl($createOrderLink);
        } catch (\Exception $exception) {
            $this->logger->critical(
                $exception->getMessage(),
                $exception->getTrace()
            );
            $this->response->setStatus(false);

            return $this->response;
        }

        return $this->response;
    }

    /**
     * Order Info
     *
     * @return \MonedaPay\MonedaPayLib\Model\Response\OrderInfoResponseInterface
     */
    public function orderInfo(): OrderInfoResponseInterface
    {
        return $this->monedaPayPayment->fillOrderInfo();
    }

    /**
     * Update Order Status
     *
     * @return \MonedaPay\MonedaPayLib\Model\Response\AggregatedOrderStatusResponseInterface
     */
    public function updateOrderStatus(): AggregatedOrderStatusResponseInterface
    {
        return $this->monedaPayPayment->getUpdatedStatus();
    }
}
