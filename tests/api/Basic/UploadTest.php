<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 09/10/15
 * Time: 22:53
 */

use Illuminate\Foundation\Testing\DatabaseMigrations;

class UploadTest extends ApiTester{
    use DatabaseMigrations;

    /**
     * @group basic
     * @group upload-test
     */
    public function test_it_must_be_authenticated(){
        $this->get($this->basic_upload_endpoint.str_random(32))->seeJson();
        $this->assertResponseStatus(401);
    }
}
