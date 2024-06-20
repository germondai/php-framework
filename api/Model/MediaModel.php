<?php

declare(strict_types=1);

namespace Api\Model;

use Api\Controller\Api;
use Utils\Helper;

class MediaModel extends Api
{
    public function actionUploadFile()
    {
        $res = [];
        foreach ($_FILES as $file)
            $res[] = Helper::uploadFile($file, Helper::getBasePath() . 'public/uploads/', 80);
        return $res;
    }
}
