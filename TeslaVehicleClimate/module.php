<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaVehicleClimate extends IPSModuleStrict
{
    use TeslaHelper;
    use VariableProfileHelper;

    public function Create() : void
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{72887112-E270-FFC1-04AC-817D82F42027}');

        $this->RegisterVariableBoolean('battery_heater', $this->Translate('Battery Heater'));
        $this->RegisterVariableBoolean('defrost_mode', $this->Translate('Defrost Mode'));
        $this->RegisterVariableBoolean('battery_heater_no_power', $this->Translate('Battery Heater no Power'));
        $this->RegisterVariableString('climate_keeper_mode', $this->Translate('Climate Keeper Mode'));
        $this->RegisterVariableFloat('driver_temp_setting', $this->Translate('Driver Temp Setting'));
        $this->RegisterVariableInteger('fan_status', $this->Translate('Fan Status'));
        $this->RegisterVariableString('inside_temp', $this->Translate('Inside Temp'));
        $this->RegisterVariableString('is_auto_conditioning_on', $this->Translate('Is Auto Conditioning on'));
        $this->RegisterVariableBoolean('is_climate_on', $this->Translate('Is Climate on'));
        $this->RegisterVariableBoolean('is_front_defroster_on', $this->Translate('Is Front Defroster on'));
        $this->RegisterVariableBoolean('is_preconditioning', $this->Translate('Is Preconditioning'));
        $this->RegisterVariableBoolean('is_rear_defroster_on', $this->Translate('Is Rear Defroster on'));
        $this->RegisterVariableString('left_temp_direction', $this->Translate('Left Temp Direction'));
        $this->RegisterVariableFloat('max_avail_temp', $this->Translate('Max Avail Temp'));
        $this->RegisterVariableFloat('min_avail_temp', $this->Translate('Min Avail Temp'));
        $this->RegisterVariableString('outside_temp', $this->Translate('outside_temp'));
        $this->RegisterVariableFloat('passenger_temp_setting', $this->Translate('Passenger Temp Setting'));
        $this->RegisterVariableBoolean('remote_heater_control_enabled', $this->Translate('Remote Heater Control Enabled'));
        $this->RegisterVariableString('right_temp_direction', $this->Translate('Right Temp Direction'));
        $this->RegisterVariableInteger('seat_heater_left', $this->Translate('Seat Heater Left'));
        $this->RegisterVariableInteger('seat_heater_rear_center', $this->Translate('Seat Heater Rear Center'));
        $this->RegisterVariableInteger('seat_heater_rear_left', $this->Translate('Seat Heater Rear Left'));
        $this->RegisterVariableInteger('seat_heater_rear_left_back', $this->Translate('Seat Heater Rear Left Back'));
        $this->RegisterVariableInteger('seat_heater_rear_right', $this->Translate('Seat Heater Rear Right'));
        $this->RegisterVariableInteger('seat_heater_rear_right_back', $this->Translate('Seat Heater Rear Right Back'));
        $this->RegisterVariableInteger('seat_heater_right', $this->Translate('Seat Heater Right'));
        $this->RegisterVariableBoolean('side_mirror_heaters', $this->Translate('Side Mirror Heaters'));
        $this->RegisterVariableBoolean('smart_preconditioning', $this->Translate('Smart Preconditioning'));
        $this->RegisterVariableBoolean('steering_wheel_heater', $this->Translate('Steering Wheel Heater'));
        $this->RegisterVariableInteger('timestamp', $this->Translate('Timestamp'));
        $this->RegisterVariableBoolean('wiper_blade_heater', $this->Translate('Wiper Blade Heater'));
    }


    public function ApplyChanges() : void
    {

        //Never delete this line!
        parent::ApplyChanges();
    }

    public function ReceiveData($JSONString): string
    {
        $response = json_decode($JSONString);

        if ($response->response != null) {
        foreach ($response->response->climate_state as $key => $Value) {
            if (@$this->GetIDForIdent($key) != false) {
                $this->SetValue($key, $Value);
            } else {
                $this->SendDebug('Variable not exist', 'Key: ' . $key . ' - Value: ' . $Value, 0);
            }
        }
    }
    }
}
