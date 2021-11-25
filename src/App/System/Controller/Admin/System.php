<?php
namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * Class System
 * @package Be\App\System\Controller
 * @BePermissionGroup("*")
 */
class System extends Auth
{

    /**
     * @throws \Be\Runtime\RuntimeException
     */
    public function historyBack()
    {
        $libHistory = Be::getLib('History');
        $libHistory->back();
    }

}