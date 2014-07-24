<?php
require_once('config.php');
require_once('lib/MysqliDb.php');
error_reporting(E_ALL);

$action = '';
$username = '';
$key = '';
$user = null;
$wowza = null;
$data = array();
$header = "Document";

/**
 * Returns the Full List of Tags for this client
 */
    function get_tags_list() {
        global $db;
        global $user;

        $result = array();
        $records = $db -> rawQuery("SELECT distinct tags FROM tvshows where userID=".$user['id']);// Should be modified...

        foreach ($records as $record) {
            if ($record['tags'] == "")
                continue;

            array_push($result, $record['tags']);
        }

        echo json_encode($result);
    }

/**
 * Returns the list of all VOD Videos with all their details
 * @param null

    Video Details
    -title: (tvshows->Title)
    -date: (tvshows->RecordDate)
    -start time: tvshows->FirstStart
    -end time: tvshows->StopTime
    -duration: tvshows->Duration 
    -direct path: “(wowzaclient->WowzaIP)/(tvshows->RecordDate)/(tvshows->Title).mp4”
    -thumbnail path: “admin.mangomolo.com/analytics/Recorder/(userid)/(tvshows->RecordDate)/(tvshows->Title).mp4.jpg
    -youtube link: (tvshows->YoutubeLink) if 0 the video is not uploaded

 */
    function get_vod_list($tags = '') {
        global $db;
        global $user;
        global $wowza;

        // $tags = $_GET['tags'];

        $result = array();
        if (empty($tags))
            $records = $db -> rawQuery("select * from tvshows where `userID`=".$user['id']);
        else
            $records = $db -> rawQuery("select * from tvshows where `tags`='".$tags."' And userID=".$user['id']);

        foreach ($records as $record) {
            $item['title'] = $record['Title'];
            $item['date'] = $record['RecordDate'];
            $item['start_time'] = $record['FirstStart'];
            $item['end_time'] = $record['StopTime'];
            $item['duration'] = $record['Duration'];
            $item['direct_path'] = $wowza['WowzaIP']."/".$record['RecordDate']."/".$record['Title'].".mp4";
            $item['thumbnail_path'] = "admin.mangomolo.com/analytics/Recorder/".$user['id']."/".$record['RecordDate']."/".$record['Title']."mp4.jpg";
            if (empty($record['YoutubeLink']))
                $item['youtube_link'] = 0;
            else
                $item['youtube_link'] = $record['YoutubeLink'];

            array_push($result, $item);
        }

        // return $result;
        echo json_encode($result);
    }


/**
 * Returns the list of all VOD Videos for a specific Date (Can be single date or Range)
 * @param date or date range
 */
function get_vod_by_date() {
    global $db;
    global $user;

    $date1 = $_GET['date1'];
    $date2 = $_GET['date2'];

    $result = array();
    if (empty($date1) && empty($date2))
        $records = $db -> get("tvshows");
    else if (empty($date2))
        $records = $db -> rawQuery("select * from tvshows where `RecordDate`='".$date1."' And userID=".$user['id']);
    else
        $records = $db -> rawQuery("select * from tvshows where `RecordDate` BETWEEN '".$date1."' AND '".$date2."' And userID=".$user['id']);

    foreach ($records as $record) {
        $item['title'] = $record['Title'];
        $item['date'] = $record['RecordDate'];
        $item['start_time'] = $record['FirstStart'];
        $item['end_time'] = $record['StopTime'];
        $item['duration'] = $record['Duration'];
        $item['direct_path'] = $wowza['WowzaIP']."/".$record['RecordDate']."/".$record['Title'].".mp4";
        $item['thumbnail_path'] = "admin.mangomolo.com/analytics/Recorder/".$user['id']."/".$record['RecordDate']."/".$record['Title']."mp4.jpg";
        if (empty($record['YoutubeLink']))
            $item['youtube_link'] = 0;
        else
            $item['youtube_link'] = $record['YoutubeLink'];

        array_push($result, $item);
    }

    echo json_encode($result);
}


/**
 * Returns the list of all VOD Videos for a specific tag
 * @param tag_value
 */ 
function get_vod_by_tag() {
    $tag_value = $_GET['tags'];
    get_vod_list($tag_value);
}

/**
 * Returns the list of all VOD Videos for a specific date and start time
 */ 
