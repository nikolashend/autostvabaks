# Autostvabaks

Лендинг сервиса выкупа автомобилей **autostvabaks.ee** (отдельный бренд; юридический оператор — Motion Motors OÜ, упоминается только в политике конфиденциальности).

Стек: Laravel 12 · PHP 8.2 · MySQL 8 · Filament 3 (админ-панель) · собственная дизайн-система `autostvabaks.css` на урезанном Bootstrap 5 (тема Carento больше не используется).

## Документация проекта (источник истины)

`docs/autostvabaks/` — **любая следующая задача начинается с `AUTOSTVABAKS_X999_MASTER_BASELINE_V1.md`** (архитектура, правила SEO/индексации, форма, админка, факты, бренд, аналитика). Рядом: аудит текущего состояния, ADR, владение поисковыми интентами, реестр фактов/утверждений, матрица маршрутов/индексации, карта UI/UX-пробелов, отчёты Stage 1 и Stage 2 и скриншоты baseline.

## Что реализовано

- **Лендинг на трёх языках** — `/` (эстонский, основной), `/ru`, `/en`. Ручной переключатель, без автоматического редиректа по языку браузера; у каждой версии свой URL для Google Ads, `hreflang`, `sitemap.xml`, JSON-LD (Organization + Brand + Service + FAQPage).
- **Двухэтапная форма заявки** прямо на первом экране:
  1. Registreerimisnumber · Telefoninumber · E-post (обязательно) · Soovitud hind (необязательно) · чекбокс согласия с условиями → **Alusta päringut** (проверка полей, заявка ещё не отправляется).
  2. Модальное окно «Sinu auto lisainfo»: Suhtluskeel (ET/RU/EN, по умолчанию = язык сайта) · Läbisõit · Lisainfo · фото (JPG/PNG/WEBP, превью, удаление, до 10 шт., сжатие в браузере до 1920px) → **Saada päring**.
  - Ошибки показываются у соответствующего поля на языке страницы; введённые данные не теряются (в т.ч. при «Tagasi»).
  - Заявка сохраняется в БД, письмо «Новая заявка — 123ABC» уходит с фото во вложении (reply-to = e-mail клиента). Если почта недоступна, заявка всё равно сохраняется, ошибка видна в админке.
  - Защита: honeypot + лимит 8 заявок / 10 мин с одного IP; фото хранятся в приватном storage и доступны только администраторам.
- **Блоки лендинга**: hero с формой и полосой фактов → 4 шага → почему мы (ответы на возражения) → что влияет на предложение → ответы: лизинг / повреждения / оплата → отзывы (только реальные; 1–3 сеткой, >3 — карусель) → KKK → локация + карта по клику → финальный CTA → футер. На телефонах — sticky-кнопка «Alusta päringut». Плавающие WhatsApp/Telegram появляются только при заполненных настройках.
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
Проверка перед публикацией: `php artisan site:release-check` (на боевом хосте блокирует noindex/debug/плейсхолдеры/черновики; `--strict` — тот же режим где угодно).

## Где что менять

| Что | Где |
|---|---|
| Тексты интерфейса, шагов и преимуществ (черновые) | `lang/et/site.php`, `lang/ru/site.php`, `lang/en/site.php` |
| Цвета бренда / токены | `public/assets/css/autostvabaks.css` → `:root` (`--asv-brand-primary`, `--asv-brand-dark`, …) и `config/autostvabaks.php` → `brand` (админка, theme-color) |
| Логотип | компонент `resources/views/components/asv-logo.blade.php` → `public/assets/imgs/brand/logo.svg` (светлый фон) / `logo-white.svg` (тёмный). Оригиналы Олега — `resources/brand/source/` (не редактировать) |
| Фон первого экрана (временный) | `public/assets/imgs/hero/hero.webp|jpg` (десктоп), `hero-mobile.webp|jpg` (телефоны, под формой). Бриф на замену — отчёт Stage 2 §13 |
| Шрифты | `public/assets/fonts/` — Urbanist (latin, latin-ext) + Manrope (кириллица), self-hosted |
| Bootstrap | `public/assets/css/vendors/bootstrap.min.css` — урезанная сборка; после добавления новых bootstrap-классов пересобрать: `node scripts/purge-bootstrap.cjs <полный bootstrap.min.css> <url …>` (см. шапку скрипта) |
| Лимиты фото, адрес для заявок по умолчанию | `config/autostvabaks.php` |
| Письмо о новой заявке | `app/Mail/NewLeadMail.php`, `resources/views/emails/new-lead*.blade.php` |
| Поведение страницы (форма, sticky CTA, карта, аналитика) | `public/assets/js/autostvabaks.js` (без jQuery; Bootstrap bundle + Swiper только при >3 отзывах) |

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

1. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://autostvabaks.ee`, `SITE_NOINDEX=false`, `STAGING_AUTH=` (пусто), доступы к БД, боевой **SMTP** вместо Mailtrap (`MAIL_FROM_ADDRESS=info@autostvabaks.ee`).
2. `composer install --no-dev --optimize-autoloader`, `php artisan migrate --force --seed`, `php artisan optimize`, `php artisan filament:assets` (всё это делает `deploy.sh`).
3. Document root веб-сервера → `public/`. PHP: `upload_max_filesize` и `post_max_size` не меньше 48M (10 фото × до 8 МБ до сжатия).
4. SPF/DKIM для отправителя `info@autostvabaks.ee`, чтобы письма с заявками не попадали в спам.

## Что ждём от клиента (не блокирует разработку)

- hero-фото (реальное авто/передача ключей) по брифу из отчёта Stage 2, фото локации Meistri 20;
- телефон, WhatsApp, Telegram → админка → Настройки;
- финальные тексты шагов, преимуществ, FAQ, отзывы, фотографии;
- финальные Kasutustingimused / Privaatsuspoliitika (сейчас черновики);
- интеграция с API регистра по номеру авто — отдельный этап после проверки доступности и условий;
- Telegram-уведомления, статусы лидов, напоминания — отдельный этап.
