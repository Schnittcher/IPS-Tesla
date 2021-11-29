<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';

class TeslaVehicleControl extends IPSModule
{
    use TeslaHelper;

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{0DE3226B-E63E-87DD-7D2F-46C1A17866D9}');

        $this->RegisterPropertyInteger('Interval', 60);

        $this->RegisterVariablenProfiles();

        $this->RegisterVariableBoolean('State', $this->Translate('State'), 'Tesla.State', 0);

        $this->RegisterVariableInteger('WakeUP', $this->Translate('Wake UP'), 'Tesla.WakeUP', 1);
        $this->EnableAction('WakeUP');

        $this->RegisterVariableInteger('Alerts', $this->Translate('Alerts'), 'Tesla.Alerts', 2);
        $this->EnableAction('Alerts');

        $this->RegisterVariableInteger('RemoteStartDrive', $this->Translate('Remote Start Drive'), 'Tesla.RemoteStartDrive', 3);
        $this->EnableAction('RemoteStartDrive');

        $this->RegisterVariableInteger('Doors', $this->Translate('Doors'), 'Tesla.Doors', 4);
        $this->EnableAction('Doors');

        $this->RegisterVariableInteger('Trunk', $this->Translate('Trunk'), 'Tesla.Trunk', 5);
        $this->EnableAction('Trunk');

        $this->RegisterVariableInteger('SunRoof', $this->Translate('Sun Roof'), 'Tesla.SunRoof', 6);
        $this->EnableAction('SunRoof');

        $this->RegisterVariableInteger('ChargePort', $this->Translate('Charge Port'), 'Tesla.ChargePort', 6);
        $this->EnableAction('ChargePort');

        $this->RegisterVariableInteger('ChargeControl', $this->Translate('Charge Control'), 'Tesla.ChargeControl', 7);
        $this->EnableAction('ChargeControl');

        $IDChargeLimit = $this->RegisterVariableString('ChargeLimit', $this->Translate('Charge Limit'), '', 8);
        IPS_SetIcon($IDChargeLimit, 'Battery');
        $this->EnableAction('ChargeLimit');

        $this->RegisterVariableInteger('ChargingAmps', $this->Translate('Charging Amps'), 'Tesla.ChargingAmps', 9);
        $this->EnableAction('ChargingAmps');

        $this->RegisterVariableInteger('ClimateAutoConditioning', $this->Translate('Climate Auto Conditioning'), 'Tesla.ClimateAutoConditioning', 20);
        $this->EnableAction('ClimateAutoConditioning');

        $this->RegisterVariableFloat('DriverTemp', $this->Translate('Driver Temperature'), '~Temperature', 21);
        $this->EnableAction('DriverTemp');

        $this->RegisterVariableFloat('PassengerTemp', $this->Translate('Passenger Temperature'), '~Temperature', 22);
        $this->EnableAction('PassengerTemp');

        $this->RegisterVariableInteger('SetTemperature', $this->Translate('Set Temperature'), 'Tesla.SetTemperature', 23);
        $this->EnableAction('SetTemperature');

        $this->RegisterVariableInteger('RemoteSeatHeaterHeater', $this->Translate('Remote Seat Heater Heater'), 'Tesla.RemoteSeatHeaterHeater', 24);
        $this->EnableAction('RemoteSeatHeaterHeater');

        $this->RegisterVariableInteger('RemoteSeatHeaterLevel', $this->Translate('Remote Seat Heater Level'), 'Tesla.RemoteSeatHeaterLevel', 25);
        $this->EnableAction('RemoteSeatHeaterLevel');

        $this->RegisterVariableInteger('SetRemoteSeatHeater', $this->Translate('Set Remote Seat Heater'), 'Tesla.SetRemoteSeatHeater', 26);
        $this->EnableAction('SetRemoteSeatHeater');

        $this->RegisterVariableBoolean('RemoteSteeringWheelHeater', $this->Translate('Remote Steering Wheel Heater'), 'Tesla.RemoteSteeringWheelHeater', 27);
        $this->EnableAction('RemoteSteeringWheelHeater');

        $this->RegisterVariableInteger('MediaPlayControl', $this->Translate('Media Play Control'), 'Tesla.MediaPlayControl', 28);
        $this->EnableAction('MediaPlayControl');

