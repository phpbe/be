<?php

namespace Be\AdminPlugin\Form;

use Be\Be;
use Be\AdminPlugin\Driver;

/**
 * 表单
 *
 * Class Form
 * @package Be\System\AdminPlugin\Form
 */
class Form extends Driver
{
    protected array $row = [];

    public function setting(array $setting = []): Driver
    {
        if (!isset($setting['theme'])) {
            $setting['theme'] = 'Blank';
        }

        if (!isset($setting['form']['action'])) {
            $setting['form']['action'] = Be::getRequest()->getUrl();
        }

        if (!isset($setting['form']['actions'])) {
            $setting['form']['actions'] = [
                'submit' => true,
                'reset' => true,
                'cancel' => true,
            ];
        }

        return parent::setting($setting);
    }

    public function setValue($row): Driver
    {
        if (is_object($row)) {
            $row = get_object_vars($row);
        }

        $this->row = $row;

        foreach ($this->setting['form']['items'] as &$item) {
            if (isset($item['value'])) {
                $value = $item['value'];
                if ($value instanceof \Closure) {
                    $item['value'] = $value($row);
                }
            } else {
                if (isset($item['name'])) {
                    $name = $item['name'];
                    if (isset($row[$name])) {
                        $item['value'] = (string)$row[$name];
                    }
                }
            }
        }
        unset($item);

        return $this;
    }


    public function display()
    {
        
        Resonse::set('setting', $this->setting);
        Resonse::set('row', $this->row);
        Resonse::set('title', $this->setting['title'] ?? '');
        Resonse::display('AdminPlugin.Form.display', $this->setting['theme']);
    }

}