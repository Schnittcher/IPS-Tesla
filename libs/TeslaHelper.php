<?php

declare(strict_types=1);

trait TeslaHelper
{
    protected function isOnline()
    {

        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN'),
            'Payload'  => ''
        ])))->response->state;
    }

    protected function UnregisterTimer($Name)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id > 0) {
            if (!IPS_EventExists($id)) {
                throw new Exception('Timer not present', E_USER_NOTICE);
            }
            IPS_DeleteEvent($id);
        }
    }
}
