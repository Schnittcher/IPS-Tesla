<?php

declare(strict_types=1);
class TeslaConfigurator extends IPSModuleStrict
{
    public function Create(): void
    {
        //Never delete this line!
        parent::Create();

        //Connect to available splitter or create a new one
        $this->ConnectParent('{D5994951-CD92-78B7-A059-3D423FCB599A}');
    }

    public function GetConfigurationForm(): string
    {
        $data = json_decode(file_get_contents(__DIR__ . '/form.json'));

        $prarentTreeIDs = [];

        if ($this->HasActiveParent()) {
            $vehicles = json_decode($this->SendDataToParent(json_encode([
                'DataID'   => '{83D77BA9-04CB-17F2-A0F9-43293FE8448C}',
                'Endpoint' => '/api/1/vehicles',
                'Payload'  => ''
            ])))->response;

            /*
            $physicalChildren = $this->getPhysicalChildren();
             */

            foreach ($vehicles as $vehicle) {
                array_push($prarentTreeIDs, $vehicle->vin);
                $this->SendDebug('Vehicle', json_encode($vehicle), 0);

                $instanceID = 0; //$this->searchDevice($vehicle->vin);
                //$physicalChildren = array_diff($physicalChildren, [$instanceID]);

                $data->actions[0]->values[] =
                 [
                     'id'           => array_search($vehicle->vin, $prarentTreeIDs) + 1,
                     'vin'          => $vehicle->vin,
                     'display_name' => $vehicle->display_name,
                     'access_type'  => $vehicle->access_type
                 ];
                //Tesla Vehicle
                $data->actions[0]->values[] =
                 [
                     'parent'        => array_search($vehicle->vin, $prarentTreeIDs) + 1,
                     'vin'           => $vehicle->vin,
                     'display_name'  => 'Tesla Vehicle',
                     'access_type'   => '',
                     'instanceID'    => $this->searchDevice('{2B8DD198-A8A8-C724-06F2-BF30C2FDCDA0}', $vehicle->vin),
                     'create'        => [
                         'moduleID'      => '{2B8DD198-A8A8-C724-06F2-BF30C2FDCDA0}',
                         'configuration' => new stdClass()
                     ],
                 ];
                //Tesla Vehicle Charging
                $data->actions[0]->values[] =
                 [
                     'parent'        => array_search($vehicle->vin, $prarentTreeIDs) + 1,
                     'vin'           => $vehicle->vin,
                     'display_name'  => 'Tesla Vehicle Charging',
                     'access_type'   => '',
                     'instanceID'    => $this->searchDevice('{F1D8CB95-00E4-B426-D7A2-B6FDCFAAF364}', $vehicle->vin),
                     'create'        => [
                         'moduleID'      => '{F1D8CB95-00E4-B426-D7A2-B6FDCFAAF364}',
                         'configuration' => new stdClass()
                     ],
                 ];
                //Tesla Vehicle Climate
                $data->actions[0]->values[] =
                [
                    'parent'        => array_search($vehicle->vin, $prarentTreeIDs) + 1,
                    'vin'           => $vehicle->vin,
                    'display_name'  => 'Tesla Vehicle Climate',
                    'access_type'   => '',
                    'instanceID'    => $this->searchDevice('{25101D5C-0935-7306-FBB6-E43AB4D91562}', $vehicle->vin),
                    'create'        => [
                        'moduleID'      => '{25101D5C-0935-7306-FBB6-E43AB4D91562}',
                        'configuration' => new stdClass()
                    ],
                ];
                //Tesla Vehicle Config
                $data->actions[0]->values[] =
                [
                    'parent'        => array_search($vehicle->vin, $prarentTreeIDs) + 1,
                    'vin'           => $vehicle->vin,
                    'display_name'  => 'Tesla Vehicle Config',
                    'access_type'   => '',
                    'instanceID'    => $this->searchDevice('{57AE4590-8CEA-95D6-62EB-36A4B5833368}', $vehicle->vin),
                    'create'        => [
                        'moduleID'      => '{57AE4590-8CEA-95D6-62EB-36A4B5833368}',
                        'configuration' => new stdClass()
                    ],
                ];
                //Tesla Vehicle Control
                $data->actions[0]->values[] =
                [
                    'parent'        => array_search($vehicle->vin, $prarentTreeIDs) + 1,
                    'vin'           => $vehicle->vin,
                    'display_name'  => 'Tesla Vehicle Control',
                    'access_type'   => '',
                    'instanceID'    => $this->searchDevice('{27C492E7-82B0-5E0C-3544-E96C0904480D}', $vehicle->vin),
                    'create'        => [
                        'moduleID'      => '{27C492E7-82B0-5E0C-3544-E96C0904480D}',
                        'configuration' => new stdClass()
                    ],
                ];
                //Tesla Vehicle Drive
                $data->actions[0]->values[] =
                [
                    'parent'        => array_search($vehicle->vin, $prarentTreeIDs) + 1,
                    'vin'           => $vehicle->vin,
                    'display_name'  => 'Tesla Vehicle Drive',
                    'access_type'   => '',
                    'instanceID'    => $this->searchDevice('{83675CDD-3E50-FB21-8AE6-B4FC718A77B6}', $vehicle->vin),
                    'create'        => [
                        'moduleID'      => '{83675CDD-3E50-FB21-8AE6-B4FC718A77B6}',
                        'configuration' => new stdClass()
                    ],
                ];
                //Tesla Vehicle GUI Settings
                $data->actions[0]->values[] =
                [
                    'parent'        => array_search($vehicle->vin, $prarentTreeIDs) + 1,
                    'vin'           => $vehicle->vin,
                    'display_name'  => 'Tesla Vehicle GUI Settings',
                    'access_type'   => '',
                    'instanceID'    => $this->searchDevice('{EF9CE1A3-AF7B-F488-698A-A1C49C798CBF}', $vehicle->vin),
                    'create'        => [[
                        'moduleID'      => '{EF9CE1A3-AF7B-F488-698A-A1C49C798CBF}',
                        'configuration' => new stdClass()
                    ],
                    [
                        'moduleID'      => '{72887112-E270-FFC1-04AC-817D82F42027}',
                        'info'          => $vehicle->vin,
                        'configuration' => [
                            'VIN' => $vehicle->vin
                        ]
                    ]]
                ];
            }
        }

        return json_encode($data);
    }

