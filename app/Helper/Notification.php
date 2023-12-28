<?php

namespace App\Helper;

use App\Models\MutedGroup;
use App\Models\MutedUser;
use App\Models\UserDevice;

class Notification
{
    /**
     * @return bool|string
     */
    public static function sendNotification(array $tokens, string $title, string $body, array $params = [], bool $is_file = false)
    {
        $muted_users = MutedUser::query()->where(['muted_user_id' => auth()->id()])->get()->pluck('user_id')->toArray();
        $muted_groups = [];

        if (!empty($params['group_id'])) {
            $muted_groups = MutedGroup::query()->where(['group_id' => $params['group_id']])->get()->pluck('user_id')->toArray();
        }

        $muted_tokens = UserDevice::query()->whereIn('user_id', array_merge($muted_users, $muted_groups))->get()->pluck('token')->toArray();

        $tokens = array_diff($tokens, $muted_tokens);

        $data = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'badge' => auth()->user()->unread_count,
            ],
        ];

        if ($is_file) {
            $data['notification']['image'] = $body;
        } else {
            $data['notification']['body'] = $body;
        }

        if (!empty($params)) {
            $data = array_merge($data, ['data' => $params]);
        }

        $encodedData = json_encode($data);

        $config_fcm = config('fcm');

        $headers = [
            sprintf('Authorization:key=%s', $config_fcm['server_key']),
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $config_fcm['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        // Execute post
        $result = curl_exec($ch);

        if ($result === false) {
            exit('Curl failed: '.curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        // FCM response
        return $result;
    }
}
