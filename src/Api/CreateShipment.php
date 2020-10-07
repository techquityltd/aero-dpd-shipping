<?php
namespace Techquity\Dpd\Api;

use Aero\Account\Models\Customer;

class CreateShipment
{
    public static function createShipment(Customer $customer)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, config('aero-dpd.host_url')."/shipping/shipment");
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
            "job_id" => null,
            "collectionOnDelivery" => false,
            "invoice" => null,
            "collectionDate" => "2015-09-23T09:00:00",
            "consolidate" => true,
            "consignment" => [
                "consignmentNumber" => null,
                "consignmentRef" => null,
                "parcels" => [],
                "collectionDetails" => [
                    "contactDetails" => [
                          "contactName" => "My Contact",
                          "telephone" => "0121 500 2500"
                    ],
                    "address" => [
                        NOTE => "Please ensure that Customs Value and Liability values are both set to “null” as per example. Not all listed post parameters are required. Please utilize example request as a basis for your own request.",
                        "organisation" => " DPD Group ",
                        "countryCode" => "GB",
                        "postcode" => "B66 1BY",
                        "street" => "Roebuck Lane",
                        "locality" => "Smethwick",
                        "town" => "Birmingham",
                        "county" => "West Midlands"
                    ]
                ],
                "deliveryDetails" => [
                    "contactDetails" => [
                         "contactName" => "My Contact",
                         "telephone" => "0121 500 2500"
                    ],
                    "address" => [
                        "organisation" => " DPD Group ",
                        "countryCode" => "GB",
                        "postcode" => "B66 1BY",
                        "street" => "Roebuck Lane",
                        "locality" => "Smethwick",
                        "town" => "Birmingham",
                        "county" => "West Midlands"
                    ],
                    "notificationDetails" => [
                        "email" => "my.email@geopostuk.com",
                        "mobile" => "07921000001"
                    ],
                    "pickupLocation" => [
                       "address" => [
                           "countryCode" => "GB",
                           "county" => "West Midlands",
                           "locality" => "Birmingham",
                           "organisation" => "DPD",
                           "postcode" => "B69 4BF",
                           "property" => "Hub 3",
                           "street" => "A Street",
                           "town" => "Oldbury"
                       ],
                       "allowRemotePickup" => true,
                       "pickupLocationCode" => "GB17426"
                    ]
                ],
                "networkCode" => "2^91",
                "numberOfParcels" => 1,
                "totalWeight" => 5,
                "shippingRef1" => "My Ref 1",
                "shippingRef2" => "My Ref 2",
                "shippingRef3" => "My Ref 3",
                "customsValue" => null,
                "deliveryInstructions" => "Please deliver with neighbour",
                "parcelDescription" => "",
                "liabilityValue" => null,
                "liability" => false
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        curl_close($ch);

        dd($response);

        return(json_decode($response));
    }
}