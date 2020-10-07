<?php
namespace Techquity\Dpd\Api;

// use Aero\Account\Models\Customer;

class Login
{
    public static function login(Customer $customer)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, config('aero-dpd.host_url')."/user/?action=login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);  //POST
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $auth = base64_encode(config('aero-dpd.username') . ':' . config('aero-dpd.password'));
        dd($auth);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization:Basic " . $auth,
        ));

        $payload = [
            'CRMAccountDetail' => [
                // Auth
                'service' => config('aero.touchretail.api_service'),
                'username' => config('aero.touchretail.api_username'),
                'password' => config('aero.touchretail.api_password'),

                'externalaccount' => $customer->id,
                'email' => $customer->email,
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        curl_close($ch);

        dd($response);

        return(json_decode($response));
    }
}