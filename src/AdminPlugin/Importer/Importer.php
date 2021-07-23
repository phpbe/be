<?php

namespace Be\AdminPlugin\Importer;

use Be\Be;
use Be\AdminPlugin\Form\Item\FormItemFile;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\AdminPluginException;
use Be\AdminPlugin\Driver;

/**
 * 导入器
 *
 * Class Importer
 * @package Be\System\AdminPlugin\Importer
 */
class Importer extends Driver
{

    /**
     *
     * Be::getAdminPlugin('Importer')->setting([
     *      'db' => 'master',
     *      'table' => 'user',
     *      'mapping' => [
     *          'items' => [
     *              [
     *                  'name' => 'username',  // 数据库字段
     *                  'label' => '用户名', // 表头
     *                  'type' => 'string', // 类型： string / number / datetime / date，默认：string
     *                  'required' => true,  // 是否必填
     *                  'check' => function($row) {
     *
     *                  }, // 睚定义校验，校验不通过时可抛异常
     *                  'value' => function($row) {
     *                      return '';
     *                  }, // 格式化
     *              ]
     *          ]
     *      ]
     * ])->execute();
     * @param array $setting
     * @return Driver
     * @throws AdminPluginException
     */
    public function setting($setting = [])
    {
        $request = Be::getRequest();

        if (!isset($setting['form']['action'])) {
            $action = $request->getUrl();
            $action .= (strpos($action, '?') === false ? '?' : '&') . 'task=import';
            $setting['form']['action'] = $action;
        }

        if (!isset($setting['form']['items'])) {
            $setting['form']['items'] = [
                [
                    'name' => 'type',
                    'label' => '文件类型',
                    'driver' => FormItemSelect::class,
                    'required' => true,
                    'keyValues' => [
                        'csv' => 'CSV（.csv）',
                        'excel' => 'Excel（.xls/.xlsx）',
                    ],
                    'value' => 'csv',
                ],
                [
                    'name' => 'file',
                    'label' => '选择文件',
                    'driver' => FormItemFile::class,
                    'path' => Be::getRuntime()->getUploadPath() . '/System/Plugin/Importer/', // 保存咱径
                    'allowUploadFileTypes' => ['.csv', '.xls', '.xlsx'],
                    'required' => true,
                ],
                [
                    'name' => 'charset',
                    'label' => '编码',
                    'driver' => FormItemSelect::class,
                    'required' => true,
                    'keyValues' => [
                        'detect' => '自动识别',
                        'gbk' => 'GBK',
                        'utf-8' => 'UTF-8'
                    ],
                    'value' => 'detect',
                    'ui' => [
                        'form-item' => [
                            'v-if' => 'formData.type == \'csv\'',
                        ]
                    ]
                ],
            ];
        }

        if (!isset($setting['form']['actions'])) {
            $setting['form']['actions'] = [
                'submit' => '导入',
                'cancel' => true,
            ];
        }

        if (!isset($setting['downloadTemplateUrl'])) {
            $downloadTemplateUrl = $request->getUrl();
            if (strpos($downloadTemplateUrl, 'task=import') === false) {
                $downloadTemplateUrl .= (strpos($downloadTemplateUrl, '?') === false ? '?' : '&') . 'task=downloadTemplate';
            } else {
                $downloadTemplateUrl = str_replace('task=import', 'task=downloadTemplate', $downloadTemplateUrl);
            }
            $setting['downloadTemplateUrl'] = $downloadTemplateUrl;
        }

        $setting['form']['actions'][] = [
            'name' => 'download',
            'label' => '下载模板',
            'url' => $setting['downloadTemplateUrl'],
            'target' => 'blank',
            'icon' => 'el-icon-download',
        ];

        if (!isset($setting['mapping']['items'])) {
            throw new AdminPluginException('导入表格（mapping.items）映射项缺失！');
        }

        foreach ($setting['mapping']['items'] as $index => $item) {
            if (!isset($item['name'])) {
                throw new AdminPluginException('第 ' . ($index + 1) . ' 项字段配置错误：未设置列名称（name）！');
            }

            if (!isset($item['label'])) {
                throw new AdminPluginException('第 ' . ($index + 1) . ' 项字段配置错误：未设置列表头（label）！');
            }
        }

        return parent::setting($setting);
    }

