<?php

declare(strict_types=1);

trait TeslaHelper
{
    protected function isOnline()
    {
        $Data['DataID'] = '{5147BF5F-95B4-BA79-CD98-F05D450F79CB}';

        $Buffer['Command'] = 'IsOnline';
        $Buffer['Params'] = '';

        $Data['Buffer'] = $Buffer;

        $Data = json_encode($Data);

        $Data = json_decode($this->SendDataToParent($Data), true);
        return $Data['response']['state'];
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
