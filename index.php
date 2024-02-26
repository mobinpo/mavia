<?php

require __DIR__ . '/vendor/autoload.php';

use WebSocket\BadOpcodeException;
use WebSocket\Client;

// Function to handle WebSocket messages
/**
 * @throws BadOpcodeException
 */

$i = 0;
$resources = [];
$units = [];
/**
 * @throws BadOpcodeException
 */
function handleMessage($message): void
{
    global $i;
    echo "Received message: $message\n";
    sleep(1);
    global $ws, $login_data,$resources,$units;
    $start_time = microtime(true);
    if (str_starts_with($message, "40")) {
        // Send auth_message
        $auth_token = $login_data['token'];
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $auth_message = '42["authenticate", {"token":"' . $auth_token . '","utcOffset":3,"clientTimestamp":' . $timestamp . '}]';
        $ws->send($auth_message);
    } elseif (str_starts_with($message, '42["Loading",{"percent":90}')) {
        // Send auth_message
        $auth_token = $login_data['token'];
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $auth_message = '42["authenticate", {"token":"' . $auth_token . '","utcOffset":3,"clientTimestamp":' . $timestamp . '}]';
        $ws->send($auth_message);
    } elseif (str_starts_with($message, '42["GetHomeData"')) {
        // Then send RequestGuestLogin message
        $cleaned_message = substr($message, 2);

        // Decode the cleaned message
        $homeData = json_decode($cleaned_message, true);
// Extract uniqueId and capacity
        foreach ($homeData[1]['buildings'] as $building) {
            $resources[]=$building;
        }
        foreach ($homeData[1]['units']['units'] as $unit) {
            $units[]=$unit;
        }
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["HomeDataPartSquad", "{\"clientTimestamp\":' . $timestamp . '}"]');

    } elseif (str_starts_with($message, '42["HomeDataPartSquad",{"success":true,')) {
        $ws->send('42["RequestGuestLogin", "{\"deviceId\":\"97b1745226ee9a81\",\"inviteCode\":\"\",\"registerTypeEnum\":\"GLOBAL\",\"idToken\":\"\",\"oAuthIdToken\":\"\",\"email\":\"\"}"]');
    } elseif (str_starts_with($message, '42["RequestGuestLogin",{"success":true')) {
        // Send HomeDataPartJobs message
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["HomeDataPartJobs", "{\"clientTimestamp\":'.$timestamp.'}"]');
    } elseif (str_starts_with($message, '42["HomeDataPartJobs",{"success":true')) {
        // Send GetCostumeByUserId message
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["GetCostumeByUserId", "{\"clientTimestamp\":'.$timestamp.',\"latency\":0}"]');
    } elseif (str_starts_with($message, '42["GetCostumeByUserId",{"success":true')) {
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["GetResourceState", "{\"clientTimestamp\":'.$timestamp.'}"]');
    } elseif (str_starts_with($message, '42["GetResourceState",{"success":true')) {
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["GetBattleRubyMeta", "{\"clientTimestamp\":'.$timestamp.'}"]');
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["GetSquadInvites", "{\"clientTimestamp\":'.$timestamp.'}"]');
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["GetDailyJobs", "{\"clientTimestamp\":'.$timestamp.',\"latency\":0}"]');
    } elseif (str_starts_with($message, '42["GetDailyJobs",{"success":true')) {
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["GetAchievements", "{\"clientTimestamp\":'.$timestamp.',\"latency\":0}"]');
    } elseif (str_starts_with($message, '42["GetAchievements",{"success"')) {
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $ws->send('42["HomeDataInitDone", "{\"clientTimestamp\":'.$timestamp.'}"]');
    } elseif (str_starts_with($message, '42["ADD_EVENT",{"key":"xmas_tree_event')) {
        $auth_token = $login_data['token'];
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $auth_message = '42["authenticate", {"token":"' . $auth_token . '","utcOffset":3,"clientTimestamp":' . $timestamp . '}]';
        $ws->send($auth_message);
    } elseif (str_starts_with($message, '42["GetSquadInvites",{"success":true')) {
        $auth_token = $login_data['token'];
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $auth_message = '42["authenticate", {"token":"' . $auth_token . '","utcOffset":3,"clientTimestamp":' . $timestamp . '}]';
        $ws->send($auth_message);
    } elseif (str_starts_with($message, '42["HomeDataInitDone",{"success":true')) {
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $end_time = microtime(true);
        $elapsed_time = $end_time - $start_time;
        $fps = 1 / $elapsed_time;
        $fps = round($fps/1000) + 9;
        $ws->send('42["SetStat", "{\"key\":\"fps\",\"value\":'.$fps.',\"clientTimestamp\":'.$timestamp.'}"]');
    } elseif (str_starts_with($message, '42["SetStat",{"success":true')) {
        $i++;
        if ($i >= 9) {
            $ws->send('41');
            $auth_token = $login_data['token'];
            $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
            $auth_message = '42["authenticate", {"token":"' . $auth_token . '","utcOffset":3,"clientTimestamp":' . $timestamp . '}]';
            $ws->send($auth_message);
            $i = 0;
        }
        $timestamp = round(microtime(true) * 1000);  // Convert to milliseconds
        $end_time = microtime(true);
        $elapsed_time = $end_time - $start_time;
        $fps = 1 / $elapsed_time;
        $fps = round($fps/1000) + 9;

        $ws->send('42["SetStat", "{\"key\":\"fps\",\"value\":'.$fps.',\"clientTimestamp\":'.$timestamp.'}"]');
        foreach ($resources as $value) {
            if (array_key_exists('capacity',$value)) {
                if ($value['capacity'] > 0) {
                    $timestamp = round(microtime(true) * 1000);

                    $ws->send('42["CollectResources", "{\"producers\":[{\"objectId\":\"'.$value['uniqueId'].'\",\"amount\":'.$value['capacity'].'}],\"clientTimestamp\":'.$timestamp.',\"latency\":0}"]');

                }
            } elseif (array_key_exists('honey',$value)) {
                $timestamp = round(microtime(true) * 1000);
                $ws->send('42["UpgradeBuilding", "{\"object_id\":\"'.$value['uniqueId'].'\",\"instantFinish\":true,\"clientTimestamp\":'.$timestamp.',\"latency\":0}"]');
            }

        }
        foreach ($units as $unit) {
            $timestamp = round(microtime(true) * 1000);
            $ws->send('42["RecruitUnits", "{\"unitId\":'.$unit['id'].',\"amount\":'.$unit['amount'].',\"startedAt\":'.$timestamp.',\"clientTimestamp\":'.$timestamp.'}"]');
        }
    } else {
        $ws->send('2');
    }
}

