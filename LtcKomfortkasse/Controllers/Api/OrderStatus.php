<?php

/**
 * Class Shopware_Controllers_Api_OrderStatus
 */
class Shopware_Controllers_Api_OrderStatus extends Shopware_Controllers_Api_Rest
{
    public function indexAction()
    {
        $sql = 'select * from s_core_states where `group`=\'state\'';
        $data = Shopware()->Db()->fetchAll($sql);
        $this->View()->assign(['success' => true, 'data' => $data]);
    }

}