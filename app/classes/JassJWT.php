<?php

namespace App\Classes;

use Illuminate\Support\Facades\App;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Illuminate\Support\Facades\Auth;

class JassJWT
{
    public static function generate()
    {
        $API_KEY = env('JASS_API_KEY');
        $APP_ID = env('JASS_APP_ID');
        $USER_EMAIL = env('JASS_USER_EMAIL');
        $USER_NAME = "Koraspond";
        $USER_IS_MODERATOR = true;
        $USER_AVATAR_URL = "";
        $USER_ID = uuid_create(UUID_TYPE_RANDOM);
        $LIVESTREAMING_IS_ENABLED = false;
        $RECORDING_IS_ENABLED = true;
        $OUTBOUND_IS_ENABLED = false;
        $TRANSCRIPTION_IS_ENABLED = false;
        $EXP_DELAY_SEC = 7200;
        $NBF_DELAY_SEC = 10;

        $file = App::basePath('jaasauth.key');
        $jwk = JWKFactory::createFromKeyFile($file);


        $algorithm = new AlgorithmManager([
            new RS256()
        ]);


        $jwsBuilder = new JWSBuilder($algorithm);


        $payload = json_encode([
            'iss' => 'chat',
            'aud' => 'jitsi',
            'exp' => time() + $EXP_DELAY_SEC,
            'nbf' => time() - $NBF_DELAY_SEC,
            'room' => "*",
            'sub' => $APP_ID,
            'context' => [
                'user' => [
                    'moderator' => $USER_IS_MODERATOR ? "true" : "false",
                    'email' => $USER_EMAIL,
                    'name' => $USER_NAME,
                    'avatar' => $USER_AVATAR_URL,
                    'id' => $USER_ID
                ],
                'features' => [
                    'recording' => $RECORDING_IS_ENABLED ? "true" : "false",
                    'livestreaming' => $LIVESTREAMING_IS_ENABLED ? "true" : "false",
                    'transcription' => $TRANSCRIPTION_IS_ENABLED ? "true" : "false",
                    'outbound-call' => $OUTBOUND_IS_ENABLED ? "true" : "false"
                ]
            ]
        ]);


        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($jwk, [
                'alg' => 'RS256',
                'kid' => $API_KEY,
                'typ' => 'JWT'
            ])
            ->build();


        $serializer = new CompactSerializer();
        $token = $serializer->serialize($jws, 0);
        return $token;
    }
}
