<?php

/*
|--------------------------------------------------------------------------
| Site data
|--------------------------------------------------------------------------
|
| Single source of truth for everything that used to be copy-pasted into
| every static page: contacts, navigation and the footer columns. Change a
| phone number here and it updates the header, the footer, the FAQ and the
| contact page at once.
|
*/

return [

    'name' => 'RYNEXClean',

    'logo' => [
        'dark'   => 'img/ui/logo2.png',
        'light'  => 'img/ui/logo2-light.png',
        'width'  => 861,
        'height' => 195,
        // The wordmark sits in the lower half of the PNG (sparkles fill the top),
        // so the raw image box has to be nudged up to line up with its neighbours.
        'lift'   => [
            'header'    => '0.85rem', // flex-centred against the menu
            'preloader' => '0.85rem', // flex-centred against the progress bar
            'footer'    => '1.5rem',  // top-aligned against the link column
        ],
    ],

    'phone'   => '+1 (224) 310-2110',
    'phone_e164' => '+12243102110',
    'email'   => 'hello@chicagocleaning.com',

    'address' => [
        'city'   => 'Lincolnshire',
        'line_1' => '250 Parkway Dr Suite 150-100',
        'line_2' => 'Lincolnshire, IL 60069',
    ],

    'hours' => [
        'Monday - Saturday: 8:00 am to 8:00 pm',
        'Sunday: Closed',
    ],

    'stats' => [
        'jobs'  => 1500,
        'years' => 4,
        'since' => 2022,
        'rating' => '4.9/5.0',
    ],

    /*
    | Service area, offered in the contact form's ZIP dropdown.
    */
    'service_zips' => [
        '60601' => 'Loop',
        '60607' => 'West Loop',
        '60610' => 'Near North Side',
        '60611' => 'Streeterville',
        '60614' => 'Lincoln Park',
        '60615' => 'Hyde Park',
        '60618' => 'North Center',
        '60622' => 'Wicker Park',
        '60625' => 'Lincoln Square',
        '60626' => 'Rogers Park',
        '60647' => 'Logan Square',
        '60201' => 'Evanston',
        '60301' => 'Oak Park',
        '60077' => 'Skokie',
        '60402' => 'Berwyn',
        '60804' => 'Cicero',
        '60540' => 'Naperville',
    ],

    'socials' => [
        ['icon' => 'fab fa-instagram',  'url' => '#.'],
        ['icon' => 'fab fa-facebook-f', 'url' => '#.'],
        ['icon' => 'fab fa-youtube',    'url' => '#.'],
    ],

];
