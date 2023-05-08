<?php

declare(strict_types=1);

//declare(strict_types=1);

class TeslaSplitter extends IPSModule
{
    //private $base_url = 'https://owner-api.teslamotors.com/api/1';
    //private $token_url = 'https://owner-api.teslamotors.com/oauth/token';

    private $base_url = 'https://owner-api.teslamotors.com/api/1';
    private $token_url = 'https://owner-api.teslamotors.com/oauth/token';
    private $token_UrlNew = 'https://auth.tesla.com/oauth2/v3/token';
    private $accessUrl = 'https://auth.tesla.com/oauth2/v3/authorize';

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString('EMail', '');
        $this->RegisterPropertyString('Password', '');

        $this->RegisterPropertyString('Client_ID', '');
        $this->RegisterPropertyString('Client_Secret', '');

        $this->RegisterPropertyBoolean('AlternativLogin', true);
        $this->RegisterPropertyString('AccessToken', '');
        $this->RegisterPropertyString('RefreshToken', '');

        $this->RegisterPropertyInteger('Interval', 5);
        $this->RegisterPropertyString('Vehicles', '');

        $this->RegisterAttributeString('RefreshToken', '');
        $this->RegisterAttributeString('AccessToken', '');
        $this->RegisterAttributeInteger('expires_in', 0);
        $this->RegisterAttributeInteger('AccessTokenExpiresAt', 0);

        $this->RegisterPropertyBoolean('ShowDebugMessages', false);

        //Old Login
        $this->RegisterAttributeString('Token', '');
        $this->RegisterAttributeString('TokenExpires', '');

