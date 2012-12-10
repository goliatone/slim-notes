<?php
class YotoController
{
    public function __construct($request, $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }
    public function tags()
    {
        echo "Hello tags!";
    }
}
