<?php

namespace Be\App\System\Controller;

use Be\Be;

class Home
{

    /**
     * 首页
     *
     * @BeMenu("系统首页")
     * @BeRoute ("/system/home")
     */
    public function index()
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
