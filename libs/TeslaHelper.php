<?php

declare(strict_types=1);

trait TeslaHelper
{
    protected function isOnline()
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{675AD8E8-253B-71E3-7304-6C8D9CDCD50B}',
            'Endpoint' => '/api/1/vehicles/%VIN%',
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
