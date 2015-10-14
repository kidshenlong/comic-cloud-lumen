<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 09/10/15
 * Time: 22:53
 */

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Rhumsaa\Uuid\Uuid;

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
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_does_not_accept_post_requests_to_a_specific_upload()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group lolz
     * @group basic
     * @group upload-test
     */
    public function test_it_creates_upload()
    {
        $this->seed();

        //$this->markTestIncomplete('This test has not been implemented yet.');

        /*AWS::shouldReceive('get')
            ->once()
            ->with('key')
            ->andReturn('value');*/

        $file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path( 'test files/test-comic-6-pages.cbz' ), 'test-comic-6-pages.cbz', 'application/zip', 1000, null, TRUE );

        $this->postWithFile($this->basic_upload_endpoint, [
            "exists" => false,
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
    public function test_uploads_must_have_match_data_exists()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_series_id()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_comic_id()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_series_title()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_series_start_year()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_uploads_must_have_match_data_comic_issue()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_fetches_all_uploads()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group lolz
     * @group basic
     * @group upload-test
     */
    public function test_it_fetches_upload()
    {
        //$this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_cannot_fetch_an_upload_that_does_not_exist()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_fetches_user_uploads_only()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_fetches_user_upload_only()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
