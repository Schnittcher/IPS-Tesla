<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaVehicleConfig extends IPSModuleStrict
{
    use TeslaHelper;
    use VariableProfileHelper;

    public function Create(): void
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{72887112-E270-FFC1-04AC-817D82F42027}');

        $this->RegisterVariableBoolean('can_accept_navigation_requests', $this->Translate('Can Accept Navigation Requests'));
        $this->RegisterVariableBoolean('can_actuate_trunks', $this->Translate('Can Actuate Trunks'));
        $this->RegisterVariableString('car_special_type', $this->Translate('Car Special Type'));
        $this->RegisterVariableString('car_type', $this->Translate('Car Type'));
        $this->RegisterVariableString('charge_port_type', $this->Translate('Charge Port Type'));
        $this->RegisterVariableBoolean('eu_vehicle', $this->Translate('EU Vehicle'));
        $this->RegisterVariableString('exterior_color', $this->Translate('Exterior Color'));
        $this->RegisterVariableBoolean('has_air_suspension', $this->Translate('Has Air Suspension'));
        $this->RegisterVariableBoolean('has_ludicrous_mode', $this->Translate('Has Ludicrous Mode'));
        $this->RegisterVariableInteger('key_version', $this->Translate('Key Version'));
        $this->RegisterVariableBoolean('motorized_charge_port', $this->Translate('Motorized Charge Port'));
        $this->RegisterVariableString('perf_config', $this->Translate('Perf Config'));
        $this->RegisterVariableBoolean('plg', $this->Translate('PLG'));
        $this->RegisterVariableInteger('rear_seat_heaters', $this->Translate('Rear Seat Heaters'));
        $this->RegisterVariableInteger('rear_seat_type', $this->Translate('Rear Seat Type'));
        $this->RegisterVariableBoolean('rhd', $this->Translate('RHD'));
        $this->RegisterVariableString('roof_color', $this->Translate('Roof Color'));
        $this->RegisterVariableInteger('seat_type', $this->Translate('Seat type'));
        $this->RegisterVariableString('spoiler_type', $this->Translate('Spoiler Type'));
        $this->RegisterVariableInteger('sun_roof_installed', $this->Translate('Sun Roof Installed'));
        $this->RegisterVariableString('third_row_seats', $this->Translate('Third Row Seats'));
        $this->RegisterVariableInteger('timestamp', $this->Translate('Timestamp'));
        $this->RegisterVariableString('trim_badging', $this->Translate('Trim Badging'));
        $this->RegisterVariableString('wheel_type', $this->Translate('Wheel Type'));
    }

    public function ApplyChanges(): void
    {
        //Never delete this line!
        parent::ApplyChanges();
    }

    public function ReceiveData($JSONString): string
    {
        $response = json_decode($JSONString);

        if ($response->response != null) {
            foreach ($response->response->vehicle_config as $key => $Value) {
                if (@$this->GetIDForIdent($key) != false) {
                    $this->SetValue($key, $Value);
                } else {
                    $this->SendDebug('Variable not exist', 'Key: ' . $key . ' - Value: ' . $Value, 0);
                }
            }
        }
        return '';
    }
}
