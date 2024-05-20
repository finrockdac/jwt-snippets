// This code uses JWT from:  https://github.com/web-token/jwt-framework 

require_once(dirname(_FILE_) . "/autoload.php");

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;


// Create the algorithm manager with RS256
        $algorithmManager = new AlgorithmManager([new RS256()]);

        // Read the private key file
        $publiceKey = file_get_contents('../admCert/mykey.pub');
        $privateKey = file_get_contents('../admCert/mykey.pem');

        // Convert the private key to JWK format
        $jwk = JWKFactory::createFromKey($privateKey, null, [
            'use' => 'sig',
            'alg' => 'RS256'  // Using RS256 algorithm
        ]);

        // Create the JWS Builder
        $jwsBuilder = new JWSBuilder($algorithmManager);

        $_nonce = rand(9999, 99999999999);
        $_iat = strtotime('now');
        $_exp = strtotime('+25 second');
        $datas = array(
            'asset' => 'BEXC_TEST'
        );
        $body = json_encode($datas);
        $hashbody = hash('sha256', $body, false);
        $uri = "/dac/v1/create-wallet";
        // Create the payload as a valid JSON object

        $payloadData = [
            'iat' => $_iat,
            'nonce' => $_nonce,
            'exp' => $_exp,
            'sub' => 'api-key',
            'uri' => $uri,
            'bodyhash' => $hashbody
        ];
        $payload = json_encode($payloadData);


        // Build the JWS
        $jws = $jwsBuilder
            ->create()                    // Create a new JWS
            ->withPayload($payload)       // Add the payload
            ->addSignature($jwk, ['alg' => 'RS256','typ' => 'JWT']) // Add signature with the key and algorithm
            ->build();                    // Build the JWS

        // Serialize the JWS to the Compact Serialization format
        $serializer = new CompactSerializer();
        $token = $serializer->serialize($jws, 0);

        // Output the generated JWT
        echo "JWT Token: " . $token . "<br>";


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.finrock.io$uri",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'asset' => 'BEXC_TEST'
            ]),
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'x-api-key: ap-key',
                'Authorization: Bearer '.$token,
                'content-type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }