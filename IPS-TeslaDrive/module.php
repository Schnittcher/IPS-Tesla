<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaDrive extends IPSModule
{
    use TeslaHelper;
    use VariableProfileHelper;

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{0DE3226B-E63E-87DD-7D2F-46C1A17866D9}');

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

        $this->RegisterTimer('Tesla_UpdateDrive', 0, 'Tesla_FetchData($_IPS[\'TARGET\']);');
    }

    public function Destroy()
    {
        $this->UnregisterTimer('Tesla_UpdateDrive');
    }

    public function ApplyChanges()
    {

        //Never delete this line!
        parent::ApplyChanges();
    }

    public function FetchData()
    {
        $Data['DataID'] = '{5147BF5F-95B4-BA79-CD98-F05D450F79CB}';

        $Buffer['Command'] = 'DriveState';
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
