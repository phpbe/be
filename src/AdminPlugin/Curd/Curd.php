<?php

namespace Be\AdminPlugin\Curd;

use Be\Db\Table;
use Be\Be;
use Be\AdminPlugin\Detail\Item\DetailItemAvatar;
use Be\AdminPlugin\Detail\Item\DetailItemCustom;
use Be\AdminPlugin\Detail\Item\DetailItemImage;
use Be\AdminPlugin\Detail\Item\DetailItemProgress;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Detail\Item\DetailItemText;
use Be\AdminPlugin\Form\Item\FormItemDatePickerMonthRange;
use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\AdminPlugin\Form\Item\FormItemHidden;
use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemTimePickerRange;
use Be\AdminPlugin\Table\Item\TableItemAvatar;
use Be\AdminPlugin\Table\Item\TableItemCustom;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\AdminPlugin\Table\Item\TableItemProgress;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\AdminPlugin\AdminPluginException;
use Be\AdminPlugin\Driver;

/**
 * 增删改查
 *
 * Class Curd
 * @package Be\Mf\Plugin
 */
class Curd extends Driver
{

    /**
     * 配置项
     *
     * @param array $setting
     * @return Driver
     */
    public function setting(array $setting = []): Driver
    {
        if (!isset($setting['db'])) {
            $setting['db'] = 'master';
        }

        $this->setting = $setting;
        return $this;
    }

    /**
     * 执行指定任务
     *
     * @param string $task
     */
    public function execute($task = null)
    {
        if ($task === null) {
            $task = Be::getRequest()->get('task', 'Grid');
        }

        if (method_exists($this, $task)) {
            $this->$task();
        }
    }

