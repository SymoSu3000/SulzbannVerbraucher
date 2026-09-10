<?php

declare(strict_types=1);

class FlexibleConsumersLive extends IPSModule
{
    // ========================================================================
    // Angelegte flexible Verbraucher
    // ========================================================================

    private const CONSUMERS = [
        [
            'id'    => 33007,
            'key'   => 'boiler',
            'name'  => 'Boiler',
            'sub'   => 'Warmwasser',
            'icon'  => 'boiler'
        ],
        [
            'id'    => 52793,
            'key'   => 'dehum_ug',
            'name'  => 'Entfeuchter UG',
            'sub'   => 'Untergeschoss',
            'icon'  => 'drop'
        ],
        [
            'id'    => 55387,
            'key'   => 'dehum_og',
            'name'  => 'Entfeuchter OG',
            'sub'   => 'Obergeschoss',
            'icon'  => 'drop'
        ],
        [
            'id'    => 31862,
            'key'   => 'pool',
            'name'  => 'Poolpumpe + Salzelektrolyse',
            'sub'   => 'Pooltechnik',
            'icon'  => 'pool'
        ],
        [
            'id'    => 16478,
            'key'   => 'buffer',
            'name'  => 'Heizstab / Puffer',
            'sub'   => 'Heizung',
            'icon'  => 'heater'
        ]
    ];


    public function Create(): void
    {
        parent::Create();

        $this->SetVisualizationType(1);
    }


    public function ApplyChanges(): void
    {
        parent::ApplyChanges();

        $this->RegisterConsumerMessages();
    }


    // ========================================================================
    // HTML
    // ========================================================================

    public function GetVisualizationTile(): string
    {
        $file = __DIR__ . '/module.html';

        if (!file_exists($file)) {
            return '<div>module.html nicht gefunden</div>';
        }

        $html = file_get_contents($file);

        if ($html === false) {
            return '<div>module.html konnte nicht gelesen werden</div>';
        }

        return $html;
    }


    public function RequestAction($Ident, $Value): void
    {
        if ($Ident === 'Refresh') {

            $this->SendLiveValues();

            return;
        }

        throw new Exception(
            'Ungültige Aktion: ' . $Ident
        );
    }


    // ========================================================================
    // Nachrichten
    // ========================================================================

    public function MessageSink(
        $TimeStamp,
        $SenderID,
        $Message,
        $Data
    ): void {
        parent::MessageSink(
            $TimeStamp,
            $SenderID,
            $Message,
            $Data
        );

        if ($Message === VM_UPDATE) {

            $this->SendLiveValues();
        }
    }


    private function RegisterConsumerMessages(): void
    {
        foreach (self::CONSUMERS as $consumer) {

            $id = intval(
                $consumer['id']
            );

            if (!IPS_ObjectExists($id)) {
                continue;
            }

            $this->RegisterVariablesRecursive(
                $id
            );
        }
    }


    private function RegisterVariablesRecursive(
        int $parentID,
        int $level = 0
    ): void {
        if ($level > 3) {
            return;
        }

        foreach (
            IPS_GetChildrenIDs($parentID)
            as
            $childID
        ) {

            if (IPS_VariableExists($childID)) {

                $this->RegisterMessage(
                    $childID,
                    VM_UPDATE
                );
            }

            $object =
                IPS_GetObject($childID);

            if (
                $object['ObjectType'] === 0
                ||
                $object['ObjectType'] === 1
            ) {

                $this->RegisterVariablesRecursive(
                    $childID,
                    $level + 1
                );
            }
        }
    }


    // ========================================================================
    // Livewerte
    // ========================================================================

