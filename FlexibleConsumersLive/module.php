<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1, viewport-fit=cover"
>

<style>

    :root {

        color-scheme:
            light dark;

        --accent:
            var(
                --accent-color,
                #18c7c0
            );

        --text:
            var(
                --content-color,
                #f2f4f6
            );

        --green:
            #20d17a;

        --blue:
            #39a8ff;

        --orange:
            #f5a623;

        --red:
            #ff6470;

        --grey:
            #9099a3;

        --muted:
            rgba(
                170,
                176,
                183,
                .92
            );

        --border:
            rgba(
                160,
                168,
                176,
                .24
            );

        --row-background:
            rgba(
                127,
                127,
                127,
                .035
            );
    }


    * {
        box-sizing: border-box;
    }


    html,
    body {

        width: 100%;
        height: 100%;

        margin: 0;
        padding: 0;

        overflow: hidden;

        background: transparent;

        color:
            var(--text);

        font-family:
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            Roboto,
            Arial,
            sans-serif;
    }


    button {
        font: inherit;
    }


    /* ================================================================ */
    /* Gesamtkachel                                                     */
    /* ================================================================ */

    #app {

        width: 100%;
        height: 100%;

        padding:
            56px
            9px
            9px
            9px;

        overflow: hidden;
    }


    #consumerList {

        width: 100%;
        height: 100%;

        display: grid;

        grid-template-rows:
            repeat(
                5,
                minmax(
                    0,
                    1fr
                )
            );

        gap: 7px;
    }


    /* ================================================================ */
    /* Verbraucher                                                      */
    /* ================================================================ */

    .consumer {

        min-width: 0;
        min-height: 0;

        overflow: hidden;

        display: grid;

        grid-template-columns:
            42px
            minmax(0, 1.55fr)
            minmax(105px, .95fr)
            minmax(72px, .55fr)
            42px;

        grid-template-areas:
            "icon info state power gear";

        align-items: center;

        column-gap: 9px;

        padding:
            6px
            8px;

        border:
            1px solid
            var(--border);

        border-radius:
            11px;

        background:
            var(--row-background);
    }


    /* ================================================================ */
    /* Icon                                                             */
    /* ================================================================ */

    .icon {

        grid-area:
            icon;

        width: 36px;
        height: 36px;

        display: flex;

        align-items: center;
        justify-content: center;

        border:
            1px solid
            var(--border);

        border-radius:
            8px;

        color:
            var(--blue);
    }


    .consumer[data-key="boiler"] .icon,
    .consumer[data-key="buffer"] .icon {

        color:
            var(--orange);
    }


    /* ================================================================ */
    /* Name                                                             */
    /* ================================================================ */

    .info {

        grid-area:
            info;

        min-width: 0;

        overflow: hidden;
    }


    .name {

        color:
            var(--text);

        font-size:
            14px;

        line-height:
            1.05;

        font-weight:
            700;

        overflow:
            hidden;

        text-overflow:
            ellipsis;

        white-space:
            nowrap;
    }


    .sub {

        margin-top:
            3px;

        color:
            var(--muted);

        font-size:
            12px;

        line-height:
            1.05;

        overflow:
            hidden;

        text-overflow:
            ellipsis;

        white-space:
            nowrap;
    }


    /* ================================================================ */
    /* Betriebsart + realer Zustand                                     */
    /* ================================================================ */

    .state {

        grid-area:
            state;

        min-width: 0;

        overflow: hidden;
    }


    .mode {

        min-width: 0;

        overflow: hidden;

        text-overflow:
            ellipsis;

        white-space:
            nowrap;

        font-size:
            12px;

        line-height:
            1.05;

        font-weight:
            700;
    }


    .mode.auto {
        color: var(--blue);
    }


    .mode.force {
        color: var(--green);
    }


    .mode.off {
        color: var(--grey);
    }


    .real-state {

        margin-top:
            5px;

        display: flex;

        align-items: center;

        gap:
            5px;

        min-width:
            0;

        color:
            var(--text);

        font-size:
            12px;

        line-height:
            1;
    }


    .state-dot {

        width:
            9px;

        height:
            9px;

        flex:
            0 0 9px;

        border-radius:
            50%;

        background:
            var(--grey);
    }


    .state-dot.on {

        background:
            var(--green);

        box-shadow:
            0 0 7px
            rgba(
                32,
                209,
                122,
                .40
            );
    }


    .real-state-text {

        min-width:
            0;

        overflow:
            hidden;

        text-overflow:
            ellipsis;

        white-space:
            nowrap;
    }


    /* ================================================================ */
    /* Grund                                                            */
    /* ================================================================ */

    .reason {
        display: none;
    }


    /* ================================================================ */
    /* Leistung                                                         */
    /* ================================================================ */

    .power {

        grid-area:
            power;

        min-width:
            0;

        text-align:
            right;

        color:
            var(--text);

        font-size:
            14px;

        font-weight:
            750;

        white-space:
            nowrap;
    }


    /* ================================================================ */
    /* Zahnrad                                                          */
    /* ================================================================ */

    .gear {

        grid-area:
            gear;

        justify-self:
            end;

        width:
            36px;

        height:
            36px;

        margin:
            0;

        padding:
            0;

        display:
            flex;

        align-items:
            center;

        justify-content:
            center;

        border:
            1px solid
            var(--border);

        border-radius:
            8px;

        background:
            rgba(
                127,
                127,
                127,
                .045
            );

        color:
            var(--text);

        cursor:
            pointer;

        font-size:
            18px;
    }


    .gear:hover {

        color:
            var(--accent);

        border-color:
            var(--accent);
    }


    .gear:active {

        transform:
            scale(.94);
    }


    /* ================================================================ */
    /* Breitere Desktopansicht                                          */
    /* ================================================================ */

    @media (min-width: 800px) {

        .consumer {

            grid-template-columns:
                46px
                minmax(170px, 1.45fr)
                minmax(150px, 1fr)
                minmax(90px, .55fr)
                minmax(180px, 1.2fr)
                46px;

            grid-template-areas:
                "icon info state power reason gear";

            column-gap:
                12px;
        }


        .name {
            font-size: 15px;
        }


        .sub {
            font-size: 13px;
        }


        .mode {
            font-size: 13px;
        }


        .real-state {
            font-size: 13px;
        }


        .power {
            font-size: 15px;
        }


        .reason {

            grid-area:
                reason;

            display:
                block;

            min-width:
                0;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

            white-space:
                nowrap;

            color:
                var(--muted);

            font-size:
                12px;
        }
    }


    /* ================================================================ */
    /* Mobile                                                           */
    /*
     * Gleiche logische Anordnung wie Desktop:
     *
     * Icon | Name | Betriebsart / Zustand | Leistung | Zahnrad
     *
     * Keine zusätzliche Zeile.
     * ================================================================ */

    @media (max-width: 520px) {

        #app {

            padding:
                51px
                7px
                7px
                7px;
        }


        #consumerList {

            gap:
                6px;
        }


        .consumer {

            grid-template-columns:
                37px
                minmax(0, 1.25fr)
                minmax(86px, .9fr)
                62px
                36px;

            grid-template-areas:
                "icon info state power gear";

            column-gap:
                6px;

            padding:
                5px
                7px;
        }


        .icon {

            width:
                33px;

            height:
                33px;
        }


        .name {

            font-size:
                13px;
        }


        .sub {

            font-size:
                11px;

            margin-top:
                3px;
        }


        .mode {

            font-size:
                12px;
        }


        .real-state {

            margin-top:
                4px;

            font-size:
                11px;
        }


        .state-dot {

            width:
                8px;

            height:
                8px;

            flex-basis:
                8px;
        }


        .power {

            font-size:
                13px;
        }


        .gear {

            width:
                33px;

            height:
                33px;

            font-size:
                17px;
        }
    }


    /* ================================================================ */
    /* Sehr schmales Mobile                                             */
    /* ================================================================ */

    @media (max-width: 390px) {

        .consumer {

            grid-template-columns:
                34px
                minmax(0, 1.2fr)
                minmax(78px, .85fr)
                58px
                33px;

            column-gap:
                5px;

            padding-left:
                5px;

            padding-right:
                5px;
        }


        .icon {

            width:
                31px;

            height:
                31px;
        }


        .name {

            font-size:
                12px;
        }


        .sub {

            font-size:
                10px;
        }


        .mode {

            font-size:
                11px;
        }


        .real-state {

            font-size:
                10px;
        }


        .power {

            font-size:
                12px;
        }


        .gear {

            width:
                31px;

            height:
                31px;

            font-size:
                16px;
        }
    }


    /* ================================================================ */
    /* Sehr flache Kachel                                               */
    /* ================================================================ */

    @media (max-height: 390px) {

        #app {

            padding-top:
                46px;

            padding-bottom:
                6px;
        }


        #consumerList {

            gap:
                5px;
        }


        .consumer {

            padding-top:
                3px;

            padding-bottom:
                3px;
        }


        .icon {

            width:
                30px;

            height:
                30px;
        }


        .gear {

            width:
                30px;

            height:
                30px;

            font-size:
                16px;
        }


        .name {

            font-size:
                12px;
        }


        .sub {

            font-size:
                10px;
        }


        .mode {

            font-size:
                10px;
        }


        .real-state {

            font-size:
                10px;
        }


        .power {

            font-size:
                12px;
        }
    }

