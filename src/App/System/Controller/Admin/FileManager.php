<?php
namespace Be\App\System\Controller\Admin;

use Be\Be;

// 文件管理器
class FileManager extends Auth
{

    public function browser()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $session = Be::getSession();

        // 要查看的路径
        $path = $request->post('path', '');

        // 显示方式 thumbnail 缩略图 list 详细列表
        $view = $request->post('view', '');

        // 排序
        $sort = $request->post('sort', '');

        // 只显示图像
        $filterImage = $request->get('filterImage', -1, 'int');

        $srcId = $request->get('srcId', '');


        // session 缓存用户选择
        if ($path == '') {
            $sessionPath = $session->get('systemFileManagerPath');
            if ($sessionPath != '') $path = $sessionPath;
        } else {
            if ($path == '/') $path = '';
            $session->set('systemFileManagerPath', $path);
        }

        if ($view == '') {
            $view = 'thumbnail';
            $sessionView = $session->get('systemFileManagerView');
            if ($sessionView != '' && ($sessionView == 'thumbnail' || $sessionView == 'list')) $view = $sessionView;
        } else {
            if ($view != 'thumbnail' && $view != 'list') $view = 'thumbnail';
            $session->set('systemFileManagerView', $view);
        }

        if ($sort == '') {
            $sessionSort = $session->get('systemFileManagerSort');
            if ($sessionSort == '') {
                $sort = 'name';
            } else {
                $sort = $sessionSort;
            }

        } else {
            $session->set('systemFileManagerSort', $sort);
        }

        if ($filterImage == -1) {
            $filterImage = 0;
            $sessionFilterImage = $session->get('systemFileManagerFilterImage', -1);
            if ($sessionFilterImage != -1 && ($sessionFilterImage == 0 || $sessionFilterImage == 1)) $filterImage = $sessionFilterImage;
        } else {
            if ($filterImage != 0 && $filterImage != 1) $filterImage = 0;
            $session->set('systemFileManagerFilterImage', $filterImage);
        }

        if ($srcId == '') {
            $srcId = $session->get('systemFileManagerSrcId', '');
        } elseif ($srcId == 'img') {
            $srcId = '';
            $session->set('systemFileManagerSrcId', $srcId);
        } else {
            $session->set('systemFileManagerSrcId', $srcId);
        }

        $option = array();
        $option['path'] = $path;
        $option['view'] = $view;
        $option['sort'] = $sort;
        $option['filterImage'] = $filterImage;

        $serviceSystemFileManager = Be::getService('App.System.Admin.FileManager');
        $files = $serviceSystemFileManager->getFiles($option);

        $response->set('path', $path);
        $response->set('view', $view);
        $response->set('sort', $sort);
        $response->set('filterImage', $filterImage);
        $response->set('srcId', $srcId);

