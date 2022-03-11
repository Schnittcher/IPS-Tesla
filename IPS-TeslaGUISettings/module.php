<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaGUISettings extends IPSModule
{
    use TeslaHelper;
    use VariableProfileHelper;

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{0DE3226B-E63E-87DD-7D2F-46C1A17866D9}');

        $this->RegisterPropertyInteger('Interval', 60);

        $this->RegisterVariableBoolean('gui_24_hour_time', $this->Translate('GUI 24 Hour Time'));
        $this->RegisterVariableString('gui_charge_rate_units', $this->Translate('GUI Charge Rate Units'));
        $this->RegisterVariableString('gui_distance_units', $this->Translate('GUI Distance Units'));
        $this->RegisterVariableString('gui_range_display', $this->Translate('GUI Range Display'));
        $this->RegisterVariableString('gui_temperature_units', $this->Translate('GUI Temperature Units'));
        $this->RegisterVariableString('timestamp', $this->Translate('timestamp'));

        $this->RegisterTimer('Tesla_UpdateGUISettings', 0, 'Tesla_FetchData($_IPS[\'TARGET\']);');
    }

    public function Destroy()
    {
        $this->UnregisterTimer('Tesla_UpdateGUISettings');
    }

    public function ApplyChanges()
    {

        //Never delete this line!
        parent::ApplyChanges();
    }

    public function FetchData()
    {
        $Data['DataID'] = '{5147BF5F-95B4-BA79-CD98-F05D450F79CB}';

        $Buffer['Command'] = 'GUISettings';
        $Buffer['Params'] = '';

        $Data['Buffer'] = $Buffer;

        $Data = json_encode($Data);

        $Data = json_decode($this->SendDataToParent($Data), true);
        if (!$Data) {
            return false;
        }
        foreach ($Data['response'] as $key => $Value) {
            if (@$this->GetIDForIdent($key) != false) {
                $this->SetValue($key, $Value);
            } else {
                $this->SendDebug('Variable not exist', 'Key: ' . $key . ' - Value: ' . $Value, 0);
            }
        }
    }
}
