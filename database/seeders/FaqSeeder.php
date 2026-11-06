<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Starter FAQ built from what has been agreed with the client so far.
 * The texts are drafts – edit them in the admin panel (Вопросы и ответы).
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        if (Faq::query()->exists()) {
            return;
        }

        $items = [
            [
                'question' => [
                    'et' => 'Milliseid autosid te ostate?',
                    'ru' => 'Какие автомобили вы покупаете?',
                    'en' => 'What kind of cars do you buy?',
                ],
                'answer' => [
                    'et' => 'Ostame igas seisukorras autosid – margist, vanusest ja läbisõidust sõltumata. Sobivad ka rikkega ja avariilised sõidukid.',
                    'ru' => 'Мы покупаем автомобили в любом состоянии – независимо от марки, возраста и пробега. Подходят также неисправные и аварийные автомобили.',
                    'en' => 'We buy cars in any condition – regardless of make, age or mileage. Faulty and damaged vehicles are welcome too.',
                ],
            ],
            [
                'question' => [
                    'et' => 'Kuidas päringu saatmine käib?',
                    'ru' => 'Как отправить заявку?',
                    'en' => 'How do I send a request?',
                ],
                'answer' => [
                    'et' => 'Sisesta auto registreerimisnumber ja oma kontaktandmed, lisa soovi korral läbisõit, lisainfo ja fotod ning vajuta „Saada päring“. See võtab vaid paar minutit.',
                    'ru' => 'Укажите регистрационный номер автомобиля и свои контактные данные, при желании добавьте пробег, описание и фотографии и нажмите «Отправить заявку». Это займёт пару минут.',
                    'en' => 'Enter the registration number and your contact details, optionally add the mileage, a description and photos, and press “Send request”. It only takes a couple of minutes.',
                ],
            ],
            [
                'question' => [
                    'et' => 'Kui kiiresti te vastate?',
                    'ru' => 'Как быстро вы отвечаете?',
                    'en' => 'How quickly do you reply?',
                ],
                'answer' => [
                    'et' => 'Vaatame iga päringu läbi ja võtame Sinuga ühendust esimesel võimalusel.',
                    'ru' => 'Мы рассматриваем каждую заявку и связываемся с вами при первой возможности.',
                    'en' => 'We review every request and contact you as soon as possible.',
                ],
            ],
            [
                'question' => [
                    'et' => 'Kas fotode lisamine on kohustuslik?',
                    'ru' => 'Обязательно ли добавлять фотографии?',
                    'en' => 'Do I have to add photos?',
                ],
                'answer' => [
                    'et' => 'Ei ole. Fotod aitavad meil aga autot täpsemalt hinnata ja teha pakkumise kiiremini.',
                    'ru' => 'Нет. Но фотографии помогают точнее оценить автомобиль и быстрее сделать предложение.',
                    'en' => 'No. Photos do help us evaluate the car more accurately and make an offer faster, though.',
                ],
            ],
            [
                'question' => [
                    'et' => 'Kuidas toimub arveldus?',
                    'ru' => 'Как происходит расчёт?',
                    'en' => 'How do I get paid?',
                ],
                'answer' => [
                    'et' => 'Pärast tehingu kokkuleppimist tasume pangaülekandega või soovi korral sularahas.',
                    'ru' => 'После согласования сделки мы платим банковским переводом или, по желанию, наличными.',
                    'en' => 'Once the deal is agreed, we pay by bank transfer or, if you prefer, in cash.',
                ],
            ],
            [
                'question' => [
                    'et' => 'Kas tegutsete ainult Tallinnas?',
                    'ru' => 'Вы работаете только в Таллинне?',
                    'en' => 'Do you only operate in Tallinn?',
                ],
                'answer' => [
                    'et' => 'Ei, ostame autosid üle kogu Eesti.',
                    'ru' => 'Нет, мы выкупаем автомобили по всей Эстонии.',
                    'en' => 'No, we buy cars all over Estonia.',
                ],
            ],
        ];

        foreach ($items as $i => $item) {
            Faq::create($item + ['sort' => ($i + 1) * 10, 'is_active' => true]);
        }
    }
}
