<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Final FAQ (Stage 3): real seller objections only, one source of truth for the
 * visible accordion and the FAQPage schema. Every answer is self-contained
 * (readable when extracted on its own) and uses confirmed facts only
 * (docs/autostvabaks/AUTOSTVABAKS_FACT_CLAIM_REGISTER_V1.md).
 *
 * Idempotent content release: records are matched by `key` (or, for rows created
 * before Stage 3, by their Estonian question) and updated in place, so
 *   php artisan db:seed --class=FaqSeeder --force
 * publishes exactly this list. Admin edits to these keys are overwritten on the
 * next run – change the text here, not only in the admin panel. Extra questions
 * added in the admin panel (without a key) are left untouched.
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::items() as $i => $item) {
            $faq = Faq::query()->where('key', $item['key'])->first()
                ?? Faq::query()->whereNull('key')->get()->first(fn (Faq $f) => in_array($f->t('question', 'et'), $item['legacy'] ?? [], true));

            $data = ['key' => $item['key'], 'question' => $item['question'], 'answer' => $item['answer'], 'sort' => ($i + 1) * 10, 'is_active' => true];

            $faq ? $faq->update($data) : Faq::create($data);
        }
    }

    /** @return array<int, array{key: string, legacy?: array<int, string>, question: array<string, string>, answer: array<string, string>}> */
    public static function items(): array
    {
        return [
            [
                'key' => 'vehicles',
                'legacy' => ['Milliseid autosid te ostate?'],
                'question' => [
                    'et' => 'Milliseid autosid Autostvabaks hindab ja ostab?',
                    'ru' => 'Какие автомобили оценивает и выкупает Autostvabaks?',
                    'en' => 'Which cars does Autostvabaks evaluate and buy?',
                ],
                'answer' => [
                    'et' => 'Autostvabaks hindab sõiduautosid igas seisukorras – margist, vanusest ja läbisõidust sõltumata. Päringu saab saata ka rikkega, avariilisele ning liisingus või laenuga autole. Pakkumine tehakse iga auto tegeliku seisukorra põhjal.',
                    'ru' => 'Autostvabaks оценивает легковые автомобили в любом состоянии – независимо от марки, возраста и пробега. Заявку можно отправить и на неисправный автомобиль, автомобиль после ДТП, а также на машину в лизинге или кредите. Предложение делается исходя из реального состояния конкретного автомобиля.',
                    'en' => 'Autostvabaks evaluates passenger cars in any condition – regardless of make, age or mileage. You can also send a request for a faulty or damaged car, or for a car under leasing or a loan. Each offer is based on the actual condition of the car.',
                ],
            ],
            [
                'key' => 'how',
                'legacy' => ['Kuidas päringu saatmine käib?'],
                'question' => [
                    'et' => 'Kuidas auto müümine Autostvabaksile käib?',
                    'ru' => 'Как проходит продажа автомобиля Autostvabaks?',
                    'en' => 'How does selling a car to Autostvabaks work?',
                ],
                'answer' => [
                    'et' => 'Sisesta auto registreerimisnumber ja kontaktandmed, soovi korral lisa läbisõit, lisainfo ja fotod ning saada päring – see võtab paar minutit. Vaatame andmed läbi ja võtame Sinuga ühendust esimesel võimalusel. Seejärel lepime kokku auto ülevaatuse, vormistame tehingu ja maksame pangaülekandega või sularahas.',
                    'ru' => 'Укажите регистрационный номер и контакты, при желании добавьте пробег, описание и фото и отправьте заявку – это займёт пару минут. Мы изучим данные и свяжемся с вами при первой возможности. Затем договоримся об осмотре автомобиля, оформим сделку и заплатим банковским переводом или наличными.',
                    'en' => 'Enter the registration number and your contact details, optionally add mileage, a description and photos, and send the request – it takes a couple of minutes. We review the details and contact you as soon as possible. Then we agree on an inspection, complete the paperwork and pay by bank transfer or in cash.',
                ],
            ],
            [
                'key' => 'response',
                'legacy' => ['Kui kiiresti te vastate?'],
                'question' => [
                    'et' => 'Kui kiiresti Autostvabaks päringule vastab?',
                    'ru' => 'Как быстро Autostvabaks отвечает на заявку?',
                    'en' => 'How quickly does Autostvabaks respond to a request?',
                ],
                'answer' => [
                    'et' => 'Vaatame iga päringu läbi ja võtame Sinuga ühendust esimesel võimalusel – e-posti või telefoni teel, mille päringus märkisid.',
                    'ru' => 'Мы рассматриваем каждую заявку и связываемся с вами при первой возможности – по e-mail или телефону, указанным в заявке.',
                    'en' => 'We review every request and contact you as soon as possible – by e-mail or phone, as given in the request.',
                ],
            ],
            [
                'key' => 'photos',
                'legacy' => ['Kas fotode lisamine on kohustuslik?'],
                'question' => [
                    'et' => 'Kas fotode lisamine päringule on kohustuslik?',
                    'ru' => 'Обязательно ли добавлять к заявке фотографии?',
                    'en' => 'Do I have to add photos to the request?',
                ],
                'answer' => [
                    'et' => 'Ei, fotod on valikulised. Fotod aitavad meil autot täpsemalt hinnata, seega on nendega pakkumine täpsem. Lisada saab kuni 10 fotot (JPG, PNG või WEBP).',
                    'ru' => 'Нет, фотографии необязательны. Они помогают точнее оценить автомобиль, поэтому с фото предложение будет точнее. Можно добавить до 10 фотографий (JPG, PNG или WEBP).',
                    'en' => 'No, photos are optional. They help us evaluate the car more precisely, so the offer is more accurate with them. You can add up to 10 photos (JPG, PNG or WEBP).',
                ],
            ],
            [
                'key' => 'valuation',
                'question' => [
                    'et' => 'Kuidas Autostvabaks auto hinna määrab?',
                    'ru' => 'Как Autostvabaks определяет цену автомобиля?',
                    'en' => 'How does Autostvabaks determine the price of a car?',
                ],
                'answer' => [
                    'et' => 'Pakkumine lähtub sarnaste autode turuhinnast Eestis ja auto tegelikust seisukorrast. Arvestame marki, mudelit ja aastat, läbisõitu, tehnilist seisukorda, kahjustusi, varustust, hooldusajalugu ja nõudlust turul. Lõplik pakkumine kujuneb pärast auto ülevaatust. Soovitud hind on päringus valikuline.',
                    'ru' => 'Предложение исходит из рыночной цены похожих автомобилей в Эстонии и реального состояния машины. Мы учитываем марку, модель и год, пробег, техническое состояние, повреждения, комплектацию, историю обслуживания и спрос на рынке. Окончательное предложение формируется после осмотра. Желаемая цена в заявке необязательна.',
                    'en' => 'The offer is based on the market price of similar cars in Estonia and the actual condition of the car. We consider make, model and year, mileage, technical condition, damage, equipment, service history and market demand. The final offer is made after an inspection. The desired price in the request is optional.',
                ],
            ],
            [
                'key' => 'area',
                'legacy' => ['Kas tegutsete ainult Tallinnas?'],
                'question' => [
                    'et' => 'Kas Autostvabaks ostab autosid ka väljaspool Tallinna?',
                    'ru' => 'Выкупает ли Autostvabaks автомобили за пределами Таллинна?',
                    'en' => 'Does Autostvabaks buy cars outside Tallinn?',
                ],
                'answer' => [
                    'et' => 'Jah. Autostvabaks ostab autosid üle kogu Eesti, mitte ainult Tallinnas ja Harjumaal. Ülevaatuse aja ja koha lepime kokku pärast päringu läbivaatamist.',
                    'ru' => 'Да. Autostvabaks выкупает автомобили по всей Эстонии, а не только в Таллинне и Харьюмаа. Время и место осмотра согласуем после рассмотрения заявки.',
                    'en' => 'Yes. Autostvabaks buys cars all over Estonia, not only in Tallinn and Harju County. The time and place of the inspection are agreed after we have reviewed the request.',
                ],
            ],
            [
                'key' => 'leasing',
                'question' => [
                    'et' => 'Kas liisingus või laenuga autot saab Autostvabaksile müüa?',
                    'ru' => 'Можно ли продать Autostvabaks автомобиль в лизинге или кредите?',
                    'en' => 'Can I sell a leased or financed car to Autostvabaks?',
                ],
                'answer' => [
                    'et' => 'Jah. Liisingus või laenuga autole saab päringu saata nagu igale teisele autole. Märgi lisainfosse, et autol on kehtiv liising või laen, ja soovi korral ka liisinguandja – tehingu täpse käigu lepime Sinuga eraldi kokku.',
                    'ru' => 'Да. На автомобиль в лизинге или кредите заявку можно отправить так же, как на любой другой. Укажите в дополнительной информации, что у автомобиля действующий лизинг или кредит, и при желании – лизингодателя. Точный порядок сделки согласуем с вами отдельно.',
                    'en' => 'Yes. A car under leasing or a loan can be submitted like any other car. Mention the active leasing or loan in the additional information, and the leasing company if you like – the exact steps of the deal are agreed with you individually.',
                ],
            ],
            [
                'key' => 'damaged',
                'question' => [
                    'et' => 'Kas avariilise või rikkega auto saab hindamisele saata?',
                    'ru' => 'Можно ли отправить на оценку автомобиль после ДТП или с неисправностью?',
                    'en' => 'Can I submit a damaged or faulty car for evaluation?',
                ],
                'answer' => [
                    'et' => 'Jah. Autostvabaks hindab ka rikkega ja avariilisi autosid ning teeb pakkumise auto tegeliku seisukorra põhjal. Kirjelda kahjustusi lisainfos ja lisa fotod – nii on pakkumine täpsem.',
                    'ru' => 'Да. Autostvabaks оценивает и неисправные автомобили, и автомобили после ДТП, и делает предложение исходя из их реального состояния. Опишите повреждения в дополнительной информации и добавьте фото – так предложение будет точнее.',
                    'en' => 'Yes. Autostvabaks evaluates faulty and damaged cars as well and makes an offer based on the actual condition of the car. Describe the damage in the additional information and add photos – the offer will be more precise.',
                ],
            ],
            [
                'key' => 'payment',
                'legacy' => ['Kuidas toimub arveldus?'],
                'question' => [
                    'et' => 'Kuidas Autostvabaks auto eest maksab?',
                    'ru' => 'Как Autostvabaks платит за автомобиль?',
                    'en' => 'How does Autostvabaks pay for the car?',
                ],
                'answer' => [
                    'et' => 'Pangaülekandega tehingu vormistamisel. Soovi korral on võimalik ka makse sularahas. Makse ja tehingu vormistamine toimuvad korraga – pärast auto ülevaatust ja kokkulepet.',
                    'ru' => 'Банковским переводом при оформлении сделки. По желанию возможна оплата наличными. Оплата и оформление сделки происходят одновременно – после осмотра автомобиля и договорённости.',
                    'en' => 'By bank transfer when the deal is completed. Cash payment is possible on request. Payment and paperwork happen at the same time – after the inspection and the agreement.',
                ],
            ],
        ];
    }
}