</style>

</head>


<body>


<div id="app">

    <div
        id="consumerList"
    ></div>

</div>



<script>

'use strict';


// ============================================================================
// Start
// ============================================================================

function init()
{
    try {

        requestAction(
            'Refresh',
            true
        );

    } catch (e) {

    }
}


// ============================================================================
// Symcon Nachricht
// ============================================================================

function handleMessage(
    message
)
{
    let data =
        message;


    if (
        typeof data
        ===
        'string'
    ) {

        try {

            data =
                JSON.parse(
                    data
                );

        } catch (e) {

            return;
        }
    }


    if (
        !data
        ||
        data.type !== 'consumers'
    ) {

        return;
    }


    renderConsumers(
        Array.isArray(
            data.items
        )
            ?
            data.items
            :
            []
    );
}


// ============================================================================
// Rendering
// ============================================================================

function renderConsumers(
    items
)
{
    const list =
        document.getElementById(
            'consumerList'
        );


    if (
        !list
    ) {

        return;
    }


    const html =
        [];


    for (
        const item
        of
        items
    ) {

        html.push(
            createConsumerHTML(
                item
            )
        );
    }


    list.innerHTML =
        html.join(
            ''
        );


    const buttons =
        list.querySelectorAll(
            '.gear'
        );


    buttons.forEach(
        button =>
        {
            button.addEventListener(
                'click',
                event =>
                {
                    event.stopPropagation();


                    const id =
                        Number(
                            button.dataset.objectId
                        );


                    if (
                        !Number.isFinite(
                            id
                        )
                        ||
                        id <= 0
                    ) {

                        return;
                    }


                    try {

                        openObject(
                            id
                        );

                    } catch (e) {

                        console.log(
                            'openObject nicht verfügbar',
                            e
                        );
                    }
                }
            );
        }
    );
}