    public function display()
    {
        $response = Be::getResponse();

        $title = isset($this->setting['title']) ? $this->setting['title'] : '导入';
        $response->set('title', $title);

        Be::getAdminPlugin('Form')
            ->setting($this->setting)
            ->display();
    }

    public function import()
    {
        $response = Be::getResponse();

        $dbName = 'master';
        if (isset($this->setting['db'])) {
            $dbName = $this->setting['db'];
        }

        $db = Be::getDb($dbName);

        $db->startTransaction();
        try {

            if (!isset($this->setting['table'])) {
                throw new AdminPluginException('未设置要导入的表名！');
            }
            $tableName = $this->setting['table'];

            $batchLimit = $this->setting['batch'] ?? 1000;

            $rows = $this->process();

            $offset = 0;
            $batch = [];
            foreach ($rows as $i => $row) {

                foreach ($this->setting['mapping']['items'] as $item) {
                    // 检查唯一性
                    if (isset($item['unique']) && $item['unique']) {
                        $sql = 'SELECT COUNT(*) FROM ' . $db->quoteKey($tableName) . ' WHERE ' . $db->quoteKey($item['name']) . '=' . $db->quoteValue($row[$item['name']]);
                        if ($db->getValue($sql) > 0) {
                            throw new AdminPluginException('第 ' . ($i + 1) . ' 行的 ' . $item['label'] . ' 已存在值为 ' . $row[$item['name']] . ' 的记录！');
                        }
                    }
                }

                $offset++;
                $batch[] = $row;
                if ($offset >= $batchLimit) {
                    $db->quickInsertMany($tableName, $batch);
                    $offset = 0;
                    $batch = [];
                }
            }

            if ($offset > 0) {
                $db->quickInsertMany($tableName, $batch);
            }

            $db->commit();
            $response->success('导入成功！');
        } catch (\Exception $e) {
            $db->rollback();
            $response->error($e->getMessage());
        }
    }

