<?php

function login()
{
    $queryParams = http_build_query(array(
        "client_id" => "621e3b8d1f964",
        "redirect_uri" => "http://localhost:8081/callback",
        "response_type" => "code",
        "scope" => "read,write",
        "state" => bin2hex(random_bytes(16))
    ));
    echo "
        <form action='callback' method='POST'>
            <input type='text' name='username'>
            <input type='text' name='password'>
            <input type='submit' value='Login'>
        </form>
    ";
    echo "<a href=\"http://localhost:8080/auth?{$queryParams}\">Se connecter via Oauth Server</a><br/>";

    $queryParams = http_build_query(array(
        "client_id" => "1010755216459252",
        "redirect_uri" => "http://localhost:8081/fb_callback",
        "response_type" => "code",
        "scope" => "public_profile,email",
        "state" => bin2hex(random_bytes(16))
    ));
    echo "<a href=\"https://www.facebook.com/v2.10/dialog/oauth?{$queryParams}\">Se connecter via Facebook</a><br/>";

    $queryParams = http_build_query(array(
        "client_id" => "e2d2cb46c32a5146d38c",
        "redirect_uri" => "http://localhost:8081/github_callback",
        "scope" => "user",
        "state" => bin2hex(random_bytes(16)),
    ));
    echo "<a href=\"https://github.com/login/oauth/authorize?{$queryParams}\">Se connecter via Github</a><br/>";

    $queryParams = http_build_query(array(
        "client_id" => "998292722169421866",
        "client_secret" => "9NCOMJIG6Wfb0SPBZvEVnQBilMpFBnL5",
        "redirect_uri" => "http://localhost:8081/discord_callback",
        "grant_type" => "authorization_code",
        "scope" => "identify guilds",
        "response_type" => "code",
    ));
    echo "<a href=\"https://discord.com/api/oauth2/authorize?{$queryParams}\">Se connecter via Discord</a><br/>";
}

function callback()
{
    if ($_SERVER["REQUEST_METHOD"] === 'POST') {
        $specifParams = [
            "grant_type" => "password",
            "username" => $_POST["username"],
            "password" => $_POST["password"]
        ];
    } else {
        $specifParams = [
            "grant_type" => "authorization_code",
            "code" => $_GET["code"],
        ];
    }
    $clientId = "621e3b8d1f964";
    $clientSecret = "621e3b8d1f966";
    $redirectUri = "http://localhost:8081/callback";
    $data = http_build_query(array_merge([
        "redirect_uri" => $redirectUri,
        "client_id" => $clientId,
        "client_secret" => $clientSecret
    ], $specifParams));
    $url = "http://oauth-server:8080/token?{$data}";
    $result = file_get_contents($url);
    $result = json_decode($result, true);
    $accessToken = $result['access_token'];

    $url = "http://oauth-server:8080/me";
    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $accessToken
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);
    echo "Hello {$result['lastname']}";
}

function fbcallback()
{
    $specifParams = [
        "grant_type" => "authorization_code",
        "code" => $_GET["code"],
    ];
    $clientId = "1010755216459252";
    $clientSecret = "b0c27b63308d46ae5d236d2bd691921b";
    $redirectUri = "http://localhost:8081/fb_callback";
    $data = http_build_query(array_merge([
        "redirect_uri" => $redirectUri,
        "client_id" => $clientId,
        "client_secret" => $clientSecret
    ], $specifParams));
    $url = "https://graph.facebook.com/v2.10/oauth/access_token?{$data}";
    $result = file_get_contents($url);
    $result = json_decode($result, true);
    $accessToken = $result['access_token'];

    $url = "https://graph.facebook.com/v2.10/me";
    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $accessToken
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);
    echo "Hello {$result['name']}";
}

function githubcallback()
{
    $client_id = 'e2d2cb46c32a5146d38c';
    $client_secret = 'e032209c45d43dbcb25dce1a5ff323e42054d49f';
    $redirect_uri = 'http://localhost:8081/github_callback';
    $authorization_code = $_GET['code'];

    if (!$authorization_code) {
        die('something went wrong!');
    }

    $url = 'https://github.com/login/oauth/access_token';
    $data = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'code' => $authorization_code
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    echo $result;
}

function discordcallback()
{
    $specifParams = [
        "grant_type" => "authorization_code",
        "code" => $_GET["code"],
    ];
    $data = http_build_query(array_merge([
        "redirect_uri" => "http://localhost:8081/discord_callback",
        "client_id" => "998292722169421866",
        "client_secret" => "9NCOMJIG6Wfb0SPBZvEVnQBilMpFBnL5"
    ], $specifParams));

    $url = "https://discord.com/api/oauth2/token?{$data}";
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $data
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);
    $accessToken = $result['access_token'];

    $url = "https://discord.com/api/users/@me";
    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $accessToken
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);

    echo "Salut {$result['username']}";
}

$route = $_SERVER['REQUEST_URI'];
switch (strtok($route, "?")) {
    case '/login':
        login();
        break;
    case '/callback':
        callback();
        break;
    case '/fb_callback':
        fbcallback();
        break;
    case '/github_callback':
        githubcallback();
        break;
    case '/discord_callback':
        discordcallback();
        break;
    default:
        echo '404';
        break;
}
