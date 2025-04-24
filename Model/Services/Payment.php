<?php
/**
 * Created by Qoliber
 *
 * @category    Ari10
 * @package     Ari10_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Ari10\MonedaPay\Model\Services;

use Ari10\MonedaPay\Api\PaymentInterface;
use Ari10\MonedaPay\Api\Response\PlaceOrderInterface as Response;
use Ari10\MonedaPay\Logger\Logger;
use Ari10\MonedaPay\Model\Payment as MonedaPayPayment;
use Ari10\MonedaPayLib\Model\Response\AggregatedOrderStatusResponseInterface;
use Ari10\MonedaPayLib\Model\Response\OrderInfoResponseInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\OrderFactory;

class Payment implements PaymentInterface
{
    /**
     * @param \Ari10\MonedaPay\Api\Response\PlaceOrderInterface $response
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Ari10\MonedaPay\Model\Payment $monedaPayPayment
     * @param \Ari10\MonedaPay\Logger\Logger $logger
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
     * @return \Ari10\MonedaPay\Api\Response\PlaceOrderInterface
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
     * @return \Ari10\MonedaPayLib\Model\Response\OrderInfoResponseInterface
     */
    public function orderInfo(): OrderInfoResponseInterface
    {
        return $this->monedaPayPayment->fillOrderInfo();
    }

    /**
     * Update Order Status
     *
     * @return \Ari10\MonedaPayLib\Model\Response\AggregatedOrderStatusResponseInterface
     */
    public function updateOrderStatus(): AggregatedOrderStatusResponseInterface
    {
        return $this->monedaPayPayment->getUpdatedStatus();
    }
}
