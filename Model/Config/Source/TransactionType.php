<?php
namespace Pepperjam\Network\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

use Pepperjam\Network\Helper\Data;

class TransactionType implements OptionSourceInterface
{
    protected $helper;

    public function __construct (Data $helper)
    {
        $this->helper = $helper;
    }

    public function toOptionArray()
    {
        return [
            [
                'value' => $this->helper::TRANSACTION_LEAD,
                'label' => __('Lead'),
            ],
            [
                'value' => $this->helper::TRANSACTION_SALE,
                'label' => __('Sale'),
            ],
        ];
    }

    public function toArray()
    {
        return [
            $this->helper::TRANSACTION_LEAD => __('Lead'),
            $this->helper::TRANSACTION_SALE => __('Sale'),
        ];
    }
}
