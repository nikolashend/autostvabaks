<?php

namespace Tests\Feature;

use App\Mail\NewLeadMail;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeadFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'reg_number' => '123 abc',
            'phone' => '+372 5555 1234',
            'email' => 'Client@Example.com',
            'desired_price' => '15 000',
            'terms' => '1',
            'language' => 'et',
            'mileage' => '185 000',
            'info' => 'Teine omanik.',
        ], $overrides);
    }

    public function test_landing_page_is_available_in_every_language_with_the_form_on_it(): void
    {
        $this->get('/')->assertOk()->assertSee('Alusta päringut')->assertSee('id="lead-step1"', false);
        $this->get('/ru')->assertOk()->assertSee('Начать заявку')->assertSee('id="lead-step1"', false);
        $this->get('/en')->assertOk()->assertSee('Start request')->assertSee('id="lead-step1"', false);
    }

    public function test_legal_pages_have_their_own_url_per_language(): void
    {
        $this->get('/kasutustingimused')->assertOk()->assertSee('Kasutustingimused');
        $this->get('/ru/politika-konfidentsialnosti')->assertOk()->assertSee('Политика конфиденциальности');
        $this->get('/en/terms-of-use')->assertOk()->assertSee('Terms of use');

        $this->get('/en/kasutustingimused')->assertNotFound();
        $this->get('/no-such-page')->assertNotFound();
    }

    public function test_a_valid_request_is_saved_and_e_mailed_with_photos_attached(): void
    {
        Mail::fake();
        Storage::fake('local');

        $response = $this->postJson('/ru/paring', $this->validPayload([
            'language' => 'ru',
            'photos' => [
                UploadedFile::fake()->image('front.jpg', 1200, 800),
                UploadedFile::fake()->image('back.png', 1200, 800),
            ],
        ]));

        $response->assertOk()->assertJson(['ok' => true, 'title' => 'Спасибо! Заявка отправлена.']);

        $lead = Lead::sole();
        $this->assertSame('123ABC', $lead->reg_number);
        $this->assertSame('client@example.com', $lead->email);
        $this->assertSame(15000, $lead->desired_price);
        $this->assertSame(185000, $lead->mileage);
        $this->assertSame('ru', $lead->language);
        $this->assertSame('ru', $lead->locale);
        $this->assertCount(2, $lead->photos);
        $this->assertNotNull($lead->mail_sent_at);
        Storage::disk('local')->assertExists($lead->photos[0]);

        Mail::assertSent(NewLeadMail::class, function (NewLeadMail $mail) use ($lead) {
            return $mail->hasTo('alan@autostvabaks.ee')
                && $mail->hasSubject('Новая заявка — 123ABC')
                && $mail->hasReplyTo('client@example.com')
                && count($mail->attachments()) === 2
                && $mail->lead->is($lead);
        });
    }

    public function test_price_mileage_info_and_photos_are_optional(): void
    {
        Mail::fake();

        $this->postJson('/paring', [
            'reg_number' => '456DEF',
            'phone' => '5555 9876',
            'email' => 'a@example.ee',
            'terms' => '1',
            'language' => 'et',
        ])->assertOk();

        $lead = Lead::sole();
        $this->assertNull($lead->desired_price);
        $this->assertNull($lead->mileage);
        $this->assertNull($lead->photos);
    }

    public function test_errors_are_returned_per_field_in_the_language_of_the_page(): void
    {
        Mail::fake();

        $this->postJson('/paring', [])
            ->assertStatus(422)
            ->assertJsonPath('errors.reg_number.0', 'Palun sisestage sõiduki reg. märk.')
            ->assertJsonPath('errors.phone.0', 'Palun sisestage telefoninumber.')
            ->assertJsonPath('errors.email.0', 'Palun sisestage e-posti aadress.')
            ->assertJsonPath('errors.terms.0', 'Palun nõustuge kasutustingimustega.');

        $this->postJson('/en/paring', $this->validPayload(['email' => 'nope', 'desired_price' => 'cheap']))
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'Please enter a valid e-mail address.')
            ->assertJsonPath('errors.desired_price.0', 'Please enter the price as a number.')
            ->assertJsonMissingPath('errors.reg_number');

        $this->assertSame(0, Lead::count());
        Mail::assertNothingSent();
    }

    public function test_only_real_images_are_accepted_as_photos(): void
    {
        Mail::fake();
        Storage::fake('local');

        $this->postJson('/paring', $this->validPayload([
            'photos' => [UploadedFile::fake()->create('shell.php', 10, 'application/x-php')],
        ]))->assertStatus(422)->assertJsonValidationErrors(['photos.0' => 'Lubatud on ainult JPG, PNG ja WEBP failid.']);

        $tooMany = array_fill(0, config('autostvabaks.leads.max_photos') + 1, UploadedFile::fake()->image('p.jpg'));
        $this->postJson('/paring', $this->validPayload(['photos' => $tooMany]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('photos');

        $this->assertSame(0, Lead::count());
    }

    public function test_a_filled_honeypot_is_silently_ignored(): void
    {
        Mail::fake();

        $this->postJson('/paring', $this->validPayload(['website' => 'http://spam.example']))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame(0, Lead::count());
        Mail::assertNothingSent();
    }

    public function test_the_lead_is_kept_when_the_e_mail_cannot_be_sent(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP is down'));

        $this->postJson('/paring', $this->validPayload())->assertOk()->assertJson(['ok' => true]);

        $lead = Lead::sole();
        $this->assertNull($lead->mail_sent_at);
        $this->assertSame('SMTP is down', $lead->mail_error);
    }

    public function test_lead_photos_are_only_served_to_signed_in_admins(): void
    {
        Mail::fake();
        Storage::fake('local');

        $this->postJson('/paring', $this->validPayload([
            'photos' => [UploadedFile::fake()->image('front.jpg', 600, 400)],
        ]))->assertOk();

        $lead = Lead::sole();
        $url = route('admin.lead-photo', ['lead' => $lead->id, 'index' => 0]);

        $this->get($url)->assertRedirect('/admin/login');
        $this->actingAs(User::first())->get($url)->assertOk();
        $this->actingAs(User::first())->get(route('admin.lead-photo', ['lead' => $lead->id, 'index' => 5]))->assertNotFound();
    }

    public function test_admin_panel_requires_login_and_opens_for_an_admin(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $this->actingAs(User::first());

        foreach (['/admin', '/admin/leads', '/admin/testimonials', '/admin/faqs', '/admin/pages', '/admin/settings', '/admin/users'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_staging_basic_auth_protects_the_whole_site_when_configured(): void
    {
        config(['autostvabaks.staging_auth' => 'tester:secret']);

        $this->get('/')->assertStatus(401)->assertHeader('WWW-Authenticate');
        $this->get('/admin/login')->assertStatus(401);
        $this->withBasicAuth('tester', 'wrong')->get('/')->assertStatus(401);
        $this->withBasicAuth('tester', 'secret')->get('/')->assertOk()->assertSee('id="lead-step1"', false);

        config(['autostvabaks.staging_auth' => '']);
        $this->get('/')->assertOk();
    }

    public function test_plain_http_is_redirected_to_https_when_app_url_is_https(): void
    {
        config(['app.url' => 'https://autostvabaks.test']);

        $this->get('http://autostvabaks.test/ru?x=1')
            ->assertRedirect('https://autostvabaks.test/ru?x=1')
            ->assertStatus(301);

        $this->get('https://autostvabaks.test/ru')->assertOk();

        config(['app.url' => 'http://localhost']);
        $this->get('http://localhost/ru')->assertOk();
    }

    public function test_noindex_flag_closes_the_site_for_search_engines(): void
    {
        config(['autostvabaks.noindex' => true]);

        $this->get('/')->assertOk()->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /')->assertDontSee('Sitemap:');
    }

    public function test_sitemap_lists_every_language_version(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(url('/ru'), false)
            ->assertSee(url('/en/privacy-policy'), false);

        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /admin');
    }
}