        $response->set('files', $files);
        $response->display();
    }

    public function createDir()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $dirName = $request->post('dirName', '');
        $return = beAdminUrl('System.FileManager.browser');

        $serviceSystemFileManager = Be::getService('App.System.Admin.FileManager');
        if ($serviceSystemFileManager->createDir($dirName)) {
            $response->success('创建文件夹(' . $dirName . ')成功！', $return);
        } else {
            $response->error($serviceSystemFileManager->getError(), $return);
        }
    }

    // 删除文件夹
    public function deleteDir()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $dirName = $request->get('dirName', '');
        $return = beAdminUrl('System.FileManager.browser');

        $serviceSystemFileManager = Be::getService('App.System.Admin.FileManager');
        if ($serviceSystemFileManager->deleteDir($dirName)) {
            $response->success('删除文件夹(' . $dirName . ')成功！', $return);
        } else {
            $response->error($serviceSystemFileManager->getError(), $return);
        }
    }

    // 修改文件夹名称
    public function editDirName()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $oldDirName = $request->post('oldDirName', '');
        $newDirName = $request->post('newDirName', '');
        $return = beAdminUrl('System.FileManager.browser');

        $serviceSystemFileManager = Be::getService('App.System.Admin.FileManager');
        if ($serviceSystemFileManager->editDirName($oldDirName, $newDirName)) {
            $response->success('重命名文件夹成功！', $return);
        } else {
            $response->error($serviceSystemFileManager->getError(), $return);
        }
    }


    public function uploadFile()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $configSystem = Be::getConfig('App.System.System');

        $return = beAdminUrl('System.FileManager.browser');

        $file = $_FILES['file'];
        if ($file['error'] == 0) {
            $fileName = $file['name'];

            $type = strtolower(substr(strrchr($fileName, '.'), 1));
            if (!in_array($type, $configSystem->allowUploadFileTypes)) {
                $response->error('不允许上传(' . $type . ')格式的文件！', $return);
            }

            if (strpos($fileName, '/') !== false) {
                $response->error('文件名称不合法！', $return);
            }

            $serviceSystemFileManager = Be::getService('App.System.Admin.FileManager');
            $absPath = $serviceSystemFileManager->getAbsPath();
            if ($absPath == false) {
                $response->error($serviceSystemFileManager->getError(), $return);
            }

            $dstPath = $absPath . '/' . $fileName;

            $rename = false;
            if (file_exists($dstPath)) {
                $i = 1;
                $name = substr($fileName, 0, strrpos($fileName, '.'));
                while (file_exists($absPath . '/' . $name . '_' . $i . '.' . $type)) {
                    $i++;
                }

                $dstPath = $absPath . '/' . $name . '_' . $i . '.' . $type;

                $rename = $name . '_' . $i . '.' . $type;
            }

            if (move_uploaded_file($file['tmpName'], $dstPath)) {
                $watermark = $request->post('watermark', 0, 'int');
                if ($watermark == 1 && in_array($type, $configSystem->allowUploadImageTypes)) {
                    $serviceSystem = Be::getService('App.System.Admin.Watermark');
                    $serviceSystem->watermark($dstPath);
                }

                if ($rename == false) {
                    $response->success('上传文件成功！', $return);
                } else {
                    $response->success('有同名文件，新上传的文件已更名为：' . $rename . '！', $return);
                }
            } else {
                $response->error('上传失败！', $return);
            }
        } else {

            $uploadErrors = array(
                '1' => '您上传的文件过大！',
                '2' => '您上传的文件过大！',
                '3' => '文件只有部分被上传！',
                '4' => '没有文件被上传！',
                '5' => '上传的文件大小为 0！'
            );

            $error = '';
            if (array_key_exists($file['error'], $uploadErrors)) {
                $error = $uploadErrors[$file['error']];
            } else {
                $error = '错误代码：' . $file['error'];
            }

            $response->error('上传失败' . '(' . $error . ')', $return);
        }
    }

    // 删除文件
    public function deleteFile()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $fileName = $request->get('fileName', '');
        $return = beAdminUrl('System.FileManager.browser');

        $serviceSystemFileManager = Be::getService('App.System.Admin.FileManager');
        if ($serviceSystemFileManager->deleteFile($fileName)) {
            $response->success('删除文件(' . $fileName . ')成功！', $return);
        } else {
            $response->error($serviceSystemFileManager->getError(), $return);
        }
    }

    // 修改文件名称
    public function editFileName()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $oldFileName = $request->post('oldFileName', '');
        $newFileName = $request->post('newFileName', '');

        $serviceSystemFileManager = Be::getService('App.System.Admin.FileManager');
        if ($serviceSystemFileManager->editFileName($oldFileName, $newFileName)) {
            $response->success('重命名文件成功！', beAdminUrl('System.FileManager.browser'));
        } else {
            $response->error($serviceSystemFileManager->getError(), beAdminUrl('System.FileManager.browser'));
        }

    }

    public function downloadFile()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $fileName = $request->get('fileName', '');

        $serviceSystemFileManager = Be::getService('App.System.Admin.FileManager');
        $absFilePath = $serviceSystemFileManager->getAbsFilePath($fileName);
        if ($absFilePath == false) {
            $response->error($serviceSystemFileManager->getError());
        } else {
            header('Pragma: private');
            header('Cache-control: private, must-revalidate');
            header("Content-Type: application/octet-stream");
            header("Content-Length: " . (string)(filesize($absFilePath)));
            header('Content-Disposition: attachment; filename="' . ($fileName) . '"');
            readfile($absFilePath);
        }
    }

}
