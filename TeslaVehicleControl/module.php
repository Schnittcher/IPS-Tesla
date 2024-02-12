<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaVehicleControl extends IPSModuleStrict
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
        $this->EnableAction('ChargeLimit');

        $this->RegisterVariableInteger('ChargingAmps', $this->Translate('Charging Amps'), 'Tesla.ChargingAmps', 9);
        $this->EnableAction('ChargingAmps');

        $this->RegisterVariableBoolean('SetPreconditioningMax', $this->Translate('Set Preconditioning Max'), '~Switch', 0);
        $this->EnableAction('SetPreconditioningMax');

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

    public function Destroy() : void
    {
        $this->UnregisterTimer('Tesla_UpdateState');
    }

    public function ApplyChanges() : void
    {

        //Never delete this line!
        parent::ApplyChanges();

        $this->SetTimerInterval('Tesla_UpdateState', $this->ReadPropertyInteger('Interval') * 1000);
    }

    public function State() : bool
    {
        $state = $this->isOnline();
        switch ($state) {
            case 'online':
                $this->SetValue('State', true);
                return true;
                break;
            case 'asleep':
                $this->SetValue('State', false);
                return false;
                break;
            default:
                $this->SendDebug(__FUNCTION__, $state, 0);
                break;
        }
    }

    public function WakeUP() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/wake_up',
            'Payload'  => '{}'
        ])));
    }

    public function HonkHorn() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/honk_horn',
            'Payload'  => '{}'
        ])));
    }

    public function FlashLights() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/flash_lights',
            'Payload'  => '{}'
        ])));
    }

    public function RemoteStartDrive() :string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/remote_start_drive',
            'Payload'  => '{}'
        ])));
    }

    //Speed Limit Functions
    public function SetSpeedLimit(int $value) : string
    {
        $params = ['limit_mph' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/speed_limit_set_limit',
            'Payload'  => json_decode($params)
        ])));
    }

    public function ActivateSpeedLimit(int $value) : string
    {
        $params = ['pin' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/speed_limit_activate',
            'Payload'  => json_decode($params)
        ])));
    }

    public function DeactivateSpeedLimit(int $value) : string
    {
        $params = ['pin' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/speed_limit_deactivate',
            'Payload'  => json_decode($params)
        ])));
    }

    public function ClearPinSpeedLimit(int $value) : string
    {
        $params = ['pin' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/speed_limit_clear_pin',
            'Payload'  => json_decode($params)
        ])));
    }

    //Valet Mode Function
    public function SetValetMode(int $pin, bool $value) : string
    {
        $params = [
            'on'       => $value,
            'password' => $pin
        ];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/set_valet_mode',
            'Payload'  => json_decode($params)
        ])));
    }

    public function ResetValetPin() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/reset_valet_pin',
            'Payload'  => '{}'
        ])));
    }

    //Senty Mode Function
    public function SetSentryMode(bool $value) : string
    {
        $params = ['on' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/set_sentry_mode',
            'Payload'  => json_decode($params)
        ])));
    }

    //Door Functions
    public function DoorUnlock() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/door_unlock',
            'Payload'  => '{}'
        ])));
    }

    public function DoorLock() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/door_lock',
            'Payload'  => '{}'
        ])));
    }

    //Frunk/Trunk Functions
    //Value = rear or front
    public function ActuateTrunk(string $value) : string
    {
        $params = ['which_trunk' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/actuate_trunk',
            'Payload'  => json_decode($params)
        ])));
    }

    //Functions for Sunroof
    //$value vent or close
    public function SunRoofControl(string $value) : string
    {
        $params = ['state' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/sun_roof_control',
            'Payload'  => json_decode($params)
        ])));
    }

    //Functions for Charging
    public function ChargePortDoorOpen() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/charge_port_door_open',
            'Payload'  => '{}'
        ])));
    }

    public function ChargePortDoorClose() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/charge_port_door_close',
            'Payload'  => '{}'
        ])));
    }

    public function ChargeStart() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/charge_start',
            'Payload'  => '{}'
        ])));
    }

    public function ChargeStop() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/charge_stop',
            'Payload'  => '{}'
        ])));
    }

    public function ChargePortStandard() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/charge_standard',
            'Payload'  => '{}'
        ])));
    }

    public function ChargeMaxRange() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/charge_max_range',
            'Payload'  => '{}'
        ])));
    }

    public function SetChargeLimit(int $value) : string
    {
        $params = ['percent' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/set_charge_limit',
            'Payload'  => json_decode($params)
        ])));
    }

    public function SetChargingAmps(int $value) : string
    {
        $params = ['charging_amps' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/set_charging_amps',
            'Payload'  => json_decode($params)
        ])));
    }

    //Climate Functions
    public function AutoConditioningStart() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/auto_conditioning_start',
            'Payload'  => '{}'
        ])));
    }

    public function AutoConditioningStop() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/auto_conditioning_stop',
            'Payload'  => '{}'
        ])));
    }

    public function SetTemps(float $driver_temp, float $passenger_temp) : string
    {
        $params = [
            'driver_temp'    => $driver_temp,
            'passenger_temp' => $passenger_temp
        ];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/set_temps',
            'Payload'  => json_decode($params)
        ])));
    }

    public function RemoteSeatHeaterRequest(int $heater, int $level) : string
    {
        $params = [
            'heater' => $heater,
            'level'  => $level
        ];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/remote_seat_heater_request',
            'Payload'  => json_decode($params)
        ])));
    }

    public function RemoteSteeringWheelHeaterRequest(bool $value) : string
    {
        $params = ['on' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/remote_steering_wheel_heater_request',
            'Payload'  => json_decode($params)
        ])));
    }
    public function SetPreconditioningMax(bool $value) : string
    {
        $params = ['on' => $value];
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/SetPreconditioningMax',
            'Payload'  => json_decode($params)
        ])));
    }

    //Media Functions
    public function MediaTogglePlayback() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/media_toggle_playback',
            'Payload'  => '{}'
        ])));
    }

    public function MediaNextTrack() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/media_next_track',
            'Payload'  => '{}'
        ])));
    }

    public function MediaPrevTrack() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/media_prev_track',
            'Payload'  => '{}'
        ])));
    }

    public function MediaNextFav() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/media_next_fav',
            'Payload'  => '{}'
        ])));
    }

    public function MediaPrevFav() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/media_prev_fav',
            'Payload'  => '{}'
        ])));
    }

    public function MediaVolumeUp() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/media_volume_up',
            'Payload'  => '{}'
        ])));
    }

    public function MediaVolumeDown() : string
    {
        return $response = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{FB4ED52F-A162-6F23-E7EA-2CBAAF48E662}',
            'Endpoint' => '/api/1/vehicles/' . $this->ReadPropertyString('VIN') . '/command/media_volume_down',
            'Payload'  => '{}'
        ])));
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

    public function RequestAction($Ident, $Value) : void
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
                $this->SetValue('DriverTemp',floatval($Value));
                break;
            case 'PassengerTemp':
                $this->SetValue('PassengerTemp',floatval($Value));
                break;
            case 'SetTemperature':
                $driver_temp = floatval($this->GetValue('DriverTemp'));
                $passenger_temp = floatval($this->GetValue('PassengerTemp'));
                $this->SetTemps($driver_temp, $passenger_temp);
                break;
            case 'RemoteSeatHeaterHeater':
                $this->SetValue('RemoteSeatHeaterHeater', intval($Value));
                break;
            case 'RemoteSeatHeaterLevel':
                $this->SetValue('RemoteSeatHeaterLevel', intval($Value));
                break;
            case 'RemoteSeatHeater':
                $heater = intval($this->GetValue('RemoteSeatHeaterHeater'));
                $level = intval($this->GetValue('RemoteSeatHeaterLevel'));
                $this->RemoteSeatHeaterRequest($heater, $level);
                break;
            case 'RemoteSteeringWheelHeater':
                $this->RemoteSteeringWheelHeaterRequest($Value);
                break;
            case 'SetPreconditioningMax':
                $this->SetPreconditioningMax($Value);
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
