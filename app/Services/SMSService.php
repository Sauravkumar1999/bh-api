<?php

namespace App\Services;

use Aws\Credentials\CredentialProvider;
use Aws\Sns\SnsClient;
class SMSService
{
    private $SnSclient;

    public function __construct()
    {
        $provider = CredentialProvider::defaultProvider();
        $memoizedProvider = CredentialProvider::memoize($provider);

        $this->SnSclient = new SnsClient([
            'region' => 'ap-northeast-1',
            'version' => 'latest', //latest
            'credentials' => $memoizedProvider
        ]);
    }

    public function send($number, $message)
    {
        return $this->SnSclient->publish([
            'Message' => $message,
            'PhoneNumber' => $number,
            'Subject' => 'A New Message',
            'MessageStructure' => 'SMS',
            'MessageAttributes' => array(
                'AWS.SNS.SMS.SenderID' => array(
                    'DataType' => 'String',
                    'StringValue' => 'B-Hub'
                ),
                'AWS.SNS.SMS.SMSType' => array(
                    'DataType' => 'String',
                    'StringValue' => 'Transactional'
                )
            ),
        ]);
    }
}
