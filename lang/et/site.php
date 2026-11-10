<?php

// Avaliku lehe tekstid – eesti keel (põhikeel).
// NB! Sammude ja eeliste tekstid on esialgsed ning asendatakse lõplike tekstidega.

return [

    'meta' => [
        'title' => 'Autostvabaks – autode kokkuost üle Eesti | Müü oma auto kiirelt',
        'description' => 'Müü oma auto kiirelt ja mugavalt. Saada päring ja saa pakkumine – ostame igas seisukorras autosid üle kogu Eesti.',
    ],

    'nav' => [
        'how' => 'Kuidas töötab',
        'why' => 'Miks Autostvabaks',
        'faq' => 'KKK',
        'contact' => 'Kontakt',
        'cta' => 'Alusta päringut',
        'menu' => 'Menüü',
        'language' => 'Keel',
        'home' => 'Avaleht',
    ],

    'hero' => [
        'eyebrow' => 'Autode kokkuost üle Eesti',
        'title' => 'Müü oma auto kiirelt ja mugavalt',
        'subtitle' => 'Täida lühike päring ja saa oma autole pakkumine. Ostame igas seisukorras autosid.',
        'ticks' => ['Igas seisukorras autod', 'Ka liisingus või laenuga', 'Üle kogu Eesti', 'Ülekanne või sularaha'],
    ],

    // Fact strip right under the hero (every item = confirmed fact F4–F7 in the claim register)
    'trust' => [
        'aria' => 'Põhifaktid',
        'items' => [
            ['title' => 'Üle kogu Eesti', 'text' => 'Mitte ainult Tallinn ja Harjumaa'],
            ['title' => 'Igas seisukorras', 'text' => 'Ka rikkega ja avariilised autod'],
            ['title' => 'Liising või laen', 'text' => 'Päringu saab saata ka sellisele autole'],
            ['title' => 'Ülekanne või sularaha', 'text' => 'Makse tehingu vormistamisel'],
        ],
    ],

    'form' => [
        'title' => 'Küsi oma autole pakkumist',
        'step' => 'Samm :n / :total',
        'reg' => 'Registreerimisnumber',
        'reg_placeholder' => '123ABC',
        'reg_hint' => 'Tühikud ja sidekriipsud ei ole olulised.',
        'price_hint' => 'Ei ole kohustuslik – kui Sul on ootus, anna sellest teada.',
        'phone' => 'Telefoninumber',
        'phone_placeholder' => '+372 5xxx xxxx',
        'email' => 'E-post',
        'email_placeholder' => 'nimi@email.ee',
        'price' => 'Soovitud hind',
        'price_placeholder' => '15 000',
        'optional' => 'valikuline',
        'terms' => 'Olen tutvunud ja nõustun :link',
        'terms_link' => 'kasutustingimustega',
        'submit' => 'Alusta päringut',
        'note' => 'Võtame Sinuga ühendust esimesel võimalusel.',
    ],

    'modal' => [
        'back' => 'Tagasi',
        'close' => 'Sulge',
        'title' => 'Sinu auto lisainfo',
        'vehicle' => 'Sõiduk',
        'language' => 'Suhtluskeel',
        'mileage' => 'Läbisõit',
        'mileage_placeholder' => '185 000',
        'info' => 'Lisainfo',
        'info_placeholder' => 'Lisainfo auto kohta...',
        'info_hint' => 'Näiteks seisukord, varustus, kahjustused, omanike arv, hooldusajalugu, lisavarustus.',
        'photos' => 'Fotod',
        'photos_button' => 'Lisa fotod',
        'photos_hint' => 'JPG, PNG või WEBP · kuni :max fotot · kuni :mb MB foto kohta',
        'photos_why' => 'Fotod aitavad meil autot täpsemalt hinnata.',
        'photo_remove' => 'Eemalda foto',
        'photo_alt' => 'Foto :n',
        'submit' => 'Saada päring',
        'sending' => 'Saadan…',
        'success_title' => 'Aitäh! Päring on saadetud.',
        'success_text' => 'Võtame Sinuga ühendust esimesel võimalusel.',
        'success_next_title' => 'Mis edasi?',
        'success_next' => [
            'Vaatame Sinu päringu ja auto andmed läbi.',
            'Võtame Sinuga ühendust e-posti või telefoni teel, mille päringus märkisid.',
            'Lepime kokku auto ülevaatuse aja ja koha.',
        ],
    ],

    'errors' => [
        'reg_required' => 'Palun sisestage sõiduki reg. märk.',
        'reg_invalid' => 'Palun kontrollige sõiduki reg. märki.',
        'phone_required' => 'Palun sisestage telefoninumber.',
        'phone_invalid' => 'Palun sisestage korrektne telefoninumber.',
        'email_required' => 'Palun sisestage e-posti aadress.',
        'email_invalid' => 'Palun sisestage korrektne e-posti aadress.',
        'price_invalid' => 'Palun sisestage hind numbrina.',
        'terms' => 'Palun nõustuge kasutustingimustega.',
        'language' => 'Palun valige suhtluskeel.',
        'mileage_invalid' => 'Palun sisestage läbisõit numbrina.',
        'info_max' => 'Lisainfo on liiga pikk (kuni :max tähemärki).',
        'photos_max' => 'Lisada saab kuni :max fotot.',
        'photo_type' => 'Lubatud on ainult JPG, PNG ja WEBP failid.',
        'photo_size' => 'Foto on liiga suur (kuni :max MB).',
        'generic' => 'Päringu saatmine ebaõnnestus. Palun proovige uuesti või võtke meiega ühendust.',
        'throttle' => 'Liiga palju päringuid. Palun proovige mõne minuti pärast uuesti.',
    ],

    'steps' => [
        'eyebrow' => 'Kuidas töötab',
        'title' => 'Lihtne kokkuost 4 sammuga',
        'items' => [
            ['title' => 'Saada päring', 'text' => 'Sisesta auto andmed ja soovi korral lisa fotod. See võtab vaid paar minutit.'],
            ['title' => 'Saad pakkumise', 'text' => 'Hindame Sinu autot ja võtame Sinuga ühendust esimesel võimalusel.'],
            ['title' => 'Vaatame auto üle', 'text' => 'Lepime kokku sobiva aja ja koha ning vaatame auto koos üle.'],
            ['title' => 'Tehing ja raha kätte', 'text' => 'Vormistame tehingu ning maksame ülekandega või sularahas.'],
        ],
    ],

    'why' => [
        'eyebrow' => 'Miks Autostvabaks',
        'title' => 'Miks müüa auto just meile?',
        'lead' => 'Teeme auto müümise Sinu jaoks võimalikult lihtsaks – ilma kuulutuste, lõputute kõnede ja ajakuluta.',
        // Each item answers a real seller objection with a confirmed fact (no empty adjectives)
        'items' => [
            ['title' => 'Ilma kuulutuse ja võõraste kõnedeta', 'text' => 'Üks päring meile asendab kuulutuse, proovisõidud ja läbirääkimised võõrastega.'],
            ['title' => 'Üle kogu Eesti', 'text' => 'Tegutseme kõikjal Eestis, mitte ainult Tallinnas ja Harjumaal.'],
            ['title' => 'Igas seisukorras, ka liisingus', 'text' => 'Mark, vanus ja seisukord ei ole takistuseks – ostame ka rikkega, avariilisi ning liisingus või laenuga autosid.'],
            ['title' => 'Selge tehing', 'text' => 'Vormistame tehingu ja maksame pangaülekandega või soovi korral sularahas.'],
        ],
    ],

    // Valuation transparency: what the offer depends on (no algorithm, no margins)
    'valuation' => [
        'eyebrow' => 'Hindamine',
        'title' => 'Mis mõjutab pakkumist?',
        'answer' => 'Pakkumine sõltub auto turuväärtusest ja tegelikust seisukorrast. Hindamisel vaatame järgmist:',
        'factors' => [
            ['title' => 'Mark, mudel ja aasta', 'text' => 'Samuti mootor ja käigukast.'],
            ['title' => 'Läbisõit', 'text' => 'Tegelik läbisõit ja selle vastavus vanusele.'],
            ['title' => 'Tehniline seisukord', 'text' => 'Mootor, käigukast, vedrustus, pidurid.'],
            ['title' => 'Kahjustused', 'text' => 'Avariid, rooste, kere- ja salongikahjustused.'],
            ['title' => 'Varustus', 'text' => 'Lisavarustus ja komplektsus.'],
            ['title' => 'Hooldusajalugu', 'text' => 'Hooldusraamat, arved, ülevaatus.'],
            ['title' => 'Nõudlus turul', 'text' => 'Kui kiiresti sarnased autod Eestis müüvad.'],
        ],
        'note' => 'Ei tea oma auto hinda? Pole probleemi – soovitud hind on päringus valikuline.',
        'cta' => 'Küsi pakkumist',
    ],

    // Direct-answer blocks: question → short answer → detail → CTA (confirmed facts F5, F6, F7 only)
    'answers' => [
        'eyebrow' => 'Vastused',
        'title' => 'Liising, kahjustused ja makse',
        'items' => [
            [
                'question' => 'Kas ostate ka liisingus või laenuga auto?',
                'answer' => 'Jah. Päringu saab saata ka autole, mis on liisingus või laenuga.',
                'detail' => 'Märgi lisainfosse, millise liisinguandja või pangaga on tegu – täpsustame edasise käigu koos Sinuga.',
            ],
            [
                'question' => 'Kas ostate avariilise või rikkega auto?',
                'answer' => 'Jah. Ostame ka rikkega ja avariilisi autosid.',
                'detail' => 'Kirjelda kahjustusi lisainfos ja lisa fotod – nii saame teha täpsema pakkumise.',
            ],
            [
                'question' => 'Kuidas raha kätte saan?',
                'answer' => 'Pangaülekandega või sularahas – Sinu valikul.',
                'detail' => 'Makse toimub tehingu vormistamisel.',
            ],
        ],
        'cta' => 'Alusta päringut',
    ],

    'final' => [
        'title' => 'Valmis oma auto müüma?',
        'text' => 'Täida lühike päring – see võtab paar minutit. Võtame Sinuga ühendust esimesel võimalusel.',
        'cta' => 'Alusta päringut',
    ],

    'sticky' => [
        'cta' => 'Alusta päringut',
        'note' => 'Päring võtab paar minutit',
    ],

    'testimonials' => [
        'eyebrow' => 'Tagasiside',
        'title' => 'Klientide tagasiside',
        'prev' => 'Eelmine',
        'next' => 'Järgmine',
        'rating' => 'Hinnang: :n / 5',
    ],

    'faq' => [
        'eyebrow' => 'KKK',
        'title' => 'Korduma kippuvad küsimused',
    ],

    'contact' => [
        'eyebrow' => 'Kontakt',
        'title' => 'Võta meiega ühendust',
        'lead' => 'Tegutseme üle kogu Eesti. Saada päring või kirjuta meile – vastame esimesel võimalusel.',
        'lead_with_phone' => 'Tegutseme üle kogu Eesti. Kirjuta või helista – vastame esimesel võimalusel.',
        'address' => 'Aadress',
        'phone' => 'Telefon',
        'email' => 'E-post',
        'messengers' => 'Kirjuta meile',
        'map_title' => 'Autostvabaks asukoht kaardil',
        'open_map' => 'Ava Google Mapsis',
        'load_map' => 'Näita kaarti',
        'load_map_note' => 'Kaardi laadimisel kasutatakse Google Mapsi teenust.',
    ],

    'footer' => [
        'about' => 'Autode kokkuost üle kogu Eesti. Ostame igas seisukorras autosid.',
        'contact' => 'Kontakt',
        'links' => 'Lingid',
        'info' => 'Kasulik info',
        'legal' => 'Juriidiline info',
        'rights' => 'Kõik õigused kaitstud.',
        'top' => 'Tagasi üles',
    ],

    'page' => [
        'updated' => 'Viimati uuendatud: :date',
        'cta_title' => 'Soovid oma auto müüa?',
        'cta_text' => 'Täida lühike päring ja saa oma autole pakkumine.',
    ],

    'not_found' => [
        'title' => 'Lehte ei leitud',
        'text' => 'Otsitud lehte ei ole olemas või see on teisaldatud.',
        'button' => 'Avalehele',
    ],
];