// ============================================================================
// Eine Zeile
// ============================================================================

function createConsumerHTML(
    item
)
{
    const name =
        escapeHTML(
            item.name
            ||
            ''
        );


    const sub =
        escapeHTML(
            item.sub
            ||
            ''
        );


    const modeText =
        escapeHTML(
            item.modeText
            ||
            ''
        );


    const statusText =
        escapeHTML(
            item.statusText
            ||
            ''
        );


    const powerText =
        escapeHTML(
            item.powerText
            ||
            '—'
        );


    const reason =
        escapeHTML(
            item.reason
            ||
            ''
        );


    const objectID =
        Number(
            item.id
        )
        ||
        0;


    const modeClass =
        getModeClass(
            Number(
                item.modeRaw
            )
        );


    return `
        <div
            class="consumer"
            data-key="${escapeHTML(item.key || '')}"
        >

            <div class="icon">
                ${getIcon(item.icon)}
            </div>


            <div class="info">

                <div
                    class="name"
                    title="${name}"
                >
                    ${name}
                </div>


                <div
                    class="sub"
                    title="${sub}"
                >
                    ${sub}
                </div>

            </div>


            <div class="state">

                <div
                    class="mode ${modeClass}"
                    title="${modeText}"
                >
                    ${modeText}
                </div>


                <div class="real-state">

                    <span
                        class="state-dot ${item.isOn ? 'on' : ''}"
                    ></span>

                    <span
                        class="real-state-text"
                    >
                        Gerät ${statusText}
                    </span>

                </div>

            </div>


            <div
                class="power"
                title="Konfigurierte EMS-Leistung: ${powerText}"
            >
                ${powerText}
            </div>


            <div
                class="reason"
                title="${reason}"
            >
                ${reason}
            </div>


            <button
                class="gear"
                type="button"
                data-object-id="${objectID}"
                title="Einstellungen öffnen"
                aria-label="Einstellungen ${name}"
            >
                ⚙
            </button>

        </div>
    `;
}


