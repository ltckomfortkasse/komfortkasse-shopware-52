<?php

/**
 * Class Shopware_Controllers_Api_LtcKomfortkasseVersion
 */
class Shopware_Controllers_Api_LtcKomfortkasseVersion extends Shopware_Controllers_Api_Rest
{
    public function indexAction()
    {
        $data['version'] = '1.3.0';
        $this->View()->assign(['success' => true, 'data' => $data]);
    }

}