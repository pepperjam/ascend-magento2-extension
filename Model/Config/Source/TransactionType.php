<?php
namespace Pepperjam\Network\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

use Pepperjam\Network\Helper\Data;

class TransactionType implements OptionSourceInterface
{
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function toOptionArray()
    {
        return [
            [
                'value' => Data::TRANSACTION_LEAD,
                'label' => __('Lead'),
            ],
            [
                'value' => Data::TRANSACTION_SALE,
                'label' => __('Sale'),
            ],
        ];
    }

    public function toArray()
    {
        return [
            Data::TRANSACTION_LEAD => __('Lead'),
            Data::TRANSACTION_SALE => __('Sale'),
        ];
    }
}