// ============================================================================
// Betriebsart-Farbe
// ============================================================================

function getModeClass(
    mode
)
{
    switch (
        mode
    ) {

        case 0:

            return 'off';


        case 1:

            return 'auto';


        case 2:

            return 'force';


        default:

            return '';
    }
}


// ============================================================================
// Icons
// ============================================================================

function getIcon(
    icon
)
{
    switch (
        icon
    ) {

        case 'boiler':

            return `
                <svg
                    viewBox="0 0 32 32"
                    width="25"
                    height="25"
                >
                    <rect
                        x="9"
                        y="4"
                        width="14"
                        height="24"
                        rx="4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.3"
                    />

                    <path
                        d="M16 11 C13 15 12 17 12 19 A4 4 0 0 0 20 19 C20 17 19 15 16 11Z"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                </svg>
            `;


        case 'drop':

            return `
                <svg
                    viewBox="0 0 32 32"
                    width="25"
                    height="25"
                >
                    <path
                        d="M16 3 C11 10 7 15 7 20 A9 9 0 0 0 25 20 C25 15 21 10 16 3Z"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linejoin="round"
                    />
                </svg>
            `;


        case 'pool':

            return `
                <svg
                    viewBox="0 0 32 32"
                    width="26"
                    height="26"
                >
                    <path
                        d="M5 11 H11 V4 M11 8 H18 V4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                    />

                    <path
                        d="M3 17 C6 14 9 20 12 17 C15 14 18 20 21 17 C24 14 27 20 30 17"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                    />

                    <path
                        d="M3 23 C6 20 9 26 12 23 C15 20 18 26 21 23 C24 20 27 26 30 23"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                    />
                </svg>
            `;


        case 'heater':

            return `
                <svg
                    viewBox="0 0 32 32"
                    width="25"
                    height="25"
                >
                    <rect
                        x="7"
                        y="5"
                        width="18"
                        height="22"
                        rx="2"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.3"
                    />

                    <path
                        d="M11 9 V23 M16 9 V23 M21 9 V23"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                </svg>
            `;


        default:

            return '●';
    }
}


// ============================================================================
// HTML escapen
// ============================================================================

function escapeHTML(
    value
)
{
    return String(
        value
        ??
        ''
    )
    .replaceAll(
        '&',
        '&amp;'
    )
    .replaceAll(
        '<',
        '&lt;'
    )
    .replaceAll(
        '>',
        '&gt;'
    )
    .replaceAll(
        '"',
        '&quot;'
    )
    .replaceAll(
        "'",
        '&#039;'
    );
}


// ============================================================================
// Start
// ============================================================================

document.addEventListener(
    'DOMContentLoaded',
    init
);

</script>


</body>

</html>
