<?php

namespace Be\Theme\Sample\Section\Footer;

use Be\Theme\Section;

class Template extends Section
{
    protected array $position = ['South'];

    public function display()
    {
        if ($this->config->enable) {
            ?>
            <style type="text/css">
                <?php
                echo '#footer-' . $this->id . ' {';
                echo 'background-color: ' . $this->config->backgroundColor . ';';
                echo '}';

                // 手机端
                echo '@media (max-width: 768px) {';
                echo '#footer-' . $this->id . ' {';
                if ($this->config->paddingTopMobile) {
                    echo 'padding-top: ' . $this->config->paddingTopMobile . 'px;';
                }
                if ($this->config->paddingBottomMobile) {
                    echo 'padding-bottom: ' . $this->config->paddingBottomMobile . 'px;';
                }
                echo '}';
                echo '}';

                // 平析端
                echo '@media (min-width: 768px) {';
                echo '#footer-' . $this->id . ' {';
                if ($this->config->paddingTopTablet) {
                    echo 'padding-top: ' . $this->config->paddingTopTablet . 'px;';
                }
                if ($this->config->paddingBottomTablet) {
                    echo 'padding-bottom: ' . $this->config->paddingBottomTablet . 'px;';
                }
                echo '}';
                echo '}';

                // 电脑端
                echo '@media (min-width: 992px) {';
                echo '#footer-' . $this->id . ' {';
                if ($this->config->paddingTopDesktop) {
                    echo 'padding-top: ' . $this->config->paddingTopDesktop . 'px;';
                }
                if ($this->config->paddingBottomDesktop) {
                    echo 'padding-bottom: ' . $this->config->paddingBottomDesktop . 'px;';
                }
                echo '}';
                echo '}';
                ?>
            </style>

            <div id="footer-<?php echo $this->id; ?>">
                <div class="be-container">
                    <div class="be-ta-center">
                        <?php
                        if (isset($this->config->copyright)) {
                            echo $this->config->copyright;
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }

}


