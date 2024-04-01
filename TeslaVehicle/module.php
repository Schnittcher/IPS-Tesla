<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/TeslaHelper.php';
require_once __DIR__ . '/../libs/vendor/SymconModulHelper/VariableProfileHelper.php';

class TeslaVehicle extends IPSModuleStrict
{
    use TeslaHelper;
    use VariableProfileHelper;

    public function Create(): void
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{72887112-E270-FFC1-04AC-817D82F42027}');

        $this->RegisterVariableInteger('api_version', $this->Translate('API Version'));
        $this->RegisterVariableString('autopark_state_v2', $this->Translate('Autopark State V2'));
        $this->RegisterVariableString('autopark_style', $this->Translate('Autopark Style'));
        $this->RegisterVariableBoolean('calendar_supported', $this->Translate('Calendar Supported'));
        $this->RegisterVariableString('car_version', $this->Translate('Car Version'));
        $this->RegisterVariableInteger('center_display_state', $this->Translate('Center Display State'));
        $this->RegisterVariableInteger('df', $this->Translate('DF'));
        $this->RegisterVariableInteger('dr', $this->Translate('DR'));
        $this->RegisterVariableInteger('ft', $this->Translate('FT'));
        $this->RegisterVariableBoolean('homelink_nearby', $this->Translate('Homelink Nearby'));
        $this->RegisterVariableBoolean('is_user_present', $this->Translate('Is User Present'));

        $this->RegisterVariableString('last_autopark_error', $this->Translate('Last Autopark Error'));
        $this->RegisterVariableBoolean('locked', $this->Translate('Locked'));
        $this->RegisterVariableBoolean('media_state_remote_control_enabled', $this->Translate('Media State Remote Control Enabled'));

        $this->RegisterVariableBoolean('notifications_supported', $this->Translate('Notifications Supported'));
        $this->RegisterVariableFloat('odometer', $this->Translate('Odometer'));
        $this->RegisterVariableBoolean('parsed_calendar_supported', $this->Translate('Parsed Calendar Supported'));
        $this->RegisterVariableInteger('pf', $this->Translate('PF'));
        $this->RegisterVariableInteger('pr', $this->Translate('PR'));
        $this->RegisterVariableBoolean('remote_start', $this->Translate('Remote Start'));
        $this->RegisterVariableBoolean('remote_start_enabled', $this->Translate('Remote Start Enabled'));
        $this->RegisterVariableBoolean('remote_start_supported', $this->Translate('Remote Start Supported'));
        $this->RegisterVariableInteger('rt', $this->Translate('RT'));
        $this->RegisterVariableBoolean('sentry_mode', $this->Translate('Sentry Mode'));
        $this->RegisterVariableInteger('software_update_expected_duration_sec', $this->Translate('Software Update Expected Duration Sec'));
        $this->RegisterVariableString('software_update_status', $this->Translate('Software Update Status'));
        
        $this->RegisterVariableInteger('fd_window', $this->Translate('FD Window'));
        $this->RegisterVariableInteger('fp_window', $this->Translate('FP Window'));
        $this->RegisterVariableInteger('rd_window', $this->Translate('RD Window'));
        $this->RegisterVariableInteger('rp_window', $this->Translate('RP Window'));

        //Speed Limit JSON
        $this->RegisterVariableBoolean('speed_limit_mode_active', $this->Translate('Speed Limit Mode Active'));
        $this->RegisterVariableFloat('speed_limit_mode_current_limit_mph', $this->Translate('Speed Limit Mode Current Limit Mph'));
        $this->RegisterVariableInteger('speed_limit_mode_max_limit_mph', $this->Translate('Speed Limit Mode Max Limit Mph'));
        $this->RegisterVariableInteger('speed_limit_mode_min_limit_mph', $this->Translate('Speed Limit Mode Min Limit Mph'));
        $this->RegisterVariableBoolean('speed_limit_mode_pin_code_set', $this->Translate('Speed Limit Mode Pin Code Set'));

        $this->RegisterVariableInteger('sun_roof_percent_open', $this->Translate('Sun Roof Percent Open'));
        $this->RegisterVariableString('sun_roof_state', $this->Translate('Sun Roof State'));
        $this->RegisterVariableInteger('timestamp', $this->Translate('Timestamp'));
        $this->RegisterVariableBoolean('valet_mode', $this->Translate('Valet Mode'));
        $this->RegisterVariableBoolean('valet_pin_needed', $this->Translate('Valet Pin Needed'));
        $this->RegisterVariableString('vehicle_name', $this->Translate('Vehicle Name'));

        $this->RegisterVariableFloat('tpms_pressure_fl', $this->Translate('Front left tire pressure'));
        $this->RegisterVariableFloat('tpms_pressure_fr', $this->Translate('Front right tire pressure'));
        $this->RegisterVariableFloat('tpms_pressure_rl', $this->Translate('Rear left tire pressure'));
        $this->RegisterVariableFloat('tpms_pressure_rr', $this->Translate('Rear right tire pressure'));
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
            foreach ($response->response->vehicle_state as $key => $Value) {
                switch ($key) {
                case 'speed_limit_mode':
                    $SpeedLimitMode = $Value;
                    foreach ($SpeedLimitMode as $SpeedLimitKey => $SpeedLimitValue) {
                        if (@$this->GetIDForIdent('speed_limit_mode_' . $SpeedLimitKey) != false) {
                            $this->SetValue('speed_limit_mode_' . $SpeedLimitKey, $SpeedLimitValue);
                        } else {
                            $this->SendDebug('Variable not exist', 'Key: speed_limit_mode_' . $SpeedLimitKey . ' - Value: ' . $SpeedLimitValue, 0);
                        }
                    }
                    break;
                case 'software_update':
                    $SoftwareUpdate = $Value;
                    foreach ($SoftwareUpdate as $SoftwareUpdateKey => $SoftwareUpdateValue) {
                        if (@$this->GetIDForIdent('software_update_' . $SoftwareUpdateKey) != false) {
                            $this->SetValue('software_update_' . $SoftwareUpdateKey, $SoftwareUpdateValue);
                        } else {
                            $this->SendDebug('Variable not exist', 'Key: software_update_' . $SoftwareUpdateKey . ' - Value: ' . $SoftwareUpdateValue, 0);
                        }
                    }
                    break;
                case 'media_state':
                    $MediaState = $Value;
                    foreach ($MediaState as $MediaStateeKey => $MediaStateValue) {
                        if (@$this->GetIDForIdent('media_state_' . $MediaStateeKey) != false) {
                            $this->SetValue('media_state_' . $MediaStateeKey, $MediaStateValue);
                        } else {
                            $this->SendDebug('Variable not exist', 'Key: media_state_' . $MediaStateeKey . ' - Value: ' . $MediaStateValue, 0);
                        }
                    }
                    break;
                default:
                    $this->SendDebug(__FUNCTION__ . ' ' . $key, $key, 0);
                        if (@$this->GetIDForIdent($key) != false) {
                            $this->SetValue($key, $Value);
                        } else {
                            if (is_object($Value)) {
                                $Value = json_encode($Value);
                            }
                            $this->SendDebug('Variable not exist', 'Key: ' . $key . ' - Value: ' . $Value, 0);
                        }
                    break;
            }
            }
        }
        return '';
    }
}
