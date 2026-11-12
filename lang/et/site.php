<?php

// Avaliku lehe tekstid – eesti keel (põhikeel, sisu kvaliteedi etalon).
// Iga väide peab vastama faktiregistrile: docs/autostvabaks/AUTOSTVABAKS_FACT_CLAIM_REGISTER_V1.md
// Lubatud ei ole: parim/kõrgeim hind, garanteeritud ost, kiireim, ajaline vastuse lubadus, välja mõeldud arvud.

return [

    'meta' => [
        'title' => 'Autode kokkuost üle Eesti – müü oma auto lihtsalt | Autostvabaks',
        'description' => 'Autostvabaks ostab autosid üle kogu Eesti – igas seisukorras, ka rikkega, avariilised ja liisingus autod. Saada lühike päring ja saa pakkumine. Makse ülekandega või sularahas.',
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
        'subtitle' => 'Üks lühike päring asendab kuulutuse, proovisõidud ja läbirääkimised. Hindame igas seisukorras autosid – ka rikkega, avariilisi ja liisingus olevaid.',
    ],

    // Fact strip right under the hero (every item = confirmed fact F4–F7 in the claim register)
    'trust' => [
        'aria' => 'Põhifaktid',
        'items' => [
            ['title' => 'Üle kogu Eesti', 'text' => 'Mitte ainult Tallinn ja Harjumaa'],
            ['title' => 'Igas seisukorras', 'text' => 'Ka rikkega ja avariilised autod'],
            ['title' => 'Liisingus või laenuga', 'text' => 'Päringu saab saata ka sellisele autole'],
            ['title' => 'Ülekanne või sularaha', 'text' => 'Makse tehingu vormistamisel'],
        ],
    ],

    'form' => [
        'title' => 'Küsi oma autole pakkumist',
        'step' => 'Samm :n / :total',
        'reg' => 'Registreerimisnumber',
        'reg_placeholder' => '123ABC',
        'reg_hint' => 'Tühikud ja sidekriipsud ei ole olulised.',
        'price_hint' => 'Ei ole kohustuslik – kui Sul on hinnaootus, anna sellest teada.',
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
        'info_hint' => 'Näiteks seisukord, varustus, kahjustused, omanike arv, hooldusajalugu, liising või laen.',
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
        'title' => 'Auto väljaost nelja sammuga',
        'items' => [
            ['title' => 'Saada päring', 'text' => 'Sisesta registreerimisnumber ja kontaktandmed, soovi korral lisa läbisõit, kirjeldus ja fotod. Kulub paar minutit.'],
            ['title' => 'Räägime pakkumise läbi', 'text' => 'Vaatame andmed läbi ja võtame Sinuga ühendust esimesel võimalusel.'],
            ['title' => 'Vaatame auto üle', 'text' => 'Lepime kokku sobiva aja ja koha. Lõplik pakkumine kujuneb auto tegeliku seisukorra põhjal.'],
            ['title' => 'Tehing ja makse', 'text' => 'Vormistame tehingu ning maksame pangaülekandega või soovi korral sularahas.'],
        ],
    ],

    'why' => [
        'eyebrow' => 'Miks Autostvabaks',
        'title' => 'Miks müüa auto just meile?',
        'lead' => 'Vastame küsimustele, mida auto müüja tavaliselt esimesena küsib.',
        // Each card = a real seller objection → factual resolution (confirmed facts only, no adjectives)
        'items' => [
            ['title' => 'Ei taha kuulutust ega võõraste kõnesid?', 'text' => 'Üks päring meile asendab kuulutuse, proovisõidud ja läbirääkimised võõrastega. Suhtled ühe ostjaga.'],
            ['title' => 'Ei tea, mis Su auto väärt on?', 'text' => 'Soovitud hind on päringus valikuline. Selgitame avalikult, millest pakkumine sõltub, ja räägime hinna Sinuga läbi.'],
            ['title' => 'Auto on liisingus, rikkega või avariiline?', 'text' => 'See ei ole takistuseks. Hindame autosid igas seisukorras, ka liisingus või laenuga autosid – märgi see päringusse.'],
            ['title' => 'Kuidas tehing ja makse käivad?', 'text' => 'Lepime kokku ülevaatuse, vormistame tehingu ja maksame pangaülekandega või soovi korral sularahas.'],
        ],
    ],

    // Valuation transparency: what the offer depends on (main factors, not the full internal method; no margins)
    'valuation' => [
        'eyebrow' => 'Hindamine',
        'title' => 'Mis mõjutab pakkumist?',
        'answer' => 'Pakkumine lähtub sarnaste autode turuhinnast Eestis ja Sinu auto tegelikust seisukorrast. Lõplik pakkumine kujuneb pärast auto ülevaatust. Peamised tegurid, mida arvestame:',
        'factors' => [
            ['title' => 'Mark, mudel ja aasta', 'text' => 'Samuti mootor ja käigukast.'],
            ['title' => 'Läbisõit', 'text' => 'Tegelik läbisõit ja selle vastavus vanusele.'],
            ['title' => 'Tehniline seisukord', 'text' => 'Mootor, käigukast, vedrustus, pidurid.'],
            ['title' => 'Kahjustused', 'text' => 'Avariid, rooste, kere- ja salongikahjustused.'],
            ['title' => 'Varustus', 'text' => 'Lisavarustus ja komplektsus.'],
            ['title' => 'Hooldusajalugu', 'text' => 'Hooldusraamat, arved, ülevaatus.'],
            ['title' => 'Nõudlus turul', 'text' => 'Kui kiiresti sarnased autod Eestis müüvad.'],
        ],
        // Expectation message: direct buyback = simplicity and speed, not an auction of asking prices
        'expectation' => 'Otsemüük meile on mõeldud lihtsuse ja kiiruse jaoks: üks päring, üks ostja ja kokkulepitud tehing – ilma kuulutuse, ootamise ja võõraste proovisõitudeta. Pakkumine lähtub turuväärtusest ja seisukorrast, mitte kuulutuste küsimishindadest.',
        'note' => 'Ei tea oma auto hinda? Soovitud hind on päringus valikuline – teeme pakkumise ka ilma selleta.',
        'cta' => 'Küsi pakkumist',
    ],

    // Direct-answer blocks: question → short self-contained answer → detail → CTA (confirmed facts F5, F6, F7 only)
    'answers' => [
        'eyebrow' => 'Vastused',
        'title' => 'Liising, kahjustused ja makse',
        'items' => [
            [
                'question' => 'Kas liisingus või laenuga autot saab Autostvabaksile müüa?',
                'answer' => 'Jah. Liisingus või laenuga autole saab päringu saata nagu igale teisele autole.',
                'detail' => 'Märgi päringu lisainfosse, et autol on kehtiv liising või laen, ja soovi korral ka liisinguandja. Tehingu täpse käigu lepime Sinuga eraldi kokku.',
            ],
            [
                'question' => 'Kas avariilise või rikkega auto saab hindamisele saata?',
                'answer' => 'Jah. Hindame ka rikkega ja avariilisi autosid ning teeme pakkumise auto tegeliku seisukorra põhjal.',
                'detail' => 'Kirjelda kahjustusi lisainfos ja lisa fotod – nii on pakkumine täpsem.',
            ],
            [
                'question' => 'Kuidas Autostvabaks auto eest maksab?',
                'answer' => 'Pangaülekandega tehingu vormistamisel. Soovi korral on võimalik ka makse sularahas.',
                'detail' => 'Makse ja tehingu vormistamine toimuvad korraga – pärast auto ülevaatust ja kokkulepet.',
            ],
        ],
        'cta' => 'Alusta päringut',
    ],

    'final' => [
        'title' => 'Valmis oma auto müüma?',
        'text' => 'Täida lühike päring – kulub paar minutit. Võtame Sinuga ühendust esimesel võimalusel.',
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
        'about' => 'Autode kokkuost üle kogu Eesti. Hindame igas seisukorras autosid – ka liisingus, rikkega ja avariilisi.',
        'contact' => 'Kontakt',
        'links' => 'Lingid',
        'info' => 'Kasulik info',
        'legal' => 'Juriidiline info',
        // Legal operator / data controller line (F2) – discreet, factual, no second brand identity
        'operator' => 'Autostvabaks teenuse osutaja: :operator (registrikood :code)',
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