    public function process()
    {
        $request = Be::getRequest();

        $postData = $request->json();
        $formData = $postData['formData'];

        $type = $formData['type'] ?? 'csv';
        $file = $formData['file'] ?? 'file';
        $charset = $formData['charset'] ?? 'detect';

        $path = Be::getRuntime()->getUploadPath() . '/tmp/' . $file;

        if ($type == 'csv') {

            $delimiter = ','; // 设置字段分界符（只允许一个字符）
            $enclosure = '"'; // 设置字段环绕符（只允许一个字符）
            $escape = '\\'; // 设置转义字符（只允许一个字符），默认是一个反斜杠

            if (isset($this->setting['csv']['delimiter'])) {
                $delimiter = $this->setting['csv']['delimiter'];
            }

            if (isset($this->setting['csv']['enclosure'])) {
                $enclosure = $this->setting['csv']['enclosure'];
            }

            if (isset($this->setting['csv']['escape'])) {
                $escape = $this->setting['csv']['escape'];
            }

            $f = fopen($path, 'r');

            $headers = fgetcsv($f, 0, $delimiter, $enclosure, $escape);

            $headerCount = count($headers);
            if ($headerCount == 0) {
                throw new AdminPluginException('您上传的文件中无数据！');
            }

            if ($charset == 'detect') {
                $charset = mb_detect_encoding(implode(',', $headers), array('GBK', 'GB2312', 'BIG5', 'UTF-8', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1'));
            } else {
                $charset = strtoupper($charset);
            }

            // 表头 - 列索引 映射
            $colMapping = [];
            $col = 0;
            foreach ($headers as &$header) {
                if ($charset != 'UTF-8') {
                    $header = iconv($charset, 'UTF-8//IGNORE', $header);
                }

                $header = str_replace(["\r", "\n", "\t"], '', $header);
                $header = trim($header);

                $colMapping[$header] = $col;
                $col++;
            }

            // 校验表头
            foreach ($this->setting['mapping']['items'] as $index => $item) {
                if (isset($item['value'])) {
                    continue;
                }

                if (!isset($colMapping[$item['label']])) {
                    throw new AdminPluginException('您上传的文件中缺少 ' . $item['label'] . ' 列！');
                }
            }

            $errors = [];

            $row = 2;
            while (!feof($f)) {
                try {

                    $values = fgetcsv($f, 0, $delimiter, $enclosure, $escape);

                    if (!is_array($values)) {
                        //$errors[] = '第 ' . $row . ' 行数据格式异常！';
                        continue;
                    }

                    if (count($values) != $headerCount) {
                        //$errors[] = '第 ' . $row . ' 行数据格式异常！';
                        continue;
                    }

                    foreach ($values as &$v) {
                        if ($charset != 'UTF-8') {
                            $v = iconv($charset, 'UTF-8//IGNORE', $v);
                        }

                        $v = trim($v);
                        $v = trim($v, "'");
                    }

                    $formattedValues = [];
                    foreach ($this->setting['mapping']['items'] as $item) {

                        if (isset($item['value'])) {
                            continue;
                        }

                        $val = $values[$colMapping[$item['label']]];

                        if (isset($item['type'])) {
                            switch ($item['type']) {
                                case 'date':
                                    $val = str_replace('年', '-', $val);
                                    $val = str_replace('月', '-', $val);
                                    $val = str_replace('日', '', $val);
                                    $val = date('Y-m-d', strtotime($val));
                                    break;
                                case 'datetime':
                                    $val = str_replace('年', '-', $val);
                                    $val = str_replace('月', '-', $val);
                                    $val = str_replace('日', '', $val);
                                    $val = date('Y-m-d H:i:s', strtotime($val));
                                    break;
                                case 'number':
                                    $val = str_replace(',', '', $val);
                                    break;
                            }
                        }

                        if (isset($item['required']) && $item['required']) {
                            if (!$val) {
                                throw new AdminPluginException('列 ' . $item['label'] . ' 不能为空！');
                            }
                        }

                        $formattedValues[$item['name']] = $val;
                    }

                    foreach ($this->setting['mapping']['items'] as $item) {
                        if (isset($item['check']) && $item['check'] instanceof \Closure) {
                            $fn = $item['check'];
                            $fn($formattedValues);
                        }

                        if (isset($item['value'])) {
                            if ($item['value'] instanceof \Closure) {
                                $fn = $item['value'];
                                $formattedValues[$item['name']] = $fn($formattedValues);
                            } else {
                                $formattedValues[$item['name']] = $item['value'];
                            }
                        }
                    }

                    if (isset($this->setting['mapping']['format']) && $this->setting['mapping']['format'] instanceof \Closure) {
                        $fn = $this->setting['mapping']['format'];
                        $formattedValues = $fn($formattedValues);
                    }

                    yield $formattedValues;

                } catch (AdminPluginException $e) {
                    $errors[] = '第' . $row . '行：' . $e->getMessage();
                }

                $row++;
            }
            fclose($f);

            if (count($errors) > 0) {
                throw new AdminPluginException('有' . count($errors) . '条数据有问题！');
            }

        } elseif ($type == 'excel') {

            $reader = null;
            $ext = strtolower(strrchr($file, '.'));
            if ($ext == '.xlsx') {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            } elseif ($ext == '.xls') {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
            }

            if (!$reader || !$reader->canRead($path)) {
                throw new AdminPluginException('上传的文件不是有效的Excel文件！');
            }

            $excel = $reader->load($path);
            $sheet = $excel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            if ($highestRow < 2) {
                throw new AdminPluginException('您上传的文件中无数据！');
            }

            $colMapping = [];
            for ($col = 0; $col <= $highestColumnIndex; $col++) {
                $header = (string)$sheet->getCellByColumnAndRow($col, 1)->getValue();
                $header = trim($header);
                $colMapping[$header] = $col;
            }

            // 校验表头
            foreach ($this->setting['mapping']['items'] as $index => $item) {
                if (isset($item['value'])) {
                    continue;
                }

                if (!isset($colMapping[$item['label']])) {
                    throw new AdminPluginException('您上传的文件中缺少 ' . $item['label'] . ' 列！');
                }
            }

            $errors = [];
            for ($row = 2; $row <= $highestRow; $row++) {

                try {
                    $values = [];
                    foreach ($this->setting['mapping']['items'] as $item) {

                        if (isset($item['value'])) {
                            continue;
                        }

                        $val = (string)$sheet->getCellByColumnAndRow($colMapping[$item['label']], $row)->getValue();

                        if (isset($item['type'])) {
                            switch ($item['type']) {
                                case 'date':
                                    if (is_numeric($val)) {
                                        $val = gmdate('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($val));
                                    } else {
                                        $val = str_replace('年', '-', $val);
                                        $val = str_replace('月', '-', $val);
                                        $val = str_replace('日', '', $val);
                                        $val = date('Y-m-d', strtotime($val));
                                    }
                                    break;
                                case 'datetime':
                                    if (is_numeric($val)) {
                                        $val = gmdate('Y-m-d H:i:s', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($val));
                                    } else {
                                        $val = str_replace('年', '-', $val);
                                        $val = str_replace('月', '-', $val);
                                        $val = str_replace('日', '', $val);
                                        $val = date('Y-m-d H:i:s', strtotime($val));
                                    }
                                    break;
                                case 'number':
                                    $val = str_replace(',', '', $val);
                                    break;
                            }
                        }

                        if (isset($item['required']) && $item['required']) {
                            if (!$val) {
                                throw new AdminPluginException('列 ' . $item['label'] . ' 不能为空！');
                            }
                        }

                        $values[$item['name']] = $val;
                    }

                    foreach ($this->setting['mapping']['items'] as $item) {
                        if (isset($item['check']) && $item['check'] instanceof \Closure) {
                            $fn = $item['check'];
                            $fn($values);
                        }

                        if (isset($item['value'])) {
                            if ($item['value'] instanceof \Closure) {
                                $fn = $item['value'];
                                $values[$item['name']] = $fn($values);
                            } else {
                                $values[$item['name']] = $item['value'];
                            }
                        }
                    }

                    if (isset($this->setting['mapping']['format']) && $this->setting['mapping']['format'] instanceof \Closure) {
                        $fn = $this->setting['mapping']['format'];
                        $values = $fn($values);
                    }

                    yield $values;

                } catch (AdminPluginException $e) {
                    $errors[] = '第' . $row . '行：' . $e->getMessage();
                }
            }

            if (count($errors) > 0) {
                throw new AdminPluginException('有' . count($errors) . '条数据有问题：' . "\n" . implode("\n", $errors));
            }

        } else {
            throw new AdminPluginException('不支持的文件类型：' . $type . '！');
        }
    }


    public function downloadTemplate()
    {
        $request = Be::getRequest();

        $postData = $request->post('data', '', '');
        $postData = json_decode($postData, true);
        $formData = $postData['formData'];

        $type = $formData['type'] ?? 'csv';
        $file = $formData['file'] ?? 'file';
        $charset = $formData['charset'] ?? 'detect';

        if ($charset == 'detect') {
            $charset = 'gbk';
        }
        $charset = strtoupper($charset);

        $exporter = Be::getAdminPlugin('Exporter');
        if ($type == 'csv' || $type == 'excel') {

            $filename = isset($this->setting['title']) ? $this->setting['title'] : '导入模板';
            $filename .= ($type == 'csv' ? '.csv' : '.xls');

            $exporter->setDriver($type)
                ->setCharset($charset)
                ->setOutput('http', $filename);

            $headers = [];
            foreach ($this->setting['mapping']['items'] as $item) {
                $headers[] = $item['label'];
            }
            $exporter->setHeaders($headers);

            $exporter->end();

        } else {
            throw new AdminPluginException('不支持的文件类型：' . $type . '！');
        }
    }

}