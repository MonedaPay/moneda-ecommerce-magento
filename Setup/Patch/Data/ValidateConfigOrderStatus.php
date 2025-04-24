<?php
/**
 * Created by Qoliber
 *
 * @category    Ari10
 * @package     Ari10_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Ari10\MonedaPay\Setup\Patch\Data;

use Ari10\MonedaPay\Model\Config;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\State;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;
use Magento\Sales\Model\Config\Source\Order\Status\NewStatus;

class ValidateConfigOrderStatus implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @param \Ari10\MonedaPay\Model\Config $config
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writer
     * @param \Magento\Sales\Model\Config\Source\Order\Status\NewStatus $orderStatus
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        private readonly Config $config,
        private readonly WriterInterface $writer,
        private readonly NewStatus $orderStatus,
        private readonly State $state
    ) {
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return $this|\Ari10\MonedaPay\Setup\Patch\Data\ValidateConfigOrderStatus
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function apply(): ValidateConfigOrderStatus|static
    {
        $configDefault = $this->config->getOrderStatus();
        $availableStatuses = [];
        $this->state->emulateAreaCode(
            Area::AREA_FRONTEND,
            function () use (&$availableStatuses) {
                $availableStatuses = array_column(
                    $this->orderStatus->toOptionArray(),
                    'value'
                );
            }
        );

        if (!in_array($configDefault, $availableStatuses)) {
            $this->writer->save(
                Config::XML_PATH_MONEDA_PAY . 'order_status',
                ''
            );
        }

        return $this;
    }
}
