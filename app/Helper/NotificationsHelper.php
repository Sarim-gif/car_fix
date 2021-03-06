<?php

namespace App\Helper;

use Edujugon\PushNotification\PushNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class NotificationsHelper
{
    function sendPushNotifications($msg = '', $deviceObject = [], $extraPayLoadData = [])
    {
        $androidDeviceToken = [];
        $iosDeviceToken = [];
        $notificationsUnreadCount = Auth::user()->notifications()->where('notification_users.status', 30)->count();
//        dd($deviceObject);

        foreach ($deviceObject as $device):
//            dd($device['device_type']);
            if (strtolower($device['device_type']) == 'android') {
                $androidDeviceToken[] = $device['device_token'];
            }
            if (strtolower($device['device_type']) == 'ios') {
                $iosDeviceToken[] = $device['device_token'];
            }
        endforeach;

        if ($androidDeviceToken) {
            $push = new PushNotification('fcm');
            $push->setMessage([
                'notification' => [
                    'title' => config('app.name'),
                    'body'  => $msg,
                    'badge' => $notificationsUnreadCount,
                    'sound' => 'default'
                ],
                'data'         => [
                    'extra_payload' => $extraPayLoadData
                ],
                'android'      => [
                    'ttl'          => '86400',
                    'notification' => [
                        'click_action' => 'MainActivity'
                    ]
                ]
            ])
                ->setApiKey(Config::get('pushnotification.fcm.apiKey'))
                ->setConfig(['dry_run' => false])
                ->setDevicesToken($androidDeviceToken)
                ->send();
        }

        /*if ($androidDeviceToken) {
            $push = new PushNotification('fcm');
            $push->setMessage([
                'notification' => [
                    'title' => config('app.name'),
                    'body'  => $msg,
                    'sound' => 'default'
                ],
                'data'         => [
                    'action_type' => $extraPayLoadData['action_type'],
                    'ref_id'      => $extraPayLoadData['ref_id'],
                    'sender_id'   => $extraPayLoadData['sender_id']
                ],
                'android'      => [
                    'ttl'          => '86400',
                    'notification' => [
                        'click_action' => 'MainActivity'
                    ]
                ]
            ])
                ->setApiKey(Config::get('constants.pushNotification.fcm'))
                ->setConfig(['dry_run' => false])
                ->setDevicesToken($androidDeviceToken)
                ->send();
        }*/

        /*Apn*/
        if ($iosDeviceToken) {
            $push = new PushNotification('apn');

            $push->setMessage([
                'aps' => [
                    'alert'        => [
                        'title' => config('app.name'),
                        'body'  => $msg
                    ],
                    'sound'        => 'default',
                    'extraPayLoad' => [
                        'action_type' => $extraPayLoadData['action_type'],
                        'ref_id'      => $extraPayLoadData['ref_id'],
                    ]
                ]
            ])->setDevicesToken($iosDeviceToken)->send();
        }
        return true;
    }
}