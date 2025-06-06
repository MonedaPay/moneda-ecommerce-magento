<?php
/**
 * Created by Qoliber
 *
 * @category    MonedaPay
 * @package     MonedaPay_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types=1);

namespace MonedaPay\MonedaPay\Model;

use MonedaPay\MonedaPay\Logger\Logger;
use MonedaPay\MonedaPay\Model\Methods\MonedaPay;
use MonedaPay\MonedaPayLib\Enum\AggregatedOrderStatus;
use MonedaPay\MonedaPayLib\Exception\OrderNotFoundException;
use MonedaPay\MonedaPayLib\Model\ArrayableInterface;
use MonedaPay\MonedaPayLib\Model\DataProvider\BasicDataProvider;
use MonedaPay\MonedaPayLib\Model\Request\CreatePaymentRequest;
use MonedaPay\MonedaPayLib\Model\Request\CreatePaymentRequestInterface;
use MonedaPay\MonedaPayLib\Model\Response\AggregatedOrderStatusResponse;
use MonedaPay\MonedaPayLib\Model\Response\OrderInfoResponse;
use MonedaPay\MonedaPayLib\Service\Client;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\OrderRepository;

class Payment
{
    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Api\OrderPaymentRepositoryInterface $paymentRepository
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \MonedaPay\MonedaPay\Logger\Logger $logger
     * @param \MonedaPay\MonedaPayLib\Service\Client $client
     * @param \MonedaPay\MonedaPay\Model\Config $config
     */
    public function __construct(
        private readonly UrlInterface                    $urlBuilder,
        private readonly OrderManagementInterface        $orderManagement,
        private readonly OrderPaymentRepositoryInterface $paymentRepository,
        private readonly OrderRepository                 $orderRepository,
        private readonly OrderFactory                    $orderFactory,
        private readonly Logger                          $logger,
        private readonly Client                          $client,
        private readonly Config $config
    ) {
    }

    /**
     * Get Link
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string|null
     */
    public function getLink(OrderInterface $order): ?string
    {
        try {
            $request = new CreatePaymentRequest();
            $payment = $order->getPayment();
            $payment->setAdditionalInformation(
                Client::HMAC_REQUEST_KEY,
                $this->client->getEncryption()->generate(
                    (string)$order->getIncrementId()
                )
            );
            $this->paymentRepository->save($payment);
            $this->setOrderParams($order, $request);

            $link = $this->client->createPaymentLink($request);
            $this->logInfo($link);

            return $link;
        } catch (\Exception $exception) {
            $this->logger->critical(
                $exception->getMessage(),
                $exception->getTrace()
            );

            return null;
        }
    }

    /**
     * Set Order Params
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \MonedaPay\MonedaPayLib\Model\Request\CreatePaymentRequestInterface $request
     * @return void
     */
    protected function setOrderParams(
        OrderInterface                $order,
        CreatePaymentRequestInterface &$request
    ): void
    {
        $callbackUrl = $this->urlBuilder->getUrl(
            'moneda_pay/payment/status'
        );
        $cancelUrl = $this->urlBuilder->getUrl(
            'moneda_pay/payment/cancelOrder'
        );
        $request->setCancelUrl($cancelUrl);
        $request->setCallbackUrl($callbackUrl);
        $request->setMerchantOrderId((string)$order->getIncrementId());
    }

    /**
     * Log Info
     *
     * @param $data
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function logInfo($data): void
    {
        if (!$this->config->isDebugMode()) {
            return;
        }

        if (is_object($data) && is_a($data, ArrayableInterface::class)) {
            $class = get_class($data);
            $data = $data->toArray();
            $data = json_encode([$class => $data], JSON_PRETTY_PRINT);

        }

        $this->logger->info($data);
    }

    /**
     * Fill Order Info
     *
     * @return \MonedaPay\MonedaPayLib\Model\Response\OrderInfoResponse
     */
    public function fillOrderInfo(): OrderInfoResponse
    {
        $response = new OrderInfoResponse();

        try {
            $dataProvider = new BasicDataProvider();
            $dataProvider->setDataCallback(
                function (&$response) {
                    /** @var OrderInfoResponse $response */
                    $order = $this->orderFactory->create()->loadByIncrementId(
                        $response->getMerchantOrderId()
                    );

                    if (!$this->isMonedaPayPayment($order)) {
                        return;
                    }

                    $address = $order->getBillingAddress();
                    $response->setFromCurrency(
                        (string)$order->getOrderCurrencyCode()
                    );
                    $response->setFromAmount((string)$order->getGrandTotal());
                    $response->setLastName($address->getLastname());
                    $response->setFirstName($address->getFirstname());
                    $response->setEmail($address->getEmail());
                    $response->setMerchantCustomerId(
                        (string)$order->getCustomerId()
                    );
                }
            );
            $response->setDataProvider($dataProvider);

            $this->client->createOrderInfoRequest($response);
            $this->logInfo($response);
        } catch (\Exception $exception) {
            $this->logger->critical(
                $exception->getMessage(),
                $exception->getTrace()
            );
            throw $exception;
        }

        return $response;
    }

    /**
     * Is Moneda Payment
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return bool
     * @throws \MonedaPay\MonedaPayLib\Exception\OrderNotFoundException
     */
    public function isMonedaPayPayment(OrderInterface $order): bool
    {
        $payment = $order->getPayment();

        if ($payment && $payment->getMethod() === MonedaPay::CODE) {
            return true;
        }

        throw new OrderNotFoundException();
    }

    /**
     * Get Updated Status
     *
     * @return \MonedaPay\MonedaPayLib\Model\Response\AggregatedOrderStatusResponse
     */
    public function getUpdatedStatus(): AggregatedOrderStatusResponse
    {
        $response = new AggregatedOrderStatusResponse();
        try {
            $result = $this->client->createStatusUpdate($response);

            $this->logInfo($result);

            $order = $this->orderFactory->create()->loadByIncrementId(
                $response->getOrderId()
            );

            if (!$this->isMonedaPayPayment($order)) {
                return $response;
            }

            $aggregatedStatus = $response->getAggregatedStatus();

            if (empty($aggregatedStatus)) {
                return $response;
            }

            $aggregatedStatus = AggregatedOrderStatus::from($aggregatedStatus);

            $orderConfig = $order->getConfig();
            $newState = Order::STATE_HOLDED;

            if (in_array(
                $aggregatedStatus,
                [
                    AggregatedOrderStatus::IN_PROGRESS,
                    AggregatedOrderStatus::CREATED,
                ]
            )) {
                $newState = Order::STATE_PENDING_PAYMENT;
            } elseif (in_array(
                $aggregatedStatus,
                [
                    AggregatedOrderStatus::UNDERPAID,
                    AggregatedOrderStatus::OVERPAID,
                    AggregatedOrderStatus::FAILURE,
                ]
            )) {
                $newState = Order::STATE_PAYMENT_REVIEW;
            } elseif ($aggregatedStatus === AggregatedOrderStatus::SUCCESS) {
                $newState = Order::STATE_PROCESSING;
            } elseif ($aggregatedStatus === AggregatedOrderStatus::CANCELLED) {
                $order->setState(Order::STATE_CANCELED);
                $order->setStatus($orderConfig->getStateDefaultStatus(Order::STATE_CANCELED));
                $this->orderRepository->save($order);
                $this->orderManagement->cancel($order->getId());

                return $response;
            }

            $order->setState($newState);
            $order->setStatus($orderConfig->getStateDefaultStatus($newState));
            $this->orderRepository->save($order);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
            throw $exception;
        }

        return $response;
    }
}
