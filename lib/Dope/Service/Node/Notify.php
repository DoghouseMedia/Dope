<?php

namespace Dope\Service\Node;

class Notify
{
    const URI = 'http://127.0.0.1:8181/dope';
    
    public static function send($channel, array $data=array())
    {
        $ch = curl_init(static::URI);
        $data = json_encode(array(
            'channel' => $channel,
            'data' => $data,
            'ext' => array(
                /**
                 * @todo This token should be stored in a config file
                 */
                'token' => '70628fd29fb2d6583b83bb1bad94d140'
            )
        ));
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        
        return curl_exec($ch);
    }
    
    public static function authorise($token)
    {
        return static::send('/auth/authorise', array(
            'token' => $token
        ));
    }
    
    public static function startService()
    {
        $nodePath = exec('which node');
        $npmPath = exec('which npm');
        $dopePath = APPLICATION_PATH . '/../dope/node/dope/dope.js';
        
        passthru($nodePath . ' ' . $dopePath . ' >/dev/null 2>&1 &');
    }
    
    public static function stopService()
    {
    	$pids = \Dope\Cli\Helper::findPidsByName('dope.js');
        
        foreach ($pids as $pid) {
            exec('kill ' . (int) $pid);
        }
    }
}