        $this->RegisterVariableInteger('MediaVolume', $this->Translate('Media Volume'), 'Tesla.MediaVolume', 29);
        $this->EnableAction('MediaVolume');

        $this->RegisterTimer('Tesla_UpdateState', 0, 'Tesla_State($_IPS[\'TARGET\']);');
    }

    public function Destroy()
    {
        $this->UnregisterTimer('Tesla_UpdateState');
    }

    public function ApplyChanges()
    {

        //Never delete this line!
        parent::ApplyChanges();

        $this->SetTimerInterval('Tesla_UpdateState', $this->ReadPropertyInteger('Interval') * 1000);
    }

    public function State()
    {
        $state = $this->isOnline();
        switch ($state) {
            case 'online':
                SetValue(IPS_GetObjectIDByIdent('State', $this->InstanceID), true);
                return $state;
                break;
            case 'asleep':
                SetValue(IPS_GetObjectIDByIdent('State', $this->InstanceID), false);
                return $state;
                break;
            default:
                $this->SendDebug(__FUNCTION__, $state, 0);
                break;
        }
    }

    public function WakeUP()
    {
        return $this->sendData('WakeUP');
    }

    public function HonkHorn()
    {
        return $this->sendData('HonkHorn');
    }

    public function FlashLights()
    {
        return $this->sendData('FlashLights');
    }

    public function RemoteStartDrive()
    {
        return $this->sendData('RemoteStartDrive');
    }

    //Speed Limit Functions
    public function SetSpeedLimit(int $value)
    {
        $params = ['limit_mph' => $value];
        return $this->sendData('SpeedLimitSetLimit', $params);
    }

    public function ActivateSpeedLimit(int $value)
    {
        $params = ['pin' => $value];
        return $this->sendData('SpeedLimitActivate', $params);
    }

    public function DeactivateSpeedLimit(int $value)
    {
        $params = ['pin' => $value];
        return $this->sendData('SpeedLimitDeactivate', $params);
    }

    public function ClearPinSpeedLimit(int $value)
    {
        $params = ['pin' => $value];
        return $this->sendData('SpeedLimitClearPin', $params);
    }

    //Valet Mode Function
    public function SetValetMode(int $pin, bool $value)
    {
        $params = [
            'on'       => $value,
            'password' => $pin
        ];
        return $this->sendData('SetValetMode', $params);
    }

    public function ResetValetPin()
    {
        return $this->sendData('ResetValetPin');
    }

    //Senty Mode Function
    public function SetSentryMode(bool $value)
    {
        $params = ['on' => $value];
        return $this->sendData('SetSentryMode', $params);
    }

    //Door Functions
    public function DoorUnlock()
    {
        return $this->sendData('DoorUnlock');
    }

    public function DoorLock()
    {
        return $this->sendData('DoorLock');
    }

    //Frunk/Trunk Functions
    //Value = rear or front
    public function ActuateTrunk(string $value)
    {
        $params = ['which_trunk' => $value];
        return $this->sendData('ActuateTrunk', $params);
    }

    //Functions for Sunroof
    //$value vent or close
    public function SunRoofControl(string $value)
    {
        $params = ['state' => $value];
        return $this->sendData('SunRoofControl', $params);
    }

    //Functions for Charging
    public function ChargePortDoorOpen()
    {
        return $this->sendData('ChargePortDoorOpen');
    }

    public function ChargePortDoorClose()
    {
        return $this->sendData('ChargePortDoorClose');
    }

    public function ChargeStart()
    {
        return $this->sendData('ChargeStart');
    }

    public function ChargeStop()
    {
        return $this->sendData('ChargeStop');
    }

    public function ChargePortStandard()
    {
        return $this->sendData('ChargeStandard');
    }

    public function ChargeMaxRange()
    {
        return $this->sendData('ChargeMaxRange');
    }

    public function SetChargeLimit(int $value)
    {
        $params = ['percent' => $value];
        return $this->sendData('SetChargeLimit', $params);
    }

    public function SetChargingAmps(int $value)
    {
        $params = ['charging_amps' => $value];
        return $this->sendData('SetChargingAmps', $params);
    }

    //Climate Functions
    public function AutoConditioningStart()
    {
        return $this->sendData('AutoConditioningStart');
    }

    public function AutoConditioningStop()
    {
        return $this->sendData('AutoConditioningStop');
    }

    public function SetTemps(float $driver_temp, float $passenger_temp)
    {
        $params = [
            'driver_temp'    => $driver_temp,
            'passenger_temp' => $passenger_temp
        ];
        return $this->sendData('SetTemps', $params);
    }

    public function RemoteSeatHeaterRequest(int $heater, int $level)
    {
        $params = [
            'heater' => $heater,
            'level'  => $level
        ];
        return $this->sendData('SetTemps', $params);
    }

    public function RemoteSteeringWheelHeaterRequest(bool $value)
    {
        $params = ['on' => $value];
        return $this->sendData('RemoteSteeringWheelHeaterRequest', $params);
    }

    //Media Functions
    public function MediaTogglePlayback()
    {
        return $this->sendData('MediaTogglePlayback');
    }

    public function MediaNextTrack()
    {
        return $this->sendData('MediaNextTrack');
    }

    public function MediaPrevTrack()
    {
        return $this->sendData('MediaPrevTrack');
    }

    public function MediaNextFav()
    {
        return $this->sendData('MediaNextFav');
    }

    public function MediaPrevFav()
    {
        return $this->sendData('MediaPrevFav');
    }

    public function MediaVolumeUp()
    {
        return $this->sendData('MediaVolumeUp');
    }

    public function MediaVolumeDown()
    {
        return $this->sendData('MediaVolumeDown');
    }

    /*TODO Navigation
     *
     *
     *
     *
     */

    /*TODO Software Updates
     *
     *
     *
     *
     */

    public function RequestAction($Ident, $Value)
    {
        $this->SendDebug(__FUNCTION__ . ' Ident', $Ident, 0);
        $this->SendDebug(__FUNCTION__ . ' Value', $Value, 0);
        switch ($Ident) {
            case 'WakeUP':
                $this->WakeUP();
                break;
            case 'Alerts':
                switch ($Value) {
                    case 1:
                        $this->HonkHorn();
                        break;
                    case 2:
                        $this->FlashLights();
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            case 'RemoteStartDrive':
                $this->RemoteStartDrive();
                break;
            case 'Doors':
                switch ($Value) {
                    case 1:
                        $this->DoorUnlock();
                        break;
                    case 2:
                        $this->DoorLock();
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            case 'Trunk':
                switch ($Value) {
                    case 1:
                        $this->ActuateTrunk('rear');
                        break;
                    case 2:
                        $this->ActuateTrunk('front');
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            case 'SunRoof':
                switch ($Value) {
                    case 1:
                        $this->SunRoofControl('vent');
                        break;
                    case 2:
                        $this->SunRoofControl('close');
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            case 'ChargePort':
                switch ($Value) {
                    case 1:
                        $this->ChargePortDoorOpen();
                        break;
                    case 2:
                        $this->ChargePortDoorClose();
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            case 'ChargeControl':
                switch ($Value) {
                    case 1:
                        $this->ChargeStart();
                        break;
                    case 2:
                        $this->ChargeStop();
                        break;
                    case 3:
                        $this->ChargePortStandard();
                        break;
                    case 4:
                        $this->ChargeMaxRange();
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            case 'ChargeLimit':
                $this->SetChargeLimit(intval($Value));
                break;
            case 'ChargingAmps':
                $this->SetChargingAmps(intval($Value));
                break;
            case 'ClimateAutoConditioning':
                switch ($Value) {
                    case 1:
                        $this->AutoConditioningStart();
                        break;
                    case 2:
                        $this->AutoConditioningStop();
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            case 'DriverTemp':
                SetValue(IPS_GetObjectIDByIdent('DriverTemp', $this->InstanceID), floatval($Value));
                break;
            case 'PassengerTemp':
                SetValue(IPS_GetObjectIDByIdent('PassengerTemp', $this->InstanceID), floatval($Value));
                break;
            case 'SetTemperature':
                $driver_temp = floatval(GetValue(IPS_GetObjectIDByIdent('DriverTemp', $this->InstanceID)));
                $passenger_temp = floatval(GetValue(IPS_GetObjectIDByIdent('PassengerTemp', $this->InstanceID)));
                $this->SetTemps($driver_temp, $passenger_temp);
                break;
            case 'RemoteSeatHeaterHeater':
                SetValue(IPS_GetObjectIDByIdent('RemoteSeatHeaterHeater', $this->InstanceID), intval($Value));
                break;
            case 'RemoteSeatHeaterLevel':
                SetValue(IPS_GetObjectIDByIdent('RemoteSeatHeaterLevel', $this->InstanceID), intval($Value));
                break;
            case 'RemoteSeatHeater':
                $heater = intval(GetValue(IPS_GetObjectIDByIdent('RemoteSeatHeaterHeater', $this->InstanceID)));
                $level = intval(GetValue(IPS_GetObjectIDByIdent('RemoteSeatHeaterLevel', $this->InstanceID)));
                $this->RemoteSeatHeaterRequest($heater, $level);
                break;
            case 'RemoteSteeringWheelHeater':
                $this->RemoteSteeringWheelHeaterRequest($Value);
                break;
            case 'MediaPlayControl':
                switch ($Value) {
                    case 1:
                        $this->MediaTogglePlayback();
                        break;
                    case 2:
                        $this->MediaNextTrack();
                        break;
                    case 3:
                        $this->MediaPrevTrack();
                        break;
                    case 4:
                        $this->MediaNextFav();
                        break;
                    case 5:
                        $this->MediaPrevFav();
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            case 'MediaVolume':
                switch ($Value) {
                    case 1:
                        $this->MediaVolumeUp();
                        break;
                    case 2:
                        $this->MediaVolumeDown();
                        break;
                    default:
                        $this->SendDebug(__FUNCTION__ . ' ' . $Ident, 'Undefined Value ' . $Value, 0);
                        break;
                }
                break;
            default:
                $this->SendDebug(__FUNCTION__, 'Undefined Ident', 0);
                break;
        }
    }

    private function sendData(string $command, $params = '')
    {
        $Data['DataID'] = '{5147BF5F-95B4-BA79-CD98-F05D450F79CB}';
        $Buffer['Command'] = $command;
        $Buffer['Params'] = $params;

        $Data['Buffer'] = $Buffer;
        $Data = json_encode($Data);

        $Data = json_decode($this->SendDataToParent($Data), true);

        if (!$Data) {
            return false;
        }

        if (array_key_exists('result', $Data['response'])) {
            return $Data['response']['result'];
        } else {
            return false;
        }
    }

    private function RegisterVariablenProfiles()
    {
        //Profile for Alerts
        if (!IPS_VariableProfileExists('Tesla.Alert')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Honk Horn'), 'Speaker', 0xFF0000];
            $Associations[] = [2, $this->Translate('Flash Lights'), 'Light', 0xFFFF00];
            $this->RegisterProfileIntegerEx('Tesla.Alerts', 'Alert', '', '', $Associations);
        }

        //Profile for Doors
        if (!IPS_VariableProfileExists('Tesla.Doors')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Unlock'), '', 0xFF0000];
            $Associations[] = [2, $this->Translate('Lock'), '', 0x7CFC00];
            $this->RegisterProfileIntegerEx('Tesla.Doors', 'Lock', '', '', $Associations);
        }

        //Trunk
        if (!IPS_VariableProfileExists('Tesla.Trunk')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Rear'), '', -1];
            $Associations[] = [2, $this->Translate('Front'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.Trunk', 'Information', '', '', $Associations);
        }

        //Profile for SunRoof
        if (!IPS_VariableProfileExists('Tesla.SunRoof')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Vent'), '', 0xFF0000];
            $Associations[] = [2, $this->Translate('Close'), '', 0x7CFC00];
            $this->RegisterProfileIntegerEx('Tesla.SunRoof', 'Sun', '', '', $Associations);
        }

        //Profile for Charge Port
        if (!IPS_VariableProfileExists('Tesla.ChargePort')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Open'), '', 0xFF0000];
            $Associations[] = [2, $this->Translate('Close'), '', 0x7CFC00];
            $this->RegisterProfileIntegerEx('Tesla.ChargePort', 'Lock', '', '', $Associations);
        }

        //Profile For Charge Control
        if (!IPS_VariableProfileExists('Tesla.ChargeControl')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Start'), '', -1];
            $Associations[] = [2, $this->Translate('Stop'), '', -1];
            $Associations[] = [3, $this->Translate('Standard'), '', -1];
            $Associations[] = [4, $this->Translate('Max Range'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.ChargeControl', 'Battery', '', '', $Associations);
        }

        //Profile For Charge Amps
        if (!IPS_VariableProfileExists('Tesla.ChargingAmps')) {
            $this->RegisterProfileInteger('Tesla.ChargingAmps', 'Battery', '', '', 5, 32, 1);
        }

        //Profile for Climate Auto Conditioning
        if (!IPS_VariableProfileExists('Tesla.ClimateAutoConditioning')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Start'), '', -1];
            $Associations[] = [2, $this->Translate('Stop'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.ClimateAutoConditioning', 'Climate', '', '', $Associations);
        }

        //Profile for Climate Remote Seat Heater Heater
        if (!IPS_VariableProfileExists('Tesla.RemoteSeatHeaterHeater')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Driver'), '', -1];
            $Associations[] = [2, $this->Translate('Passenger'), '', -1];
            $Associations[] = [3, $this->Translate('Rear left'), '', -1];
            $Associations[] = [4, $this->Translate('Rear center'), '', -1];
            $Associations[] = [5, $this->Translate('Rear right'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.RemoteSeatHeaterHeater', 'Climate', '', '', $Associations);
        }

        //Profile for Climate Remote Seat Heater Level
        if (!IPS_VariableProfileExists('Tesla.RemoteSeatHeaterLevel')) {
            $this->RegisterProfileInteger('Tesla.RemoteSeatHeaterLevel', 'Climate', '', '', 0, 3, 1);
        }
        //Profile for Remote Seat Heater
        if (!IPS_VariableProfileExists('Tesla.SetRemoteSeatHeater')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Set Remote Seat Heater'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.SetRemoteSeatHeater', 'Temperature', '', '', $Associations);
        }

        //Profile for Climate Remote Steering Wheel Heater
        if (!IPS_VariableProfileExists('Tesla.RemoteSteeringWheelHeater')) {
            $this->RegisterProfileBooleanEx('Tesla.RemoteSteeringWheelHeater', 'Climate', '', '', [
                [false, 'Off',  '', -1],
                [true, 'On',  '', -1],
            ]);
        }

        //Profile for Tesla State
        if (!IPS_VariableProfileExists('Tesla.State')) {
            $this->RegisterProfileBooleanEx('Tesla.State', 'Power', '', '', [
                [false, 'Standby',  '', -1],
                [true, 'Online',  '', -1],
            ]);
        }

        //Profile for Media Playback
        if (!IPS_VariableProfileExists('Tesla.MediaPlayControl')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Toggle Playback'), '', -1];
            $Associations[] = [2, $this->Translate('Next Track'), '', -1];
            $Associations[] = [3, $this->Translate('Prev Track'), '', -1];
            $Associations[] = [4, $this->Translate('Next Favorite'), '', -1];
            $Associations[] = [5, $this->Translate('Prev Favorite'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.MediaPlayControl', 'Music', '', '', $Associations);
        }

        //Profile for Media Volume
        if (!IPS_VariableProfileExists('Tesla.MediaVolume')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Up'), '', -1];
            $Associations[] = [2, $this->Translate('Down'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.MediaVolume', 'Speaker', '', '', $Associations);
        }

        //Profile for Set Temperature
        if (!IPS_VariableProfileExists('Tesla.SetTemperature')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Set Temperature'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.SetTemperature', 'Temperature', '', '', $Associations);
        }

        //Profile for WakeUP
        if (!IPS_VariableProfileExists('Tesla.WakeUP')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('WakeUP'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.WakeUP', 'Clock', '', '', $Associations);
        }

        //Profile for Remote Start Drive
        if (!IPS_VariableProfileExists('Tesla.RemoteStartDrive')) {
            $Associations = [];
            $Associations[] = [1, $this->Translate('Remote Start'), '', -1];
            $this->RegisterProfileIntegerEx('Tesla.RemoteStartDrive', 'Key', '', '', $Associations);
        }
    }
}
