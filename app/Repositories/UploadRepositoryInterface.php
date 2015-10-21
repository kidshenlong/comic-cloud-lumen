<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 21/10/15
 * Time: 01:11
 */

namespace Repositories;


interface UploadRepositoryInterface
{
    public function getAllUploads();

    public function getUpload($id);

}