function get_vod_by_date_starttime() {
    global $db;
    global $user;

    $date1 = $_GET['date1'];
    $date2 = $_GET['date2'];
    $start_time = $_GET['start_time'];

    $result = array();
    if (empty($date1) && empty($date2))
        $records = $db -> rawQuery("select * from tvshows where `StartTime`='".$start_time."' And userID=".$user['id']);
    else if (empty($date2))
        $records = $db -> rawQuery("select * from tvshows where `RecordDate`='".$date1."' AND `StartTime`='".$start_time."' And userID=".$user['id']);
    else
        $records = $db -> rawQuery("select * from tvshows where `RecordDate` BETWEEN '".$date1."' AND '".$date2."' AND `StartTime`='".$start_time."' And userID=".$user['id']);

    foreach ($records as $record) {
        $item['title'] = $record['Title'];
        $item['date'] = $record['RecordDate'];
        $item['start_time'] = $record['FirstStart'];
        $item['end_time'] = $record['StopTime'];
        $item['duration'] = $record['Duration'];
        $item['direct_path'] = $wowza['WowzaIP']."/".$record['RecordDate']."/".$record['Title'].".mp4";
        $item['thumbnail_path'] = "admin.mangomolo.com/analytics/Recorder/".$user['id']."/".$record['RecordDate']."/".$record['Title']."mp4.jpg";
        if (empty($record['YoutubeLink']))
            $item['youtube_link'] = 0;
        else
            $item['youtube_link'] = $record['YoutubeLink'];

        array_push($result, $item);
    }

    echo json_encode($result);
}


/**
 * Returns the list of all VOD Videos for a specific time interval
 */ 
function get_vod_by_timeinterval() {
    global $db;
    global $user;

    $interval = $_GET['interval'];
    $result = array();

    if (empty($interval))
        $records = $db -> rawQuery("select * from tvshows where `Duration`='0' And userID=".$user['id']);
    else
        $records = $db -> rawQuery("select * from tvshows where `Duration` = '".$interval."' And userID='".$user['id']."'");

    foreach ($records as $record) {
        $item['title'] = $record['Title'];
        $item['date'] = $record['RecordDate'];
        $item['start_time'] = $record['FirstStart'];
        $item['end_time'] = $record['StopTime'];
        $item['duration'] = $record['Duration'];
        $item['direct_path'] = $wowza['WowzaIP']."/".$record['RecordDate']."/".$record['Title'].".mp4";
        $item['thumbnail_path'] = "admin.mangomolo.com/analytics/Recorder/".$user['id']."/".$record['RecordDate']."/".$record['Title']."mp4.jpg";
        if (empty($record['YoutubeLink']))
            $item['youtube_link'] = 0;
        else
            $item['youtube_link'] = $record['YoutubeLink'];

        array_push($result, $item);
    }

    echo json_encode($result);
}
 

/**
 * Returns the top (x) viewed VODs where ‘x’ is a parameter you decide
 */ 
function Get_topviewed() {
    global $db;
    global $user;
    global $wowza;

    // $tags = $_GET['tags'];
    
    $result = array();
    if (empty($tags))
        $records = $db -> rawQuery("select * from tvshows where `userID`=".$user['id']);
    else
        $records = $db -> rawQuery("select * from tvshows where `tags`='".$tags."' And userID=".$user['id']);

    foreach ($records as $record) {
        $item['title'] = $record['Title'];
        $item['date'] = $record['RecordDate'];
        $item['start_time'] = $record['FirstStart'];
        $item['end_time'] = $record['StopTime'];
        $item['duration'] = $record['Duration'];
        $item['direct_path'] = $wowza['WowzaIP']."/".$record['RecordDate']."/".$record['Title'].".mp4";
        $item['thumbnail_path'] = "admin.mangomolo.com/analytics/Recorder/".$user['id']."/".$record['RecordDate']."/".$record['Title']."mp4.jpg";
        if (empty($record['YoutubeLink']))
            $item['youtube_link'] = 0;
        else
            $item['youtube_link'] = $record['YoutubeLink'];

        array_push($result, $item);
    }

    echo json_encode($result);
}
 
/**
 * Returns the top (x) viewed VODs by date where ‘x’ is a parameter you decide
 */
