<?php

abstract class Provider
{
    protected $client_id;
    protected $client_secret;
    protected $auth_url;
    protected $user_info_url;
    protected $access_token_url;
    protected $redirect_uri;
    protected $options;
    protected $headers;
    protected $request;

    protected function __construct($client_id, $client_secret, $auth_url,  $access_token_url, $user_info_url, $redirect_uri, $options = [], $headers = [])
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        $this->auth_url =  $auth_url;
        $this->access_token_url = $access_token_url;
        $this->user_info_url = $user_info_url;

        $this->redirect_uri = $redirect_uri;

        $this->options = $options;

        $this->request = new Request();
    }
}
