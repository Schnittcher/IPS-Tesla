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

        $this->ConnectParent('{72887112-E270-FFC1-04AC-817D82F42027}');

        $this->RegisterVariableBoolean('gui_24_hour_time', $this->Translate('GUI 24 Hour Time'));
        $this->RegisterVariableString('gui_charge_rate_units', $this->Translate('GUI Charge Rate Units'));
        $this->RegisterVariableString('gui_distance_units', $this->Translate('GUI Distance Units'));
        $this->RegisterVariableString('gui_range_display', $this->Translate('GUI Range Display'));
        $this->RegisterVariableString('gui_temperature_units', $this->Translate('GUI Temperature Units'));
        $this->RegisterVariableString('timestamp', $this->Translate('timestamp'));
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
            foreach ($response->response->gui_settings as $key => $Value) {
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