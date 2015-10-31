<?php namespace App\Http\Controllers\Basic;

use App\Models\ComicImage;

use Input;
use Validator;


use App\Http\Controllers\ApiController;
use Intervention\Image\Facades\Image as Image;

class ComicImagesController extends ApiController {

    /**
     * Display the specified resource.
     *
     * @param $image_slug
     * @param int $width
     * @return Response
     */
	public function show($image_slug, $width = 500){

        $currentUser = $this->getUser();

        //$size = (Input::get('size')? (is_numeric(Input::get('size'))? Input::get('size') : 500) : 500);//TODO: Extract this to global config

        $comicImage = ComicImage::where('image_slug', '=', $image_slug)->first();

        if(!$comicImage) {
            return $this->respondNotFound([
                'title' => 'Image Not Found',
                'detail' => 'Image Not Found',
                'status' => 404,
                'code' => ''
            ]);
        }

        $userCbaIds = $currentUser->comics()->lists('comic_book_archive_id')->all();
        $comicCbaIds = $comicImage->comicBookArchives()->lists('comic_book_archive_id')->all();

        foreach($comicCbaIds as $comicCbaId){
            if(!in_array($comicCbaId, $userCbaIds)) {
                return $this->respondNotFound([
                    'title' => 'Image Not Found',
                    'detail' => 'Image Not Found',
                    'status' => 404,
                    'code' => ''
                ]);
            }
        }

        $img = Image::make($comicImage->image_url);

        $imgCache = Image::cache(function($image) use ($img, $width) {
            $image->make($img)->interlace()->resize(null, $width, function ($constraint) { $constraint->aspectRatio(); $constraint->upsize(); });
        }, 60, true);

        //dd($imgCache->response());

        return $imgCache->response();

	}

}
