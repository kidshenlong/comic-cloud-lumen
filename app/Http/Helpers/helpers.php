<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 20/10/15
 * Time: 21:50
 */

use App\Models\Comic;
use App\Models\ComicBookArchive;
use App\Models\Series;
use Aws\Laravel\AwsFacade as AWS;


/**
 * @return ComicBookArchive
 */
function createComicBookArchive($upload_id, $cba_hash, $permanent_location)
{
    $cba = new ComicBookArchive();
    $cba->upload_id = $upload_id;
    $cba->comic_book_archive_hash = $cba_hash;
    $cba->comic_book_archive_status = 0;
    $cba->comic_book_archive_permanent_location = $permanent_location;
    $cba->save();
    return $cba;
}
/**
 * @param $match_data
 * @return Series
 */
function createSeries($user_id, $match_data){
    $series = new Series;
    $newSeriesID = $match_data['series_id'];
    $series->id = $newSeriesID;
    $series->series_title = $match_data['series_title'];
    $series->series_start_year = $match_data['series_start_year'];
    $series->series_publisher = 'Unknown';
    $series->user_id = $user_id;
    $series->save();
    return $series;
}
/**
 * @param $comic_info
 * @return Comic
 */
function createComic($user_id, $comic_info){
    $cba = ComicBookArchive::find($comic_info['comic_book_archive_id']);

    $newComicID = $comic_info['comic_id'];

    $comic = new Comic;
    $comic->id = $newComicID;
    $comic->comic_issue = $comic_info['comic_issue'];
    $comic->comic_writer = $comic_info['comic_writer'];
    $comic->comic_book_archive_contents = (($cba->comic_book_archive_contents ? $cba->comic_book_archive_contents : ''));
    $comic->user_id = $user_id;
    $comic->series_id = $comic_info['series_id'];
    $comic->comic_book_archive_id = $cba->id;
    $comic->save();

    return $comic;
}


function getFileUrl($driver, $file, $expiry = 0 ){
    //Supported: "local", "s3", "rackspace"
    if($driver == "s3"){
        $client = AWS::createClient('s3');
        if($expiry){
            $plainUrl = $client->getObjectUrl(env('AWS_S3_Uploads'), $file, $expiry);
        }else{
            $plainUrl = $client->getObjectUrl(env('AWS_S3_Uploads'), $file);
        }
    }else if($driver == "rackspace"){

    }else{
        dd("Not Supported");//TODO: Throw HTTP Exception
    }

}