<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 自动完成
 */
class FormItemAutoComplete extends FormItem
{

    protected array $suggestions = [];
    protected ?string $remote = null;

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct(array $params = [], array $row = [])
    {
        parent::__construct($params, $row);

        if (isset($params['suggestions'])) {
            $suggestions = $params['suggestions'];
            if ($suggestions instanceof \Closure) {
                $this->suggestions = $suggestions($row);
            } else {
                $this->suggestions = $suggestions;
            }
        } elseif (isset($this->keyValues)) {
            $suggestions = [];
            foreach ($this->keyValues as $value) {
                $suggestions[] = ['value' => $value];
            }
            $this->suggestions = $suggestions;
        }

        if (isset($params['remote'])) {
            $remote = $params['remote'];
            if ($remote instanceof \Closure) {
                $this->remote = $remote($row);
            } else {
                $this->remote = $remote;
            }
        }

        if ($this->required) {
            if (!isset($this->ui['form-item'][':rules'])) {
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请输入'.$this->label.'\', trigger: \'blur\' }]';
            }
        }

        if ($this->disabled) {
            if (!isset($this->ui['disabled'])) {
                $this->ui['disabled'] = 'true';
            }
        }

        if (!isset($this->ui['clearable'])) {
            $this->ui['clearable'] = null;
        }

        if (!isset($this->ui[':fetch-suggestions'])) {
            $this->ui[':fetch-suggestions'] = 'formItemAutoComplete_' . $this->name . '_fetchSuggestions';
        }

        if ($this->name !== null) {
            if (!isset($this->ui['v-model'])) {
                $this->ui['v-model'] = 'formData.' . $this->name;
            }
        }
    }


    /**
     * 获取html内容ß
     *
     * @return string
     */
    public function getHtml(): string
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

        $html .= '<el-autocomplete';
        foreach ($this->ui as $k => $v) {
            if ($k === 'form-item') {
                continue;
            }

            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '</el-autocomplete>';

        if ($this->description) {
            $html .= '<div class="be-c-999 be-mt-50 be-lh-150">' . $this->description . '</div>';
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
        return [
            'formItems' => [
                $this->name => [
                    'suggestions' => $this->suggestions,
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
        if ($this->remote === null) {
            return [
                'formItemAutoComplete_' . $this->name . '_fetchSuggestions' => 'function(keywords, cb) {
                    if (keywords) {
                        var results = [];
                        var suggestion;
                        for(var x in this.formItems.' . $this->name . '.suggestions) {
                            suggestion = this.formItems.' . $this->name . '.suggestions[x];
                            if (suggestion.value.toLowerCase().indexOf(keywords.toLowerCase()) !== -1) {
                                results.push(suggestion);
                            }
                        }
                        cb(results);
                    } else {
                        cb(this.formItems.' . $this->name . '.suggestions);
                    }
                }',
            ];
        } else {
            return [
                'formItemAutoComplete_' . $this->name . '_fetchSuggestions' => 'function(keywords, cb) {
                    var _this = this;
                    this.$http.post(\''.$this->remote.'\', {"keywords": keywords}).then(function (response) {
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                 if (responseData.data.suggestions) {
                                    cb(responseData.data.suggestions);
                                } else if (responseData.data.values) {
                                    var suggestions = [];
                                    for(var x in responseData.data.values) {
                                        suggestions.push({
                                            "value" : responseData.data.values[x]
                                        });
                                    }
                                    cb(suggestions);
                                } else {
                                    cb([]);
                                }
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                                cb([]);
                            }
                        }
                    }).catch(function (error) {
                        _this.$message.error(error);
                        cb([]);
                    });
                }',
            ];
        }
    }

}
