<?php

namespace Be\App\System\AdminController;

use Be\App\System\AdminService\AdminPlugin\Form\FormItemUEditor\Uploader;
use Be\Be;
use Be\Util\FileSystem\FileSize;
use Be\Util\Net\FileUpload;

/**
 * @BePermissionGroup("*")
 */
class AdminPlugin
{

    public function uploadFile()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $file = $request->files('file');
        if ($file['error'] == 0) {
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

            $newFileName = date('YmdHis') . '-' . \Be\Util\Random::simple(10) . '.' . $ext;
            $newFilePath = Be::getRuntime()->getUploadPath() . '/tmp/' . $newFileName;

            $dir = dirname($newFilePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                @chmod($dir, 0755);
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
        if ($file['error'] == 0) {

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
                $maxWidth = $request->post('maxWidth', 0, 'int');
                $maxHeight = $request->post('maxHeight', 0, 'int');

                if ($maxWidth > 0 && $maxHeight> 0) {
                    if ($libImage->getWidth() > $maxWidth || $libImage->getheight() > $maxHeight) {
                        $libImage->resize($maxWidth, $maxHeight, 'center');
                    }
                }

                $newImageName = date('YmdHis') . '-' . \Be\Util\Random::simple(10) . '.' . $libImage->getType();
                $newImagePath = Be::getRuntime()->getUploadPath() . '/tmp/' . $newImageName;

                $dir = dirname($newImagePath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                    @chmod($dir, 0755);
                }

                if ($libImage->save($newImagePath)) {
                    $newImageUrl = Be::getRequest()->getUploadUrl(). '/tmp/' . $newImageName;
                    $response->set('newValue', $newImageName);
                    $response->set('url', $newImageUrl);
                    $response->set('success', true);
                    $response->set('message', '上传成功！');
                    $response->json();
                    return;
                }
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
        if ($file['error'] == 0) {

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
                $maxWidth = $request->post('maxWidth', 0, 'int');
                $maxHeight = $request->post('maxHeight', 0, 'int');
                $filename = $request->post('filename', '');

                if ($maxWidth > 0 && $maxHeight> 0) {
                    if ($libImage->getWidth() > $maxWidth || $libImage->getheight() > $maxHeight) {
                        $libImage->resize($maxWidth, $maxHeight, 'scale');
                    }
                }

                $newImageName = null;
                if ($filename) {
                    if (strpos($filename, '{datetime}') !== false) {
                        $filename = str_replace('{datetime}', date('YmdHis'), $filename);
                    }

                    if (strpos($filename, '{random}') !== false) {
                        $random = \Be\Util\Random::simple(10);
                        $filename = str_replace('{random}', $random, $filename);
                    }

                    $newImageName = $filename . '.' . $libImage->getType();
                } else {
                    $newImageName = date('YmdHis') . '-' . \Be\Util\Random::simple(10) . '.' . $libImage->getType();
                }
                $newImagePath = Be::getRuntime()->getUploadPath() . '/tmp/' . $newImageName;

                $dir = dirname($newImagePath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                    @chmod($dir, 0755);
                }

                if ($libImage->save($newImagePath)) {
                    $newImageUrl = Be::getRequest()->getUploadUrl(). '/tmp/' . $newImageName;
                    $response->set('newValue', $newImageName);
                    $response->set('url', $newImageUrl);
                    $response->set('success', true);
                    $response->set('message', '上传成功！');
                    $response->json();
                    return;
                }
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

    public function formItemUEditor() {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $action = $request->get('action');

        $path = Be::getRuntime()->getRootPath() .  Be::getProperty('App.System')->getPath() . '/AdminService/AdminPlugin/Form/FormItemUEditor/config.json';
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($path)), true);

        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                /* 上传配置 */
                $base64 = "upload";
                switch (htmlspecialchars($action)) {
                    case 'uploadimage':
                        $config = array(
                            "pathFormat" => $CONFIG['imagePathFormat'],
                            "maxSize" => $CONFIG['imageMaxSize'],
                            "allowFiles" => $CONFIG['imageAllowFiles']
                        );
                        $fieldName = $CONFIG['imageFieldName'];
                        break;
                    case 'uploadscrawl':
                        $config = array(
                            "pathFormat" => $CONFIG['scrawlPathFormat'],
                            "maxSize" => $CONFIG['scrawlMaxSize'],
                            "allowFiles" => $CONFIG['scrawlAllowFiles'],
                            "oriName" => "scrawl.png"
                        );
                        $fieldName = $CONFIG['scrawlFieldName'];
                        $base64 = "base64";
                        break;
                    case 'uploadvideo':
                        $config = array(
                            "pathFormat" => $CONFIG['videoPathFormat'],
                            "maxSize" => $CONFIG['videoMaxSize'],
                            "allowFiles" => $CONFIG['videoAllowFiles']
                        );
                        $fieldName = $CONFIG['videoFieldName'];
                        break;
                    case 'uploadfile':
                    default:
                        $config = array(
                            "pathFormat" => $CONFIG['filePathFormat'],
                            "maxSize" => $CONFIG['fileMaxSize'],
                            "allowFiles" => $CONFIG['fileAllowFiles']
                        );
                        $fieldName = $CONFIG['fileFieldName'];
                        break;
                }

                /* 生成上传实例对象并完成上传 */
                $up = new Uploader($fieldName, $config, $base64);

                /**
                 * 得到上传文件所对应的各个参数,数组结构
                 * array(
                 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
                 *     "url" => "",            //返回的地址
                 *     "title" => "",          //新文件名
                 *     "original" => "",       //原始文件名
                 *     "type" => ""            //文件类型
                 *     "size" => "",           //文件大小
                 * )
                 */

                /* 返回数据 */
                $result = json_encode($up->getFileInfo());

                break;

            /* 列出图片 */
            case 'listimage':
            case 'listfile':

                /* 判断类型 */
                switch ($action) {
                    /* 列出文件 */
                    case 'listfile':
                        $allowFiles = $CONFIG['fileManagerAllowFiles'];
                        $listSize = $CONFIG['fileManagerListSize'];
                        $path = $CONFIG['fileManagerListPath'];
                        break;
                    /* 列出图片 */
                    case 'listimage':
                    default:
                        $allowFiles = $CONFIG['imageManagerAllowFiles'];
                        $listSize = $CONFIG['imageManagerListSize'];
                        $path = $CONFIG['imageManagerListPath'];
                }
                $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

                /* 获取参数 */
                $size = $request->get('size');
                $start = $request->get('start');
                $size = $size ? htmlspecialchars($size) : $listSize;
                $start = $start ? htmlspecialchars($start) : 0;
                $end = $start + $size;

                /* 获取文件列表 */
                //$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
                $path = Be::getRuntime()->getRootPath() . (substr($path, 0, 1) == "/" ? "":"/") . $path;
                $files = $this->getfiles($path, $allowFiles);
                if (!count($files)) {
                    return json_encode(array(
                        "state" => "no match file",
                        "list" => array(),
                        "start" => $start,
                        "total" => count($files)
                    ));
                }

                /* 获取指定范围的列表 */
                $len = count($files);
                for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
                    $list[] = $files[$i];
                }
                //倒序
                //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
                //    $list[] = $files[$i];
                //}

                /* 返回数据 */
                $result = json_encode(array(
                    "state" => "SUCCESS",
                    "list" => $list,
                    "start" => $start,
                    "total" => count($files)
                ));
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                /* 上传配置 */
                $config = array(
                    "pathFormat" => $CONFIG['catcherPathFormat'],
                    "maxSize" => $CONFIG['catcherMaxSize'],
                    "allowFiles" => $CONFIG['catcherAllowFiles'],
                    "oriName" => "remote.png"
                );
                $fieldName = $CONFIG['catcherFieldName'];

                /* 抓取远程图片 */
                $list = array();
                $source = $request->post($fieldName);
                if (!$source) {
                    $source = $request->get($fieldName);
                }
                foreach ($source as $imgUrl) {
                    $item = new Uploader($imgUrl, $config, "remote");
                    $info = $item->getFileInfo();
                    array_push($list, array(
                        "state" => $info["state"],
                        "url" => $info["url"],
                        "size" => $info["size"],
                        "title" => htmlspecialchars($info["title"]),
                        "original" => htmlspecialchars($info["original"]),
                        "source" => htmlspecialchars($imgUrl)
                    ));
                }

                /* 返回抓取数据 */
                $result = json_encode(array(
                    'state'=> count($list) ? 'SUCCESS':'ERROR',
                    'list'=> $list
                ));

                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        $callback = $request->get('callback');
        /* 输出结果 */
        if ($callback !== null) {
            if (preg_match("/^[\w_]+$/",$callback)) {
                $response->end(htmlspecialchars($callback) . '(' . $result . ')');
            } else {
                $response->end(json_encode(array(
                    'state'=> 'callback参数不合法'
                )));
            }
        } else {
            $response->end($result);
        }
    }



    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return [];
        $rootPath = Be::getRuntime()->getRootPath();
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($rootPath)),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }
}