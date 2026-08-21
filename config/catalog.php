<?php

/*
|--------------------------------------------------------------------------
| Service catalogue
|--------------------------------------------------------------------------
|
| Shared by the home page, the services page and the contact form's service
| dropdown, so a new service is added in one place instead of four files.
|
*/

return [

    /*
    | The three core lines, rendered as wide cards with a photo.
    */
    'core' => [
        [
            'title' => 'Carpets & area rugs',
            'tagline' => 'wall-to-wall · wool · silk · synthetic',
            'text' => 'Ground-in traffic lanes, spills and pet accidents lifted out of the pile — not just off the top. Wool and hand-knotted rugs get a low-moisture, colorfast-tested treatment.',
            'image' => 'img/services/1.jpg',
            'alt' => 'carpet and area rug cleaning in Chicago',
            'items' => [
                'Wall-to-wall carpet and stairs',
                'Area, wool and silk rugs',
                'Traffic lanes and spot treatment',
                'Pet stain and odor removal',
                'Stain protector on request',
            ],
            'before_image' => 'img/gallery/ba-1-before.jpg',
            'after_image' => 'img/gallery/ba-1-after.jpg',
            // "How we clean it" on this service's own page — a service's steps
            // instead of one shared page-level list, see ImportConfigContent.
            'steps' => [
                ['title' => 'Fabric test <br>and inspection'],
                ['title' => 'Furniture moved <br>and protected'],
                ['title' => 'Dry soil <br>removal'],
                ['title' => 'Pre-spray and <br>spot treatment'],
                ['title' => 'Hot water <br>extraction'],
                ['title' => 'Neutralizing <br>rinse'],
                ['title' => 'Grooming, drying <br>and final check'],
            ],
        ],
        [
            'title' => 'Sofas & armchairs',
            'tagline' => 'sectionals · recliners · dining chairs',
            'text' => 'Every cushion, arm, back panel and seam cleaned by hand tool. Cotton, linen, microfiber, chenille and velvet — we test the fabric first and pick the method to match.',
            'image' => 'img/services/upholstery.jpg',
            'alt' => 'sofa and armchair upholstery cleaning',
            'items' => [
                'Sofas, loveseats and sectionals',
                'Armchairs, recliners and ottomans',
                'Dining and office chairs',
                'Cushions cleaned on both sides',
                'Fabric test before any solution',
            ],
            'before_image' => 'img/gallery/ba-3-before.jpg',
            'after_image' => 'img/gallery/ba-3-after.jpg',
        ],
        [
            'title' => 'Mattresses & soft furniture',
            'tagline' => 'mattresses · headboards · curtains',
            'text' => 'Dust mites, sweat and allergens pulled out of the padding, then an anti-allergen rinse. The rest of the soft furniture in the room gets the same treatment while we are there.',
            'image' => 'img/services/2.jpg',
            'alt' => 'mattress and soft furniture cleaning',
            'items' => [
                'Mattresses, both sides',
                'Upholstered beds and headboards',
                'Benches, poufs and window seats',
                'Curtains and drapes on the rail',
                'Anti-allergen and deodorizing rinse',
            ],
        ],
    ],

    /*
    | Home & office work, rendered as wide cards with a check list.
    */
    'home_office' => [
        [
            'title' => 'Home cleaning',
            'items' => [
                'Dust and wipe all surfaces',
                'Vacuum and mop floors',
                'Kitchen, sink and appliances',
                'Bathrooms fully disinfected',
                'Trash out, beds made',
            ],
        ],
        [
            'title' => 'Office cleaning',
            'items' => [
                'Desks and workstations',
                'Floors, glass and doors',
                'Upholstered office chairs',
                'Restrooms and restocking',
                'After hours, no downtime',
            ],
        ],
        [
            'title' => 'Move in / move out',
            'items' => [
                'Carpets in every room',
                'Built-in closets and shelves',
                'Appliances inside and out',
                'Windows and sills',
                'Ready for photos or handover',
            ],
        ],
        [
            'title' => 'Airbnb turnover',
            'items' => [
                'Mattress and sofa refresh',
                'Rugs spot-treated',
                'Linens and towels changed',
                'Same-day between guests',
                'Photo-ready by check-in',
            ],
        ],
    ],

];
