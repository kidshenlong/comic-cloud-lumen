<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model {

    public $incrementing = false;

    protected $fillable = [];

    protected $guarded = ['updated_at', 'created_at'];

    protected $hidden = ['user_id', 'file_upload_name', 'match_data', 'file_original_file_type', 'file_permanent_location', 'updated_at'];

    protected $casts = ['file_size' => 'integer'];

	//
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function ComicBookArchives()
    {
        return $this->hasMany('App\Models\ComicBookArchive');
    }

}
