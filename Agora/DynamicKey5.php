<?php
namespace App\Agora;

class DynamicKey5
{
    public $version = "005";
    public $NO_UPLOAD = "0";
    public $AUDIO_VIDEO_UPLOAD = "3";

    // InChannelPermissionKey
    public $ALLOW_UPLOAD_IN_CHANNEL = 1;

    // Service Type
    public $MEDIA_CHANNEL_SERVICE = 1;
    public $RECORDING_SERVICE = 2;
    public $PUBLIC_SHARING_SERVICE = 3;
    public $IN_CHANNEL_PERMISSION = 4;

    public function generateRecordingKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs)
    {
        return $this->generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $GLOBALS["RECORDING_SERVICE"], array());
    }

    public function generateMediaChannelKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs)
    {
        return $this->generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $GLOBALS["MEDIA_CHANNEL_SERVICE"], array());
    }

    public function generateInChannelPermissionKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $permission)
    {
        $extra[$GLOBALS["ALLOW_UPLOAD_IN_CHANNEL"]] = $permission;
        return $this->generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $GLOBALS["IN_CHANNEL_PERMISSION"], $extra);
    }

    public function generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $serviceType, $extra)
    {
        $signature = $this->generateSignature($serviceType, $appID, $appCertificate, $channelName, $uid, $ts, $randomInt, $expiredTs, $extra);
        $content = $this->packContent($serviceType, $signature, hex2bin($appID), $ts, $randomInt, $expiredTs, $extra);
        // echo bin2hex($content);
        return $GLOBALS["version"] . base64_encode($content);
    }

    public function generateSignature($serviceType, $appID, $appCertificate, $channelName, $uid, $ts, $salt, $expiredTs, $extra)
    {
        $rawAppID = hex2bin($appID);
        $rawAppCertificate = hex2bin($appCertificate);

        $buffer = pack("S", $serviceType);
        $buffer .= pack("S", strlen($rawAppID)) . $rawAppID;
        $buffer .= pack("I", $ts);
        $buffer .= pack("I", $salt);
        $buffer .= pack("S", strlen($channelName)) . $channelName;
        $buffer .= pack("I", $uid);
        $buffer .= pack("I", $expiredTs);

        $buffer .= pack("S", count($extra));
        foreach ($extra as $key => $value) {
            $buffer .= pack("S", $key);
            $buffer .= pack("S", strlen($value)) . $value;
        }

        return strtoupper(hash_hmac('sha1', $buffer, $rawAppCertificate));
    }

    public function packString($value)
    {
        return pack("S", strlen($value)) . $value;
    }

    public function packContent($serviceType, $signature, $appID, $ts, $salt, $expiredTs, $extra)
    {
        $buffer = pack("S", $serviceType);
        $buffer .= $this->packString($signature);
        $buffer .= $this->packString($appID);
        $buffer .= pack("I", $ts);
        $buffer .= pack("I", $salt);
        $buffer .= pack("I", $expiredTs);

        $buffer .= pack("S", count($extra));
        foreach ($extra as $key => $value) {
            $buffer .= pack("S", $key);
            $buffer .= $this->packString($value);
        }

        return $buffer;
    }
}
