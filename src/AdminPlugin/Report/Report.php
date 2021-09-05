<?php

namespace Be\AdminPlugin\Report;

use Be\Be;
use Be\AdminPlugin\Form\Item\FormItemDatePickerMonthRange;
use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemTimePickerRange;
use Be\AdminPlugin\AdminPluginException;
use Be\AdminPlugin\Driver;

/**
 * 报表
 *
 * Class Report
 * @package Be\System\AdminPlugin\Report
 */
class Report extends Driver
{

    /**
     * 配置项
     *
     * @param array $setting
     * @return Driver
     */
    public function setting($setting = [])
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
            $task = Be::getRequest()->request('task', 'Grid');
        }

        if (method_exists($this, $task)) {
            $this->$task();
        }
    }

    /**
     * 列表展示
     *
     */
    public function lists()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            try {
                $db = Be::getDb($this->setting['db']);

                if (!isset($this->setting['sql'])) {
                    throw new AdminPluginException('报表设置项 sql 缺失！');
                }

                $sqlCount = null;
                $sqlData = null;
                if (is_array($this->setting['sql'])) {
                    if (!isset($this->setting['sql']['count'])) {
                        throw new AdminPluginException('报表设置项 sql.count 缺失！');
                    }

                    if (!isset($this->setting['sql']['data'])) {
                        throw new AdminPluginException('报表设置项 sql.data 缺失！');
                    }

                    $sqlCount = $this->setting['sql']['count'];
                    $sqlData = $this->setting['sql']['data'];
                } else {
                    $sqlData = $this->setting['sql'];
                    $sqlCount = 'SELECT COUNT(*) FROM (' . $sqlData . ') t';
                }

                $postData = $request->json();
                $formData = $postData['formData'];

                $wheres = $this->getWheres($formData);
                if (count($wheres) > 0) {
                    $whereStr = implode(' AND ', $wheres);
                    $pos1 = strpos($sqlCount, '{where}');
                    if ($pos1) {
                        $sqlCountWhereStr = $whereStr;
                        $pos2 = strpos(strtoupper($sqlCount), ' WHERE ');
                        if ($pos2) {
                            if ($pos1 > $pos2 && $pos1 - $pos2 > 7) {
                                if (trim(substr($sqlCount, $pos2 + 7, $pos1 - $pos2 - 7))) {
                                    $sqlCountWhereStr = ' AND ' . $whereStr;
                                }
                            }
                        } else {
                            $sqlCountWhereStr = ' WHERE ' . $whereStr;
                        }
                        $sqlCount = str_replace('{where}', $sqlCountWhereStr, $sqlCount);
                    }

                    $pos1 = strpos($sqlData, '{where}');
                    if ($pos1) {
                        $sqlDataWhereStr = $whereStr;
                        $pos2 = strpos(strtoupper($sqlData), ' WHERE ');
                        if ($pos2) {
                            if ($pos1 > $pos2 && $pos1 - $pos2 > 7) {
                                if (trim(substr($sqlData, $pos2 + 7, $pos1 - $pos2 - 7))) {
                                    $sqlDataWhereStr = ' AND ' . $whereStr;
                                }
                            }
                        } else {
                            $sqlDataWhereStr = ' WHERE ' . $whereStr;
                        }
                        $sqlData = str_replace('{where}', $sqlDataWhereStr, $sqlData);
                    }
                } else {
                    $sqlCount = str_replace('{where}', '', $sqlCount);
                    $sqlData = str_replace('{where}', '', $sqlData);
                }

                $total = $db->getValue($sqlCount);

                $posOrderBy = strpos($sqlData, '{orderBy}');
                $orderBy = isset($postData['orderBy']) ? $postData['orderBy'] : '';
                if ($orderBy) {
                    $orderByDir = isset($postData['orderByDir']) ? strtoupper($postData['orderByDir']) : '';
                    if (!in_array($orderByDir, ['ASC', 'DESC'])) {
                        $orderByDir = 'DESC';
                    }

                    $orderBySql = 'ORDER BY ' . $orderBy . ' ' . $orderByDir;

                    if ($posOrderBy !== false) {
                        $sqlData = str_replace('{orderBy}', $orderBySql, $sqlData);
                    } else {
                        $sqlData .= ' ' . $orderBySql;
                    }
                } else {
                    if (isset($this->setting['Grid']['orderBy'])) {
                        $orderBy = $this->setting['Grid']['orderBy'];
                        if (isset($this->setting['Grid']['orderByDir'])) {
                            $orderByDir = $this->setting['Grid']['orderByDir'];
                            $orderBySql = ' ORDER BY ' . $orderBy . ' ' . $orderByDir;
                        } else {
                            $orderBySql = ' ORDER BY ' . $orderBy;
                        }

                        if ($posOrderBy !== false) {
                            $sqlData = str_replace('{orderBy}', $orderBySql, $sqlData);
                        } else {
                            $sqlData .= $orderBySql;
                        }
                    } else {
                        if ($posOrderBy !== false) {
                            $sqlData = str_replace('{orderBy}', '', $sqlData);
                        }
                    }
                }

                $page = $postData['page'];
                $pageSize = $postData['pageSize'];
                $limitSql = 'LIMIT ' . (($page - 1) * $pageSize) . ', ' . $pageSize;

                $posLimit = strpos($sqlData, '{limit}');
                if ($posLimit !== false) {
                    $sqlData = str_replace('{limit}', $limitSql, $sqlData);
                } else {
                    $sqlData .= ' ' . $limitSql;
                }

                $rows = $db->getArrays($sqlData);

                $formattedRows = [];
                foreach ($rows as $row) {
                    $formattedRow = [];

                    foreach ($this->setting['Grid']['table']['items'] as $item) {
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

                        $formattedRow[$itemName] = $itemValue;
                    }

                    foreach ($row as $k => $v) {
                        if (isset($formattedRow[$k])) {
                            continue;
                        }

                        if (isset($this->setting['Grid']['table']['exclude']) &&
                            is_array($this->setting['Grid']['table']['exclude']) &&
                            in_array($k, $this->setting['Grid']['table']['exclude'])
                        ) {
                            continue;
                        }

                        $formattedRow[$k] = $v;
                    }
                    $formattedRows[] = $formattedRow;
                }

                $response->set('success', true);
                $response->set('data', [
                    'total' => $total,
                    'gridData' => $formattedRows,
                ]);
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
                Be::getLog()->error($t);
            }

        } else {
            $setting = $this->setting['Grid'];

            Be::getAdminPlugin('Grid')
                ->setting($setting)
                ->display();

            $response->createHistory();
        }
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
            $db = Be::getDb($this->setting['db']);

            if (!isset($this->setting['sql'])) {
                throw new AdminPluginException('报表设置项 sql 缺失！');
            }

            $sqlData = null;
            if (is_array($this->setting['sql'])) {
                if (!isset($this->setting['sql']['data'])) {
                    throw new AdminPluginException('报表设置项 sql.data 缺失！');
                }

                $sqlData = $this->setting['sql']['data'];
            } else {
                $sqlData = $this->setting['sql'];
            }

            $postData = $request->post('data', '', '');
            $postData = json_decode($postData, true);
            $formData = $postData['formData'];

            $wheres = $this->getWheres($formData);
            if (count($wheres) > 0) {
                $whereStr = implode(' AND ', $wheres);
                $pos1 = strpos($sqlData, '{where}');
                if ($pos1) {
                    $sqlDataWhereStr = $whereStr;
                    $pos2 = strpos(strtoupper($sqlData), ' WHERE ');
                    if ($pos2) {
                        if ($pos1 > $pos2 && $pos1 - $pos2 > 7) {
                            if (trim(substr($sqlData, $pos2 + 7, $pos1 - $pos2 - 7))) {
                                $sqlDataWhereStr = ' AND ' . $whereStr;
                            }
                        }
                    } else {
                        $sqlDataWhereStr = ' WHERE ' . $whereStr;
                    }
                    $sqlData = str_replace('{where}', $sqlDataWhereStr, $sqlData);
                }
            } else {
                $sqlData = str_replace('{where}', '', $sqlData);
            }

            $rows = $db->getYieldArrays($sqlData);

            $exporter = Be::getAdminPlugin('Exporter');

            $exportDriver = isset($postData['postData']['driver']) ? $postData['postData']['driver'] : 'csv';

            $filename = null;
            if (isset($this->setting['export']['title'])) {
                $filename = $this->setting['export']['title'];
            } elseif (isset($this->setting['Grid']['title'])) {
                $filename = $this->setting['Grid']['title'];
            }
            $filename .= '（' . date('YmdHis') . '）';
            $filename .= ($exportDriver == 'csv' ? '.csv' : '.xls');

            $exporter->setDriver($exportDriver)->setOutput('http', $filename);

            $fields = null;
            if (isset($this->setting['export']['items'])) {
                $fields = $this->setting['export']['items'];
            } else {
                $fields = $this->setting['Grid']['table']['items'];
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
            } elseif (isset($this->setting['Grid']['title'])) {
                $content = '导出 ' . $this->setting['Grid']['title'] . '（' . $exportDriver . '）';
            } else {
                $content = '导出 ' . $exportDriver;
            }

            if (!isset($this->setting['opLog']) || $this->setting['opLog']) {
                beAdminOpLog($content, $postData);
            }

        } catch (\Throwable $t) {
            $response->error($t->getMessage());
            Be::getLog()->error($t);
        }
    }

    /**
     * 生成 where 条件
     *
     * @param array $formData 查询条件
     * @return array
     */
    private function getWheres($formData)
    {
        $db = Be::getDb($this->setting['db']);

        $wheres = [];

        if (isset($this->setting['Grid']['filter']) && count($this->setting['Grid']['filter']) > 0) {
            foreach ($this->setting['Grid']['filter'] as $filter) {
                if (is_array($filter)) {
                    $n = count($filter);
                    if ($n == 2) {
                        $wheres[] = $db->quoteKey($filter[0]) . ' = ' . $db->quoteValue($filter[1]);
                    } elseif ($n > 2) {
                        $wheres[] = $db->quoteKey($filter[0]) . ' ' . $db->quoteValue($filter[1]) . ' ' . $db->quoteValue($filter[2]);
                    }
                } else {
                    $wheres[] = $filter;
                }
            }
        }

        if (isset($this->setting['Grid']['tab'])) {
            if (isset($item['buildSql']) && $item['buildSql'] instanceof \Closure) {
                $buildSql = $item['buildSql'];
                $sql = $buildSql($this->setting['db'], $formData);
                if ($sql) {
                    $wheres[] = $sql;
                }
            } else {
                $driver = new \Be\AdminPlugin\Tab\Driver($this->setting['Grid']['tab']);
                $driver->submit($formData);
                if ($driver->newValue !== '') {
                    $sql = '';
                    if (isset($this->setting['Grid']['tab']['table'])) {
                        $sql .= $db->quoteKey($this->setting['Grid']['tab']['table']) . '.';
                    }
                    $sql .= $db->quoteKey($driver->name) . ' = ' . $db->quoteValue($driver->newValue);
                    $wheres[] = $sql;
                }
            }
        }

        // 表单搜索
        if (isset($this->setting['Grid']['form']['items']) && count($this->setting['Grid']['form']['items']) > 0) {
            foreach ($this->setting['Grid']['form']['items'] as $item) {

                $driverClass = null;
                if (isset($item['driver'])) {
                    if (substr($item['driver'], 0, 8) == 'FormItem') {
                        $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $item['driver'];
                    } else {
                        $driverClass = $item['driver'];
                    }
                } else {
                    $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                }
                $driver = new $driverClass($item);

                $driver->submit($formData);

                if ($driver->newValue === '') {
                    continue;
                }

                if (isset($item['buildSql']) && $item['buildSql'] instanceof \Closure) {
                    $buildSql = $item['buildSql'];
                    $sql = $buildSql($this->setting['db'], $formData);
                    if ($sql) {
                        $wheres[] = $sql;
                    }
                } else {

                    $op = null;
                    if (isset($item['op'])) {
                        $op = strtoupper($item['op']);
                    } else {
                        switch ($driverName) {
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

                    $sql = '';
                    if (isset($item['table'])) {
                        $sql .= $db->quoteKey($item['table']) . '.';
                    }

                    switch ($op) {
                        case 'LIKE':
                            $sql .= $db->quoteKey($driver->name) . ' LIKE ' . $db->quoteValue($driver->newValue);
                            break;
                        case '%LIKE%':
                            $sql .= $db->quoteKey($driver->name) . ' LIKE ' . $db->quoteValue('%' . $driver->newValue . '%');
                            break;
                        case 'LIKE%':
                            $sql .= $db->quoteKey($driver->name) . ' LIKE ' . $db->quoteValue($driver->newValue . '%');
                            break;
                        case '%LIKE':
                            $sql .= $db->quoteKey($driver->name) . ' LIKE ' . $db->quoteValue('%' . $driver->newValue);
                            break;
                        case 'RANGE':
                            if (is_array($driver->newValue) && count($driver->newValue) == 2) {
                                $sql .= $db->quoteKey($driver->name) . ' >= ' . $db->quoteValue($driver->newValue[0]);
                                $sql .= ' AND ';
                                $sql .= $db->quoteKey($driver->name) . ' < ' . $db->quoteValue($driver->newValue[1]);
                            }
                            break;
                        case 'BETWEEN':
                            if (is_array($driver->newValue) && count($driver->newValue) == 2) {
                                $sql .= $db->quoteKey($driver->name) . ' BETWEEN ' . $db->quoteValue($driver->newValue[0]);
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
                                $sql .= $db->quoteKey($driver->name) . ' IN (' . implode(',', $newValue) . ')';
                            }
                            break;
                        default:
                            $sql .= $db->quoteKey($driver->name) . ' = ' . $db->quoteValue($driver->newValue);
                            break;
                    }

                    if ($sql) {
                        $wheres[] = $sql;
                    }
                }
            }
        }
        return $wheres;
    }


}

