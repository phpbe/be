<?php

namespace Be\App\System\Controller;

use Be\Be;

class User
{

    /**
     * 首页
     *
     * @BeMenu("系统首页")
     * @BeRoute ("/system/user/login")
     */
    public function login()
    {
        
        

        $pageConfig = Resonse::getPageConfig();
        Resonse::set('pageConfig', $pageConfig);

        Resonse::set('title', $pageConfig->title ?: '');
        Resonse::set('metaDescription', $pageConfig->metaDescription ?: '');
        Resonse::set('metaKeywords', $pageConfig->metaKeywords ?: '');
        Resonse::set('pageTitle', $pageConfig->pageTitle ?: ($pageConfig->title ?: ''));

        Resonse::display();
    }


}
