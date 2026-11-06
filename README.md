# Autostvabaks

Лендинг сервиса выкупа автомобилей **autostvabaks.ee** (отдельный бренд; юридический оператор — Motion Motors OÜ, упоминается только в политике конфиденциальности).

Стек: Laravel 12 · PHP 8.2 · MySQL 8 · Filament 3 (админ-панель) · HTML-тема Carento v2.1 (ThemeForest).

## Что реализовано

- **Лендинг на трёх языках** — `/` (эстонский, основной), `/ru`, `/en`. Ручной переключатель, без автоматического редиректа по языку браузера; у каждой версии свой URL для Google Ads, `hreflang`, `sitemap.xml`, JSON-LD (AutoDealer + FAQPage).
- **Двухэтапная форма заявки** прямо на первом экране:
  1. Registreerimisnumber · Telefoninumber · E-post (обязательно) · Soovitud hind (необязательно) · чекбокс согласия с условиями → **Alusta päringut** (проверка полей, заявка ещё не отправляется).
  2. Модальное окно «Sinu auto lisainfo»: Suhtluskeel (ET/RU/EN, по умолчанию = язык сайта) · Läbisõit · Lisainfo · фото (JPG/PNG/WEBP, превью, удаление, до 10 шт., сжатие в браузере до 1920px) → **Saada päring**.
  - Ошибки показываются у соответствующего поля на языке страницы; введённые данные не теряются (в т.ч. при «Tagasi»).
  - Заявка сохраняется в БД, письмо «Новая заявка — 123ABC» уходит с фото во вложении (reply-to = e-mail клиента). Если почта недоступна, заявка всё равно сохраняется, ошибка видна в админке.
  - Защита: honeypot + лимит 8 заявок / 10 мин с одного IP; фото хранятся в приватном storage и доступны только администраторам.
- **Блоки лендинга**: 4 шага выкупа → почему мы → карусель отзывов → KKK (аккордеон) → контакты + Google Maps (Meistri 20, Tallinn) → футер (4 колонки, tel:/mailto:). Плавающие кнопки WhatsApp/Telegram.
- **Админ-панель** `/admin` (на русском): Заявки (просмотр, фото, удаление, без статусов/CRM) · Отзывы · Вопросы и ответы · Страницы (ET/RU/EN, свой URL на каждом языке, автоматически попадают в футер) · Настройки (телефон, WhatsApp, Telegram, e-mail'ы, адрес/карта, код аналитики) · Администраторы.
- Юридические страницы *Kasutustingimused* и *Privaatsuspoliitika* — **черновики-заготовки**, помечены как Mustand; заменить финальным текстом в админке.

## Локальный запуск

```bash
composer install
cp .env.example .env            # DB_DATABASE=autostvabaks, DB_USERNAME/DB_PASSWORD
php artisan key:generate
php artisan migrate --seed      # создаст первого администратора и выведет пароль в консоль
php artisan serve --port=8010
```

Сайт: http://127.0.0.1:8010 · Админка: http://127.0.0.1:8010/admin

Пароль администратора задаётся через `ADMIN_EMAIL` / `ADMIN_PASSWORD` в `.env` до первого `db:seed`, иначе генерируется случайный. Нового администратора можно добавить в админке или командой `php artisan make:filament-user`.

Тесты (SQLite in-memory, боевая база не затрагивается): `php artisan test`.

## Где что менять

| Что | Где |
|---|---|
| Тексты интерфейса, шагов и преимуществ (черновые) | `lang/et/site.php`, `lang/ru/site.php`, `lang/en/site.php` |
| Цвета бренда | `public/assets/css/autostvabaks.css` → `:root` (`--bs-brand-2` и далее) |
| Логотип (сейчас текстовая заглушка) | `resources/views/site/partials/logo.blade.php` — один файл для шапки, мобильного меню и футера |
| Фон первого экрана | `public/assets/imgs/hero/hero.webp|jpg`, `hero-mobile.webp|jpg` |
| Лимиты фото, адрес для заявок по умолчанию | `config/autostvabaks.php` |
| Письмо о новой заявке | `app/Mail/NewLeadMail.php`, `resources/views/emails/new-lead*.blade.php` |
| Тема Carento | `public/assets/css/theme.css` (main.css темы без `@import`), используются только Bootstrap, Swiper и собственный JS `public/assets/js/autostvabaks.js` |

Отзывы, FAQ, страницы и контакты редактируются в админке.

## Тестовый сервер (reg.ru)

- https://autostvabaks.adme.ee — хостинг reg.ru (ispmanager), SSH-алиас `autostvabaks` в `~/.ssh/config`, каталог `~/www/autostvabaks.adme.ee` (document root → `public/`), PHP 8.3 (`/opt/php/8.3/bin/php`), MySQL `u1579543_autostvabaks`.
- Код приходит из GitHub `git@github.com:nikolashend/autostvabaks.git` через deploy-ключ (`~/.ssh/autostvabaks_deploy`, алиас `github.com-autostvabaks`).
- В `.env` сервера `SITE_NOINDEX=true` — тестовый домен закрыт от поисковиков (`robots.txt` + `noindex`). Почта — Mailtrap sandbox.
- Сертификат самоподписанный (как у других тестовых поддоменов); в браузере нужно подтвердить исключение.

Обновление после `git push`:

```bash
ssh autostvabaks "cd ~/www/autostvabaks.adme.ee && bash deploy.sh"
```

## Деплой (production, autostvabaks.ee)

1. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://autostvabaks.ee`, `SITE_NOINDEX=false`, доступы к БД, боевой **SMTP** вместо Mailtrap (`MAIL_FROM_ADDRESS=info@autostvabaks.ee`).
2. `composer install --no-dev --optimize-autoloader`, `php artisan migrate --force --seed`, `php artisan optimize`, `php artisan filament:assets` (всё это делает `deploy.sh`).
3. Document root веб-сервера → `public/`. PHP: `upload_max_filesize` и `post_max_size` не меньше 48M (10 фото × до 8 МБ до сжатия).
4. SPF/DKIM для отправителя `info@autostvabaks.ee`, чтобы письма с заявками не попадали в спам.

## Что ждём от клиента (не блокирует разработку)

- логотип (SVG) и фирменные цвета → `logo.blade.php` + `:root` в CSS;
- телефон, WhatsApp, Telegram → админка → Настройки;
- финальные тексты шагов, преимуществ, FAQ, отзывы, фотографии;
- финальные Kasutustingimused / Privaatsuspoliitika (сейчас черновики);
- интеграция с API регистра по номеру авто — отдельный этап после проверки доступности и условий;
- Telegram-уведомления, статусы лидов, напоминания — отдельный этап.
