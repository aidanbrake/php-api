<?php
require_once('lib/MysqliDb.php');
error_reporting(E_ALL);
$action = '';
$username = '';
$key = '';
$user = null;
$data = array();

/*


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

/**
 * Returns the Full List of Tags for this client
 */
    function get_tags_list() {
        global $db;
        global $user;

        $result = array();
        $records = $db -> rawQuery("SELECT distinct tags FROM tvshows where userID=54");// Should be modified...

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
 */
    function get_vod_list() {
        global $db;

        $tags = $_GET['tags'];
        $result = array();
        if (empty($tags))
            $records = $db -> get("tvshows");
        else
            $records = $db -> rawQuery("select * from tvshows where `tags`like'".$tags."'");

        foreach ($records as $record) {
            $item['title'] = $record['Title'];
            $item['date'] = $record['RecordDate'];
            $item['start_time'] = $record['FirstStart'];
            $item['end_time'] = $record['StopTime'];
            $item['duration'] = $record['Duration'];
            // $item['direct_path'] = $record['Title'];
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
 * Returns the list of all VOD Videos for a specific Date (Can be single date or Range)
 * @param date or date range
 */
function get_vod_by_date($date) {
    global $db;

    $date = $_GET['date'];
    $result = array();
    if (empty($tags))
        get_vod_list();
    else
        $records = $db -> rawQuery("select * from tvshows where `tags`like'".$tags."'");

    foreach ($records as $record) {
        $item['title'] = $record['Title'];
        $item['date'] = $record['RecordDate'];
        $item['start_time'] = $record['FirstStart'];
        $item['end_time'] = $record['StopTime'];
        $item['duration'] = $record['Duration'];
        // $item['direct_path'] = $record['Title'];
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
 */ 
function get_vod_by_tag() {
    echo "";
}

/**
 * Returns the list of all VOD Videos for a specific date and start time
 */ 
function get_vod_by_date_starttime() {
    echo "";
}


/**
 * Returns the list of all VOD Videos for a specific time interval
 */ 
function get_vod_by_timeinterval() {
    echo "";
}
 

/**
 * Returns the top (x) viewed VODs where ‘x’ is a parameter you decide
 */ 
function Get_topviewed() {
    echo "";
}
 
/**
 * Returns the top (x) viewed VODs by date where ‘x’ is a parameter you decide
 */
function Get_topviewed_bydate() {
    echo "";
}
 

/**
 * Returns the top (x) viewed VODs by date and time interval where ‘x’ is a parameter you decide
 */
function Get_topviewed_bydate_time() {
    echo "";
}
 

/**
 * Returns the top viewed (x) VODs of all time
 */
function Get_topviewed_alltime() {
    echo "";
}


/**
 * upload video to mangomolo
 */
function post_video() {
    echo "";
}

/**
 * Authentication
 */
function isAuthenticated($username="", $key="") {
    global $db;
    global $user;


    $query = "select * from users where `username`='".$username."' and `key`='".$key."';";
    $result = $db->rawQuery($query);
    if ($result)
    {
        $user = $result[0];
        return true;
    }
    else
    {
        $user = null;
        return false;
    }
}

$db = new Mysqlidb ('localhost', 'root', 'root', 'testdb');
if ($_GET) {
    $action = $_GET['action'];
    $username = $_GET['username'];
    $key = $_GET['key'];

    if (!isAuthenticated($username, $key))
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

<?
if (empty($action))
{
?>

<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Users</title>
</head>
<body>

<center>
<h3>Users:</h3>
<table width='50%'>
    <tr bgcolor='#cccccc'>
        <th>ID</th>
        <th>Username</th>
        <th>Key</th>
    </tr>
    <?php printUsers();?>

</table>
<hr width=50%>
<form action='index.php?action=<?php echo $action?>' method=post>
    <!-- <input type=hidden name='id' value='<?php echo $data['id']?>'>
    <input type=text name='login' required placeholder='Login' value='<?php echo $data['login']?>'>
    <input type=text name='firstName' required placeholder='First Name' value='<?php echo $data['firstName']?>'>
    <input type=text name='lastName' required placeholder='Last Name' value='<?php echo $data['lastName']?>'>
    <input type=password name='password' placeholder='Password'>
    <input type=submit value='New User'></td> -->
<form>
</table>
</center>
</body>
</html>
<?
}
?>