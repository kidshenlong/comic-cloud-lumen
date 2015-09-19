<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 27/08/15
 * Time: 23:40
 */

class ApiTester extends TestCase {

    protected $web_client_id = 'SBziat92Is6qqShG';
    protected $web_client_secret = 'dVPoCStWKNuAgsZagS21lqTKklpbVF8z';
    protected $admin_web_client_id = 'PJQG0e3tOKWibQAS';
    protected $admin_web_client_secret = 'WDOMm55MIsz4DoExTEnpyuZ1Nq6khZLN';
    protected $lambda_processor_client_id = 'r9kO96j16pDdmQf9';
    protected $lambda_processor_client_secret = 'jeeSHlMdKO1wHhVtGzCmUwMaH0CbzJRy';

    protected $test_basic_access_token = "y2ZRXZridqzVZP0mIzlaWBoQmLJplvqCcXmKOt4j";
    protected $test_admin_access_token = "iw8yKb073hI0O8szPou8ZliIlvzLHS9sPrT4WmmJ";
    protected $test_processor_access_token = "m7wQwuDdCq2FQvW2tjzALUnVc0KZe2YogLaxSOA6";

    protected $oauth_endpoint = "/oauth/access_token/";
    protected $register_endpoint = "/auth/register/";

    protected $basic_comic_image_endpoint = "/v0.1/images/";
    protected $admin_comic_image_endpoint = "/admin/images/";
    protected $processor_comic_image_endpoint = "/processor/images/";

    protected $basic_comic_endpoint = "/v0.1/comics/";
    protected $admin_comic_endpoint = "/admin/comics/";

    protected $basic_series_endpoint = "/v0.1/series/";
    protected $admin_series_endpoint = "/admin/series/";

    protected $meta_endpoint = "/meta";

    protected $admin_comic_book_archive_endpoint = "/admin/comicbookarchives/";
    protected $processor_comic_book_archive_endpoint = "/processor/comicbookarchives/";

    /**
     * Visit the given URI with a JSON request.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return $this
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        $content = json_encode($data);
        $headers = array_merge([
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);
        $this->call(
            $method, $uri, [], [], [], $this->transformHeadersToServerVars($headers), $content
        );
        return $this;
    }

}