        $this->SetBuffer('Cookies', '{}');
    }

    public function ApplyChanges()
    {

        //Never delete this line!
        parent::ApplyChanges();
    }

    public function GetConfigurationForm()
    {
        $Form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        $Form = [];

        $Form['elements'][0]['type'] = 'ValidationTextBox';
        $Form['elements'][0]['name'] = 'EMail';
        $Form['elements'][0]['caption'] = 'E-Mail';

        $Form['elements'][1]['type'] = 'PasswordTextBox';
        $Form['elements'][1]['name'] = 'Password';
        $Form['elements'][1]['caption'] = 'Password';

        $Form['elements'][2]['type'] = 'ValidationTextBox';
        $Form['elements'][2]['name'] = 'Client_ID';
        $Form['elements'][2]['caption'] = 'Client ID';

        $Form['elements'][3]['type'] = 'ValidationTextBox';
        $Form['elements'][3]['name'] = 'Client_Secret';
        $Form['elements'][3]['caption'] = 'Client Secret';

        $Form['elements'][4]['type'] = 'CheckBox';
        $Form['elements'][4]['name'] = 'AlternativLogin';
        $Form['elements'][4]['caption'] = 'Alternativer Login';

        $Form['elements'][5]['type'] = 'ValidationTextBox';
        $Form['elements'][5]['visible'] = true;
        $Form['elements'][5]['name'] = 'AccessToken';
        $Form['elements'][5]['caption'] = 'AccessToken';

        $Form['elements'][6]['type'] = 'ValidationTextBox';
        $Form['elements'][6]['name'] = 'RefreshToken';
        $Form['elements'][6]['caption'] = 'RefreshToken';

        $Form['elements'][7]['type'] = 'CheckBox';
        $Form['elements'][7]['name'] = 'ShowDebugMessages';
        $Form['elements'][7]['caption'] = 'Zeige Debug Meldungen';

        $EMail = $this->ReadPropertyString('EMail');
        $Password = $this->ReadPropertyString('Password');
        $Client_ID = $this->ReadPropertyString('Client_ID');
        $Client_Secret = $this->ReadPropertyString('Client_Secret');

        $FormElementCount = 8;
        if ($EMail || $Password || $Client_ID || $Client_Secret != '') {
            $Vehicles = $this->getVehicles();
            if (is_array($Vehicles)) {
                if ($Vehicles['count'] > 0) {
                    $Form['elements'][$FormElementCount]['type'] = 'Select';
                    $Form['elements'][$FormElementCount]['name'] = 'Vehicles';
                    $Form['elements'][$FormElementCount]['caption'] = 'Vehicles';
                    $selectOptions[0]['caption'] = $this->Translate('Please select a car!');
                    $selectOptions[0]['value'] = '0';
                    $optionsElementCount = 1;
                    foreach ($Vehicles['response'] as $Vehicle) {
                        $selectOptions[$optionsElementCount]['caption'] = $Vehicle['display_name'];
                        $selectOptions[$optionsElementCount]['value'] = strval($Vehicle['id_s']);
                        $optionsElementCount++;
                    }
                    $Form['elements'][$FormElementCount]['options'] = $selectOptions;
                }
            }
        }

        return json_encode($Form);
    }

    public function ForwardData($JSONString)
    {
        $this->logger(__FUNCTION__, $JSONString);
        $data = json_decode($JSONString);

        switch ($data->Buffer->Command) {
            case 'IsOnline':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles'));
                break;
            case 'ChargingState':
                //$result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/data_request/charge_state');
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/vehicle_data');
                IPS_LogMessage('Debug Test', print_r($result,true));
                $result = $result['response']['charge_state'];
                break;
            case 'ClimateState':
                //$result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/data_request/climate_state');
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/vehicle_data');
                $result = $result['response']['climate_state'];
                break;
            case 'DriveState':
                //$result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/data_request/drive_state');
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/vehicle_data');
                $result = $result['response']['drive_state'];
                break;
            case 'GUISettings':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/vehicle_data');
                //$result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/data_request/gui_settings');
                $result = $result['response']['gui_settings'];
                break;
            case 'VehicleState':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/vehicle_data');
                //$result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/data_request/vehicle_state');
                $result = $result['response']['vehicle_state'];
                break;
            case 'VehicleConfig':
                //$result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/data_request/vehicle_config');
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/vehicle_data');
                $result = $result['response']['vehicle_config'];
                break;
            case 'WakeUP':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/wake_up', [], 'POST');
                break;
            case 'HonkHorn':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/honk_horn', [], 'POST');
                break;
            case 'FlashLights':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/flash_lights', [], 'POST');
                break;
            case 'RemoteStartDrive':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/remote_start_drive?password=' . $this->ReadPropertyString('Password'), [], 'POST');
                break;
            case 'SpeedLimitSetLimit':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/speed_limit_set_limit', $params, 'POST');
                break;
            case 'SpeedLimitActivate':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/speed_limit_activate', $params, 'POST');
                break;
            case 'SpeedLimitDeactivate':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/speed_limit_deactivate', $params, 'POST');
                break;
            case 'SpeedLimitClearPin':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/speed_limit_clear_pin', $params, 'POST');
                break;
            case 'SetValetMode':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/set_valet_mode', $params, 'POST');
                break;
            case 'ResetValetPin':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/reset_valet_pin', [], 'POST');
                break;
            case 'SetSentryMode':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/set_sentry_mode', $params, 'POST');
                break;
            case 'DoorUnlock':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/door_unlock', [], 'POST');
                break;
            case 'DoorLock':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/door_lock', [], 'POST');
                break;
            case 'ActuateTrunk':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/actuate_trunk', $params, 'POST');
                break;
            case 'SunRoofControl':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/sun_roof_control', $params, 'POST');
                break;
            case 'ChargePortDoorOpen':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/charge_port_door_open', [], 'POST');
                break;
            case 'ChargePortDoorClose':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/charge_port_door_close', [], 'POST');
                break;
            case 'ChargeStart':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/charge_start', [], 'POST');
                break;
            case 'ChargeStop':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/charge_stop', [], 'POST');
                break;
            case 'ChargeStandard':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/charge_standard', [], 'POST');
                break;
            case 'ChargeMaxRange':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/charge_max_range', [], 'POST');
                break;
            case 'SetChargeLimit':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/set_charge_limit', $params, 'POST');
                break;
            case 'SetChargingAmps':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/set_charging_amps', $params, 'POST');
                break;
            case 'AutoConditioningStart':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/auto_conditioning_start', [], 'POST');
                break;
            case 'AutoConditioningStop':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/auto_conditioning_stop', [], 'POST');
                break;
            case 'SetTemps':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/set_temps', $params, 'POST');
                break;
            case 'RemoteSeatHeaterRequest':
                $params = (array) $data->Buffer->Params;
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/remote_seat_heater_request', $params, 'POST');
                break;
            case 'RemoteSteeringWheelHeaterRequest':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/remote_steering_wheel_heater_request', [], 'POST');
                break;
            case 'SetPreconditioningMax':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/SetPreconditioningMax', [], 'POST');
                break;
            case 'MediaTogglePlayback':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/media_toggle_playback', [], 'POST');
                break;
            case 'MediaNextTrack':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/media_next_track', [], 'POST');
                break;
            case 'MediaPrevTrack':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/media_prev_track', [], 'POST');
                break;
            case 'MediaNextFav':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/media_next_fav', [], 'POST');
                break;
            case 'MediaPrevFav':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/media_prev_fav', [], 'POST');
                break;
            case 'MediaVolumeUp':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/media_volume_up', [], 'POST');
                break;
            case 'MediaVolumeDown':
                $result = $this->sendRequest('/vehicles/' . $this->ReadPropertyString('Vehicles') . '/command/media_volume_down', [], 'POST');
                break;
            default:
                $this->logger(__FUNCTION__, $data->Buffer->Command);
                break;
        }
        $this->logger(__FUNCTION__, json_encode($result));
        return json_encode($result);
    }

    public function base64url_encode(string $data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function FetchAccessToken()
    {

       /*###Step 0: Get ClientID & ClientSecret

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://pastebin.com/raw/pS7Z6yyP');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                $api = explode(PHP_EOL,$result);
                $id=explode('=',$api[0]);
                $secret=explode('=',$api[1]);
                $this->setClientId(trim($id[1]));
                $this->setClientSecret(trim($secret[1]));
        */
        ###Step 1: Obtain the login page

        $code_verifier = substr(hash('sha512', strval(mt_rand())), 0, 86);
        $state = $this->base64url_encode(substr(hash('sha256', strval(mt_rand())), 0, 12));
        $code_challenge = $this->base64url_encode($code_verifier);

        $data = [
            'client_id'             => 'ownerapi',
            'code_challenge'        => $code_challenge,
            'code_challenge_method' => 'S256',
            'redirect_uri'          => 'https://auth.tesla.com/void/callback',
            'response_type'         => 'code',
            'scope'                 => 'openid email offline_access',
            'state'                 => $state,
        ];

        $GetUrl = $this->accessUrl . '?' . http_build_query($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Symcon/5.7'); // . IPS_GetKernelVersion());
        curl_setopt($ch, CURLOPT_URL, $GetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

        $apiResult = curl_exec($ch);
        $HederOut = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($apiResult, 0, $header_len);
        $this->grabCookies($header);
        curl_close($ch);
        $this->logger('Step 1 URL', $GetUrl);
        $this->logger('Step 1 Result', $apiResult);
        $this->logger('Step 1 HederOut', $HederOut);
        $body = substr($apiResult, $header_len);

        $dom = new DomDocument();
        @$dom->loadHTML($body);
        $child_elements = $dom->getElementsByTagName('input'); //DOMNodeList
        foreach ($child_elements as $h2) {
            $hiddeninputs[$h2->getAttribute('name')] = $h2->getAttribute('value');
        }

        #echo '<pre>';print_r($headers);echo '<pre/>';
        #echo '<pre>';print_r($hiddeninputs);echo '<pre/><p>';
        #print $cookie;exit;

        ###Step 2: Obtain an authorization code

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Symcon/5.6'); // . IPS_GetKernelVersion());
        curl_setopt($ch, CURLOPT_URL, $GetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_COOKIE, $this->getCookies()['tesla']);
        curl_setopt($ch, CURLOPT_ENCODING, 'deflate');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_MAX_TLSv1_2); // Force TLS1.2

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $postData = [
            '_csrf'          => $hiddeninputs['_csrf'],
            '_phase'         => $hiddeninputs['_phase'],
            '_process'       => $hiddeninputs['_process'],
            'transaction_id' => $hiddeninputs['transaction_id'],
            'cancel'         => $hiddeninputs['cancel'],
            'identity'       => $this->ReadPropertyString('EMail'),
            'credential'     => $this->ReadPropertyString('Password')
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $apiResult = curl_exec($ch);
        $this->logger('Step 2 URL', $GetUrl);
        $this->logger('Step 2 Result', $apiResult);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($apiResult, 0, $header_len);
        $this->logger('Step 2 Header', $header);
        //$this->grabCookies($header);
        curl_close($ch);

        $codePart = explode('https://auth.tesla.com/void/callback?code=', $apiResult);
        $code = explode('&', $codePart[1])[0];
        //print 'CODE'.$code;exit;

        ###Step 3: Exchange authorization code for bearer token

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Symcon/5.6'); // . IPS_GetKernelVersion());
        curl_setopt($ch, CURLOPT_URL, $this->token_UrlNew);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_COOKIE, $this->getCookies()['tesla']);
        curl_setopt($ch, CURLOPT_ENCODING, 'deflate');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'grant_type'    => 'authorization_code',
            'client_id'     => 'ownerapi',
            'code'          => $code,
            'code_verifier' => $code_verifier,
            'redirect_uri'  => 'https://auth.tesla.com/void/callback'
        ]));

        $apiResult = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($apiResult, 0, $header_len);
        $this->grabCookies($header);
        curl_close($ch);
        $apiResultJson = json_decode($apiResult, true);
        #print_r($apiResultJson);exit;

        $BearerToken = $apiResultJson['access_token'];
        $RefreshToken = $apiResultJson['refresh_token'];

        ###Step 4: Exchange bearer token for access token

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Symcon/' . IPS_GetKernelVersion());
        curl_setopt($ch, CURLOPT_URL, $this->token_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_COOKIE, $this->getCookies()['tesla']);
        curl_setopt($ch, CURLOPT_ENCODING, 'deflate');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $BearerToken,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'grant_type'    => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'client_id'     => $this->ReadPropertyString('Client_ID'),
            'client_secret' => $this->ReadPropertyString('Client_Secret'),
        ]));

        $apiResult = curl_exec($ch);
        curl_close($ch);

        $apiResultJson = json_decode($apiResult, true);
        $apiResultJson['refresh_token'] = $RefreshToken;
        #print_r($apiResultJson);exit;

        $this->WriteAttributeString('Token', $apiResultJson['access_token']);
        //$this->accessToken = $apiResultJson['access_token'];

        return true;
    }

    public function refreshToken()
    {
        if ($this->ReadAttributeString('AccessToken') == '') {
            $accessToken = $this->ReadPropertyString('AccessToken');
        } else {
            $accessToken = $this->ReadAttributeString('AccessToken');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://auth.tesla.com/oauth2/v3/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Mozilla/5.0 (Linux; Android 9.0.0; VS985 4G Build/LRX21Y; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36',
            'X-Tesla-User-Agent: TeslaApp/3.4.4-350/fad4a582e/android/9.0.0',
            'Authorization: Bearer ' . $accessToken,
        ]);

        $params['grant_type'] = 'refresh_token';
        $params['client_id'] = 'ownerapi';
        $params['refresh_token'] = $this->ReadPropertyString('RefreshToken');
        $params['scope'] = 'openid email offline_access';
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $apiResultJSON = curl_exec($ch);
        $headerInfo = curl_getinfo($ch);
        $apiResult = json_decode($apiResultJSON, true);
        curl_close($ch);
        $this->logger(__FUNCTION__, 'API RESULT: ' . $apiResultJSON);
        if (is_array($apiResult)) {
            if (array_key_exists('access_token', $apiResult)) {
                $this->WriteAttributeString('AccessToken', $apiResult['access_token']);
            }
            if (array_key_exists('expires_in', $apiResult)) {
                $this->WriteAttributeInteger('AccessTokenExpiresAt', time() + $apiResult['expires_in'] - 10);
            }
        }

        $this->logger(__FUNCTION__, 'next Token refresh at ' . date('H:i:s, d.m', $this->ReadAttributeInteger('AccessTokenExpiresAt')));
        //$this->WriteAttributeString('Token', $apiResult['access_token']);
    }

    public function resetPairing()
    {
        $this->WriteAttributeString('AccessToken', '');
        $this->WriteAttributeInteger('AccessTokenExpiresAt', 0);
    }

    private function getVehicles()
    {
        $result = $this->sendRequest('/vehicles');
        return $result;
    }

    private function sendRequest(string $endpoint, array $params = [], string $method = 'GET')
    {
        $accessToken = '';

        if ($this->ReadAttributeString('AccessToken') == '') {
            $accessToken = $this->ReadPropertyString('AccessToken'); // Get Token from User Config
        } else {
            $accessToken = $this->ReadAttributeString('AccessToken');
        }
        $tokenExpires = $this->ReadAttributeInteger('AccessTokenExpiresAt');

        if (time() >= ($tokenExpires - 1800)) {
            $this->refreshToken();
            $accessToken = $this->ReadAttributeString('AccessToken');
        }
        $this->logger(__FUNCTION__, 'sent at: ' . time() . ' token expires at: ' . $tokenExpires . ', valid for: ' . ($tokenExpires - time()));

        $ch = curl_init();
        $this->logger(__FUNCTION__, $this->base_url . $endpoint);
        curl_setopt($ch, CURLOPT_URL, $this->base_url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Mozilla/5.0 (Linux; Android 9.0.0; VS985 4G Build/LRX21Y; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36',
            'X-Tesla-User-Agent: TeslaApp/3.4.4-350/fad4a582e/android/9.0.0',
            'Authorization: Bearer ' . $accessToken,
        ]);

        if ($method == 'POST' || $method == 'PUT' || $method == 'DELETE') {
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
            }
            if (in_array($method, ['PUT', 'DELETE'])) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $apiResult = curl_exec($ch);

        $result = [];
        if ($apiResult === false) {
            $result['errorcode'] = 0;
            $result['errormessage'] = curl_error($ch);
            $this->logger(__FUNCTION__, $result['errorcode'] . ': ' . $result['errormessage'], KL_ERROR);
            return false;
        }

        $headerInfo = curl_getinfo($ch);
        curl_close($ch);
        if (!in_array($headerInfo['http_code'], ['200', '201', '204'])) {
            $errorMsg = $headerInfo['http_code'] . ': ';
            if (isset($apiresult)) {
                $errorMsg .= $apiresult;
            }
            $this->logger(__FUNCTION__, $errorMsg, KL_ERROR);
            return false;
        }

        $apiResultJson = json_decode($apiResult, true);

        if (is_array($apiResultJson)) {
            if (array_key_exists('error', $apiResultJson)) {
                $this->logger(__FUNCTION__, $apiResultJson['error']);
                if (fnmatch('*vehicle unavailable*', $apiResultJson['error'])) {
                    $this->logger(__FUNCTION__, 'Vehicle unavailable');
                    return false;
                }
            }
        } else {
            $this->logger(__FUNCTION__, 'Vehicle unavailable');
            return false;
        }

        return $apiResultJson ?? $apiResult;
    }

    private function getCookies()
    {
        return json_decode($this->GetBuffer('Cookies'), true);
        //return $this->cookies;
    }

    /**
     * @param array $cookies
     */
    private function setCookies(array $cookies): void
    {
        $this->SetBuffer('Cookies', json_encode($cookies));
        //$this->cookies = $cookies;
    }

    /**
     * This function make sure, that we always have the up-to-date cookie from tesla
     *
     * @param string $header
     */
    private function grabCookies(string $header)
    {
        $headers = [];
        $output = rtrim($header);
        $data = explode("\n", $output);
        $headers['status'] = $data[0];
        array_shift($data);
        $cookies = [];
        foreach ($data as $part) {
            $middle = explode(':', $part, 2);
            if (!isset($middle[1])) {
                $middle[1] = null;
            }
            $headers[trim($middle[0])] = trim($middle[1]);

            if (strtolower(trim($middle[0])) === 'set-cookie') {
                $cookies[] = trim($middle[1]);
            }
        }

        if (!empty($cookies)) {
            $knownCookies = $this->getCookies();

            foreach ($cookies as $cookie) {
                $key = substr($cookie, 0, 5);

                $knownCookies[$key] = $cookie;
            }

            $this->setCookies($knownCookies);
        }
    }

    private function logger(string $sender, string $message, int $type = KL_DEBUG)
    {
        if ($this->ReadPropertyBoolean('ShowDebugMessages') && ($type == KL_DEBUG || $type == KL_MESSAGE)) {
            IPS_LogMessage('TeslaSplitter ' . $sender, $message);
        } else {
            $this->LogMessage($sender . ' - ' . $message, $type);
        }
    }
}