function Get_topviewed_bydate() {
    global $db;
    global $user;

    $date1 = $_GET['date1'];
    $date2 = $_GET['date2'];

    $result = array();
    if (empty($date1) && empty($date2))
        $records = $db -> get("tvshows");
    else if (empty($date2))
        $records = $db -> rawQuery("select * from tvshows where `RecordDate`='".$date1."' And userID=".$user['id']);
    else
        $records = $db -> rawQuery("select * from tvshows where `RecordDate` BETWEEN '".$date1."' AND '".$date2."' And userID=".$user['id']);

    foreach ($records as $record) {
        $item['title'] = $record['Title'];
        $item['date'] = $record['RecordDate'];
        $item['start_time'] = $record['FirstStart'];
        $item['end_time'] = $record['StopTime'];
        $item['duration'] = $record['Duration'];
        $item['direct_path'] = $wowza['WowzaIP']."/".$record['RecordDate']."/".$record['Title'].".mp4";
        $item['thumbnail_path'] = "admin.mangomolo.com/analytics/Recorder/".$user['id']."/".$record['RecordDate']."/".$record['Title']."mp4.jpg";
        if (empty($record['YoutubeLink']))
            $item['youtube_link'] = 0;
        else
            $item['youtube_link'] = $record['YoutubeLink'];

        array_push($result, $item);
    }

    echo json_encode($result);
}
 

/**
 * Returns the top (x) viewed VODs by date and time interval where ‘x’ is a parameter you decide
 */
function Get_topviewed_bydate_time() {
    global $db;
    global $user;
    global $wowza;

    $date1 = $_GET['date1'];
    $date2 = $_GET['date2'];
    $start_time = $_GET['start_time'];

    $result = array();
    if (empty($date1) && empty($date2))
        $records = $db -> rawQuery("select * from tvshows where `StartTime`='".$start_time."' And userID=".$user['id']);
    else if (empty($date2))
        $records = $db -> rawQuery("select * from tvshows where `RecordDate`='".$date1."' AND `StartTime`='".$start_time."' And userID=".$user['id']);
    else
        $records = $db -> rawQuery("select * from tvshows where `RecordDate` BETWEEN '".$date1."' AND '".$date2."' AND `StartTime`='".$start_time."' And userID=".$user['id']);

    foreach ($records as $record) {
        $item['title'] = $record['Title'];
        $item['date'] = $record['RecordDate'];
        $item['start_time'] = $record['FirstStart'];
        $item['end_time'] = $record['StopTime'];
        $item['duration'] = $record['Duration'];
        $item['direct_path'] = $wowza['WowzaIP']."/".$record['RecordDate']."/".$record['Title'].".mp4";
        $item['thumbnail_path'] = "admin.mangomolo.com/analytics/Recorder/".$user['id']."/".$record['RecordDate']."/".$record['Title']."mp4.jpg";
        if (empty($record['YoutubeLink']))
            $item['youtube_link'] = 0;
        else
            $item['youtube_link'] = $record['YoutubeLink'];

        array_push($result, $item);
    }

    echo json_encode($result);
}
 

/**
 * Returns the top viewed (x) VODs of all time
 */
function Get_topviewed_alltime() {
    global $db;
    global $user;

    $date1 = $_GET['date1'];
    $date2 = $_GET['date2'];
    $start_time = $_GET['start_time'];

    $result = array();
    if (empty($date1) && empty($date2))
        $records = $db -> rawQuery("select * from tvshows where userID=".$user['id']);
    else if (empty($date2))
        $records = $db -> rawQuery("select * from tvshows where `RecordDate`='".$date1."' And userID=".$user['id']);
    else
        $records = $db -> rawQuery("select * from tvshows where `RecordDate` BETWEEN '".$date1."' AND '".$date2."' And userID=".$user['id']);

    foreach ($records as $record) {
        $item['title'] = $record['Title'];
        $item['date'] = $record['RecordDate'];
        $item['start_time'] = $record['FirstStart'];
        $item['end_time'] = $record['StopTime'];
        $item['duration'] = $record['Duration'];
        $item['direct_path'] = $wowza['WowzaIP']."/".$record['RecordDate']."/".$record['Title'].".mp4";
        $item['thumbnail_path'] = "admin.mangomolo.com/analytics/Recorder/".$user['id']."/".$record['RecordDate']."/".$record['Title']."mp4.jpg";
        if (empty($record['YoutubeLink']))
            $item['youtube_link'] = 0;
        else
            $item['youtube_link'] = $record['YoutubeLink'];

        array_push($result, $item);
    }

    echo json_encode($result);
}


/**
 * upload video to mangomolo
 */
function post_video() {
    echo "Not finished yet.";
}

/**
 * Authentication
 */
function isAuthenticated() {
    global $db;
    global $user;
    global $wowza;


    $query = "select * from users where `apikey`='".$key."'";
    $result = $db->rawQuery($query);
    if ($result)
    {
        $user = $result[0];
        $wowzas = $db->rawQuery("select * from wowzaclient where id=".$user['id']);
        if (!$wowzas)
        {
            $user = null;
            $wowza = null;
            return false;
        }
        $wowza = $wowzas[0];
        return true;
    }
    else
    {
        $user = null;
        $wowza = null;
        return false;
    }
}

$db = new Mysqlidb ($hostname, $db_username, $password, $dbname);



if ($_GET) {
    $action = $_GET['action'];
    $key = $_GET['key'];

    if (!isAuthenticated($key))
    {
        echo "Authentication failure";
        exit;
    }

    $f = $_GET['action'];
    if (function_exists ($f)) {
        $f();
    }
}

?>






<?php
$action = $_GET['action'];
if (empty($action)) {
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>API Document</title>
    <link href="/public/css/index.css" rel="stylesheet" />
</head>
<body>
    <div id="content">
        <div id="header">
            <a href="http://mangomolo.com/" target="_blank"><img class="logo" src="public/images/logo.png" /></a>
            <p><h2>Mangomolo | Help V 1.0 | you can always email us on <a href="mailto:">hello@mangomolo.com</a></h2><br />
                <em>Written by Mango |&nbsp;Last Updated: July 24, 2014</em></p>

            <ul>
                <li><a href="#get_tags_list">get_tags_list</a></li>
                <li><a href="#get_vod_list">get_vod_list</a></li>
                <li><a href="#get_vod_by_date">get_vod_by_date</a></li>
                <li><a href="#get_vod_by_tag">get_vod_by_tag</a></li>
                <li><a href="#get_vod_by_date_starttime">get_vod_by_date_starttime</a></li>
                <li><a href="#get_vod_by_timeinterval">get_vod_by_timeinterval</a></li>
                <li><a href="#Get_topviewed">get_topviewed</a></li>
                <li><a href="#Get_topviewed_bydate">get_topviewed_bydate</a></li>
                <li><a href="#Get_topviewed_bydate_time">get_topviewed_bydate_time</a></li>
                <li><a href="#Get_topviewed_alltime">get_topviewed_alltime</a></li>
                <li><a href="#post_video">post_video</a></li>
            </ul>
        </div>

        <div id="document">
            <div class="general-information">
                <p class="title">
                    <strong>General Information</strong>
                </p>
                <div class="api-url">
                    <p><strong>API url: </strong>http://admin.mangomolo.com/api/index.php</p>
                </div>
                <div class="default-parameters">
                    <p>Default Parameters</p>
                    <p class="param"><strong>key:</strong>&nbsp;string</p>
                    <p class="param"><strong>action:</strong>&nbsp;string</p>
                </div>
            </div>
            <section class="api-block" id="get_tags_list">
                <p class='title'>get_tags_list</p>
                <p class="summary">Returns the Full List of Tags for this client</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>None</p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>key:    a684eceee7...3286a8</p>
                        <p>action: get_tags_list</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab1">"3on",</li>
                            <li class="tab1">"tag",</li>
                            <li class="tab1">"tessss",</li>
                            <li class="tab1">"news",</li>
                            <li class="tab1">"news, politics",</li>
                            <li class="tab1">"show",</li>
                            <li class="tab1">"politics",</li>
                            <li class="tab1">...</li>
                            <li class="tab1">"documentary"</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="get_vod_list">
                <p class='title'>get_vod_list</p>
                <p class="summary">Returns the list of all VOD Videos with all their details</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>None</p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>key:    a684eceee7...3286a8</p>
                        <p>action: get_vod_list</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"news",</li>
                            <li class="tab2">"date":"2014-06-26",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"news",</li>
                            <li class="tab2">"date":"2014-06-26",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">...</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="get_vod_by_date">
                <p class='title'>get_vod_by_date</p>
                <p class="summary">Returns the list of all VOD Videos for a specific Date (Can be single date or Range)</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>date1:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;string<br/>date2:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;string</p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>action: get_vod_by_date</p>
                        <p>date1:  2014-06-30</p>
                        <p>date2:  2014-07-01</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"news",</li>
                            <li class="tab2">"date":"2014-06-26",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"test1",</li>
                            <li class="tab2">"date":"2014-06-29",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">...</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"sdfsddfsd",</li>
                            <li class="tab2">"date":"2014-06-30",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">}</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="get_vod_by_tag">
                <p class='title'>get_vod_by_tag</p>
                <p class="summary">Returns the list of all VOD Videos for a specific tag</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>tags: string</p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>action: get_vod_by_tag</p>
                        <p>tags:   news</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"news",</li>
                            <li class="tab2">"date":"2014-06-26",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"test1",</li>
                            <li class="tab2">"date":"2014-06-29",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">...</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"sdfsddfsd",</li>
                            <li class="tab2">"date":"2014-06-30",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">}</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="get_vod_by_date_starttime">
                <p class='title'>get_vod_by_date_starttime</p>
                <p class="summary">Returns the list of all VOD Videos for a specific date and start time</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>
                        date1:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;string<br/>
                        date2:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;string<br/>
                        starttime:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;string
                    </p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>actin: get_vod_by_date_starttime</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"news",</li>
                            <li class="tab2">"date":"2014-06-26",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"test1",</li>
                            <li class="tab2">"date":"2014-06-29",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">...</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"sdfsddfsd",</li>
                            <li class="tab2">"date":"2014-06-30",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"58",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">}</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="get_vod_by_timeinterval">
                <p class='title'>get_vod_by_timeinterval</p>
                <p class="summary">Returns the list of all VOD Videos for a specific time interval</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>
                        interval:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;integer
                    </p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>action:    get_vod_by_timeinterval</p>
                        <p>interval:  10</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"news",</li>
                            <li class="tab2">"date":"2014-06-26",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"10",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"test1",</li>
                            <li class="tab2">"date":"2014-06-29",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"10",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">...</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"sdfsddfsd",</li>
                            <li class="tab2">"date":"2014-06-30",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"10",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">}</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="Get_topviewed">
                <p class='title'>Get_topviewed</p>
                <p class="summary">Returns the top (x) viewed VODs where ‘x’ is a parameter you decide</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>
                        
                    </p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>action:    Get_topviewed</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"news",</li>
                            <li class="tab2">"date":"2014-06-26",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"10",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"test1",</li>
                            <li class="tab2">"date":"2014-06-29",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"10",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">},</li>
                            <li class="tab1">...</li>
                            <li class="tab1">{</li>
                            <li class="tab2">"title":"sdfsddfsd",</li>
                            <li class="tab2">"date":"2014-06-30",</li>
                            <li class="tab2">"start_time":"14:17:21",</li>
                            <li class="tab2">"end_time":"14:18:19",</li>
                            <li class="tab2">"duration":"10",</li>
                            <li class="tab2">"direct_path":"\/2014-06-26\/news.mp4",</li>
                            <li class="tab2">"thumbnail_path":"admin.mangomolo.com\/analytics\/Recorder\/54\/2014-06-26\/newsmp4.jpg",</li>
                            <li class="tab2">"youtube_link":0</li>
                            <li class="tab1">}</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="Get_topviewed_bydate">
                <p class='title'>Get_topviewed_bydate</p>
                <p class="summary">Returns the top (x) viewed VODs by date where ‘x’ is a parameter you decide</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>
                        <p>date:    string</p>
                    </p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>action:    Get_topviewed_bydate</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="Get_topviewed_bydate_time">
                <p class='title'>Get_topviewed_bydate_time</p>
                <p class="summary">Returns the top (x) viewed VODs by date and time interval where ‘x’ is a parameter you decide</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>
                        
                    </p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>action:    Get_topviewed_bydate_time</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="Get_topviewed_alltime">
                <p class='title'>Get_topviewed_alltime</p>
                <p class="summary">Returns the top viewed (x) VODs of all time </p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>
                        
                    </p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>action:    Get_topviewed_alltime</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="api-block" id="post_video">
                <p class='title'>post_video</p>
                <p class="summary">upload video to mangomolo</p>
                <div class="parameter-block">
                    <p>Parameters</p>
                    <p>
                        
                    </p>
                </div>
                <div class="example">
                    <p>Example</p>
                    <div class="input">
                        Input:
                        <p>action:    post_video</p>
                    </div>
                    <div class="output">
                        Output:
                        <ol class="code-block">
                            <li class="tab0">[</li>
                            <li class="tab0">]</li>
                        </ol>
                    </div>
                </div>
            </section>

        </div>

    </div>
</body>
</html>

<?php
}
?>