    private function getPhysicalChildren(): array
    {
        $connectionID = IPS_GetInstance($this->InstanceID);
        $ids = IPS_GetInstanceListByModuleID('{81DE9D16-04F1-DE04-AC2D-77096E0A405A}');
        $result = [];
        foreach ($ids as $id) {
            $i = IPS_GetInstance($id);
            if ($i['ConnectionID'] == $connectionID['ConnectionID']) {
                $result[] = $id;
            }
        }
        return $result;
    }
    private function searchDevice($guid, $vin): int
    {
        $connectionID = IPS_GetInstance($this->InstanceID);

        $SpliterIDs = IPS_GetInstanceListByModuleID('{72887112-E270-FFC1-04AC-817D82F42027}');
        $SplitterID = 0;

        foreach ($SpliterIDs as $sID) {
            $i = IPS_GetInstance($sID);
            if ($i['ConnectionID'] == $connectionID['ConnectionID']) {
                if (IPS_GetProperty($sID, 'VIN') == $vin) {
                    $SplitterID = $sID;
                }
            }

            $ids = IPS_GetInstanceListByModuleID($guid);
            foreach ($ids as $id) {
                $x = IPS_GetInstance($id);
                if ($x['ConnectionID'] == $SplitterID) {
                    return $id;
                }
            }
        }
        return 0;
    }
}
