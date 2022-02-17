<?php

namespace Be\App\System\Controller\Admin;

use Be\App\ControllerException;
use Be\Be;
use Be\Util\FileSystem\FileSize;
use Be\Util\Net\FileUpload;

/**
 * 后台组件相关功能
 */
class AdminPlugin
{

    public function __construct()
    {
        $my = Be::getAdminUser();
        if ($my->id === '') {
            throw new ControllerException('登录超时，请重新登录！');
        }
    }

    public function uploadFile()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $file = $request->files('file');
        if ($file['error'] === 0) {
            $configSystem = Be::getConfig('App.System.System');
            $maxSize = $configSystem->uploadMaxSize;
            $maxSizeInt = FileSize::string2Int($maxSize);
            if ($file['size'] > $maxSizeInt) {
                $response->set('success', false);
                $response->set('message', '您上传的文件尺寸已超过最大限制：' . $maxSize . '！');
                $response->json();
                return;
            }

            $ext = '';
            $rPos = strrpos($file['name'], '.');
            if ($rPos !== false) {
                $ext = substr($file['name'], $rPos + 1);
            }
            if (!in_array($ext, $configSystem->allowUploadFileTypes)) {
                $response->set('success', false);
                $response->set('message', '禁止上传的文件类型：' . $ext . '！');
                $response->json();
                return;
            }

            $newFileName = md5_file($file['tmp_name']) .'.' .$ext;
            $newFilePath = Be::getRuntime()->getUploadPath() . '/tmp/' . $newFileName;

            $dir = dirname($newFilePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
                chmod($dir, 0777);
            }

            if (move_uploaded_file($file['tmp_name'], $newFilePath)) {
                $newFileUrl = Be::getRequest()->getUploadUrl(). '/tmp/' . $newFileName;
                $response->set('newValue', $newFileName);
                $response->set('url', $newFileUrl);
                $response->set('success', true);
                $response->set('message', '上传成功！');
                $response->json();
                return;
            } else {
                $response->set('success', false);
                $response->set('message', '服务器处理上传文件出错！');
                $response->json();
                return;
            }
        } else {
            $errorDesc = FileUpload::errorDescription($file['error']);
            $response->set('success', false);
            $response->set('message', '上传失败' . '(' . $errorDesc . ')');
            $response->json();
            return;
        }
    }

    public function uploadAvatar()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $file = $request->files('file');
        if ($file['error'] === 0) {

            $configSystem = Be::getConfig('App.System.System');
            $maxSize = $configSystem->uploadMaxSize;
            $maxSizeInt = FileSize::string2Int($maxSize);
            if ($file['size'] > $maxSizeInt) {
                $response->set('success', false);
                $response->set('message', '您上传的头像尺寸已超过最大限制：' . $maxSize . '！');
                $response->json();
                return;
            }

            $ext = '';
            $rPos = strrpos($file['name'], '.');
            if ($rPos !== false) {
                $ext = substr($file['name'], $rPos + 1);
            }
            if (!in_array($ext, $configSystem->allowUploadImageTypes)) {
                $response->set('success', false);
                $response->set('message', '禁止上传的图像类型：' . $ext . '！');
                $response->json();
                return;
            }

            ini_set('memory_limit', '-1');
            $libImage = Be::getLib('Image');
            $libImage->open($file['tmp_name']);
            if ($libImage->isImage()) {
                $newImageName = md5_file($file['tmp_name']) .'.' .$libImage->getType();
                $newImagePath = Be::getRuntime()->getUploadPath() . '/tmp/' . $newImageName;

                $dir = dirname($newImagePath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                    chmod($dir, 0777);
                }

                $resize = false;
                $maxWidth = $request->post('maxWidth', 0, 'int');
                $maxHeight = $request->post('maxHeight', 0, 'int');
                if ($maxWidth > 0 && $maxHeight> 0) {
                    if ($libImage->getWidth() > $maxWidth || $libImage->getheight() > $maxHeight) {
                        $libImage->resize($maxWidth, $maxHeight, 'center');
                        $resize = true;
                    }
                }

                if ($resize) {
                    $libImage->save($newImagePath);
                } else {
                    move_uploaded_file($file['tmp_name'], $newImagePath);
                }

                $newImageUrl = Be::getRequest()->getUploadUrl(). '/tmp/' . $newImageName;
                $response->set('newValue', $newImageName);
                $response->set('url', $newImageUrl);
                $response->set('success', true);
                $response->set('message', '上传成功！');
                $response->json();
                return;
            } else {
                $response->set('success', false);
                $response->set('message', '您上传的不是有效的图像文件！');
                $response->json();
                return;
            }
        } else {
            $errorDesc = FileUpload::errorDescription($file['error']);
            $response->set('success', false);
            $response->set('message', '上传失败' . '(' . $errorDesc . ')');
            $response->json();
            return;
        }
    }

    public function uploadImage()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $file = $request->files('file');
        if ($file['error'] === 0) {

            $configSystem = Be::getConfig('App.System.System');
            $maxSize = $configSystem->uploadMaxSize;
            $maxSizeInt = FileSize::string2Int($maxSize);
            if ($file['size'] > $maxSizeInt) {
                $response->set('success', false);
                $response->set('message', '您上传的图像尺寸已超过最大限制：' . $maxSize . '！');
                $response->json();
                return;
            }

            $ext = '';
            $rPos = strrpos($file['name'], '.');
            if ($rPos !== false) {
                $ext = substr($file['name'], $rPos + 1);
            }
            if (!in_array($ext, $configSystem->allowUploadImageTypes)) {
                $response->set('success', false);
                $response->set('message', '禁止上传的图像类型：' . $ext . '！');
                $response->json();
                return;
            }

            ini_set('memory_limit', '-1');
            $libImage = Be::getLib('Image');
            $libImage->open($file['tmp_name']);
            if ($libImage->isImage()) {

                $filename = $request->post('filename', '');
                $newImageName = null;
                if ($filename) {
                    if (strpos($filename, '{datetime}') !== false) {
                        $filename = str_replace('{datetime}', date('YmdHis'), $filename);
                    }

                    if (strpos($filename, '{random}') !== false) {
                        $random = \Be\Util\Crypt\Random::simple(10);
                        $filename = str_replace('{random}', $random, $filename);
                    }

                    $newImageName = $filename . '.' . $libImage->getType();
                } else {
                    $newImageName = md5_file($file['tmp_name']) .'.' .$libImage->getType();
                }
                $newImagePath = Be::getRuntime()->getUploadPath() . '/tmp/' . $newImageName;

                $dir = dirname($newImagePath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                    chmod($dir, 0777);
                }

                $resize = false;
                $maxWidth = $request->post('maxWidth', 0, 'int');
                $maxHeight = $request->post('maxHeight', 0, 'int');
                if ($maxWidth > 0 && $maxHeight> 0) {
                    if ($libImage->getWidth() > $maxWidth || $libImage->getheight() > $maxHeight) {
                        $libImage->resize($maxWidth, $maxHeight, 'scale');
                    }
                }

                if ($resize) {
                    $libImage->save($newImagePath);
                } else {
                    move_uploaded_file($file['tmp_name'], $newImagePath);
                }

                $newImageUrl = Be::getRequest()->getUploadUrl(). '/tmp/' . $newImageName;
                $response->set('newValue', $newImageName);
                $response->set('url', $newImageUrl);
                $response->set('success', true);
                $response->set('message', '上传成功！');
                $response->json();
                return;
            } else {
                $response->set('success', false);
                $response->set('message', '您上传的不是有效的图像文件！');
                $response->json();
                return;
            }
        } else {
            $errorDesc = FileUpload::errorDescription($file['error']);
            $response->set('success', false);
            $response->set('message', '上传失败' . '(' . $errorDesc . ')');
            $response->json();
            return;
        }
    }



}