<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaVehicleCharging extends IPSModuleStrict
{
    use TeslaHelper;
    use VariableProfileHelper;

    public function Create() : void
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{D5994951-CD92-78B7-A059-3D423FCB599A}');

        $this->RegisterPropertyString('VIN','');
        $this->RegisterPropertyInteger('Interval', 60);

        $this->RegisterVariableBoolean('battery_heater_on', $this->Translate('Battery Heater On'), '~Switch');
        $this->RegisterVariableInteger('battery_level', $this->Translate('Battery Level'));
        $this->RegisterVariableFloat('battery_range', $this->Translate('Battery Range'));
        $this->RegisterVariableInteger('charge_current_request', $this->Translate('Charge Current Request'));
        $this->RegisterVariableInteger('charge_current_request_max', $this->Translate('Charge Current Request Max'));
        $this->RegisterVariableBoolean('charge_enable_request', $this->Translate('Charge Enable Request'), '~Switch');
        $this->RegisterVariableFloat('charge_energy_added', $this->Translate('Charge Energy Added'));
        $this->RegisterVariableInteger('charge_limit_soc', $this->Translate('Charge Limit Soc'));
        $this->RegisterVariableInteger('charge_limit_soc_max', $this->Translate('Charge Limit Soc Max'));
        $this->RegisterVariableInteger('charge_limit_soc_min', $this->Translate('Charge Limit Soc Min'));
        $this->RegisterVariableInteger('charge_limit_soc_std', $this->Translate('Charge Limit Soc Std'));
        $this->RegisterVariableFloat('charge_miles_added_ideal', $this->Translate('Charge Miles Added Ideal'));
        $this->RegisterVariableFloat('charge_miles_added_rated', $this->Translate('Charge Miles Added Rated'));
        $this->RegisterVariableBoolean('charge_port_cold_weather_mode', $this->Translate('Charge Port Cold Weather Mode'), '~Switch');
        $this->RegisterVariableBoolean('charge_port_door_open', $this->Translate('Charge Port Door Open'), '~Switch');
        $this->RegisterVariableString('charge_port_latch', $this->Translate('Charge Port Latch'));
        $this->RegisterVariableFloat('charge_rate', $this->Translate('Charge Rate'));
        $this->RegisterVariableBoolean('charge_to_max_range', $this->Translate('Charge To Max Range'));
        $this->RegisterVariableInteger('charger_actual_current', $this->Translate('Charger Actual Current'));
        $this->RegisterVariableString('charger_phases', $this->Translate('Charger Phases'));
        $this->RegisterVariableInteger('charger_pilot_current', $this->Translate('Charger Pilot Current'));
        $this->RegisterVariableInteger('charger_power', $this->Translate('Charger Power'));
        $this->RegisterVariableInteger('charger_voltage', $this->Translate('Charger Voltage'));
        $this->RegisterVariableString('charging_state', $this->Translate('Charging State'));
        $this->RegisterVariableString('conn_charge_cable', $this->Translate('Conn Charge Cable'));
        $this->RegisterVariableFloat('est_battery_range', $this->Translate('Est Battery Range'));
        $this->RegisterVariableString('fast_charger_brand', $this->Translate('Fast Charger Brand'));
        $this->RegisterVariableBoolean('fast_charger_present', $this->Translate('Fast Charger Present'));
        $this->RegisterVariableString('fast_charger_type', $this->Translate('Fast Charger Type'));
        $this->RegisterVariableFloat('ideal_battery_range', $this->Translate('Ideal Battery Range'));
        $this->RegisterVariableBoolean('managed_charging_active', $this->Translate('Managed Charging Active'));
        $this->RegisterVariableString('managed_charging_start_time', $this->Translate('Managed Charging Start Time'));
        $this->RegisterVariableBoolean('managed_charging_user_canceled', $this->Translate('Managed Charging User Canceled'));
        $this->RegisterVariableInteger('max_range_charge_counter', $this->Translate('Max Range Charge Counter'));
        $this->RegisterVariableBoolean('not_enough_power_to_heat', $this->Translate('Not Enough Power To Heat'));
        $this->RegisterVariableBoolean('scheduled_charging_pending', $this->Translate('Scheduled Charging Pending'));
        $this->RegisterVariableString('scheduled_charging_start_time', $this->Translate('Scheduled Charging Start Time'));
        $this->RegisterVariableFloat('time_to_full_charge', $this->Translate('Time To Full Charge'));
        $this->RegisterVariableInteger('timestamp', $this->Translate('Timestamp'));
        $this->RegisterVariableBoolean('trip_charging', $this->Translate('Trip Charging'));
        $this->RegisterVariableInteger('usable_battery_level', $this->Translate('Usable Battery Level'));
        $this->RegisterVariableString('user_charge_enable_request', $this->Translate('User Charge Enable Request'));

        $this->RegisterTimer('Tesla_UpdateCharging', 0, 'Tesla_FetchData($_IPS[\'TARGET\']);');
    }

    public function Destroy() : void
    {
        $this->UnregisterTimer('Tesla_UpdateCharging');
    }

    public function ApplyChanges() : void
    {

        //Never delete this line!
        parent::ApplyChanges();

        $this->SetTimerInterval('Tesla_UpdateCharging', $this->ReadPropertyInteger('Interval') * 1000);
    }

    public function FetchData() : void
    {
        $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/vehicle_data',
            'Payload'  => ''
        ])))->response->charge_state;

        foreach ($response as $key => $Value) {
            if (@$this->GetIDForIdent($key) != false) {
                $this->SetValue($key, $Value);
            } else {
                $this->SendDebug('Variable not exist', 'Key: ' . $key . ' - Value: ' . $Value, 0);
            }
        }
    }
}
