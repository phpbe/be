<?php

namespace Be\AdminPlugin\Detail;

use Be\Be;
use Be\AdminPlugin\Driver;

/**
 * 明细
 *
 * Class Detail
 * @package Be\System\AdminPlugin\Detail
 */
class Detail extends Driver
{

    protected $row = [];

    public function setting($setting = [])
    {
        if (!isset($setting['title'])) {
            $setting['title'] = '查看明细';
        }

        if (!isset($setting['theme'])) {
            $setting['theme'] = 'Blank';
        }

        if (!isset($setting['form']['actions'])) {
            $setting['form']['actions'] = [
                'cancel' => true,
            ];
        }

        return parent::setting($setting);
    }


    public function setValue($row)
    {
        if (is_object($row)) {
            $row = get_object_vars($row);
        }

        $this->row = $row;

        foreach ($this->setting['form']['items'] as &$item) {
            $itemName = $item['name'];
            $itemValue = '';
            if (isset($item['value'])) {
                $value = $item['value'];
                if ($value instanceof \Closure) {
                    $itemValue = $value($row);
                } else {
                    $itemValue = $value;
                }
            } else {
                if (isset($row[$itemName])) {
                    $itemValue = $row[$itemName];
                }
            }

            $item['value'] = $itemValue;
        }
        unset($item);

        return $this;
    }


    public function display()
    {
        $response = Be::getResponse();

        $response->set('title', $this->setting['title']);
        $response->set('setting', $this->setting);
        $response->set('row', $this->row);
        $response->display('AdminPlugin.Detail.display', $this->setting['theme']);
    }

}

