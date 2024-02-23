<?php

declare(strict_types=1);

class TeslaVehicleConnector extends IPSModuleStrict
{
    public function Create(): void
    {
        //Never delete this line!
        parent::Create();
        $this->RegisterPropertyBoolean('Active', false);
        $this->RegisterPropertyString('VIN', '');
        $this->RegisterPropertyInteger('Interval', 700);
        $this->ConnectParent('{D5994951-CD92-78B7-A059-3D423FCB599A}');

        $this->RegisterTimer('Tesla_UpdateData', 0, 'Tesla_FetchData($_IPS[\'TARGET\']);');
    }

    public function Destroy(): void
    {
        $this->UnregisterTimer('Tesla_UpdateData');
    }

    public function ApplyChanges(): void
    {
        //Never delete this line!
        parent::ApplyChanges();

        if ($this->ReadPropertyBoolean('Active')) {
            $this->SetTimerInterval('Tesla_UpdateData', $this->ReadPropertyInteger('Interval') * 1000);
            $this->SetStatus(102);
        } else {
            $this->SetTimerInterval('Tesla_UpdateData', 0);
            $this->SetStatus(104);
        }
    }

    public function FetchData(): void
    {
        $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{83D77BA9-04CB-17F2-A0F9-43293FE8448C}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/vehicle_data',
            'Payload'  => ''
        ])));

        $this->SendDebug('Response', json_encode($response), 0);
        $response->DataID = '{9825E0F7-B5DF-6ED8-4F73-5C7366FBB11F}';
        $this->SendDataToChildren(json_encode($response));
    }

    public function ForwardData($JSONString): string
    {
        $data = json_decode($JSONString);
        $data->DataID = '{83D77BA9-04CB-17F2-A0F9-43293FE8448C}';
        $data->Endpoint = str_replace('%VIN%', $this->ReadPropertyString('VIN'), $data->Endpoint);
        $response = $this->SendDataToParent(json_encode($data));

        return $response;
    }
}
