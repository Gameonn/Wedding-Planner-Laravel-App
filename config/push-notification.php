<?php

return array(

    'appNameIOS'     => array(
        'environment' =>'production',   //'development',  // 'production'
        'certificate' =>'ckProdUser.pem',  // 'ckUser.pem',    // 'ckProdUser.pem',
        'passPhrase'  =>'codebrew1234',
        'service'     =>'apns'
    ),
    'appNameIOS2'     => array(
        'environment' =>'production',   //'development',  // 'production'
        'certificate' =>'ckProd.pem',  // 'ck.pem',    // 'ckProd.pem',
        'passPhrase'  =>'codebrew1234',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'AIzaSyCbIuJ9-WxIJAjbYU_l2T9Z6m128AocRDM',
        'service'     =>'gcm'
    ),
    'appNameAndroid2' => array(
        'environment' =>'production',
        'apiKey'      =>'AIzaSyCEMDHEi102ki_SCiy7K3KLHqTqT5DVTvc',
        'service'     =>'gcm'
    )

);