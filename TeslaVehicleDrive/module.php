<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaVehicleDrive extends IPSModuleStrict
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

        $this->RegisterVariableString('gps_as_of', $this->Translate('GPS as of'));
        $this->RegisterVariableInteger('heading', $this->Translate('Heading'));
        $this->RegisterVariableString('latitude', $this->Translate('Latitude'));
        $this->RegisterVariableString('longitude', $this->Translate('Longitude'));
        $this->RegisterVariableString('native_latitude', $this->Translate('Native Latitude'));
        $this->RegisterVariableInteger('native_location_supported', $this->Translate('Native Location Supported'));
        $this->RegisterVariableString('native_longitude', $this->Translate('Native Longitude'));
        $this->RegisterVariableString('native_type', $this->Translate('Native Type'));
        $this->RegisterVariableInteger('power', $this->Translate('Power'));
        $this->RegisterVariableString('shift_state', $this->Translate('Shift State'));
        $this->RegisterVariableString('speed', $this->Translate('Speed'));
        $this->RegisterVariableString('timestamp', $this->Translate('Timestamp'));
        $this->RegisterVariableFloat('active_route_latitude', $this->Translate('Active Route Latitude'));
        $this->RegisterVariableFloat('active_route_longitude', $this->Translate('Active Route Longitude'));
        $this->RegisterVariableInteger('active_route_traffic_minutes_delay', $this->Translate('Active Route Traffic Minutes Delay'));
        $this->RegisterVariableInteger('active_route_energy_at_arrival', $this->Translate('Active Route Energy at arrival'));
        $this->RegisterVariableFloat('active_route_miles_to_arrival', $this->Translate('Active Route Miles to arrival'));
        $this->RegisterVariableFloat('active_route_route_destination', $this->Translate('Active Route destination'));

        $this->RegisterTimer('Tesla_UpdateDrive', 0, 'Tesla_FetchData($_IPS[\'TARGET\']);');
    }

    public function Destroy() : void
    {
        $this->UnregisterTimer('Tesla_UpdateDrive');
    }

    public function ApplyChanges() : void
    {

        //Never delete this line!
        parent::ApplyChanges();
    }


    public function FetchData() : void
    {
        $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/vehicle_data',
            'Payload'  => ''
        ])))->response->drive_state;

        IPS_LogMessage('test',print_r($response,true));

        foreach ($response as $key => $Value) {
            if (@$this->GetIDForIdent($key) != false) {
                $this->SetValue($key, $Value);
            } else {
                $this->SendDebug('Variable not exist', 'Key: ' . $key . ' - Value: ' . $Value, 0);
            }
        }
    }
}
