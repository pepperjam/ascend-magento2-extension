<?php
namespace Pepperjam\Network\Model\ResourceModel\Network;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    function _construct()
    {
        $this->_init('Pepperjam\Network\Model\Network', 'Pepperjam\Network\Model\ResourceModel\Network');
    }
}
