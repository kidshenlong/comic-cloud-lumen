<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 20/10/15
 * Time: 21:50
 */
use Aws\Laravel\AwsFacade as AWS;

function getFileUrl($driver, $file, $expiry = 0 ){
    //Supported: "local", "s3", "rackspace"
    if($driver == "s3"){
        $client = AWS::createClient('s3');
        //if($expiry){
            //$plainUrl = $client->getObjectUrl(env('S3_USER_UPLOADS_BUCKET'), $file, '+10 minutes');
        //}else{
            //$plainUrl = $client->getObjectUrl(env('S3_USER_UPLOADS_BUCKET'), $file);

        $command = $client->getCommand('GetObject', [
            'Bucket' => env('S3_USER_UPLOADS_BUCKET'),
            'Key' => $file,
        ]);

        $request = $client->createPresignedRequest($command, '+10 minutes');

        $plainUrl = $presignedUrl = (string) $request->getUri();

        //}
    }else if($driver == "rackspace"){

    }else{
        dd("Not Supported");//TODO: Throw HTTP Exception
    }
    return $plainUrl;

}