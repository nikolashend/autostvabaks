<?php

// Public site texts – English. Concise, natural; written for English-speaking residents and
// international owners in Estonia, not expanded for SEO. Every statement must match the fact
// register (docs/autostvabaks/AUTOSTVABAKS_FACT_CLAIM_REGISTER_V1.md): no best/highest price,
// no guarantees, no "fastest", no reply-time promise, no invented numbers.

return [

    'meta' => [
        'title' => 'Car buyback across Estonia – sell your car the simple way | Autostvabaks',
        'description' => 'Autostvabaks buys cars all over Estonia – any condition, including faulty, damaged and leased cars. Send a short request, get an offer, close the deal. Payment by bank transfer or cash.',
    ],

    'nav' => [
        'how' => 'How it works',
        'why' => 'Why Autostvabaks',
        'faq' => 'FAQ',
        'contact' => 'Contact',
        'cta' => 'Start request',
        'menu' => 'Menu',
        'language' => 'Language',
        'home' => 'Home',
    ],

    'hero' => [
        'eyebrow' => 'Car buyback across Estonia',
        'title' => 'Sell your car quickly and easily',
        'subtitle' => 'One short request instead of listings, viewings and haggling. We evaluate cars in any condition – faulty, damaged or under leasing.',
    ],

    'trust' => [
        'aria' => 'Key facts',
        'items' => [
            ['title' => 'All over Estonia', 'text' => 'Not only Tallinn and Harju County'],
            ['title' => 'Any condition', 'text' => 'Including faulty and damaged cars'],
            ['title' => 'Leased or financed', 'text' => 'You can send a request for such a car too'],
            ['title' => 'Bank transfer or cash', 'text' => 'Paid when the deal is completed'],
        ],
    ],

    'form' => [
        'title' => 'Get an offer for your car',
        'step' => 'Step :n of :total',
        'reg' => 'Registration number',
        'reg_placeholder' => '123ABC',
        'reg_hint' => 'Spaces and dashes do not matter.',
        'price_hint' => 'Optional – if you have a price in mind, let us know.',
        'phone' => 'Phone number',
        'phone_placeholder' => '+372 5xxx xxxx',
        'email' => 'E-mail',
        'email_placeholder' => 'name@email.com',
        'price' => 'Desired price',
        'price_placeholder' => '15 000',
        'optional' => 'optional',
        'terms' => 'I have read and agree to the :link',
        'terms_link' => 'terms of use',
        'submit' => 'Start request',
        'note' => 'We will contact you as soon as possible.',
    ],

    'modal' => [
        'back' => 'Back',
        'close' => 'Close',
        'title' => 'More about your car',
        'vehicle' => 'Vehicle',
        'language' => 'Preferred language',
        'mileage' => 'Mileage',
        'mileage_placeholder' => '185 000',
        'info' => 'Additional information',
        'info_placeholder' => 'Additional information about the car...',
        'info_hint' => 'For example: condition, equipment, damage, number of owners, service history, leasing or loan.',
        'photos' => 'Photos',
        'photos_button' => 'Add photos',
        'photos_hint' => 'JPG, PNG or WEBP · up to :max photos · up to :mb MB each',
        'photos_why' => 'Photos help us evaluate your car more precisely.',
        'photo_remove' => 'Remove photo',
        'photo_alt' => 'Photo :n',
        'submit' => 'Send request',
        'sending' => 'Sending…',
        'success_title' => 'Thank you! Your request has been sent.',
        'success_text' => 'We will contact you as soon as possible.',
        'success_next_title' => 'What happens next?',
        'success_next' => [
            'We review your request and the car details.',
            'We contact you by e-mail or phone, as given in the request.',
            'We agree on a time and place to inspect the car.',
        ],
    ],

    'errors' => [
        'reg_required' => 'Please enter the vehicle registration number.',
        'reg_invalid' => 'Please check the vehicle registration number.',
        'phone_required' => 'Please enter your phone number.',
        'phone_invalid' => 'Please enter a valid phone number.',
        'email_required' => 'Please enter your e-mail address.',
        'email_invalid' => 'Please enter a valid e-mail address.',
        'price_invalid' => 'Please enter the price as a number.',
        'terms' => 'Please accept the terms of use.',
        'language' => 'Please choose a language.',
        'mileage_invalid' => 'Please enter the mileage as a number.',
        'info_max' => 'The text is too long (up to :max characters).',
        'photos_max' => 'You can add up to :max photos.',
        'photo_type' => 'Only JPG, PNG and WEBP files are allowed.',
        'photo_size' => 'The photo is too large (up to :max MB).',
        'generic' => 'The request could not be sent. Please try again or contact us.',
        'throttle' => 'Too many requests. Please try again in a few minutes.',
    ],

    'steps' => [
        'eyebrow' => 'How it works',
        'title' => 'Car buyback in four steps',
        'items' => [
            ['title' => 'Send a request', 'text' => 'Enter the registration number and your contact details; add mileage, a description and photos if you like. It takes a couple of minutes.'],
            ['title' => 'We discuss the offer', 'text' => 'We review the details and contact you as soon as possible.'],
            ['title' => 'We inspect the car', 'text' => 'We agree on a convenient time and place. The final offer is based on the actual condition of the car.'],
            ['title' => 'Deal and payment', 'text' => 'We complete the paperwork and pay by bank transfer or, if you prefer, in cash.'],
        ],
    ],

    'why' => [
        'eyebrow' => 'Why Autostvabaks',
        'title' => 'Why sell your car to us?',
        'lead' => 'The questions sellers usually ask first – answered.',
        'items' => [
            ['title' => 'No listing, no calls from strangers?', 'text' => 'One request to us replaces the listing, the viewings and the haggling with unknown buyers. You deal with one buyer.'],
            ['title' => 'Not sure what your car is worth?', 'text' => 'The desired price in the request is optional. We explain openly what the offer depends on and discuss the price with you.'],
            ['title' => 'Car under leasing, faulty or damaged?', 'text' => 'Not an obstacle. We evaluate cars in any condition, including leased or financed cars – just mention it in the request.'],
            ['title' => 'How do the deal and payment work?', 'text' => 'We agree on an inspection, complete the paperwork and pay by bank transfer or, if you prefer, in cash.'],
        ],
    ],

    'valuation' => [
        'eyebrow' => 'Valuation',
        'title' => 'What affects the offer?',
        'answer' => 'The offer is based on the market price of similar cars in Estonia and the actual condition of your car. The final offer is made after an inspection. The main factors we consider:',
        'factors' => [
            ['title' => 'Make, model and year', 'text' => 'As well as engine and gearbox.'],
            ['title' => 'Mileage', 'text' => 'Actual mileage and how it matches the age.'],
            ['title' => 'Technical condition', 'text' => 'Engine, gearbox, suspension, brakes.'],
            ['title' => 'Damage', 'text' => 'Accidents, rust, body and interior damage.'],
            ['title' => 'Equipment', 'text' => 'Extras and completeness.'],
            ['title' => 'Service history', 'text' => 'Service book, invoices, inspection.'],
            ['title' => 'Market demand', 'text' => 'How quickly similar cars sell in Estonia.'],
        ],
        'expectation' => 'Selling directly to us is about simplicity and speed: one request, one buyer and an agreed deal – no listing, no waiting, no viewings with strangers. The offer is based on market value and condition, not on asking prices in listings.',
        'note' => 'Not sure what your car is worth? The desired price in the request is optional – we make an offer without it too.',
        'cta' => 'Ask for an offer',
    ],

    'answers' => [
        'eyebrow' => 'Answers',
        'title' => 'Leasing, damage and payment',
        'items' => [
            [
                'question' => 'Can a leased or financed car be sold to Autostvabaks?',
                'answer' => 'Yes. A car under leasing or a loan can be submitted for evaluation like any other car.',
                'detail' => 'Mention the active leasing or loan in the additional information, and the leasing company if you like. The exact steps of the deal are agreed with you individually.',
            ],
            [
                'question' => 'Can a damaged or faulty car be submitted for evaluation?',
                'answer' => 'Yes. We evaluate faulty and damaged cars as well and make an offer based on the actual condition of the car.',
                'detail' => 'Describe the damage in the additional information and add photos – the offer will be more precise.',
            ],
            [
                'question' => 'How does Autostvabaks pay for the car?',
                'answer' => 'By bank transfer when the deal is completed. Cash payment is possible on request.',
                'detail' => 'Payment and paperwork happen at the same time – after the inspection and the agreement.',
            ],
        ],
        'cta' => 'Start request',
    ],

    'final' => [
        'title' => 'Ready to sell your car?',
        'text' => 'Fill in a short request – it takes a couple of minutes. We will contact you as soon as possible.',
        'cta' => 'Start request',
    ],

    'sticky' => [
        'cta' => 'Start request',
        'note' => 'Takes a couple of minutes',
    ],

    'testimonials' => [
        'eyebrow' => 'Testimonials',
        'title' => 'What our customers say',
        'prev' => 'Previous',
        'next' => 'Next',
        'rating' => 'Rating: :n out of 5',
    ],

    'faq' => [
        'eyebrow' => 'FAQ',
        'title' => 'Frequently asked questions',
    ],

    'contact' => [
        'eyebrow' => 'Contact',
        'title' => 'Get in touch',
        'lead' => 'We work all over Estonia. Send a request or write to us – we will reply as soon as possible.',
        'lead_with_phone' => 'We work all over Estonia. Write or call us – we will reply as soon as possible.',
        'address' => 'Address',
        'phone' => 'Phone',
        'email' => 'E-mail',
        'messengers' => 'Message us',
        'map_title' => 'Autostvabaks on the map',
        'open_map' => 'Open in Google Maps',
        'load_map' => 'Show map',
        'load_map_note' => 'Loading the map uses the Google Maps service.',
    ],

    'footer' => [
        'about' => 'Car buyback all over Estonia. We evaluate cars in any condition – leased, faulty and damaged cars included.',
        'contact' => 'Contact',
        'links' => 'Links',
        'info' => 'Useful information',
        'legal' => 'Legal',
        'operator' => 'The Autostvabaks service is provided by :operator (registry code :code)',
        'rights' => 'All rights reserved.',
        'top' => 'Back to top',
    ],

    'page' => [
        'updated' => 'Last updated: :date',
        'cta_title' => 'Want to sell your car?',
        'cta_text' => 'Fill in a short request and get an offer for your car.',
    ],

    'not_found' => [
        'title' => 'Page not found',
        'text' => 'The page you are looking for does not exist or has been moved.',
        'button' => 'Back to home',
    ],
];
