<?php

declare(strict_types=1);

class FlexibleConsumersLive extends IPSModule
{
    private const CONSUMERS = [

        'ug' => [
            'mode'   => 48725,
            'status' => 21846,
            'power'  => 47479,
            'reason' => 57923,
            'temp'   => 0,
            'limit'  => 0
        ],

        'og' => [
            'mode'   => 37326,
            'status' => 20351,
            'power'  => 48347,
            'reason' => 55280,
            'temp'   => 0,
            'limit'  => 0
        ],

        'pool' => [
            'mode'   => 29959,
            'status' => 21048,
            'power'  => 56815,
            'reason' => 26781,
            'temp'   => 0,
            'limit'  => 0
        ],

        'boiler' => [
            'mode'   => 17552,
            'status' => 10737,
            'power'  => 19394,
            'reason' => 23015,

            // B3 oben
            'temp'   => 39112,

            // Temperaturgrenze
            'limit'  => 41703
        ],

        'heater' => [
            'mode'   => 24813,
            'status' => 53546,
            'power'  => 37285,
            'reason' => 50074,

            // Puffer B4 oben
            'temp'   => 27553,

            // Temperaturgrenze
            'limit'  => 50024
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

        foreach ($this->GetObservedIDs() as $id) {

            if (
                $id > 0 &&
                IPS_VariableExists($id)
            ) {

                $this->RegisterMessage(
                    $id,
                    VM_UPDATE
                );
            }
        }
    }


    public function GetVisualizationTile(): string
    {
        $file =
            __DIR__
            .
            DIRECTORY_SEPARATOR
            .
            'module.html';


        if (!file_exists($file)) {

            return
                '<div style="padding:20px;">module.html nicht gefunden.</div>';
        }


        $html =
            file_get_contents($file);


        if ($html === false) {

            return
                '<div style="padding:20px;">module.html konnte nicht gelesen werden.</div>';
        }


        return $html;
    }


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


    public function RequestAction(
        $Ident,
        $Value
    ): void {

        if ($Ident === 'Refresh') {

            $this->SendLiveValues();

            return;
        }


        throw new Exception(
            'Invalid Ident: '
            .
            $Ident
        );
    }


    private function GetObservedIDs(): array
    {
        $ids = [];


        foreach (
            self::CONSUMERS
            as $config
        ) {

            foreach (
                [
                    'mode',
                    'status',
                    'power',
                    'reason',
                    'temp',
                    'limit'
                ]
                as $key
            ) {

                $id =
                    (int)$config[$key];

                if ($id > 0) {

                    $ids[] = $id;
                }
            }
        }


        return array_values(
            array_unique($ids)
        );
    }


    private function ReadInt(
        int $id
    ): ?int {

        if (
            $id <= 0 ||
            !IPS_VariableExists($id)
        ) {
            return null;
        }


        return (int)GetValue($id);
    }


    private function ReadFloat(
        int $id
    ): ?float {

        if (
            $id <= 0 ||
            !IPS_VariableExists($id)
        ) {
            return null;
        }


        return (float)GetValue($id);
    }


    private function ReadBool(
        int $id
    ): ?bool {

        if (
            $id <= 0 ||
            !IPS_VariableExists($id)
        ) {
            return null;
        }


        return (bool)GetValue($id);
    }


    private function ReadString(
        int $id
    ): ?string {

        if (
            $id <= 0 ||
            !IPS_VariableExists($id)
        ) {
            return null;
        }


        return (string)GetValue($id);
    }


    private function SendLiveValues(): void
    {
        $payload = [];


        foreach (
            self::CONSUMERS
            as $key => $config
        ) {

            $payload[$key] = [

                'mode' =>
                    $this->ReadInt(
                        (int)$config['mode']
                    ),

                'active' =>
                    $this->ReadBool(
                        (int)$config['status']
                    ),

                'power' =>
                    $this->ReadFloat(
                        (int)$config['power']
                    ),

                'reason' =>
                    $this->ReadString(
                        (int)$config['reason']
                    ),

                'temperature' =>
                    $this->ReadFloat(
                        (int)$config['temp']
                    ),

                'temperatureLimit' =>
                    $this->ReadFloat(
                        (int)$config['limit']
                    )
            ];
        }


        $json =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE
                |
                JSON_UNESCAPED_SLASHES
            );


        if ($json === false) {
            return;
        }


        $this->UpdateVisualizationValue(
            $json
        );
    }
}
