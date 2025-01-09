<?php
namespace App\Agora;

class DynamicKey4
{
    public function generateRecordingKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs ,$serviceType='ARS')
    {
        return $this->generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs ,$serviceType);
    }

    public function generateMediaChannelKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs ,$serviceType='ACS')
    {
        return $this->generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs ,$serviceType);
    }

    public function generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs ,$serviceType)
    {
        $version = "004";

        $randomStr = "00000000" . dechex($randomInt);
        $randomStr = substr($randomStr,-8);

        $uidStr = "0000000000" . $uid;
	    $uidStr = substr($uidStr,-10);

        $expiredStr = "0000000000" . $expiredTs;
        $expiredStr = substr($expiredStr,-10);

        $signature = $this->generateSignature($appID, $appCertificate, $channelName, $ts, $randomStr, $uidStr, $expiredStr ,$serviceType);

        return $version . $signature . $appID . $ts . $randomStr . $expiredStr;
    }

    public function generateSignature($appID, $appCertificate, $channelName, $ts, $randomStr, $uidStr, $expiredStr ,$serviceType)
    {
        $concat = $serviceType . $appID . $ts . $randomStr . $channelName . $uidStr . $expiredStr;
        return hash_hmac('sha1', $concat, $appCertificate);
    }
}
