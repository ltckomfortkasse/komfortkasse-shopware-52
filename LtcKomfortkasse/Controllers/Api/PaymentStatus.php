<?php

/**
 * Class Shopware_Controllers_Api_PaymentStatus
 */
class Shopware_Controllers_Api_PaymentStatus extends Shopware_Controllers_Api_Rest
{
    public function indexAction()
    {
        $sql = 'select * from s_core_states where `group`=\'payment\'';
        $data = Shopware()->Db()->fetchAll($sql);
        $this->View()->assign(['success' => true, 'data' => $data]);
    }

}