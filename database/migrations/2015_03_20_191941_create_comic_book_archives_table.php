<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComicBookArchivesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comic_book_archives', function(Blueprint $table)
		{
            $table->increments('id');
            $table->string('upload_id')->length(40);
            $table->foreign('upload_id')->references('id')->on('uploads')->onDelete('cascade');
            $table->text('comic_book_archive_contents')->nullable();//TODO: Nullable may break stuff...
            $table->string('comic_book_archive_hash');
            $table->integer('comic_book_archive_status');
			$table->text('comic_book_archive_permanent_location');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comic_book_archives');
	}

}
