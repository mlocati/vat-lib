<?php

$acceptFound = false;
foreach (getallheaders() as $name => $value) {
    switch (strtolower($name)) {
        case 'accept':
            if (!preg_match('/^application\/json(;\s*=[Uu][Tt][Ff]-8)?$/', $value)) {
                http_response_code(417);
                die("Invalid Accept request header: {$value}");
            }
            $acceptFound = true;
            break;
    }
}
if (!$acceptFound) {
    http_response_code(417);
    die('Missing Accept request header');
}
header('Content-Type: application/json;charset=UTF-8');
echo <<<'EOT'
{
    "vow": {
        "available": true
    },
    "countries": [
        {
            "countryCode": "AT",
            "availability": "Available"
        },
        {
            "countryCode": "BE",
            "availability": "Available"
        },
        {
            "countryCode": "BG",
            "availability": "Available"
        },
        {
            "countryCode": "CY",
            "availability": "Available"
        },
        {
            "countryCode": "CZ",
            "availability": "Available"
        },
        {
            "countryCode": "DE",
            "availability": "Available"
        },
        {
            "countryCode": "DK",
            "availability": "Available"
        },
        {
            "countryCode": "EE",
            "availability": "Available"
        },
        {
            "countryCode": "EL",
            "availability": "Available"
        },
        {
            "countryCode": "ES",
            "availability": "Available"
        },
        {
            "countryCode": "FI",
            "availability": "Available"
        },
        {
            "countryCode": "FR",
            "availability": "Available"
        },
        {
            "countryCode": "HR",
            "availability": "Available"
        },
        {
            "countryCode": "HU",
            "availability": "Available"
        },
        {
            "countryCode": "IE",
            "availability": "Available"
        },
        {
            "countryCode": "IT",
            "availability": "Available"
        },
        {
            "countryCode": "LT",
            "availability": "Available"
        },
        {
            "countryCode": "LU",
            "availability": "Available"
        },
        {
            "countryCode": "LV",
            "availability": "Available"
        },
        {
            "countryCode": "MT",
            "availability": "Available"
        },
        {
            "countryCode": "NL",
            "availability": "Available"
        },
        {
            "countryCode": "PL",
            "availability": "Available"
        },
        {
            "countryCode": "PT",
            "availability": "Available"
        },
        {
            "countryCode": "RO",
            "availability": "Available"
        },
        {
            "countryCode": "SE",
            "availability": "Available"
        },
        {
            "countryCode": "SI",
            "availability": "Available"
        },
        {
            "countryCode": "SK",
            "availability": "Available"
        },
        {
            "countryCode": "XI",
            "availability": "Available"
        }
    ]
}
EOT;
