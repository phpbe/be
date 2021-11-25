<?php

namespace Be\App\System\Service\Admin;

use Be\Db\Tuple;
use Be\Util\Random;
use Be\App\ServiceException;
use Be\Be;

class AdminUser
{

    /**
     * 登录
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $ip IP 地址
     * @return \stdClass
     * @throws \Exception
     */
    public function login($username, $password, $ip)
    {
        $username = trim($username);
        if (!$username) {
            throw new ServiceException('参数用户名（username）缺失！');
        }

        $password = trim($password);
        if (!$password) {
            throw new ServiceException('参数密码（password）缺失！');
        }

        $ip = trim($ip);
        if (!$ip) {
            throw new ServiceException('参数IP（$ip）缺失！');
        }

        $request = Be::getRequest();
        $response = Be::getResponse();
        $session = Be::getSession();

        $timesKey = 'be-adminUserLoginIp-' . $ip;
        $times = $session->get($timesKey);
        if (!$times) $times = 0;
        $times++;
        if ($times > 10) {
            throw new ServiceException('登陆失败次数过多，请稍后再试！');
        }
        $session->set($timesKey, $times);

        $tupleAdminUserLoginLog = Be::newTuple('system_admin_user_login_log');
        $tupleAdminUserLoginLog->username = $username;
        $tupleAdminUserLoginLog->ip = $ip;
        $tupleAdminUserLoginLog->create_time = date('Y-m-d H:i:s');

        $db = Be::getDb();
        $db->beginTransaction();
        try {
            $tupleAdminUser = Be::newTuple('system_admin_user');

            $configAdminUser = Be::getConfig('App.System.AdminUser');
            if ($configAdminUser->ldap) {

                $conn = null;
                try {
                    $conn = ldap_connect($configAdminUser->ldap_host);
                } catch (\Throwable $e) {
                    throw new ServiceException('无法连接到LDAP服务器（' . $configAdminUser->ldap_host . '）！');
                }

                ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

                $bind = null;
                try {
                    if ($configAdminUser->ldap_pattern) {
                        $pattern = str_replace('{username}', $username, $configAdminUser->ldap_pattern);
                        $bind = ldap_bind($conn, $pattern, $password);
                    } else {
                        $bind = ldap_bind($conn, $username, $password);
                    }
                } catch (\Throwable $e) {
                    $ldapErr = ldap_error($conn);
                    ldap_close($conn);
                    throw new ServiceException('LDAP登录失败' . ($ldapErr ? ('（' . $ldapErr . '）') : '') . '！');
                }

                if (!$bind) {
                    ldap_close($conn);
                    throw new ServiceException('用户账号和密码不匹配！');
                }

                ldap_close($conn);

                try {
                    $tupleAdminUser->loadBy('username', $username);
                } catch (\Exception $e) {
                    $tupleAdminUser->username = $username;
                    $tupleAdminUser->salt = Random::complex(32);
                    $tupleAdminUser->create_time = date('Y-m-d H:i:s');
                }

                $tupleAdminUser->password = $this->encryptPassword($password, $tupleAdminUser->salt);
                $tupleAdminUser->last_login_time = $tupleAdminUser->this_login_time;
                $tupleAdminUser->this_login_time = date('Y-m-d H:i:s');
                $tupleAdminUser->last_login_ip = $tupleAdminUser->this_login_ip;
                $tupleAdminUser->this_login_ip = $ip;
                $tupleAdminUser->update_time = date('Y-m-d H:i:s');
                $tupleAdminUser->save();

            } else {

                try {
                    $tupleAdminUser->loadBy('username', $username);
                } catch (\Exception $e) {
                    throw new ServiceException('用户账号（' . $username . '）不存在！');
                }

                if ($tupleAdminUser->password === $this->encryptPassword($password, $tupleAdminUser->salt)) {
                    if ($tupleAdminUser->is_delete == 1) {
                        throw new ServiceException('用户账号（' . $username . '）不可用！');
                    } elseif ($tupleAdminUser->is_enable == 0) {
                        throw new ServiceException('用户账号（' . $username . '）已被禁用！');
                    } else {
                        $tupleAdminUser->last_login_time = $tupleAdminUser->this_login_time;
                        $tupleAdminUser->this_login_time = date('Y-m-d H:i:s');
                        $tupleAdminUser->last_login_ip = $tupleAdminUser->this_login_ip;
                        $tupleAdminUser->this_login_ip = $ip;
                        $tupleAdminUser->update_time = date('Y-m-d H:i:s');
                        $tupleAdminUser->save();
                    }
                } else {
                    throw new ServiceException('密码错误！');
                }
            }

            $this->makeLogin($tupleAdminUser);

            $adminRememberMe = $username . '|' . base64_encode($this->rc4($password, $tupleAdminUser->salt));
            $response->cookie('be-adminUserRememberMe', $adminRememberMe, time() + 30 * 86400, '/', '', false, true);

            $tupleAdminUserLoginLog->success = 1;
            $tupleAdminUserLoginLog->description = '登陆成功！';

            $session->delete($timesKey);

            $db->commit();
            $tupleAdminUserLoginLog->save();
            return $tupleAdminUser;

        } catch (\Exception $e) {
            $db->rollback();

            $tupleAdminUserLoginLog->description = $e->getMessage();
            $tupleAdminUserLoginLog->save();
            throw $e;
        }
    }

    /**
     * 标记用户已成功登录
     *
     * @param Tuple | Object | int $adminUserId 用户Row对象 | Object数据 | 用户ID
     * @throws ServiceException
     */
    public function makeLogin($adminUserId)
    {
        $adminUser = null;
        if ($adminUserId instanceof Tuple) {
            $adminUser = $adminUserId->toObject();
        } elseif (is_object($adminUserId)) {
            $adminUser = $adminUserId;
        } elseif (is_numeric($adminUserId)) {
            $tupleAdminUser = Be::newTuple('system_admin_user');
            $tupleAdminUser->load($adminUserId);
            $adminUser = $tupleAdminUser->toObject();
        } else {
            throw new ServiceException('参数无法识别！');
        }

        unset($adminUser->password);
        unset($adminUser->salt);
        unset($adminUser->remember_me_token);

        Be::setAdminUser($adminUser);
    }

    /**
     * 记住我 自动登录
     *
     * @throws \Exception
     */
    public function rememberMe()
    {
        $request = Be::getRequest();
        $rememberMe = $request->cookie('be-adminUserRememberMe', null);
        if ($rememberMe) {
            $rememberMe = explode('|', $rememberMe);
            if (count($rememberMe) != 2) return;

            $username = $rememberMe[0];

            $tupleAdminUser = Be::newTuple('system_admin_user');
            try {
                $tupleAdminUser->loadBy('username', $username);
                if ($tupleAdminUser->is_delete == 0 && $tupleAdminUser->is_enable == 1) {
                    $password = base64_decode($rememberMe[1]);
                    $password = $this->rc4($password, $tupleAdminUser->salt);
                    $this->login($username, $password, $request->getIp());
                }
            } catch (\Exception $e) {
                return;
            }
        }
    }

    /**
     * 退出
     *
     */
    public function logout()
    {
        Be::getSession()->wipe();
        Be::getResponse()->cookie('be-adminUserRememberMe', '', -1);
    }

    /**
     * 密码 Hash
     *
     * @param string $password 密码
     * @param string $salt 盐值
     * @return string
     */
    public function encryptPassword($password, $salt)
    {
        return sha1(sha1($password) . $salt);
    }


    public function rc4($txt, $pwd)
    {
        $result = '';
        $kL = strlen($pwd);
        $tL = strlen($txt);
        $level = 256;
        $key = [];
        $box = [];

        for ($i = 0; $i < $level; ++$i) {
            $key[$i] = ord($pwd[$i % $kL]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < $level; ++$i) {
            $j = ($j + $box[$i] + $key[$i]) % $level;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $tL; ++$i) {
            $a = ($a + 1) % $level;
            $j = ($j + $box[$a]) % $level;

            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k = $box[($box[$a] + $box[$j]) % $level];
            $result .= chr(ord($txt[$i]) ^ $k);
        }

        return $result;
    }


}
