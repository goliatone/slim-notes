*Github API Wrapper*
=================================================
-------------------------------------------------
A minimal PHP wrapper created for making interaction with the Github API(v3) easy and predictable.

http://developer.github.com/v3/

*OAuth Authentication*
-------------------------------------------------
You need a Github application set up first. Get the Client ID and Client Secret from your application's settings page.

Example authentication:
<pre>
// ID & Secret shown on your Github app's page.
$clientID = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$github = new GithubOAuth( $clientID, $clientSecret );

// Scope of permissions requested
$scope = array( 'user', 'repo', 'gist' );
// Requests access & redirects to the URL defined in your Github application's settings
$github->requestAccessCode( $scope );

// Retrieve access code from URL after redirect, and exchange for access token
$github->setTokenFromCode( $_GET['code'] );
</pre>

*Making Requests*
=================================================
-------------------------------------------------
Use the API documentation as a reference.  There are primarily 2 or 3 parts to each request:
<ul> 
    <li><b>HTTP Verb</b> ... See http://developer.github.com/v3/#http-verbs</li>
    <li><b>Path</b> ... E.g. For <code>http://api.github.com/user/repos</code>, the path is <code>'user/repos'</code></li>
    <li><b>URL Parameters</b> ... E.g. <code>array( 'foo'=>'bar', 'herp'=>'derp' );</code> becomes <code>foo=bar&herp=derp</code></li>
</ul> 


*Example Request for Authenticated User's Data*
-------------------------------------------------
<pre>
$response = $github->executeRequest( 'GET', 'user' );
</pre>
<h4>The response...</h4>
<pre>
{
    "headers": {
        "url": "https://api.github.com/user?access_token=[YOUR_TOKEN]",
        "content_type": "application/json; charset=utf-8",
        "http_code": 200,
        "header_size": 347,
        "request_size": 111,
        "filetime": -1,
        "ssl_verify_result": 0,
        "redirect_count": 0,
        "total_time": 0.110919,
        "namelookup_time": 0.00618,
        "connect_time": 0.014401,
        "pretransfer_time": 0.054725,
        "size_upload": 0,
        "size_download": 817,
        "speed_download": 7365,
        "speed_upload": 0,
        "download_content_length": 817,
        "upload_content_length": 0,
        "starttransfer_time": 0.110853,
        "redirect_time": 0
    },
    "response": {
        "type": "User",
        "url": "https://api.github.com/users/username",
        "private_gists": 2,
        "created_at": "2011-07-07T22:20:48Z",
        "email": "example@gmail.com",
        "html_url": "https://github.com/username",
        "gravatar_id": "123123123123123123123123123",
        "collaborators": 1,
        "hireable": false,
        "disk_usage": 13348,
        "total_private_repos": 3,
        "following": 4,
        "blog": "http://www.yoursitename.com/",
        "bio": null,
        "avatar_url": "https://www.avatar.com/pathtoyouravatar.png",
        "login": "username",
        "owned_private_repos": 3,
        "followers": 1,
        "name": "Your Name",
        "plan": {
            "private_repos": 10,
            "collaborators": 3,
            "space": 614400,
            "name": "micro"
        },
        "location": "Your City, NY",
        "id": 902312,
        "public_repos": 0,
        "public_gists": 17,
        "company": "Your Company Name, LLC"
    }
}
</pre>


*Example Request Creating a New Repository*
-------------------------------------------------
<pre>
$query = array(
    'name'         => $repoName,            // required
    'description'  => $repoDescription,     // optional
    'homepage'     => $repoHomePage,        // optional
    'private'      => $repoPrivacy          // optional
    'has_issues'   => $repoIssuesEnabled    // optional
    'has_wiki'     => $repoWikiEnabled      // optional
    'has_downloads'=> $repoDownloadsEnabled // optional
    'team_id'      => $collabTeamID         // optional
);
$response = $github->executeRequest('POST','user/repos',$query);
</pre>

<h4>The response...</h4>
<pre>
{
    "headers": {
        "url": "https://api.github.com/user/repos?access_token=[YOUR_TOKEN]",
        "content_type": "application/json; charset=utf-8",
        "http_code": 201,
        "header_size": 413,
        "request_size": 358,
        "filetime": -1,
        "ssl_verify_result": 0,
        "redirect_count": 0,
        "total_time": 0.280588,
        "namelookup_time": 0.014798,
        "connect_time": 0.023159,
        "pretransfer_time": 0.065582,
        "size_upload": 170,
        "size_download": 975,
        "speed_download": 3474,
        "speed_upload": 605,
        "download_content_length": 975,
        "upload_content_length": 170,
        "starttransfer_time": 0.280533,
        "redirect_time": 0
    },
    "response": {
        "url": "https://api.github.com/repos/username/Example",
        "watchers": 1,
        "has_issues": true,
        "created_at": "2012-02-14T03:40:49Z",
        "html_url": "https://github.com/username/Example",
        "has_downloads": true,
        "ssh_url": "git@github.com:username/Example.git",
        "svn_url": "https://github.com/username/Example",
        "description": "ExampleDescription",
        "mirror_url": null,
        "clone_url": "https://github.com/username/Example.git",
        "forks": 1,
        "fork": false,
        "has_wiki": true,
        "private": false,
        "homepage": "www.examplepage.com",
        "size": 0,
        "updated_at": "2012-02-14T03:40:49Z",
        "owner": {
            "url": "https://api.github.com/users/username",
            "avatar_url": "https://secure.gravatar.com/avatar/someimage.png",
            "gravatar_id": "948f18791231231231231231231",
            "login": "username",
            "id": 901650
        },
        "name": "Example",
        "open_issues": 0,
        "master_branch": null,
        "pushed_at": null,
        "id": 3436770,
        "git_url": "git://github.com/username/Example.git",
        "language": null
    }
}
</pre>