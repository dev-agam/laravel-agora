# laravel-agora
Implementation of agora in laravel applications.

## Paste this directory into App/

__
use App\Agora\RtcTokenBuilder2;

public function GetToken()
{
    $appId = env('AGORA_APP_ID');
    $appCertificate = env('PRIMARY_CERTIFICATE');
    $uid = $this->currentUserId;         // current logged in user id
    $expirationTimeInSeconds = 7200;
    $currentTimeStamp = time();
    $privilegeExpiredTs = $currentTimeStamp + $expirationTimeInSeconds;

    // dd($this->channelName, $appId, $appCertificate, $this->channelName, $uid, $privilegeExpiredTs);

    $token = RtcTokenBuilder2::buildTokenWithUid($appId, $appCertificate, $this->channelName, $uid, RtcTokenBuilder2::ROLE_PUBLISHER, $privilegeExpiredTs);

    return ['token' => $token, 'uid' => $uid];
}