// HTTP POST to log in endpoint
$login_url = 'https://prod-auth.mavia.io/login';
$headers = [
    'Content-Type: application/json',
    'User-Agent: BestHTTP 1.12.3'
];
$payload = [
    "device_id" => "<device_id>",
    "platform" => "android",
    "device_platform_version" => "Android OS <android_version>",
    "device_model" => "Xiaomi <device_model_number>",
    "device_make" => "<unknown>",
    "device_ram" => <your_device_ram>,
    "device_cores" => <your_cpu_cores>,
    "device_mhz" => 4800,
    "version" => "2.1.7",
    "app_version" => "2.1.7",
    "tzDaylight" => "+0430",
    "tzStandard" => "+0330",
    "tzDaylightSaving" => "False",
    "tzOffset" => 3,
    "tutorial_version" => "6",
    "bundle_id" => "com.skrice.mavia",
    "store" => "GooglePlay",
    "locale" => "EN"
];

$response = file_get_contents($login_url, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => $headers,
        'content' => json_encode($payload)
    ]
]));

// Print the response data
echo "Login response: $response\n";

// Decode the JSON response
$login_data = json_decode($response, true);

// Establish WebSocket connection
$websocket_url = $login_data['servers']['game'] . '?EIO=4&transport=websocket';

function connectWebSocket() {
    global $ws, $websocket_url;
    $ws = new Client($websocket_url);
}

connectWebSocket();

// You can now handle messages directly in the loop
while (true) {
    try {
        global $ws;
        $message = $ws->receive();
        handleMessage($message);
    } catch (WebSocket\TimeoutException $e) {
        // Handle timeout error
        echo "Timeout error: {$e->getMessage()}\n";
        echo "Attempting to reconnect...\n";

        // Reconnect to WebSocket server
        connectWebSocket();
    }
}
