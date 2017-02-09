<?php
namespace Pepperjam\Network\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Network extends AbstractDb
{
    function _construct()
    {
        $this->_init('pepperjam_network', 'id');
    }
}
