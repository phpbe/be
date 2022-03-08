<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 计划任务时间选择器
 */
class FormItemCron extends FormItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        parent::__construct($params, $row);

        if ($this->required) {
            if (!isset($this->ui['form-item'][':rules'])) {
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请输入' . $this->label . '\', trigger: \'blur\' }]';
            }
        }
    }


    /**
     * 获取html内容ß
     *
     * @return string
     */
    public function getHtml()
    {
        $html = '<el-form-item';
        foreach ($this->ui['form-item'] as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';

        $html .= '<div>';
        $html .= '<el-radio-group v-model="formItems.' . $this->name . '.type" @change="formItemCron_changeType(\'' . $this->name . '\')">';
        $html .= '<el-radio-button label="none">无</el-radio-button>';
        $html .= '<el-radio-button label="picker">选择器</el-radio-button>';
        $html .= '<el-radio-button label="custom">自定义</el-radio-button>';
        $html .= '</el-radio-group>';
        $html .= '</div>';

        $html .= '<div v-show="formItems.' . $this->name . '.type==\'picker\'" style="padding-top:5px;">';

        $html .= '<span style="color:#999;">每</span>&nbsp;';
        $html .= '<el-select v-model="formItems.' . $this->name . '.per" @change="formItemCron_pickerChange(\'' . $this->name . '\')" style="width:70px;">';
        $html .= '<el-option label="分钟" value="per_minute"></el-option>';
        $html .= '<el-option label="小时" value="per_hour"></el-option>';
        $html .= '<el-option label="天" value="per_day"></el-option>';
        $html .= '<el-option label="周" value="per_week"></el-option>';
        $html .= '<el-option label="月" value="per_month"></el-option>';
        $html .= '<el-option label="年" value="per_year"></el-option>';
        $html .= '</el-select>';

        $html .= '<span v-show="formItems.' . $this->name . '.per==\'per_year\'">';
        $html .= '&nbsp;<span style="color:#999;">的</span>&nbsp;<el-select v-model="formItems.' . $this->name . '.month" @change="formItemCron_pickerChange(\'' . $this->name . '\')" multiple collapse-tags style="width:130px;">';
        $html .= '<el-option label="每月" value="*"></el-option>';
        for ($i = 1; $i <= 12; $i++) {
            $html .= '<el-option label="' . $i . '月" value="' . $i . '"></el-option>';
        }
        $html .= '</el-select>';
        $html .= '</span>';

        $html .= '<span v-show="formItems.' . $this->name . '.per==\'per_year\' || formItems.' . $this->name . '.per==\'per_month\'">';
        $html .= '&nbsp;<span style="color:#999;">的</span>&nbsp;<el-select v-model="formItems.' . $this->name . '.day" @change="formItemCron_pickerChange(\'' . $this->name . '\')" multiple collapse-tags style="width:130px;">';
        $html .= '<el-option label="每天" value="*"></el-option>';
        for ($i = 1; $i <= 31; $i++) {
            $html .= '<el-option label="' . $i . '日" value="' . $i . '"></el-option>';
        }
        $html .= '</el-select>';
        $html .= '</span>';

        $html .= '<span v-show="formItems.' . $this->name . '.per==\'per_week\'">';
        $html .= '&nbsp;<span style="color:#999;">的</span>&nbsp;<el-select v-model="formItems.' . $this->name . '.week" @change="formItemCron_pickerChange(\'' . $this->name . '\')" multiple collapse-tags style="width:130px;">';
        $html .= '<el-option label="每天" value="*"></el-option>';
        $html .= '<el-option label="周一" value="1"></el-option>';
        $html .= '<el-option label="周二" value="2"></el-option>';
        $html .= '<el-option label="周三" value="3"></el-option>';
        $html .= '<el-option label="周四" value="4"></el-option>';
        $html .= '<el-option label="周五" value="5"></el-option>';
        $html .= '<el-option label="周六" value="6"></el-option>';
        $html .= '<el-option label="周日" value="0"></el-option>';
        $html .= '</el-select>';
        $html .= '</span>';


        $html .= '<span v-show="formItems.' . $this->name . '.per!=\'per_minute\' && formItems.' . $this->name . '.per!=\'per_hour\'">';
        $html .= '&nbsp;<el-select v-model="formItems.' . $this->name . '.hour" @change="formItemCron_pickerChange(\'' . $this->name . '\')" multiple collapse-tags style="width:130px;">';
        $html .= '<el-option label="每小时" value="*"></el-option>';
        for ($i = 0; $i <= 23; $i++) {
            $html .= '<el-option label="' . $i . '点" value="' . $i . '"></el-option>';
        }
        $html .= '</el-select>&nbsp;<span style="color:#999;">:</span>';
        $html .= '</span>';

        $html .= '<span v-show="formItems.' . $this->name . '.per!=\'per_minute\'">';
        $html .= '&nbsp;<el-select v-model="formItems.' . $this->name . '.minute" @change="formItemCron_pickerChange(\'' . $this->name . '\')" multiple collapse-tags style="width:130px;">';
        $html .= '<el-option label="每分钟" value="*"></el-option>';
        for ($i = 0; $i <= 59; $i++) {
            $html .= '<el-option label="' .  $i . '分" value="' . $i . '"></el-option>';
        }
        $html .= '</el-select>';
        $html .= '</span>';

        $html .= '</div>';

        $html .= '<div v-show="formItems.' . $this->name . '.type==\'custom\'" style="padding-top:5px;">';
        $html .= '<el-input v-model="formItems.' . $this->name . '.custom" @change="formItemCron_customChange(\'' . $this->name . '\')"></el-input>';
        $html .= '</div>';

        if ($this->description) {
            $html .= '<div class="be-c-bbb be-mt-50 be-lh-150">' . $this->description . '</div>';
        }

        $html .= '</el-form-item>';
        return $html;
    }


    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        $type = 'none';
        $per = 'per_day';
        $month = ['*'];
        $week = ['*'];
        $day = ['*'];
        $hour = ['0'];
        $minute = ['0'];

        $parsed = false;
        if ($this->value) {
            $parts = explode(' ', $this->value);
            if (count($parts) === 5) {
                $parsed = true;

                if (strpos($parts[0], ',') !== false) {
                    $arr = explode(',', $parts[0]);
                    $arr = array_unique($arr);
                    foreach ($arr as $x) {
                        if (!is_numeric($x)) {
                            $parsed = false;
                        }
                    }

                    if (count($arr) === 60) {
                        $minute = ['*'];
                    } else {
                        $minute = $arr;
                    }
                } elseif ($parts[0] === '*' || is_numeric($parts[0])) {
                    $minute = [$parts[0]];
                } else {
                    $parsed = false;
                }

                if (strpos($parts[1], ',') !== false) {
                    $arr = explode(',', $parts[1]);
                    $arr = array_unique($arr);
                    foreach ($arr as $x) {
                        if (!is_numeric($x)) {
                            $parsed = false;
                        }
                    }

                    if (count($arr) === 24) {
                        $hour = ['*'];
                    } else {
                        $hour = $arr;
                    }
                } elseif ($parts[1] === '*' || is_numeric($parts[1])) {
                    $hour = [$parts[1]];
                } else {
                    $parsed = false;
                }

                if (strpos($parts[2], ',') !== false) {
                    $arr = explode(',', $parts[2]);
                    $arr = array_unique($arr);
                    foreach ($arr as $x) {
                        if (!is_numeric($x)) {
                            $parsed = false;
                        }
                    }

                    if (count($arr) === 31) {
                        $day = ['*'];
                    } else {
                        $day = $arr;
                    }
                } elseif ($parts[2] === '*' || is_numeric($parts[2])) {
                    $day = [$parts[2]];
                } else {
                    $parsed = false;
                }

                if (strpos($parts[3], ',') !== false) {
                    $arr = explode(',', $parts[3]);
                    $arr = array_unique($arr);
                    foreach ($arr as $x) {
                        if (!is_numeric($x)) {
                            $parsed = false;
                        }
                    }

                    if (count($arr) === 12) {
                        $month = ['*'];
                    } else {
                        $month = $arr;
                    }
                } elseif ($parts[3] === '*' || is_numeric($parts[3])) {
                    $month = [$parts[3]];
                } else {
                    $parsed = false;
                }

                if (strpos($parts[4], ',') !== false) {
                    $arr = explode(',', $parts[4]);
                    $arr = array_unique($arr);
                    foreach ($arr as $x) {
                        if (!is_numeric($x)) {
                            $parsed = false;
                        }
                    }

                    if (count($arr) === 7) {
                        $week = ['*'];
                    } else {
                        $week = $arr;
                    }
                } elseif ($parts[4] === '*' || is_numeric($parts[4])) {
                    $week = [$parts[4]];
                } else {
                    $parsed = false;
                }

                if ($parts[3] !== "*") {
                    $per = "per_year";
                } else {
                    if ($parts[4] !== "*") {
                        $per = "per_week";
                    } else {
                        if ($parts[2] !== "*") {
                            $per = "per_month";
                        } else {
                            if ($parts[1] !== "*") {
                                $per = "per_day";
                            } else {
                                if ($parts[0] !== "*") {
                                    $per = "per_hour";
                                } else {
                                    $per = "per_minute";
                                }
                            }
                        }
                    }
                }
            }

            $type = $parsed ? 'picker' : 'custom';
        }

        return [
            'formItems' => [
                $this->name => [
                    'type' => $type,
                    'per' => $per,
                    'month' => $month,
                    'week' => $week,
                    'day' => $day,
                    'hour' => $hour,
                    'minute' => $minute,
                    'custom' => $this->value,
                ]
            ]
        ];
    }


    /**
     * 获取 vue 方法
     *
     * @return false | array
     */
    public function getVueMethods()
    {
        return [
            'formItemCron_changeType' => 'function(name) {
                if (this.formItems[name].type === "none") {
                    this.formData[name] = "";
                } else if (this.formItems[name].type === "picker") {
                    this.formItems[name].month = ["*"];
                    this.formItems[name].week = ["*"];
                    this.formItems[name].day = ["*"];
                    this.formItems[name].hour = ["0"];
                    this.formItems[name].minute = ["0"];
                    var per = "per_day";
                    var parts = this.formItems[name].custom.split(" ");
                    if (parts.length === 5) {
                        var arr, len, i, abort;
                        if (parts[0].indexOf(",") !== -1) {
                            arr = parts[0].split(",");
                            arr = this.formItemCron_arrayUnion(arr);
                            len = arr.length;
                            if (len > 1) {
                                abort = false;
                                for(i=0; i<len; i++) {
                                    if (arr[i] === "*") {
                                        this.formItems[name].minute = ["*"];
                                        abort = true;
                                        break;
                                    }
                                }
            
                                if (!abort) {
                                    if (len === 60) {
                                        this.formItems[name].minute = ["*"];
                                    } else {
                                        this.formItems[name].minute = arr;
                                    }
                                }
                            }
                        } else if (parts[0] === "*" || !isNaN(parts[0])) {
                            this.formItems[name].minute = [parts[0]];
                        }

                        
                        if (parts[1].indexOf(",") !== -1) {
                            arr = parts[1].split(",");
                            arr = this.formItemCron_arrayUnion(arr);
                            len = arr.length;
                            if (len > 1) {
                                abort = false;
                                for(i=0; i<len; i++) {
                                    if (arr[i] === "*") {
                                        this.formItems[name].hour = ["*"];
                                        abort = true;
                                        break;
                                    }
                                }
            
                                if (!abort) {
                                    if (len === 24) {
                                        this.formItems[name].hour = ["*"];
                                    } else {
                                        this.formItems[name].hour = arr;
                                    }
                                }
                            }
                        } else if (parts[1] === "*" || !isNaN(parts[1])) {
                            this.formItems[name].hour = [parts[1]];
                        }
                        
                        if (parts[2].indexOf(",") !== -1) {
                            arr = parts[2].split(",");
                            arr = this.formItemCron_arrayUnion(arr);
                            len = arr.length;
                            if (len > 1) {
                                abort = false;
                                for(i=0; i<len; i++) {
                                    if (arr[i] === "*") {
                                        this.formItems[name].day = ["*"];
                                        abort = true;
                                        break;
                                    }
                                }
            
                                if (!abort) {
                                    if (len === 32) {
                                        this.formItems[name].day = ["*"];
                                    } else {
                                        this.formItems[name].day = arr;
                                    }
                                }
                            }
                        } else if (parts[2] === "*" || !isNaN(parts[2])) {
                            this.formItems[name].day = [parts[2]];
                        }
                        
                        if (parts[3].indexOf(",") !== -1) {
                            arr = parts[3].split(",");
                            arr = this.formItemCron_arrayUnion(arr);
                            len = arr.length;
                            if (len > 1) {
                                abort = false;
                                for(i=0; i<len; i++) {
                                    if (arr[i] === "*") {
                                        this.formItems[name].month = ["*"];
                                        abort = true;
                                        break;
                                    }
                                }
            
                                if (!abort) {
                                    if (len === 12) {
                                        this.formItems[name].month = ["*"];
                                    } else {
                                        this.formItems[name].month = arr;
                                    }
                                }
                            }
                        } else if (parts[3] === "*" || !isNaN(parts[3])) {
                            this.formItems[name].month = [parts[3]];
                        }
                                                
                        if (parts[4].indexOf(",") !== -1) {
                            arr = parts[4].split(",");
                            arr = this.formItemCron_arrayUnion(arr);
                            len = arr.length;
                            if (len > 1) {
                                abort = false;
                                for(i=0; i<len; i++) {
                                    if (arr[i] === "*") {
                                        this.formItems[name].month = ["*"];
                                        abort = true;
                                        break;
                                    }
                                }
            
                                if (!abort) {
                                    if (len === 7) {
                                        this.formItems[name].week = ["*"];
                                    } else {
                                        this.formItems[name].week = arr;
                                    }
                                }
                            }
                        } else if (parts[4] === "*" || !isNaN(parts[4])) {
                            this.formItems[name].week = [parts[4]];
                        }
                        
                        if (parts[3] !== "*") {
                            per = "per_year";
                        } else {
                            if (parts[4] !== "*") {
                                per = "per_week";
                            } else {
                                if (parts[2] !== "*") {
                                    per = "per_month";
                                } else {
                                    if (parts[1] !== "*") {
                                        per = "per_day";
                                    } else {
                                        if (parts[0] !== "*") {
                                            per = "per_hour";
                                        } else {
                                            per = "per_minute";
                                        }
                                    }
                                }
                            }
                        }
                    }
                    this.formItems[name].per = per;
                    
                    this.formData[name] = this.formItemCron_sort(this.formItems[name].minute).join(",") 
                        + " " + this.formItemCron_sort(this.formItems[name].hour).join(",") 
                        + " " + this.formItemCron_sort(this.formItems[name].day).join(",") 
                        + " " + this.formItemCron_sort(this.formItems[name].month).join(",") 
                        + " " + this.formItemCron_sort(this.formItems[name].week).join(",");
    
                    this.formItems[name].custom = this.formData[name];
                } else if (this.formItems[name].type === "custom") {
                    this.formData[name] = this.formItems[name].custom;
                }
            }',
            'formItemCron_pickerChange' => 'function(name) {
                if (this.formItems[name].per === "per_minute") {
                    this.formItems[name].month = ["*"];
                    this.formItems[name].week = ["*"];
                    this.formItems[name].day = ["*"];
                    this.formItems[name].hour = ["*"];
                    this.formItems[name].minute = ["*"];
                } else if (this.formItems[name].per === "per_hour") {
                    this.formItems[name].month = ["*"];
                    this.formItems[name].week = ["*"];
                    this.formItems[name].day = ["*"];
                    this.formItems[name].hour = ["*"];
                } else if (this.formItems[name].per === "per_day") {
                    this.formItems[name].month = ["*"];
                    this.formItems[name].week = ["*"];
                    this.formItems[name].day = ["*"];
                } else if (this.formItems[name].per === "per_month") {
                    this.formItems[name].week = ["*"];
                } else if (this.formItems[name].per === "per_week") {
                    this.formItems[name].month = ["*"];
                    this.formItems[name].day = ["*"];
                } else if (this.formItems[name].per === "per_year") {
                    this.formItems[name].week = ["*"];
                }
                
                len = this.formItems[name].minute.length;
                if (len > 1) {
                    if (this.formItems[name].minute[len-1] === "*") {
                        this.formItems[name].minute = ["*"];
                    } else {
                        if (this.formItems[name].minute[0] === "*") {
                            this.formItems[name].minute.splice(0, 1);
                        }
                        
                        if (this.formItems[name].minute.length === 60) {
                            this.formItems[name].minute = ["*"];
                        }
                    }
                } else if (len === 0) {
                    this.formItems[name].minute = ["*"];
                }
                
                len = this.formItems[name].hour.length;
                if (len > 1) {
                    if (this.formItems[name].hour[len-1] === "*") {
                        this.formItems[name].hour = ["*"];
                    } else {
                        if (this.formItems[name].hour[0] === "*") {
                            this.formItems[name].hour.splice(0, 1);
                        }
                        
                        if (this.formItems[name].hour.length === 24) {
                            this.formItems[name].hour = ["*"];
                        }
                    }
                } else if (len === 0) {
                    this.formItems[name].hour = ["*"];
                }
                
                len = this.formItems[name].day.length;
                if (len > 1) {
                    if (this.formItems[name].day[len-1] === "*") {
                        this.formItems[name].day = ["*"];
                    } else {
                        if (this.formItems[name].day[0] === "*") {
                            this.formItems[name].day.splice(0, 1);
                        }
                        
                        if (this.formItems[name].day.length === 31) {
                            this.formItems[name].day = ["*"];
                        }
                    }
                } else if (len === 0) {
                    this.formItems[name].day = ["*"];
                }

                var len = this.formItems[name].month.length;
                if (len > 1) {
                    if (this.formItems[name].month[len-1] === "*") {
                        this.formItems[name].month = ["*"];
                    } else {
                        if (this.formItems[name].month[0] === "*") {
                            this.formItems[name].month.splice(0, 1);
                        }
                        
                        if (this.formItems[name].month.length === 12) {
                            this.formItems[name].month = ["*"];
                        }
                    }
                } else if (len === 0) {
                    this.formItems[name].month = ["*"];
                }
                
                len = this.formItems[name].week.length;
                if (len > 1) {
                    if (this.formItems[name].week[len-1] === "*") {
                        this.formItems[name].week = ["*"];
                    } else {
                        if (this.formItems[name].week[0] === "*") {
                            this.formItems[name].week.splice(0, 1);
                        }
                        
                        if (this.formItems[name].week.length === 7) {
                            this.formItems[name].week = ["*"];
                        }
                    }
                } else if (len === 0) {
                    this.formItems[name].week = ["*"];
                }
                
                this.formData[name] = this.formItemCron_sort(this.formItems[name].minute).join(",") 
                    + " " + this.formItemCron_sort(this.formItems[name].hour).join(",") 
                    + " " + this.formItemCron_sort(this.formItems[name].day).join(",") 
                    + " " + this.formItemCron_sort(this.formItems[name].month).join(",") 
                    + " " + this.formItemCron_sort(this.formItems[name].week).join(",");

                this.formItems[name].custom = this.formData[name];
            }',

            'formItemCron_customChange' => 'function(name) {
                this.formData[name] = this.formItems[name].custom;
            }',

            'formItemCron_arrayUnion' => 'function(arr) {
                for (var i=0; i<arr.length; i++) {
                    for (var j=i+1; j<arr.length; j++) {
                        if (arr[i] === arr[j]) {
                            arr.splice(j, 1);
                            j--;
                        }
                    }
                }
                return arr;
            }',

            'formItemCron_sort' => 'function(arr) {
                return arr.sort(function(a,b){
                    return Number(a) - Number(b);
                });
            }',
        ];
    }

}
