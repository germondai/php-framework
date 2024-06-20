<?php

declare(strict_types=1);

namespace Api\Model;

use Api\Controller\Api;
use Utils\Helper;

class MediaModel extends Api
{
    public function actionUploadFile()
    {
        dumpe($this->params);

        $medias = [];
        foreach ($_FILES as $file)
            $medias[] = Helper::uploadFile($file, Helper::getBasePath() . 'public/uploads/', 80);

        foreach ($medias as &$m)
            $this->em->persist($m);

        return $this->em->flush();
    }
}