    private function SendLiveValues(): void
    {
        $payload = [
            'type'      => 'consumers',
            'timestamp' => time(),
            'items'     => []
        ];


        foreach (
            self::CONSUMERS
            as
            $definition
        ) {

            $payload['items'][] =
                $this->BuildConsumerData(
                    $definition
                );
        }


        $this->UpdateVisualizationValue(
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            )
        );
    }


    private function BuildConsumerData(
        array $definition
    ): array {
        $parentID =
            intval(
                $definition['id']
            );


        $variables =
            $this->CollectVariables(
                $parentID
            );


        $mode =
            $this->FindVariable(
                $variables,
                [
                    'betriebsart',
                    'modus',
                    'mode'
                ]
            );


        $status =
            $this->FindVariable(
                $variables,
                [
                    'status',
                    'schaltzustand',
                    'zustand'
                ]
            );


        $switch =
            $this->FindVariable(
                $variables,
                [
                    'schalten',
                    'switch',
                    'value',
                    'ein aus'
                ]
            );


        $priority =
            $this->FindVariable(
                $variables,
                [
                    'priorität',
                    'priority'
                ]
            );


        $power =
            $this->FindVariable(
                $variables,
                [
                    'leistung',
                    'power',
                    'verbrauch',
                    'kw'
                ]
            );


        $reason =
            $this->FindVariable(
                $variables,
                [
                    'grund',
                    'reason',
                    'status text',
                    'status-text',
                    'ems grund'
                ]
            );


        $minimumRun =
            $this->FindVariable(
                $variables,
                [
                    'mindestlauf',
                    'minrun',
                    'mindest lauf'
                ]
            );


        $hysteresis =
            $this->FindVariable(
                $variables,
                [
                    'hysterese',
                    'hyst'
                ]
            );


        $emsEnabled =
            $this->FindVariable(
                $variables,
                [
                    'verbraucher im ems verwenden',
                    'ems verwenden',
                    'ems aktiv',
                    'ems enabled'
                ]
            );


        $displayMode =
            $this->VariableDisplay(
                $mode
            );


        $displayStatus =
            $this->VariableDisplay(
                $status
            );


        $displaySwitch =
            $this->VariableDisplay(
                $switch
            );


        $isOn =
            $this->DetermineOnState(
                $status,
                $switch
            );


        return [
            'id' =>
                $parentID,

            'key' =>
                strval(
                    $definition['key']
                ),

            'name' =>
                strval(
                    $definition['name']
                ),

            'sub' =>
                strval(
                    $definition['sub']
                ),

            'icon' =>
                strval(
                    $definition['icon']
                ),

            'exists' =>
                IPS_ObjectExists(
                    $parentID
                ),

            'isOn' =>
                $isOn,

            'mode' =>
                $displayMode,

            'status' =>
                $displayStatus,

            'switch' =>
                $displaySwitch,

            'priority' =>
                $this->VariableDisplay(
                    $priority
                ),

            'power' =>
                $this->PowerDisplay(
                    $power
                ),

            'reason' =>
                $this->VariableDisplay(
                    $reason
                ),

            'minimumRun' =>
                $this->VariableDisplay(
                    $minimumRun
                ),

            'hysteresis' =>
                $this->VariableDisplay(
                    $hysteresis
                ),

            'emsEnabled' =>
                $this->VariableDisplay(
                    $emsEnabled
                )
        ];
    }


    // ========================================================================
    // Variablen sammeln
    // ========================================================================

    private function CollectVariables(
        int $parentID,
        int $level = 0
    ): array {
        $result = [];


        if (
            !IPS_ObjectExists(
                $parentID
            )
        ) {

            return $result;
        }


        if ($level > 3) {

            return $result;
        }


        foreach (
            IPS_GetChildrenIDs(
                $parentID
            )
            as
            $childID
        ) {

            if (
                IPS_VariableExists(
                    $childID
                )
            ) {

                $object =
                    IPS_GetObject(
                        $childID
                    );


                $result[] = [

                    'id' =>
                        $childID,

                    'name' =>
                        IPS_GetName(
                            $childID
                        ),

                    'ident' =>
                        strval(
                            $object['ObjectIdent']
                            ??
                            ''
                        ),

                    'value' =>
                        GetValue(
                            $childID
                        )
                ];
            }


            $object =
                IPS_GetObject(
                    $childID
                );


            if (
                $object['ObjectType'] === 0
                ||
                $object['ObjectType'] === 1
            ) {

                $result =
                    array_merge(
                        $result,
                        $this->CollectVariables(
                            $childID,
                            $level + 1
                        )
                    );
            }
        }


        return $result;
    }


    // ========================================================================
    // Variable anhand Name / Ident suchen
    // ========================================================================

    private function FindVariable(
        array $variables,
        array $terms
    ): ?array {
        foreach (
            $variables
            as
            $variable
        ) {

            $search =
                mb_strtolower(
                    strval(
                        $variable['name']
                    )
                    .
                    ' '
                    .
                    strval(
                        $variable['ident']
                    )
                );


            foreach (
                $terms
                as
                $term
            ) {

                if (
                    mb_strpos(
                        $search,
                        mb_strtolower(
                            $term
                        )
                    )
                    !==
                    false
                ) {

                    return $variable;
                }
            }
        }


        return null;
    }


    // ========================================================================
    // Darstellung
    // ========================================================================

    private function VariableDisplay(
        ?array $variable
    ): string {
        if ($variable === null) {

            return '';
        }


        $id =
            intval(
                $variable['id']
            );


        if (
            IPS_VariableExists(
                $id
            )
        ) {

            try {

                return
                    GetValueFormatted(
                        $id
                    );

            } catch (
                Throwable $e
            ) {

            }
        }


        $value =
            $variable['value']
            ??
            '';


        if (is_bool($value)) {

            return
                $value
                    ?
                    'Ein'
                    :
                    'Aus';
        }


        if (is_float($value)) {

            return
                number_format(
                    $value,
                    2,
                    '.',
                    ''
                );
        }


        return
            strval(
                $value
            );
    }


    private function PowerDisplay(
        ?array $variable
    ): string {
        if ($variable === null) {

            return '';
        }


        $value =
            floatval(
                $variable['value']
                ??
                0
            );


        /*
         * Falls Profil bereits W/kW enthält:
         * formatierte Darstellung übernehmen.
         */

        $id =
            intval(
                $variable['id']
            );


        try {

            $formatted =
                GetValueFormatted(
                    $id
                );


            if (
                stripos(
                    $formatted,
                    'W'
                )
                !==
                false
            ) {

                return $formatted;
            }

        } catch (
            Throwable $e
        ) {

        }


        if (
            abs(
                $value
            )
            >=
            1000
        ) {

            return
                number_format(
                    $value / 1000,
                    2,
                    '.',
                    ''
                )
                .
                ' kW';
        }


        return
            number_format(
                $value,
                0,
                '.',
                ''
            )
            .
            ' W';
    }


    private function DetermineOnState(
        ?array $status,
        ?array $switch
    ): bool {
        foreach (
            [
                $status,
                $switch
            ]
            as
            $candidate
        ) {

            if ($candidate === null) {

                continue;
            }


            $value =
                $candidate['value']
                ??
                false;


            if (is_bool($value)) {

                return $value;
            }


            if (is_int($value)) {

                return
                    $value !== 0;
            }


            if (is_float($value)) {

                return
                    abs($value) > 0.01;
            }


            $text =
                mb_strtolower(
                    strval(
                        $value
                    )
                );


            if (
                in_array(
                    $text,
                    [
                        'ein',
                        'on',
                        'true',
                        'aktiv',
                        'active'
                    ],
                    true
                )
            ) {

                return true;
            }
        }


        return false;
    }
}
