/* Autostvabaks – site behaviour (no jQuery; Bootstrap + Swiper are the only libraries). */
(function () {
  'use strict';

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

  /* ------------------------------------------------------------------ *
   * Analytics contract (docs/autostvabaks/AUTOSTVABAKS_X999_MASTER_BASELINE_V1.md).
   * Events go to window.dataLayer only; nothing here may carry personal data
   * (no plate, phone, e-mail, message text, file names).
   * ------------------------------------------------------------------ */
  var ATTRIBUTION_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid'];
  var ATTRIBUTION_STORE = 'asv_attribution';

  function track(event, params) {
    var payload = { event: event };
    Object.keys(params || {}).forEach(function (k) { payload[k] = params[k]; });
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(payload);
  }
  window.asvTrack = track;

  // First-touch attribution: keep the campaign parameters of the landing URL for the
  // visit (sessionStorage), so a lead sent from a later page view is still attributed.
  function attribution() {
    var found = {};
    try {
      var stored = JSON.parse(sessionStorage.getItem(ATTRIBUTION_STORE) || 'null');
      if (stored) return stored;
    } catch (err) { /* private mode */ }
    try {
      var params = new URLSearchParams(window.location.search);
      ATTRIBUTION_KEYS.forEach(function (k) {
        var v = (params.get(k) || '').trim();
        if (v) found[k] = v.slice(0, 200);
      });
      if (Object.keys(found).length) sessionStorage.setItem(ATTRIBUTION_STORE, JSON.stringify(found));
    } catch (err) { /* ignore */ }
    return found;
  }
  attribution();

  /* ------------------------------------------------------------------ *
   * Header: compact / glass state after scrolling
   * ------------------------------------------------------------------ */
  var header = $('.asv-header');
  var onScroll = function () {
    if (header) header.classList.toggle('is-stuck', window.scrollY > 24);
    // lets the floating WhatsApp / Telegram buttons appear below the first screen on mobile
    document.body.classList.toggle('asv-scrolled', window.scrollY > 320);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ------------------------------------------------------------------ *
   * Mobile sticky CTA: visible only while the hero form is out of view and
   * the footer is not; CSS hides it while the modal / menu is open.
   * ------------------------------------------------------------------ */
  var sticky = $('[data-sticky-cta]');
  var stickyForm = $('#lead-step1');
  var stickyFooter = $('.asv-footer');
  if (sticky && stickyForm && 'IntersectionObserver' in window) {
    var formVisible = true;
    var footerVisible = false;
    var stickyLink = $('a', sticky);
    var updateSticky = function () {
      var show = !formVisible && !footerVisible;
      sticky.classList.toggle('is-visible', show);
      sticky.setAttribute('aria-hidden', show ? 'false' : 'true');
      if (stickyLink) stickyLink.tabIndex = show ? 0 : -1;
    };
    new IntersectionObserver(function (entries) {
      formVisible = entries[0].isIntersecting;
      updateSticky();
    }, { rootMargin: '-56px 0px 0px 0px', threshold: 0.05 }).observe(stickyForm);
    if (stickyFooter) {
      new IntersectionObserver(function (entries) {
        footerVisible = entries[0].isIntersecting;
        updateSticky();
      }, { threshold: 0.1 }).observe(stickyFooter);
    }
  }

  /* ------------------------------------------------------------------ *
   * Mobile menu
   * ------------------------------------------------------------------ */
  var menu = $('#asv-mobile-menu');
  var menuOpener = $('[data-menu-open]');

  function setMenu(open) {
    if (!menu) return;
    menu.classList.toggle('is-open', open);
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    document.body.classList.toggle('asv-lock', open);
    if (menuOpener) menuOpener.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      var close = $('.asv-mobile__close', menu);
      if (close) close.focus();
    } else if (menuOpener && document.activeElement && menu.contains(document.activeElement)) {
      menuOpener.focus();
    }
  }

  if (menu) {
    if (menuOpener) menuOpener.addEventListener('click', function () { setMenu(true); });
    $$('[data-menu-close]', menu).forEach(function (el) {
      el.addEventListener('click', function () { setMenu(false); });
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('is-open')) setMenu(false);
    });
  }

  /* ------------------------------------------------------------------ *
   * "Alusta päringut" buttons: scroll to the form and put the cursor in it
   * ------------------------------------------------------------------ */
  $$('[data-focus-form]').forEach(function (link) {
    link.addEventListener('click', function () {
      var zone = link.closest('section, header, footer');
      var location = link.getAttribute('data-cta-location') || (zone ? (zone.id || zone.getAttribute('aria-labelledby') || zone.tagName.toLowerCase()) : 'page');
      track('buyback_cta_click', { cta_location: location });
      var reg = $('#lead-reg');
      if (!reg) return; // inner page – the link simply navigates to the landing page
      setTimeout(function () { reg.focus({ preventScroll: true }); }, 450);
    });
  });

  /* ------------------------------------------------------------------ *
   * Scroll reveal
   * ------------------------------------------------------------------ */
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if ('IntersectionObserver' in window && !reduceMotion) {
    var revealTargets = $$('.asv-section__head, .asv-step, .asv-benefit, .asv-factor, .asv-answer, .asv-review, .asv-faq .accordion-item, .asv-location, .asv-map, .asv-final');
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -8% 0px' });

    revealTargets.forEach(function (el, i) {
      el.classList.add('asv-reveal');
      el.style.transitionDelay = (i % 4) * 70 + 'ms';
      observer.observe(el);
    });
  }

  /* ------------------------------------------------------------------ *
   * Contact clicks + FAQ opens (no personal data in the events)
   * ------------------------------------------------------------------ */
  $$('a[href^="tel:"]').forEach(function (a) { a.addEventListener('click', function () { track('buyback_phone_click'); }); });
  $$('a[href*="wa.me"]').forEach(function (a) { a.addEventListener('click', function () { track('buyback_whatsapp_click'); }); });
  $$('a[href*="t.me"]').forEach(function (a) { a.addEventListener('click', function () { track('buyback_telegram_click'); }); });
  $$('.asv-faq .accordion-button').forEach(function (btn, i) {
    btn.addEventListener('click', function () {
      if (btn.classList.contains('collapsed')) track('buyback_faq_open', { faq_index: i + 1 });
    });
  });

  /* ------------------------------------------------------------------ *
   * Map: the Google Maps iframe is loaded only when the visitor asks for it
   * ------------------------------------------------------------------ */
  $$('[data-map-load]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var box = btn.closest('.asv-map');
      if (!box) return;
      var iframe = document.createElement('iframe');
      iframe.src = box.getAttribute('data-map-src');
      iframe.title = box.getAttribute('data-map-title') || 'Map';
      iframe.setAttribute('referrerpolicy', 'no-referrer-when-downgrade');
      iframe.setAttribute('allowfullscreen', '');
      box.classList.add('is-loaded');
      box.replaceChild(iframe, btn);
      iframe.focus();
      track('buyback_map_load');
    });
  });

  /* ------------------------------------------------------------------ *
   * Reviews carousel – only rendered (and Swiper only loaded) with more
   * than three real reviews; 1–3 reviews are a plain grid.
   * ------------------------------------------------------------------ */
  function initReviews() {
    var slider = $('.asv-reviews__slider');
    if (!slider || typeof Swiper === 'undefined') return;

    new Swiper(slider, {
      slidesPerView: 1,
      spaceBetween: 20,
      watchOverflow: true,
      keyboard: { enabled: true, onlyInViewport: true },
      navigation: { prevEl: '.asv-reviews__nav--prev', nextEl: '.asv-reviews__nav--next' },
      pagination: { el: '.asv-reviews__dots', clickable: true },
      breakpoints: { 768: { slidesPerView: 2 }, 992: { slidesPerView: 3 } }
    });
  }

  /* ------------------------------------------------------------------ *
   * Lead form (step 1 in the hero, step 2 in the modal)
   * ------------------------------------------------------------------ */
  function initLeadForm() {
    var cfg = window.ASV;
    var step1 = $('#lead-step1');
    var step2 = $('#lead-step2');
    var modalEl = $('#lead-modal');
    if (!cfg || !step1 || !step2 || !modalEl || typeof bootstrap === 'undefined') return;

    var msg = cfg.messages || {};
    var modal = new bootstrap.Modal(modalEl);
    var success = $('[data-lead-success]', modalEl);
    var formError = $('[data-form-error]', step2);
    var submitBtn = $('[data-submit]', step2);
    var submitLabel = $('[data-submit-label]', step2);
    var submitSpinner = $('[data-submit-spinner]', step2);
    var photoInput = $('#lead-photos');
    var photoList = $('[data-photo-list]', step2);
    var photoBox = $('.asv-photos', step2);
    var STEP1_FIELDS = ['reg_number', 'phone', 'email', 'desired_price', 'terms'];
    var photos = []; // { file: File, url: objectURL }
    var sending = false;
    var sent = false;
    var started = false;
    var step1Submit = $('button[type="submit"]', step1);

    // First interaction with the form = funnel start (once per page view)
    step1.addEventListener('focusin', function () {
      if (started) return;
      started = true;
      track('buyback_form_start');
    });

    /* ----- helpers ----- */

    function fieldWrap(name) {
      return $('[data-field="' + name + '"]', step1) || $('[data-field="' + name + '"]', step2);
    }

    function setError(name, text) {
      var wrap = fieldWrap(name);
      if (!wrap) return;
      var box = $('.asv-error', wrap);
      wrap.classList.toggle('is-invalid', !!text);
      if (box) box.textContent = text || '';
      $$('input, textarea', wrap).forEach(function (el) {
        if (text) el.setAttribute('aria-invalid', 'true'); else el.removeAttribute('aria-invalid');
      });
    }

    function clearErrors(form) {
      $$('[data-field]', form).forEach(function (wrap) { setError(wrap.getAttribute('data-field'), ''); });
    }

    function digits(value) { return String(value || '').replace(/\D/g, ''); }

    function groupThousands(value) {
      return digits(value).replace(/^0+(?=\d)/, '').replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    function normalizeReg(value) {
      return String(value || '').replace(/[\s-]+/g, '').toUpperCase();
    }

    /* ----- input formatting ----- */

    var reg = $('#lead-reg');
    reg.addEventListener('input', function () {
      var pos = reg.selectionStart;
      var before = reg.value;
      reg.value = before.toUpperCase().replace(/[^0-9A-ZÕÄÖÜŠŽ]/g, '');
      if (reg.value.length === before.length && pos !== null) reg.setSelectionRange(pos, pos);
    });

    $$('[data-thousands]').forEach(function (input) {
      input.addEventListener('input', function () { input.value = groupThousands(input.value); });
    });

    // An error disappears as soon as the user starts fixing the field.
    $$('[data-field]').forEach(function (wrap) {
      var name = wrap.getAttribute('data-field');
      $$('input, textarea', wrap).forEach(function (el) {
        el.addEventListener(el.type === 'checkbox' || el.type === 'radio' ? 'change' : 'input', function () {
          if (wrap.classList.contains('is-invalid')) setError(name, '');
        });
      });
    });

    /* ----- step 1 ----- */

    function validateStep1() {
      var errors = {};
      var regValue = normalizeReg(step1.elements.reg_number.value);
      var phone = step1.elements.phone.value.trim();
      var phoneDigits = digits(phone);
      var email = step1.elements.email.value.trim();
      var price = step1.elements.desired_price.value.trim();

      if (!regValue) errors.reg_number = msg.reg_required;
      else if (!/^[0-9A-ZÕÄÖÜŠŽ]{2,10}$/.test(regValue)) errors.reg_number = msg.reg_invalid;

      if (!phone) errors.phone = msg.phone_required;
      else if (!/^\+?[\d\s()\-]{5,}$/.test(phone) || phoneDigits.length < 5 || phoneDigits.length > 15) errors.phone = msg.phone_invalid;

      if (!email) errors.email = msg.email_required;
      else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) errors.email = msg.email_invalid;

      if (price && (!/^\d[\d\s]*$/.test(price) || Number(digits(price)) < 1)) errors.desired_price = msg.price_invalid;

      if (!step1.elements.terms.checked) errors.terms = msg.terms;

      return errors;
    }

    function showStep1Errors(errors) {
      var first = null;
      STEP1_FIELDS.forEach(function (name) {
        setError(name, errors[name] || '');
        if (errors[name] && !first) first = name;
      });
      if (first) {
        var wrap = fieldWrap(first);
        var input = $('input', wrap);
        if (input) input.focus();
        wrap.scrollIntoView({ block: 'center', behavior: reduceMotion ? 'auto' : 'smooth' });
      }
      return !first;
    }

    // "Alusta päringut": validate and open step 2 – nothing is sent yet.
    step1.addEventListener('submit', function (e) {
      e.preventDefault();
      var errors = validateStep1();
      if (!showStep1Errors(errors)) {
        track('buyback_form_error', { step: 1, fields: Object.keys(errors).join(',') });
        return;
      }

      if (sent) resetAll(false); // a new request after a successful one
      $('[data-lead-summary]', modalEl).textContent = normalizeReg(step1.elements.reg_number.value);
      track('buyback_form_step1_complete');
      modal.show();
    });

    // tracked when the modal starts opening (not after the transition), so step2_view always
    // precedes photo_add / submit in the dataLayer even for very fast users or automation
    modalEl.addEventListener('show.bs.modal', function () {
      if (!sent) track('buyback_form_step2_view');
    });

    modalEl.addEventListener('shown.bs.modal', function () {
      var target = sent ? success : $('#lead-mileage');
      if (target) target.focus({ preventScroll: true });
    });

    // "Back" / close keeps everything the user typed; after success the forms start clean.
    // Focus returns to the button that opened the modal (Bootstrap does not restore it).
    modalEl.addEventListener('hidden.bs.modal', function () {
      if (sent) resetAll(true);
      if (step1Submit) step1Submit.focus({ preventScroll: true });
    });

    /* ----- photos ----- */

    function renderPhotos() {
      photoList.innerHTML = '';
      photos.forEach(function (photo, index) {
        var li = document.createElement('li');
        li.className = 'asv-photos__item';

        var img = document.createElement('img');
        img.src = photo.url;
        img.alt = (msg.photo_alt || 'Photo :n').replace(':n', index + 1);

        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'asv-photos__remove';
        remove.setAttribute('aria-label', (msg.photo_remove || 'Remove') + ' ' + (index + 1));
        remove.innerHTML = '&times;';
        remove.addEventListener('click', function () {
          URL.revokeObjectURL(photo.url);
          photos.splice(photos.indexOf(photo), 1);
          setError('photos', '');
          renderPhotos();
        });

        li.appendChild(img);
        li.appendChild(remove);
        photoList.appendChild(li);
      });
      photoBox.classList.toggle('is-full', photos.length >= cfg.maxPhotos);
    }

    // Downscale in the browser: phone photos are 4–12 MB each, which makes uploads slow
    // and the notification e-mail too heavy. Falls back to the original file on any problem.
    function shrink(file) {
      var max = cfg.resizePx || 1920;
      return new Promise(function (resolve) {
        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
          URL.revokeObjectURL(url);
          var scale = Math.min(1, max / Math.max(img.naturalWidth, img.naturalHeight));
          if (scale === 1 && file.size < 1.5 * 1024 * 1024) return resolve(file);
          try {
            var canvas = document.createElement('canvas');
            canvas.width = Math.round(img.naturalWidth * scale);
            canvas.height = Math.round(img.naturalHeight * scale);
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(function (blob) {
              if (!blob || blob.size >= file.size) return resolve(file);
              var name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
              resolve(new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() }));
            }, 'image/jpeg', 0.85);
          } catch (err) {
            resolve(file);
          }
        };
        img.onerror = function () { URL.revokeObjectURL(url); resolve(file); };
        img.src = url;
      });
    }

    function addPhotos(chosen) {
      if (!chosen.length) return;

      var error = '';
      var allowed = chosen.filter(function (file) {
        var ok = /^image\/(jpeg|png|webp)$/.test(file.type) || /\.(jpe?g|png|webp)$/i.test(file.name);
        if (!ok) error = msg.photo_type;
        return ok;
      });

      var room = cfg.maxPhotos - photos.length;
      if (allowed.length > room) {
        allowed = allowed.slice(0, Math.max(room, 0));
        error = msg.photos_max;
      }

      Promise.all(allowed.map(shrink)).then(function (files) {
        files.forEach(function (file) {
          if (file.size > cfg.maxPhotoMb * 1024 * 1024) { error = msg.photo_size; return; }
          photos.push({ file: file, url: URL.createObjectURL(file) });
        });
        if (files.length) track('buyback_photo_add', { photo_count: photos.length });
        setError('photos', error);
        renderPhotos();
      });
    }

    photoInput.addEventListener('change', function () {
      var chosen = Array.prototype.slice.call(photoInput.files || []);
      photoInput.value = ''; // allow picking the same file again after removing it
      addPhotos(chosen);
    });

    // Drag & drop onto the photo zone (desktop); the file dialog stays the primary path.
    var dropZone = $('.asv-photos__add', step2);
    if (dropZone) {
      ['dragenter', 'dragover'].forEach(function (type) {
        dropZone.addEventListener(type, function (e) { e.preventDefault(); dropZone.classList.add('is-dragover'); });
      });
      ['dragleave', 'drop'].forEach(function (type) {
        dropZone.addEventListener(type, function () { dropZone.classList.remove('is-dragover'); });
      });
      dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        addPhotos(Array.prototype.slice.call((e.dataTransfer && e.dataTransfer.files) || []));
      });
    }

    /* ----- step 2: send ----- */

    function setSending(state) {
      sending = state;
      submitBtn.disabled = state;
      submitLabel.textContent = state ? msg.sending : msg.submit;
      submitSpinner.classList.toggle('d-none', !state);
      modalEl.setAttribute('aria-busy', state ? 'true' : 'false');
    }

    function resetAll(andForms) {
      sent = false;
      success.classList.add('d-none');
      step2.classList.remove('d-none');
      if (!andForms) return;
      var language = (step2.querySelector('input[name="language"][value="' + cfg.locale + '"]') || {});
      step1.reset();
      step2.reset();
      language.checked = true;
      photos.forEach(function (p) { URL.revokeObjectURL(p.url); });
      photos = [];
      renderPhotos();
      clearErrors(step1);
      clearErrors(step2);
      formError.textContent = '';
    }

    function applyServerErrors(errors) {
      var step1Errors = {};
      var hasStep1 = false;
      var firstStep2 = null;

      Object.keys(errors).forEach(function (key) {
        var text = [].concat(errors[key])[0];
        var name = key.split('.')[0];
        if (STEP1_FIELDS.indexOf(name) !== -1) {
          step1Errors[name] = text;
          hasStep1 = true;
        } else if (fieldWrap(name)) {
          setError(name, text);
          firstStep2 = firstStep2 || name;
        } else {
          formError.textContent = text;
        }
      });

      if (hasStep1) {
        // The problem is in the first form: go back to it, keeping everything typed in.
        modalEl.addEventListener('hidden.bs.modal', function () { showStep1Errors(step1Errors); }, { once: true });
        modal.hide();
      } else if (firstStep2) {
        var wrap = fieldWrap(firstStep2);
        var input = $('input:not([type=file]), textarea', wrap);
        if (input) input.focus();
        wrap.scrollIntoView({ block: 'center' });
      }
    }

    step2.addEventListener('submit', function (e) {
      e.preventDefault();
      if (sending) return;

      clearErrors(step2);
      formError.textContent = '';

      var mileage = step2.elements.mileage.value.trim();
      if (mileage && !/^\d[\d\s]*$/.test(mileage)) {
        setError('mileage', msg.mileage_invalid);
        step2.elements.mileage.focus();
        track('buyback_form_error', { step: 2, fields: 'mileage' });
        return;
      }

      var data = new FormData();
      data.append('reg_number', normalizeReg(step1.elements.reg_number.value));
      data.append('phone', step1.elements.phone.value.trim());
      data.append('email', step1.elements.email.value.trim());
      data.append('desired_price', digits(step1.elements.desired_price.value));
      data.append('terms', step1.elements.terms.checked ? '1' : '');
      data.append('language', (step2.querySelector('input[name="language"]:checked') || {}).value || cfg.locale);
      data.append('mileage', digits(mileage));
      data.append('info', step2.elements.info.value);
      data.append('website', step2.elements.website.value);
      data.append('referrer', document.referrer || '');
      var attr = attribution();
      Object.keys(attr).forEach(function (k) { data.append(k, attr[k]); });
      photos.forEach(function (photo) { data.append('photos[]', photo.file, photo.file.name); });

      setSending(true);
      track('buyback_form_submit', { photo_count: photos.length, has_price: !!digits(step1.elements.desired_price.value) });

      fetch(cfg.endpoint, {
        method: 'POST',
        body: data,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (response) {
          return response.json().catch(function () { return {}; }).then(function (body) {
            return { status: response.status, body: body };
          });
        })
        .then(function (result) {
          setSending(false);

          if (result.status >= 200 && result.status < 300 && result.body.ok) {
            sent = true;
            if (result.body.title) $('[data-success-title]', success).textContent = result.body.title;
            if (result.body.message) $('[data-success-text]', success).textContent = result.body.message;
            step2.classList.add('d-none');
            success.classList.remove('d-none');
            success.focus();
            track('buyback_form_success');
            return;
          }

          track('buyback_form_error', { step: 2, status: result.status, fields: result.body.errors ? Object.keys(result.body.errors).join(',') : '' });
          if (result.status === 422 && result.body.errors) return applyServerErrors(result.body.errors);
          if (result.status === 413) return setError('photos', msg.photo_size);
          if (result.status === 429) { formError.textContent = result.body.message || msg.throttle; return; }

          formError.textContent = msg.generic;
        })
        .catch(function () {
          setSending(false);
          track('buyback_form_error', { step: 2, status: 0 });
          formError.textContent = msg.generic;
        });
    });
  }

  /* ------------------------------------------------------------------ */

  function ready(fn) {
    if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    initReviews();
    initLeadForm();
  });
})();
