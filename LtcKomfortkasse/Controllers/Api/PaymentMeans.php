<?php

/**
 * Class Shopware_Controllers_Api_PaymentMeans
 */
class Shopware_Controllers_Api_PaymentMeans extends Shopware_Controllers_Api_Rest
{
    public function indexAction()
    {
        $sql = 'select * from s_core_paymentmeans';
        $data = Shopware()->Db()->fetchAll($sql);
        $this->View()->assign(['success' => true, 'data' => $data]);
    }

}