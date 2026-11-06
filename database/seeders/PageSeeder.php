<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * System pages linked from the form and the footer.
 *
 * IMPORTANT: the texts below are structural DRAFTS, not legal advice. They must be
 * reviewed and replaced with the final Kasutustingimused / Privaatsuspoliitika
 * (admin panel → Страницы) before the site is published.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $operator = config('autostvabaks.settings.operator_name', 'Motion Motors OÜ');
        $address = config('autostvabaks.settings.address');
        $email = config('autostvabaks.settings.public_email');

        $draft = [
            'et' => '<p><em>Mustand – lõplik tekst lisatakse enne lehe avalikustamist.</em></p>',
            'ru' => '<p><em>Черновик – окончательный текст будет добавлен до публикации сайта.</em></p>',
            'en' => '<p><em>Draft – the final text will be added before the site is published.</em></p>',
        ];

        Page::firstOrCreate(['key' => 'terms'], [
            'slug_et' => 'kasutustingimused',
            'slug_ru' => 'usloviya-ispolzovaniya',
            'slug_en' => 'terms-of-use',
            'footer_group' => 'legal',
            'sort' => 10,
            'title' => [
                'et' => 'Kasutustingimused',
                'ru' => 'Условия использования',
                'en' => 'Terms of use',
            ],
            'meta_description' => [
                'et' => 'Autostvabaks veebilehe ja päringuvormi kasutustingimused.',
                'ru' => 'Условия использования сайта и формы заявки Autostvabaks.',
                'en' => 'Terms of use of the Autostvabaks website and request form.',
            ],
            'content' => [
                'et' => $draft['et'].<<<HTML
<h2>1. Üldsätted</h2>
<p>Käesolevad tingimused kehtivad veebilehe autostvabaks.ee ja sellel oleva päringuvormi kasutamisel. Päringu saatmisega kinnitad, et oled tingimustega tutvunud ja nõustud nendega.</p>
<h2>2. Teenuse kirjeldus</h2>
<p>Autostvabaks võtab vastu sõidukite müügisoove ning teeb sobivuse korral sõiduki ostupakkumise. Ostame sõidukeid üle kogu Eesti, olenemata sõiduki seisukorrast.</p>
<h2>3. Päring ja pakkumine</h2>
<ul>
<li>Päringu saatmine on tasuta ega kohusta Sind sõidukit müüma.</li>
<li>Päring ei ole siduv pakkumine. Lõplik hind lepitakse kokku pärast sõiduki andmete ja seisukorra hindamist.</li>
<li>Võtame Sinuga ühendust esimesel võimalusel päringus märgitud kontaktandmetel.</li>
</ul>
<h2>4. Kasutaja kinnitused</h2>
<p>Päringu saatmisega kinnitad, et esitatud andmed on õiged, et Sul on õigus sõidukit müüa või esindad isikut, kellel see õigus on, ning et lisatud fotod on Sinu tehtud või on Sul õigus neid kasutada.</p>
<h2>5. Arveldus</h2>
<p>Tehingu sõlmimisel tasutakse ostuhind pangaülekandega või poolte kokkuleppel sularahas.</p>
<h2>6. Isikuandmed</h2>
<p>Isikuandmete töötlemist kirjeldab privaatsuspoliitika.</p>
<h2>7. Kontakt</h2>
<p>Küsimuste korral kirjuta meile: <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
                'ru' => $draft['ru'].<<<HTML
<h2>1. Общие положения</h2>
<p>Настоящие условия действуют при использовании сайта autostvabaks.ee и размещённой на нём формы заявки. Отправляя заявку, вы подтверждаете, что ознакомились с условиями и согласны с ними.</p>
<h2>2. Описание услуги</h2>
<p>Autostvabaks принимает заявки на продажу автомобилей и при заинтересованности делает предложение о выкупе. Мы выкупаем автомобили по всей Эстонии независимо от их состояния.</p>
<h2>3. Заявка и предложение</h2>
<ul>
<li>Отправка заявки бесплатна и не обязывает вас продавать автомобиль.</li>
<li>Заявка не является обязывающим предложением. Окончательная цена согласуется после оценки данных и состояния автомобиля.</li>
<li>Мы свяжемся с вами при первой возможности по контактным данным, указанным в заявке.</li>
</ul>
<h2>4. Подтверждения пользователя</h2>
<p>Отправляя заявку, вы подтверждаете, что указанные данные достоверны, что вы вправе продать автомобиль или представляете лицо, имеющее такое право, и что приложенные фотографии сделаны вами либо вы вправе их использовать.</p>
<h2>5. Расчёт</h2>
<p>При заключении сделки покупная цена выплачивается банковским переводом или, по договорённости сторон, наличными.</p>
<h2>6. Персональные данные</h2>
<p>Обработка персональных данных описана в политике конфиденциальности.</p>
<h2>7. Контакты</h2>
<p>По вопросам пишите нам: <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
                'en' => $draft['en'].<<<HTML
<h2>1. General</h2>
<p>These terms apply to the use of the autostvabaks.ee website and its request form. By sending a request you confirm that you have read and accept these terms.</p>
<h2>2. The service</h2>
<p>Autostvabaks accepts requests to sell vehicles and, where suitable, makes an offer to buy the vehicle. We buy vehicles all over Estonia regardless of their condition.</p>
<h2>3. Request and offer</h2>
<ul>
<li>Sending a request is free of charge and does not oblige you to sell your vehicle.</li>
<li>A request is not a binding offer. The final price is agreed after the vehicle's details and condition have been assessed.</li>
<li>We will contact you as soon as possible using the contact details given in the request.</li>
</ul>
<h2>4. Your confirmations</h2>
<p>By sending a request you confirm that the information provided is correct, that you are entitled to sell the vehicle or represent a person who is, and that the attached photos were taken by you or you have the right to use them.</p>
<h2>5. Payment</h2>
<p>When a deal is concluded, the purchase price is paid by bank transfer or, if the parties so agree, in cash.</p>
<h2>6. Personal data</h2>
<p>The processing of personal data is described in the privacy policy.</p>
<h2>7. Contact</h2>
<p>If you have any questions, write to us: <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
            ],
        ]);

        Page::firstOrCreate(['key' => 'privacy'], [
            'slug_et' => 'privaatsuspoliitika',
            'slug_ru' => 'politika-konfidentsialnosti',
            'slug_en' => 'privacy-policy',
            'footer_group' => 'legal',
            'sort' => 20,
            'title' => [
                'et' => 'Privaatsuspoliitika',
                'ru' => 'Политика конфиденциальности',
                'en' => 'Privacy policy',
            ],
            'meta_description' => [
                'et' => 'Kuidas Autostvabaks isikuandmeid kogub, kasutab ja säilitab.',
                'ru' => 'Как Autostvabaks собирает, использует и хранит персональные данные.',
                'en' => 'How Autostvabaks collects, uses and stores personal data.',
            ],
            'content' => [
                'et' => $draft['et'].<<<HTML
<h2>1. Milliseid andmeid kogume</h2>
<p>Päringu saatmisel kogume andmed, mille ise vormi sisestad: sõiduki registreerimisnumber, telefoninumber, e-posti aadress, soovitud hind, läbisõit, lisainfo, fotod ja eelistatud suhtluskeel. Lisaks salvestatakse tehnilised andmed (nt IP-aadress ja päringu saatmise aeg).</p>
<h2>2. Milleks andmeid kasutame</h2>
<ul>
<li>sõiduki hindamiseks ja ostupakkumise tegemiseks;</li>
<li>Sinuga ühenduse võtmiseks päringu asjus;</li>
<li>tehingu ettevalmistamiseks ja sõlmimiseks;</li>
<li>seadusest tulenevate kohustuste täitmiseks.</li>
</ul>
<h2>3. Õiguslik alus</h2>
<p>Töötleme andmeid Sinu taotlusel lepingu sõlmimisele eelnevate toimingute tegemiseks ning Sinu nõusoleku alusel.</p>
<h2>4. Andmete säilitamine ja edastamine</h2>
<p>Säilitame andmeid nii kaua, kui see on vajalik päringu menetlemiseks ja seadusest tulenevate kohustuste täitmiseks. Me ei müü Sinu andmeid kolmandatele isikutele. Andmeid võivad töödelda meie teenusepakkujad (nt veebimajutus ja e-post) ulatuses, mis on vajalik teenuse osutamiseks.</p>
<h2>5. Sinu õigused</h2>
<p>Sul on õigus tutvuda oma andmetega, nõuda nende parandamist või kustutamist, piirata töötlemist, võtta nõusolek tagasi ning esitada kaebus Andmekaitse Inspektsioonile (<a href="https://www.aki.ee" target="_blank" rel="noopener">www.aki.ee</a>).</p>
<h2>6. Vastutav töötleja ja kontakt</h2>
<p>Isikuandmete vastutav töötleja on {$operator}, {$address}. Andmekaitsega seotud küsimustes kirjuta: <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
                'ru' => $draft['ru'].<<<HTML
<h2>1. Какие данные мы собираем</h2>
<p>При отправке заявки мы получаем данные, которые вы сами указываете в форме: регистрационный номер автомобиля, номер телефона, адрес электронной почты, желаемую цену, пробег, дополнительную информацию, фотографии и предпочитаемый язык общения. Также сохраняются технические данные (например, IP-адрес и время отправки заявки).</p>
<h2>2. Для чего мы используем данные</h2>
<ul>
<li>для оценки автомобиля и подготовки предложения о выкупе;</li>
<li>для связи с вами по вашей заявке;</li>
<li>для подготовки и заключения сделки;</li>
<li>для выполнения обязанностей, предусмотренных законом.</li>
</ul>
<h2>3. Правовое основание</h2>
<p>Мы обрабатываем данные по вашему запросу для действий, предшествующих заключению договора, а также на основании вашего согласия.</p>
<h2>4. Хранение и передача данных</h2>
<p>Мы храним данные столько, сколько необходимо для обработки заявки и выполнения обязанностей, предусмотренных законом. Мы не продаём ваши данные третьим лицам. Данные могут обрабатывать наши поставщики услуг (например, хостинг и электронная почта) в объёме, необходимом для оказания услуги.</p>
<h2>5. Ваши права</h2>
<p>Вы вправе ознакомиться со своими данными, потребовать их исправления или удаления, ограничить обработку, отозвать согласие и подать жалобу в Инспекцию по защите данных (<a href="https://www.aki.ee" target="_blank" rel="noopener">www.aki.ee</a>).</p>
<h2>6. Ответственный обработчик и контакты</h2>
<p>Ответственный обработчик персональных данных – {$operator}, {$address}. По вопросам защиты данных пишите: <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
                'en' => $draft['en'].<<<HTML
<h2>1. What data we collect</h2>
<p>When you send a request we collect the data you enter in the form: the vehicle registration number, phone number, e-mail address, desired price, mileage, additional information, photos and your preferred language. Technical data (such as your IP address and the time of the request) is stored as well.</p>
<h2>2. What we use the data for</h2>
<ul>
<li>to evaluate the vehicle and prepare an offer;</li>
<li>to contact you about your request;</li>
<li>to prepare and conclude the deal;</li>
<li>to comply with legal obligations.</li>
</ul>
<h2>3. Legal basis</h2>
<p>We process the data at your request in order to take steps prior to entering into a contract, and on the basis of your consent.</p>
<h2>4. Storage and sharing</h2>
<p>We keep the data for as long as necessary to handle the request and to comply with legal obligations. We do not sell your data to third parties. The data may be processed by our service providers (such as hosting and e-mail) to the extent necessary to provide the service.</p>
<h2>5. Your rights</h2>
<p>You have the right to access your data, have it corrected or erased, restrict processing, withdraw your consent and lodge a complaint with the Estonian Data Protection Inspectorate (<a href="https://www.aki.ee" target="_blank" rel="noopener">www.aki.ee</a>).</p>
<h2>6. Data controller and contact</h2>
<p>The controller of personal data is {$operator}, {$address}. For data protection matters write to: <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
            ],
        ]);
    }
}
