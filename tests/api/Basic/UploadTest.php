<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 09/10/15
 * Time: 22:53
 */

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Rhumsaa\Uuid\Uuid;
use Aws\Laravel\AwsFacade as AWS;

class UploadTest extends ApiTester
{
    use DatabaseMigrations;

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_must_be_authenticated()
    {
        $this->get($this->basic_upload_endpoint . str_random(32))->seeJson();
        $this->assertResponseStatus(401);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_does_not_accept_patch_or_delete_requests()
    {
        $this->patch($this->basic_upload_endpoint)->seeJson();
        $this->assertResponseStatus(405);

        $this->delete($this->basic_upload_endpoint)->seeJson();
        $this->assertResponseStatus(405);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_does_not_accept_post_requests_to_a_specific_upload()
    {
        $this->seed();

        $upload = factory(App\Models\Upload::class)->create(['user_id' => 1]);

        $this->post($this->basic_upload_endpoint.$upload->id, ['HTTP_Authorization' => 'Bearer '. $this->test_basic_access_token])->seeJson();

        $this->assertResponseStatus(405);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_creates_upload()
    {
        $this->seed();

        Storage::shouldReceive('disk->put');

        AWS::shouldReceive('createClient->getCommand');
        AWS::shouldReceive('createClient->createPresignedRequest->getUri');
        AWS::shouldReceive('createClient->getObjectUrl')->andReturn('value');
        AWS::shouldReceive('Lambda');
        AWS::shouldReceive('Lambda->invokeAsync');

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );
		
        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => Uuid::uuid4()->toString(),
            "comic_id" => Uuid::uuid4()->toString(),
            "series_title" => "test",
            "series_start_year" => "2015",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);
        $this->assertResponseStatus(201);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_be_a_specific_size()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }


    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_series_id()
    {
        $this->seed();

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "comic_id" => Uuid::uuid4()->toString(),
            "series_title" => "test",
            "series_start_year" => "2015",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);
        $this->assertResponseStatus(400);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_comic_id()
    {
        $this->seed();

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => Uuid::uuid4()->toString(),
            "series_title" => "test",
            "series_start_year" => "2015",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);
        $this->assertResponseStatus(400);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_series_title()
    {
        $this->seed();

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => Uuid::uuid4()->toString(),
            "comic_id" => Uuid::uuid4()->toString(),
            "series_start_year" => "2015",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);
        $this->assertResponseStatus(400);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_series_start_year()
    {
        $this->seed();

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => Uuid::uuid4()->toString(),
            "comic_id" => Uuid::uuid4()->toString(),
            "series_title" => "test",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);
        $this->assertResponseStatus(400);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_comic_issue()
    {
        $this->seed();

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => Uuid::uuid4()->toString(),
            "comic_id" => Uuid::uuid4()->toString(),
            "series_title" => "test",
            "series_start_year" => "2015"
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);
        $this->assertResponseStatus(400);
    }
    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_a_valid_for_comic__or_series_id()
    {
        $this->seed();

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => "xyz",
            "comic_id" => "xyz",
            "series_title" => "test",
            "series_start_year" => "2015",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);
    }
    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_a_unique_comic_id()
    {
        $this->seed();

        $comic = factory(App\Models\Comic::class)->create([
            'user_id' => 1
        ]);

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => Uuid::uuid4()->toString(),
            "comic_id" => $comic->id,
            "series_title" => "test",
            "series_start_year" => "2015",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);
        $this->assertResponseStatus(400);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_can_have_an_existing_series_id()
    {
        $this->seed();

        Storage::shouldReceive('disk->put');

        AWS::shouldReceive('createClient->getCommand');
        AWS::shouldReceive('createClient->createPresignedRequest->getUri');
        AWS::shouldReceive('createClient->getObjectUrl')->andReturn('value');
        AWS::shouldReceive('Lambda');
        AWS::shouldReceive('Lambda->invokeAsync');

        $series = factory(App\Models\Series::class)->create([
            'user_id' => 1
        ]);

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => $series->id,
            "comic_id" => Uuid::uuid4()->toString(),
            "series_title" => "test",
            "series_start_year" => "2015",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);

        $this->assertResponseStatus(201);

    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_with_an_existing_series_id_must_belong_to_the_user()
    {
        $this->seed();

        $other_user_series = factory(App\Models\Series::class)->create([
            'user_id' => 2
        ]);

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $req = $this->postWithFile($this->basic_upload_endpoint, [
            "series_id" => $other_user_series->id,
            "comic_id" => Uuid::uuid4()->toString(),
            "series_title" => "test",
            "series_start_year" => "2015",
            "comic_issue" => 1
        ], [
            'Authorization' => 'Bearer '. $this->test_basic_access_token
        ], [
            'file' => $file
        ]);

        $this->assertResponseStatus(400);

    }
    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_fetches_all_uploads()
    {

        $this->seed();

        $uploads = factory(App\Models\Upload::class, rand(2,2/*0*/))->create(['user_id' => 1]);

        $uploads = $uploads->toArray();

        if(!isset($uploads[0])) $uploads = [$uploads];//Fix for toArray not returning single objects in collection format

        $this->get($this->basic_upload_endpoint, ['HTTP_Authorization' => 'Bearer '. $this->test_basic_access_token])->seeJson([
            "upload" => $uploads
        ]);

        $this->assertResponseStatus(200);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_fetches_upload()
    {

        $this->seed();

        $upload = factory(App\Models\Upload::class)->create(['user_id' => 1]);

        $this->get($this->basic_upload_endpoint.$upload->id, ['HTTP_Authorization' => 'Bearer '. $this->test_basic_access_token])->seeJson($upload->toArray());

        $this->assertResponseStatus(200);


    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_cannot_fetch_an_upload_that_does_not_exist()
    {

        $this->seed();

        $this->get($this->basic_upload_endpoint.str_random(32), ['HTTP_Authorization' => 'Bearer '. $this->test_basic_access_token])->seeJson();

        $this->assertResponseStatus(404);
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_fetches_user_uploads_only()
    {

        $this->seed();

        $uploads = factory(App\Models\Upload::class, 5)->create(['user_id' => 1]);

        $other_user_upload = factory(App\Models\Upload::class)->create(['user_id' => 2]);

        $this->get($this->basic_upload_endpoint, ['HTTP_Authorization' => 'Bearer '. $this->test_basic_access_token])->seeJson([
            "upload" => $uploads
        ]);

        $this->assertResponseStatus(200);


    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_fetches_user_upload_only()
    {
        $this->seed();

        $other_user_upload = factory(App\Models\Upload::class)->create(['user_id' => 2]);

        $this->get($this->basic_upload_endpoint.$other_user_upload, ['HTTP_Authorization' => 'Bearer '. $this->test_basic_access_token])->seeJson();

        $this->assertResponseStatus(404);
    }
}
