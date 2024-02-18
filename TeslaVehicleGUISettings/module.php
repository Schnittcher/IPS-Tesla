<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaVehicleGUISettings extends IPSModuleStrict
{
    use TeslaHelper;
    use VariableProfileHelper;

    public function Create(): void
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{D5994951-CD92-78B7-A059-3D423FCB599A}');

        $this->RegisterPropertyString('VIN', '');
        $this->RegisterPropertyInteger('Interval', 60);

        $this->RegisterVariableBoolean('gui_24_hour_time', $this->Translate('GUI 24 Hour Time'));
        $this->RegisterVariableString('gui_charge_rate_units', $this->Translate('GUI Charge Rate Units'));
        $this->RegisterVariableString('gui_distance_units', $this->Translate('GUI Distance Units'));
        $this->RegisterVariableString('gui_range_display', $this->Translate('GUI Range Display'));
        $this->RegisterVariableString('gui_temperature_units', $this->Translate('GUI Temperature Units'));
        $this->RegisterVariableString('timestamp', $this->Translate('timestamp'));

        $this->RegisterTimer('Tesla_UpdateGUISettings', 0, 'Tesla_FetchData($_IPS[\'TARGET\']);');
    }

    public function Destroy(): void
    {
        $this->UnregisterTimer('Tesla_UpdateGUISettings');
    }

    public function ApplyChanges(): void
    {

        //Never delete this line!
        parent::ApplyChanges();
    }

    public function FetchData(): void
    {
        $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/vehicle_data',
            'Payload'  => ''
        ])));

        if ($response->response != null) {
            foreach ($response->response->gui_settings as $key => $Value) {
                if (@$this->GetIDForIdent($key) != false) {
                    $this->SetValue($key, $Value);
                } else {
                    $this->SendDebug('Variable not exist', 'Key: ' . $key . ' - Value: ' . $Value, 0);
                }
            }
        }
    }
}