    /**
     * 列表展示
     *
     */
    public function Grid()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            try {
                $postData = $request->json();
                $formData = $postData['formData'];
                $table = $this->getTable($formData);

                $total = $table->count();

                $orderBy = isset($postData['orderBy']) ? $postData['orderBy'] : '';
                if ($orderBy) {
                    $orderByDir = isset($postData['orderByDir']) ? strtoupper($postData['orderByDir']) : '';
                    if (!in_array($orderByDir, ['ASC', 'DESC'])) {
                        $orderByDir = 'DESC';
                    }

                    $table->orderBy($orderBy, $orderByDir);
                } else {
                    if (isset($this->setting['grid']['orderBy'])) {
                        $orderBy = $this->setting['grid']['orderBy'];
                        if (isset($this->setting['grid']['orderByDir'])) {
                            $orderByDir = $this->setting['grid']['orderByDir'];
                            $table->orderBy($orderBy, $orderByDir);
                        } else {
                            $table->orderBy($orderBy);
                        }
                    }
                }

                $actualLayout = $postData['actualLayout'] ?? 'table';
                $page = $postData['page'];
                $pageSize = $postData['pageSize'];
                $table->offset(($page - 1) * $pageSize)->limit($pageSize);

                $rows = $table->getArrays();

                $formattedRows = [];
                foreach ($rows as &$row) {
                    $formattedRow = [];

                    foreach ($this->setting['grid'][$actualLayout]['items'] as $item) {
                        if (!isset($item['name'])) {
                            continue;
                        }
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

                        if (isset($item['keyValues'])) {
                            $keyValues = $item['keyValues'];
                            if ($keyValues instanceof \Closure) {
                                $itemValue = $keyValues($itemValue);
                            } else {
                                if (isset($keyValues[$itemValue])) {
                                    $itemValue = $keyValues[$itemValue];
                                } else {
                                    $itemValue = '';
                                }
                            }
                        }

                        $row[$itemName] = $itemValue;
                        $formattedRow[$itemName] = $itemValue;
                    }

                    foreach ($row as $k => $v) {
                        if (isset($formattedRow[$k])) {
                            continue;
                        }

                        if (isset($this->setting['grid'][$actualLayout]['exclude']) &&
                            is_array($this->setting['grid'][$actualLayout]['exclude']) &&
                            in_array($k, $this->setting['grid'][$actualLayout]['exclude'])
                        ) {
                            continue;
                        }

                        $formattedRow[$k] = $v;
                    }
                    $formattedRows[] = $formattedRow;
                }
                unset($row);

                $responseData = [
                    'total' => $total,
                    'gridData' => $formattedRows,
                ];

                if (isset($this->setting['grid']['tab']['counter']) && $this->setting['grid']['tab']['counter']) {
                    $responseData['tabCounters'] = $this->getTabCounters();
                }

                $response->set('success', true);
                $response->set('data', $responseData);
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
                Be::getLog()->error($t);
            }

        } else {
            $setting = $this->setting['grid'];

            Be::getAdminPlugin('Grid')
                ->setting($setting)
                ->display();

            $response->createHistory();
        }

    }

    /*
     * 导入
     *
     */
    public function import()
    {
        $request = Be::getRequest();

        $importer = Be::getAdminPlugin('Importer');

        $setting = $this->setting['import'] ?? [];

        $setting['db'] = $this->setting['db'];
        $setting['table'] = $this->setting['table'];

        if (!isset($setting['grid']['title']) && isset($this->setting['grid']['title'])) {
            $setting['title'] = $this->setting['grid']['title'] . ' - 导入';
        }

        if (!isset($setting['grid']['theme']) && isset($this->setting['grid']['theme'])) {
            $setting['theme'] = $this->setting['grid']['theme'];
        }

        if (!isset($setting['mapping']['items'])) {
            $mappingItems = [];
            foreach ($this->setting['grid']['table']['items'] as $item) {
                $mappingItems[] = [
                    'name' => $item['name'],
                    'label' => $item['label'],
                ];
            }
            $setting['mapping']['items'] = $mappingItems;
        }

        if (isset($this->setting['import']['events'])) {
            $setting['events'] = $this->setting['import']['events'];
        }

        if (!isset($setting['downloadTemplateUrl'])) {
            $downloadTemplateUrl = $request->getUrl();
            if (strpos($downloadTemplateUrl, 'task=import') === false) {
                $downloadTemplateUrl .= (strpos($downloadTemplateUrl, '?') === false ? '?' : '&') . 'task=importDownloadTemplate';
            } else {
                $downloadTemplateUrl = str_replace('task=import', 'task=importDownloadTemplate', $downloadTemplateUrl);
            }
            $setting['downloadTemplateUrl'] = $downloadTemplateUrl;
        }

        if ($request->isAjax()) {
            $importer->setting($setting)->import();
        } else {
            $importer->setting($setting)->display();
        }
    }

    /**
     * 导入 - 下载模板
     */
    public function importDownloadTemplate()
    {
        $importer = Be::getAdminPlugin('Importer');

        $setting = $this->setting['import'] ?? [];

        if (!isset($setting['grid']['title']) && isset($this->setting['grid']['title'])) {
            $setting['title'] = $this->setting['grid']['title'] . ' - 导入模板';
        }

        if (!isset($setting['grid']['theme']) && isset($this->setting['grid']['theme'])) {
            $setting['theme'] = $this->setting['grid']['theme'];
        }

        if (!isset($setting['mapping']['items'])) {
            $mappingItems = [];
            foreach ($this->setting['grid']['table']['items'] as $item) {
                $mappingItems[] = [
                    'name' => $item['name'],
                    'label' => $item['label'],
                ];
            }
            $setting['mapping']['items'] = $mappingItems;
        }

        $importer->setting($setting)->downloadTemplate();
    }

    /*
     * 导出
     *
     */
    public function export()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->post('data', '', '');
            $postData = json_decode($postData, true);
            $formData = $postData['formData'];

            $table = $this->getTable($formData);
            $rows = $table->getYieldArrays();

            $exporter = Be::getAdminPlugin('Exporter');

            $exportDriver = isset($postData['postData']['driver']) ? $postData['postData']['driver'] : 'csv';

            $filename = null;
            if (isset($this->setting['export']['title'])) {
                $filename = $this->setting['export']['title'];
            } elseif (isset($this->setting['grid']['title'])) {
                $filename = $this->setting['grid']['title'];
            }
            $filename .= '（' . date('YmdHis') . '）';
            $filename .= ($exportDriver === 'csv' ? '.csv' : '.xls');

            $exporter->setDriver($exportDriver)->setOutput('http', $filename);

            $fields = null;
            if (isset($this->setting['export']['items'])) {
                $fields = $this->setting['export']['items'];
            } else {
                $fields = $this->setting['grid']['table']['items'];
            }

            $headers = [];
            foreach ($fields as $item) {
                if (!isset($item['label'])) {
                    continue;
                }
                $driver = null;
                if (isset($item['driver'])) {
                    $driverName = $item['driver'];
                    $driver = new $driverName($item);
                } else {
                    $driver = new \Be\AdminPlugin\Table\Item\TableItemText($item);
                }

                $headers[] = $driver->label;
            }
            $exporter->setHeaders($headers);

            foreach ($rows as $row) {
                $formattedRow = [];

                foreach ($fields as $item) {
                    if (!isset($item['label'])) {
                        continue;
                    }

                    $itemName = $item['name'];
                    $itemValue = '';
                    if (isset($item['exportValue'])) {
                        $value = $item['exportValue'];
                        if ($value instanceof \Closure) {
                            $itemValue = $value($row);
                        } else {
                            $itemValue = $value;
                        }
                    } else {
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

                        if (isset($item['keyValues'])) {
                            $keyValues = $item['keyValues'];
                            if ($keyValues instanceof \Closure) {
                                $itemValue = $keyValues($itemValue);
                            } else {
                                if (isset($keyValues[$itemValue])) {
                                    $itemValue = $keyValues[$itemValue];
                                } else {
                                    $itemValue = '';
                                }
                            }
                        }
                    }

                    $formattedRow[$itemName] = $itemValue;
                }

                $exporter->addRow($formattedRow);
            }

            $exporter->end();

            $content = null;
            if (isset($this->setting['export']['title'])) {
                $content = $this->setting['export']['title'] . '（' . $exportDriver . '）';
            } elseif (isset($this->setting['grid']['title'])) {
                $content = '导出 ' . $this->setting['grid']['title'] . '（' . $exportDriver . '）';
            } else {
                $content = '导出 ' . $exportDriver;
            }

            if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                beAdminOpLog($content, $formData);
            }

        } catch (\Throwable $t) {
            $response->error($t->getMessage());
            Be::getLog()->error($t);
        }
    }

    /**
     * 获取Tab项计数
     *
     * @return array
     */
    private function getTabCounters(): array
    {
        $counters = [];
        if (isset($this->setting['grid']['tab'])) {
            $db = Be::getDb($this->setting['db']);
            $driver = new \Be\AdminPlugin\Tab\Driver($this->setting['grid']['tab']);
            foreach ($driver->keyValues as $key => $val) {
                $key = (string)$key;

                $table = Be::getTable($this->setting['table'], $this->setting['db']);

                if (isset($this->setting['grid']['filter']) && count($this->setting['grid']['filter']) > 0) {
                    foreach ($this->setting['grid']['filter'] as $filter) {
                        $table->where($filter);
                    }
                }

                if ($key !== $driver->nullValue) {
                    if (isset($this->setting['grid']['tab']['buildSql']) && $this->setting['grid']['tab']['buildSql'] instanceof \Closure) {
                        $buildSql = $this->setting['grid']['tab']['buildSql'];
                        $sql = $buildSql($this->setting['db'], [$driver->name => $key]);
                        if ($sql) {
                            if (isset($sql[0]) && is_array($sql[0])) {
                                $table->condition($sql);
                            } else {
                                $table->where($sql);
                            }
                        }
                    } else {
                        $sql = $db->quoteKey($driver->name) . ' = ' . $db->quoteValue($key);
                        $table->where($sql);
                    }
                }

                $counters[$key] = $table->count();
            }
        }
        return $counters;
    }

    /**
     * 获取Table
     *
     * @param array $formData 查询条件
     * @return Table
     */
    private function getTable($formData)
    {
        $db = Be::getDb($this->setting['db']);
        $table = Be::getTable($this->setting['table'], $this->setting['db']);

        if (isset($this->setting['grid']['filter']) && count($this->setting['grid']['filter']) > 0) {
            foreach ($this->setting['grid']['filter'] as $filter) {
                $table->where($filter);
            }
        }

        if (isset($this->setting['grid']['tab'])) {
            if (isset($this->setting['grid']['tab']['buildSql']) && $this->setting['grid']['tab']['buildSql'] instanceof \Closure) {
                $buildSql = $this->setting['grid']['tab']['buildSql'];
                $sql = $buildSql($this->setting['db'], $formData);
                if ($sql) {
                    if (isset($sql[0]) && is_array($sql[0])) {
                        $table->condition($sql);
                    } else {
                        $table->where($sql);
                    }
                }
            } else {
                $driver = new \Be\AdminPlugin\Tab\Driver($this->setting['grid']['tab']);
                $driver->submit($formData);
                if ($driver->newValue !== $driver->nullValue) {
                    $sql = $db->quoteKey($driver->name) . ' = ' . $db->quoteValue($driver->newValue);
                    $table->where($sql);
                }
            }
        }

        if (isset($this->setting['grid']['form']['items']) && count($this->setting['grid']['form']['items']) > 0) {
            foreach ($this->setting['grid']['form']['items'] as $item) {

                $driverClass = null;
                if (isset($item['driver'])) {
                    if (substr($item['driver'], 0, 8) === 'FormItem') {
                        $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $item['driver'];
                    } else {
                        $driverClass = $item['driver'];
                    }
                } else {
                    $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                }
                $driver = new $driverClass($item);

                if ($driver->name === null) {
                    continue;
                }

                $driver->submit($formData);

                if ($driver->newValue === $driver->nullValue) {
                    continue;
                }

                if (isset($item['buildSql']) && $item['buildSql'] instanceof \Closure) {
                    $buildSql = $item['buildSql'];
                    $sql = $buildSql($this->setting['db'], $formData);
                    if ($sql) {
                        if (isset($sql[0]) && is_array($sql[0])) {
                            $table->condition($sql);
                        } else {
                            $table->where($sql);
                        }
                    }
                } else {

                    $op = null;
                    if (isset($item['op'])) {
                        $op = strtoupper($item['op']);
                    } else {
                        switch ($driverClass) {
                            case FormItemDatePickerMonthRange::class:
                            case FormItemDatePickerRange::class:
                            case FormItemTimePickerRange::class:
                                $op = 'RANGE';
                                break;
                            case FormItemInput::class:
                                $op = '%LIKE%';
                                break;
                            default:
                                $op = '=';
                        }
                    }

                    $sql = null;
                    switch ($op) {
                        case 'LIKE':
                            $sql = $db->quoteKey($driver->name) . ' LIKE ' . $db->quoteValue($driver->newValue);
                            break;
                        case '%LIKE%':
                            $sql = $db->quoteKey($driver->name) . ' LIKE ' . $db->quoteValue('%' . $driver->newValue . '%');
                            break;
                        case 'LIKE%':
                            $sql = $db->quoteKey($driver->name) . ' LIKE ' . $db->quoteValue($driver->newValue . '%');
                            break;
                        case '%LIKE':
                            $sql = $db->quoteKey($driver->name) . ' LIKE ' . $db->quoteValue('%' . $driver->newValue);
                            break;
                        case 'RANGE':
                            if (is_array($driver->newValue) && count($driver->newValue) === 2) {
                                $sql = $db->quoteKey($driver->name) . ' >= ' . $db->quoteValue($driver->newValue[0]);
                                $sql .= ' AND ';
                                $sql .= $db->quoteKey($driver->name) . ' < ' . $db->quoteValue($driver->newValue[1]);
                            }
                            break;
                        case 'BETWEEN':
                            if (is_array($driver->newValue) && count($driver->newValue) === 2) {
                                $sql = $db->quoteKey($driver->name) . ' BETWEEN ' . $db->quoteValue($driver->newValue[0]);
                                $sql .= ' AND ';
                                $sql .= $db->quoteValue($driver->newValue[1]);
                            }
                            break;
                        case 'IN':
                            if (is_array($driver->newValue)) {
                                $newValue = [];
                                foreach ($driver->newValue as $x) {
                                    $newValue[] = $db->quoteValue($x);
                                }
                                $sql = $db->quoteKey($driver->name) . ' IN (' . implode(',', $newValue) . ')';
                            }
                            break;
                        default:
                            $sql = $db->quoteKey($driver->name) . ' = ' . $db->quoteValue($driver->newValue);
                            break;
                    }

                    if ($sql) {
                        $table->where($sql);
                    }
                }
            }
        }
        return $table;
    }

    /**
     * 明细
     *
     * @param array $setting 配置项
     */
    public function detail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        $postData = json_decode($postData, true);

        $tuple = Be::getTuple($this->setting['table'], $this->setting['db']);

        try {

            $primaryKey = $tuple->getPrimaryKey();

            $primaryKeyValue = null;
            if (is_array($primaryKey)) {
                $primaryKeyValue = [];
                foreach ($primaryKey as $pKey) {
                    if (!isset($postData['row'][$pKey])) {
                        throw new AdminPluginException('主键（row.' . $pKey . '）缺失！');
                    }

                    $primaryKeyValue[$pKey] = $postData['row'][$pKey];
                }
            } else {
                if (!isset($postData['row'][$primaryKey])) {
                    throw new AdminPluginException('主键（row.' . $primaryKey . '）缺失！');
                }

                $primaryKeyValue = $postData['row'][$primaryKey];
            }

            $tuple->load($primaryKeyValue);
            $row = $tuple->toArray();

            $fields = null;
            if (isset($this->setting['detail']['form']['items'])) {
                $fields = $this->setting['detail']['form']['items'];
            } else {
                $fields = [];

                $listFields = $this->setting['grid']['form']['items'];
                foreach ($listFields as &$item) {
                    if (!isset($item['label'])) {
                        continue;
                    }

                    if (isset($item['driver'])) {
                        switch ($item['driver']) {
                            case TableItemAvatar::class:
                                $item['driver'] = DetailItemAvatar::class;
                                break;
                            case TableItemCustom::class:
                                $item['driver'] = DetailItemCustom::class;
                                break;
                            case TableItemImage::class:
                                $item['driver'] = DetailItemImage::class;
                                break;
                            case TableItemProgress::class:
                                $item['driver'] = DetailItemProgress::class;
                                break;
                            case TableItemSwitch::class:
                                $item['driver'] = DetailItemSwitch::class;
                                break;
                            default:
                                $item['driver'] = DetailItemText::class;
                                break;
                        }
                    }

                    $fields[] = $item;
                }
                unset($item);
            }

            $setting = $this->setting['detail'];
            $setting['form']['items'] = $fields;


            if (isset($this->setting['detail']['events']['before'])) {
                $this->on('before', $this->setting['delete']['events']['before']);
            }

            if (isset($this->setting['detail']['events']['after'])) {
                $this->on('after', $this->setting['delete']['events']['after']);
            }

            $this->trigger('before', $tuple, $postData);

            Be::getAdminPlugin('Detail')
                ->setting($setting)
                ->setValue($row)
                ->display();

            $this->trigger('after', $tuple, $postData);

        } catch (\Throwable $t) {
            $response->error($t->getMessage());
            Be::getLog()->error($t);
        }
    }

    /**
     * 创建
     *
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $title = isset($this->setting['create']['title']) ? $this->setting['create']['title'] : '新建';

        if ($request->isAjax()) {

            $db = Be::getDb($this->setting['db']);
            $db->startTransaction();
            try {

                $postData = $request->json();
                $formData = $postData['formData'];

                $tuple = Be::getTuple($this->setting['table'], $this->setting['db']);

                if (isset($this->setting['create']['events']['before'])) {
                    $this->on('before', $this->setting['create']['events']['before']);
                }

                if (isset($this->setting['create']['events']['after'])) {
                    $this->on('after', $this->setting['create']['events']['after']);
                }

                if (isset($this->setting['create']['events']['success'])) {
                    $this->on('success', $this->setting['create']['events']['success']);
                }

                if (isset($this->setting['create']['events']['error'])) {
                    $this->on('error', $this->setting['create']['events']['error']);
                }

                if (isset($this->setting['create']['form']['items']) && count($this->setting['create']['form']['items']) > 0) {
                    foreach ($this->setting['create']['form']['items'] as $item) {

                        $driverClass = null;
                        if (isset($item['driver'])) {
                            if (substr($item['driver'], 0, 8) === 'FormItem') {
                                $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $item['driver'];
                            } else {
                                $driverClass = $item['driver'];
                            }
                        } else {
                            $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                        }
                        $driver = new $driverClass($item);

                        if ($driver->name === null) {
                            continue;
                        }

                        $driver->submit($formData);
                        $name = $driver->name;

                        // 必填字段
                        if ($driver->required) {
                            if ($driver->newValue === $driver->nullValue) {
                                throw new AdminPluginException($driver->label . ' 缺失！');
                            }
                        }

                        // 检查唯一性
                        if (isset($item['unique']) && $item['unique']) {
                            $tableUnique = Be::getTable($this->setting['table'], $this->setting['db']);
                            $tableUnique->where($name, $driver->newValue);
                            if (is_array($item['unique'])) {
                                $tableUnique->condition($item['unique']);
                            }
                            if ($tableUnique->count() > 0) {
                                throw new AdminPluginException($driver->label . ' 已存在 ' . $driver->newValue . ' 的记录！');
                            }
                        }

                        $tuple->$name = $driver->newValue;
                    }
                }

                if (isset($tuple->create_time)) {
                    $tuple->create_time = date('Y-m-d H:i:s');
                }

                if (isset($tuple->update_time)) {
                    $tuple->update_time = date('Y-m-d H:i:s');
                }

                $this->trigger('before', $tuple, $postData);
                $tuple->insert();
                $this->trigger('after', $tuple, $postData);

                $primaryKey = $tuple->getPrimaryKey();

                if (is_array($primaryKey)) {
                    $strPrimaryKey = '（' . implode(',', $primaryKey) . '）';
                    $primaryKeyValue = [];
                    foreach ($primaryKey as $pKey) {
                        $primaryKeyValue[] = $tuple->$pKey;
                    }
                    $strPrimaryKeyValue = '（' . implode(',', $primaryKeyValue) . '）';
                } else {
                    $strPrimaryKey = $primaryKey;
                    $strPrimaryKeyValue = $tuple->$primaryKey;
                }

                if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                    beAdminOpLog($title . '：新建' . $strPrimaryKey . '为' . $strPrimaryKeyValue . '的记录！', $formData);
                }
                $db->commit();
                $this->trigger('success', $tuple, $postData);
                $response->success($title . '：新建成功！');

            } catch (\Throwable $t) {
                $db->rollback();
                $this->trigger('error', $t, $postData);
                $response->error($t->getMessage());
                Be::getLog()->error($t);
            }

        } else {
            $response->set('title', $title);

            $setting = $this->setting['create'];
            Be::getAdminPlugin('Form')->setting($setting)->display();
        }
    }

    /**
     * 编辑
     *
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $title = isset($this->setting['edit']['title']) ? $this->setting['edit']['title'] : '编辑';

        $tuple = Be::getTuple($this->setting['table'], $this->setting['db']);

        if ($request->isAjax()) {

            $db = Be::getDb($this->setting['db']);
            $db->startTransaction();
            try {

                $postData = $request->json();
                $formData = $postData['formData'];

                $primaryKey = $tuple->getPrimaryKey();
                $primaryKeyValue = null;
                if (is_array($primaryKey)) {
                    $primaryKeyValue = [];
                    foreach ($primaryKey as $pKey) {
                        if (!isset($formData[$pKey])) {
                            throw new AdminPluginException('主键（' . $pKey . '）缺失！');
                        }

                        $primaryKeyValue[$pKey] = $formData[$pKey];
                    }
                } else {
                    if (!isset($formData[$primaryKey])) {
                        throw new AdminPluginException('主键（' . $primaryKey . '）缺失！');
                    }

                    $primaryKeyValue = $formData[$primaryKey];
                }
                $tuple->load($primaryKeyValue);

                if (isset($this->setting['edit']['events']['before'])) {
                    $this->on('before', $this->setting['edit']['events']['before']);
                }

                if (isset($this->setting['edit']['events']['after'])) {
                    $this->on('after', $this->setting['edit']['events']['after']);
                }

                if (isset($this->setting['edit']['events']['success'])) {
                    $this->on('success', $this->setting['edit']['events']['success']);
                }

                if (isset($this->setting['edit']['events']['error'])) {
                    $this->on('error', $this->setting['edit']['events']['error']);
                }

                if (isset($this->setting['edit']['form']['items']) && count($this->setting['edit']['form']['items']) > 0) {
                    $row = $tuple->toArray();
                    foreach ($this->setting['edit']['form']['items'] as $item) {

                        // 禁止编辑字段
                        if (isset($item['disabled']) && $item['disabled']) {
                            continue;
                        }

                        $driverClass = null;
                        if (isset($item['driver'])) {
                            if (substr($item['driver'], 0, 8) === 'FormItem') {
                                $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $item['driver'];
                            } else {
                                $driverClass = $item['driver'];
                            }
                        } else {
                            $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                        }

                        if (!isset($item['name'])) {
                            continue;
                        }

                        $name = $item['name'];
                        if (isset($tuple->$name) && !isset($item['value'])) {
                            $item['value'] = $tuple->$name;
                        }

                        $driver = new $driverClass($item, $row);

                        $driver->submit($formData);

                        // 必填字段
                        if ($driver->required) {
                            if ($driver->newValue === $driver->nullValue) {
                                throw new AdminPluginException($driver->label . ' 缺失！');
                            }
                        }

                        // 检查唯一性
                        if (isset($item['unique']) && $item['unique']) {
                            $tableUnique = Be::getTable($this->setting['table'], $this->setting['db']);
                            $tableUnique->where($name, $driver->newValue);

                            if (is_array($primaryKey)) {
                                foreach ($primaryKey as $pKey) {
                                    $tableUnique->where($pKey, '!=', $formData[$pKey]);
                                }
                            } else {
                                $tableUnique->where($primaryKey, '!=', $formData[$primaryKey]);
                            }

                            if (is_array($item['unique'])) {
                                $tableUnique->condition($item['unique']);
                            }

                            if ($tableUnique->count() > 0) {
                                throw new AdminPluginException($driver->label . ' 已存在 ' . $driver->newValue . ' 的记录！');
                            }
                        }

                        $tuple->$name = $driver->newValue;
                    }
                }

                if (isset($tuple->update_time)) {
                    $tuple->update_time = date('Y-m-d H:i:s');
                }

                $this->trigger('before', $tuple, $postData);
                $tupleChangeDetails = $tuple->getChangeDetails();
                $tuple->update();
                $this->trigger('after', $tuple, $postData);
                $strPrimaryKey = null;
                $strPrimaryKeyValue = null;
                if (is_array($primaryKey)) {
                    $strPrimaryKey = '（' . implode(',', $primaryKey) . '）';
                    $strPrimaryKeyValue = '（' . implode(',', array_values($primaryKeyValue)) . '）';
                } else {
                    $strPrimaryKey = $primaryKey;
                    $strPrimaryKeyValue = $tuple->$primaryKey;
                }

                if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                    beAdminOpLog($title . '：编辑' . $strPrimaryKey . '为' . $strPrimaryKeyValue . '的记录！', $tupleChangeDetails);
                }
                $db->commit();
                $this->trigger('success', $tuple, $postData);
                $response->success($title . '：编辑成功！');
            } catch (\Throwable $t) {
                $db->rollback();
                $this->trigger('error', $t, $postData);
                $response->error($t->getMessage());
                Be::getLog()->error($t);
            }

        } else {

            try {

                $postData = $request->post('data', '', '');
                $postData = json_decode($postData, true);

                $primaryKey = $tuple->getPrimaryKey();
                $primaryKeyValue = null;
                if (is_array($primaryKey)) {
                    $primaryKeyValue = [];
                    foreach ($primaryKey as $pKey) {
                        if (!isset($postData['row'][$pKey])) {
                            throw new AdminPluginException('主键（row.' . $pKey . '）缺失！');
                        }

                        $primaryKeyValue[$pKey] = $postData['row'][$pKey];
                    }
                } else {
                    if (!isset($postData['row'][$primaryKey])) {
                        throw new AdminPluginException('主键（row.' . $primaryKey . '）缺失！');
                    }

                    $primaryKeyValue = $postData['row'][$primaryKey];
                }

                $tuple->load($primaryKeyValue);

                $setting = $this->setting['edit'];

                if (is_array($primaryKeyValue)) {
                    foreach ($primaryKeyValue as $pKey => $pVal) {
                        $setting['form']['items'][] = [
                            'name' => $pKey,
                            'value' => $pVal,
                            'driver' => FormItemHidden::class,
                        ];
                    }
                } else {
                    $setting['form']['items'][] = [
                        'name' => $primaryKey,
                        'value' => $primaryKeyValue,
                        'driver' => FormItemHidden::class,
                    ];
                }

                $response->set('title', $title);
                Be::getAdminPlugin('Form')
                    ->setting($setting)
                    ->setValue($tuple->toArray())
                    ->display();

            } catch (\Throwable $t) {
                $response->error($t->getMessage());
                Be::getLog()->error($t);
            }
        }
    }

    /**
     * 复制
     *
     */
    public function copy()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $title = isset($this->setting['copy']['title']) ? $this->setting['copy']['title'] : '复制';

        $tuple = Be::getTuple($this->setting['table'], $this->setting['db']);

        $postData = $request->json();

        $db = Be::getDb($this->setting['db']);
        $db->startTransaction();
        try {

            $postData = $request->json();
            $formData = $postData['formData'];

            $primaryKey = $tuple->getPrimaryKey();
            if (is_array($primaryKey)) {
                $primaryKeyValue = [];
                $primaryKeyDefault = [];
                foreach ($primaryKey as $pKey) {
                    if (!isset($postData['row'][$pKey])) {
                        throw new AdminPluginException('主键（' . $pKey . '）缺失！');
                    }

                    $primaryKeyDefault[$pKey] = $tuple->$pKey;
                    $primaryKeyValue[$pKey] = $postData['row'][$pKey];
                }
            } else {
                if (!isset($postData['row'][$primaryKey])) {
                    throw new AdminPluginException('主键（' . $primaryKey . '）缺失！');
                }

                $primaryKeyDefault = $tuple->$primaryKey;
                $primaryKeyValue = $postData['row'][$primaryKey];
            }
            $tuple->load($primaryKeyValue);

            if (isset($this->setting['copy']['events']['before'])) {
                $this->on('before', $this->setting['copy']['events']['before']);
            }

            if (isset($this->setting['copy']['events']['after'])) {
                $this->on('after', $this->setting['copy']['events']['after']);
            }

            if (isset($this->setting['copy']['events']['success'])) {
                $this->on('success', $this->setting['copy']['events']['success']);
            }

            if (isset($this->setting['copy']['events']['error'])) {
                $this->on('error', $this->setting['copy']['events']['error']);
            }

            if (isset($tuple->update_time)) {
                $tuple->update_time = date('Y-m-d H:i:s');
            }

            if (is_array($primaryKey)) {
                foreach ($primaryKey as $pKey) {
                    $tuple->$pKey = $primaryKeyDefault[$pKey];
                }
            } else {
                $tuple->$primaryKey = $primaryKeyDefault;
            }

            $this->trigger('before', $tuple, $postData);
            $tupleChangeDetails = $tuple->getChangeDetails();
            $tuple->insert();
            $this->trigger('after', $tuple, $postData);

            $strPrimaryKey = null;
            $strPrimaryKeyValue = null;
            if (is_array($primaryKey)) {
                $strPrimaryKey = '（' . implode(',', $primaryKey) . '）';
                $strPrimaryKeyValue = '（' . implode(',', array_values($primaryKeyValue)) . '）';
            } else {
                $strPrimaryKey = $primaryKey;
                $strPrimaryKeyValue = $tuple->$primaryKey;
            }

            if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                beAdminOpLog($title . '：复制' . $strPrimaryKey . '为' . $strPrimaryKeyValue . '的记录！', $tupleChangeDetails);
            }

            $db->commit();
            $this->trigger('success', $tuple, $postData);
            $response->success($title . '：复制成功！');
        } catch (\Throwable $t) {
            $db->rollback();
            $this->trigger('error', $t, $postData);
            $response->error($t->getMessage());
            Be::getLog()->error($t);
        }
    }

    /**
     * 编辑某个字段的值
     */
    public function fieldEdit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if (isset($this->setting['fieldEdit']['events']['before'])) {
            $this->on('before', $this->setting['fieldEdit']['events']['before']);
        }

        if (isset($this->setting['fieldEdit']['events']['after'])) {
            $this->on('after', $this->setting['fieldEdit']['events']['after']);
        }

        if (isset($this->setting['fieldEdit']['events']['success'])) {
            $this->on('success', $this->setting['fieldEdit']['events']['success']);
        }

        if (isset($this->setting['fieldEdit']['events']['error'])) {
            $this->on('error', $this->setting['fieldEdit']['events']['error']);
        }

        $postData = $request->json();
        if (!isset($postData['postData']['field'])) {
            $response->error('参数（postData.field）缺失！');
            return;
        }
        $field = $postData['postData']['field'];

        $fieldLabel = '';
        if (isset($this->setting['grid']['field']['items'])) {
            foreach ($this->setting['grid']['field']['items'] as $fieldItem) {
                if ($fieldItem['name'] === $field) {
                    $fieldLabel = $fieldItem['label'];
                    break;
                }
            }
        }

        $title = null;

        $db = Be::getDb($this->setting['db']);

        if (isset($postData['selectedRows'])) {

            $db->startTransaction();
            try {

                if (!is_array($postData['selectedRows']) || count($postData['selectedRows']) === 0) {
                    throw new AdminPluginException('你尚未选择要操作的数据！');
                }

                if (!isset($postData['postData']['value'])) {
                    throw new AdminPluginException('参数（postData.value）缺失！');
                }
                $value = $postData['postData']['value'];

                $strPrimaryKey = null;
                $primaryKeyValues = [];

                $i = 0;
                foreach ($postData['selectedRows'] as $row) {
                    $tuple = Be::getTuple($this->setting['table'], $this->setting['db']);
                    $primaryKey = $tuple->getPrimaryKey();

                    $primaryKeyValue = null;
                    if (is_array($primaryKey)) {
                        $primaryKeyValue = [];
                        foreach ($primaryKey as $pKey) {
                            if (!isset($row[$pKey])) {
                                throw new AdminPluginException('主键（selectedRows[' . $i . '].' . $pKey . '）缺失！');
                            }

                            $primaryKeyValue[$pKey] = $row[$pKey];
                        }
                    } else {
                        if (!isset($row[$primaryKey])) {
                            throw new AdminPluginException('主键（selectedRows[' . $i . '].' . $primaryKey . '）缺失！');
                        }

                        $primaryKeyValue = $row[$primaryKey];
                    }

                    $tuple->load($primaryKeyValue);
                    $tuple->$field = $value;
                    if (isset($tuple->update_time)) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    }

                    $this->trigger('before', $tuple, $postData);
                    $tuple->save();
                    $this->trigger('after', $tuple, $postData);

                    if ($strPrimaryKey === null) {
                        if (is_array($primaryKey)) {
                            $strPrimaryKey = '（' . implode(',', $primaryKey) . '）';
                        } else {
                            $strPrimaryKey = $primaryKey;
                        }
                    }

                    if (is_array($primaryKeyValue)) {
                        $primaryKeyValues[] = '（' . implode(',', $primaryKeyValue) . '）';
                    } else {
                        $primaryKeyValues[] = $primaryKeyValue;
                    }

                    $i++;
                }

                $strPrimaryKeyValue = implode(',', $primaryKeyValues);

                if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                    $opLog = '修改字段 ' . $fieldLabel . '（' . $field . '）的值为' . $value;
                    if (isset($this->setting['fieldEdit']['title'])) {
                        $opLog = $this->setting['fieldEdit']['title'] . '（' . $title . '）';
                    }

                    beAdminOpLog($opLog . '（' . count($primaryKeyValues) . ' 条）', $opLog . '（#' . $strPrimaryKey . '：' . $strPrimaryKeyValue . '）');
                }
                $db->commit();

                $this->trigger('success', $postData);
                $response->success('执行成功！');

            } catch (\Throwable $t) {
                $db->rollback();
                $this->trigger('error', $t, $postData);
                $response->error($t->getMessage());
                Be::getLog()->error($t);
            }

        } elseif (isset($postData['row'])) {

            $db->startTransaction();
            try {

                if (!isset($postData['row'][$field])) {
                    throw new AdminPluginException('参数（row.' . $field . '）缺失！');
                }

                $value = isset($postData['postData']['value']) ? $postData['postData']['value'] : $postData['row'][$field];

                $tuple = Be::getTuple($this->setting['table'], $this->setting['db']);
                $primaryKey = $tuple->getPrimaryKey();

                $primaryKeyValue = null;
                if (is_array($primaryKey)) {
                    $primaryKeyValue = [];
                    foreach ($primaryKey as $pKey) {
                        if (!isset($postData['row'][$pKey])) {
                            throw new AdminPluginException('主键（row.' . $pKey . '）缺失！');
                        }

                        $primaryKeyValue[$pKey] = $postData['row'][$pKey];
                    }
                } else {
                    if (!isset($postData['row'][$primaryKey])) {
                        throw new AdminPluginException('主键（row.' . $primaryKey . '）缺失！');
                    }

                    $primaryKeyValue = $postData['row'][$primaryKey];
                }
                $tuple->load($primaryKeyValue);
                $tuple->$field = $value;
                if (isset($tuple->update_time)) {
                    $tuple->update_time = date('Y-m-d H:i:s');
                }

                $this->trigger('before', $tuple, $postData);
                $tuple->save();
                $this->trigger('after', $tuple, $postData);

                $strPrimaryKey = null;
                $strPrimaryKeyValue = null;
                if (is_array($primaryKey)) {
                    $strPrimaryKey = '（' . implode(',', $primaryKey) . '）';
                    $strPrimaryKeyValue = '（' . implode(',', $primaryKeyValue) . '）';
                } else {
                    $strPrimaryKey = $primaryKey;
                    $strPrimaryKeyValue = $primaryKeyValue;
                }

                if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                    $opLog = '修改字段 ' . $fieldLabel . '（' . $field . '）的值为' . $value;
                    if (isset($this->setting['fieldEdit']['title'])) {
                        $opLog = $this->setting['fieldEdit']['title'] . '（' . $title . '）';
                    }

                    beAdminOpLog($opLog . '（#' . $strPrimaryKey . '：' . $strPrimaryKeyValue . '）');
                }
                $db->commit();
                $this->trigger('success', $tuple, $postData);
                $response->success('执行成功！');
            } catch (\Throwable $t) {
                $db->rollback();
                $this->trigger('error', $t, $postData);
                $response->error($t->getMessage());
                Be::getLog()->error($t);
            }
        } else {
            $response->error('参数（rows或row）缺失！');
        }
    }

    /**
     * 删除
     *
     */
    public function delete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->json();

        $title = null;
        if (isset($this->setting['delete']['title'])) {
            $title = $this->setting['delete']['title'];
        } else {
            $title = '删除记录';
        }

        if (isset($this->setting['delete']['events']['before'])) {
            $this->on('before', $this->setting['delete']['events']['before']);
        }

        if (isset($this->setting['delete']['events']['after'])) {
            $this->on('after', $this->setting['delete']['events']['after']);
        }

        if (isset($this->setting['delete']['events']['success'])) {
            $this->on('success', $this->setting['delete']['events']['success']);
        }

        if (isset($this->setting['delete']['events']['error'])) {
            $this->on('error', $this->setting['delete']['events']['error']);
        }

        $db = Be::getDb($this->setting['db']);

        if (isset($postData['selectedRows'])) {

            if (!is_array($postData['selectedRows']) || count($postData['selectedRows']) === 0) {
                $response->error('你尚未选择要操作的数据！');
                return;
            }

            $db->startTransaction();
            try {
                $strPrimaryKey = null;
                $primaryKeyValues = [];

                $i = 0;
                foreach ($postData['selectedRows'] as $row) {
                    $tuple = Be::getTuple($this->setting['table'], $this->setting['db']);
                    $primaryKey = $tuple->getPrimaryKey();

                    $primaryKeyValue = null;
                    if (is_array($primaryKey)) {
                        $primaryKeyValue = [];
                        foreach ($primaryKey as $pKey) {
                            if (!isset($row[$pKey])) {
                                throw new AdminPluginException('主键（selectedRows[' . $i . '].' . $pKey . '）缺失！');
                            }

                            $primaryKeyValue[$pKey] = $row[$pKey];
                        }
                    } else {
                        if (!isset($row[$primaryKey])) {
                            throw new AdminPluginException('主键（selectedRows[' . $i . '].' . $primaryKey . '）缺失！');
                        }

                        $primaryKeyValue = $row[$primaryKey];
                    }

                    $tuple->load($primaryKeyValue);
                    $this->trigger('before', $tuple, $postData);
                    $tuple->delete();
                    $this->trigger('after', $tuple, $postData);

                    if ($strPrimaryKey === null) {
                        if (is_array($primaryKey)) {
                            $strPrimaryKey = '（' . implode(',', $primaryKey) . '）';
                        } else {
                            $strPrimaryKey = $primaryKey;
                        }
                    }

                    if (is_array($primaryKeyValue)) {
                        $primaryKeyValues[] = '（' . implode(',', $primaryKeyValue) . '）';
                    } else {
                        $primaryKeyValues[] = $primaryKeyValue;
                    }

                    $i++;
                }

                $strPrimaryKeyValue = implode(',', $primaryKeyValues);

                if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                    beAdminOpLog($title . '（' . count($primaryKeyValues) . ' 条）', $title . '（#' . $strPrimaryKey . '：' . $strPrimaryKeyValue . '）');
                }
                $db->commit();
                $this->trigger('success', $postData);
                $response->success($title . '，执行成功！');
            } catch (\Throwable $t) {
                $db->rollback();
                $this->trigger('error', $t, $postData);
                $response->error($t->getMessage());
                Be::getLog()->error($t);
            }

        } elseif (isset($postData['row'])) {
            $db->startTransaction();
            try {
                $tuple = Be::getTuple($this->setting['table'], $this->setting['db']);
                $primaryKey = $tuple->getPrimaryKey();

                $primaryKeyValue = null;
                if (is_array($primaryKey)) {
                    $primaryKeyValue = [];
                    foreach ($primaryKey as $pKey) {
                        if (!isset($postData['row'][$pKey])) {
                            throw new AdminPluginException('主键（row.' . $pKey . '）缺失！');
                        }

                        $primaryKeyValue[$pKey] = $postData['row'][$pKey];
                    }
                } else {
                    if (!isset($postData['row'][$primaryKey])) {
                        throw new AdminPluginException('主键（row.' . $primaryKey . '）缺失！');
                    }

                    $primaryKeyValue = $postData['row'][$primaryKey];
                }
                $tuple->load($primaryKeyValue);
                $this->trigger('before', $tuple, $postData);
                $tuple->delete();
                $this->trigger('after', $tuple, $postData);

                $strPrimaryKey = null;
                $strPrimaryKeyValue = null;
                if (is_array($primaryKey)) {
                    $strPrimaryKey = '（' . implode(',', $primaryKey) . '）';
                    $strPrimaryKeyValue = '（' . implode(',', $primaryKeyValue) . '）';
                } else {
                    $strPrimaryKey = $primaryKey;
                    $strPrimaryKeyValue = $primaryKeyValue;
                }

                if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                    beAdminOpLog($title . '（#' . $strPrimaryKey . '：' . $strPrimaryKeyValue . '）');
                }
                $db->commit();
                $this->trigger('success', $tuple, $postData);
                $response->success($title . '，执行成功！');
            } catch (\Throwable $t) {
                $db->rollback();
                $this->trigger('error', $t, $postData);
                $response->error($t->getMessage());
                Be::getLog()->error($t);
            }
        }
    }


}

