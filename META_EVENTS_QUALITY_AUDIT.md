# Audit Meta Events Quality — CoineDev

**Data audit:** 2026-05-07
**Scope:** Event Match Quality (EMQ), Conversions API server-side, eventi & mapping, custom data, tracking accettazione/rifiuto cookie.
**Stack rilevante:** Laravel 12, `combindma/laravel-facebook-pixel` v5.1, `facebook/php-business-sdk`, Inertia + React 19, soluzione cookie-consent custom (no vendor SDK).

---

## 1. Executive summary

L'integrazione Meta è **funzionante e relativamente matura** (Pixel + CAPI già attivi, dedup via `event_id`, gating consenso lato client e server, hashing PII delegato all'SDK). Le aree di miglioramento sono però significative e quasi tutte azionabili senza riscritture importanti:

| Area | Maturità | Priorità intervento |
|---|---|---|
| EMQ — user_data inviati | **Bassa** (solo email/phone/IP/UA/fbp/fbc) | **Alta** |
| CAPI robustezza (retry/queue, idempotency) | **Bassa** (chiamate sincrone, log-only su errore) | **Alta** |
| Eventi standard & dedup | **Media** (UUID OK, ma dedup parziale e custom event non standard) | **Media** |
| Custom data (value, content_*, predicted_ltv) | **Assente** (CustomData vuoto) | **Alta** |
| Tracking consenso cookie (analytics interno) | **Assente** (nessuna persistenza) | **Media** |
| Consent Mode v2 / Granular Consent Meta | **Parziale** (solo GA gtag consent) | **Media** |

I tre interventi a maggior ROI sull'EMQ — invio di `fn`/`ln`/`ct`/`zp`/`country`/`external_id`, queue + retry per CAPI, e custom data sui Lead — possono portare lo score EMQ da una fascia tipica `4–6/10` a `8–10/10` su Lead e CompleteRegistration.

---

## 2. Stato attuale (sintesi tecnica)

### 2.1 Pixel client-side
- Script `fbevents.js` caricato in `resources/views/vendor/meta-pixel/head.blade.php:18`, gated da `hasMarketingConsent()` in `resources/views/app.blade.php:39`.
- Pixel ID da `META_PIXEL_ID` (`config/meta-pixel.php:7`); abilitazione da `META_PIXEL_ENABLED`.
- Eventi triggerati lato browser:
  - `PageView` — `resources/js/hooks/useMetaPixel.ts:65` (Inertia navigation listener).
  - `Lead` — `resources/js/components/contactForm.tsx:28`, `resources/js/components/sections/healthCheckQuiz.tsx:235`.
  - `CompleteRegistration` — `resources/js/components/sections/healthCheckQuiz.tsx:265`.
  - `startQuiz` (custom) — `resources/js/components/sections/healthCheckQuiz.tsx:180`.
- Advanced Matching automatico abilitato (`META_PIXEL_ADVANCED_MATCHING_ENABLED=true`).

### 2.2 Conversions API server-side
- Wrapper: facade `Combindma\FacebookPixel\Facades\MetaPixel`.
- Mirror server-side dei quattro eventi sopra in:
  - `app/Http/Middleware/TrackMetaPageView.php:40` (PageView per ogni request non-API).
  - `app/Services/LeadService.php:36-40` (Lead).
  - `app/Http/Controllers/HealthCheckQuizController.php:81-86` (CompleteRegistration).
  - `app/Http/Controllers/HealthCheckQuizController.php:28` (startQuiz).
- User data raccolti in `app/Services/Meta/MetaPixelUserDataFactory.php`: `em`, `ph`, `client_ip_address`, `client_user_agent`, `fbp`, `fbc`.
- Hashing SHA-256 delegato al Facebook Business SDK (commento esplicito a `MetaPixelUserDataFactory.php:17-18`).
- `event_id` UUID generato lato browser e ripassato server-side via `metaEventId` nel payload form (`ContactFormRequest.php:47`, `HealthCheckQuizRequest.php:50`).
- `test_event_code` configurabile (`config/meta-pixel.php:45-47`).

### 2.3 Form di lead
- Contact form: `POST /contact` → `app/Http/Controllers/ContactFormController.php:12` → `LeadService::createAndTrack`.
- Health Check Quiz: `POST /health-check/start`, `POST /health-check`, `PATCH /health-check`.
- Validazione email rafforzata: `indisposable` rule + `app/Rules/NotFakeEmail.php` (blocklist da `config/lead-validation.php`).

### 2.4 Cookie consent
- Soluzione custom basata su cookie `cookie_consent` JSON (`{necessary, marketing, analytics}`).
- Banner React in `resources/js/components/cookieConsentBanner.tsx`, helper server in `app/Helpers/CookieConsent.php`.
- **Non c'è persistenza** delle scelte di consenso (no DB, no log strutturato, no dashboard Filament).
- Google Consent Mode v2 implementato a livello `gtag('consent', ...)` in `resources/views/app.blade.php:32-46`.
- Nessuna integrazione equivalente per Meta (Granular Consent / Limited Data Use).

---

## 3. Event Match Quality — gap analysis e raccomandazioni

L'EMQ score (0–10) di Meta dipende dalla quantità e qualità dei parametri `user_data` inviati. La libreria oggi invia solo i campi base. Sotto i campi mancanti **già raccolti dal sito** o **facilmente derivabili**, ordinati per impatto.

### 3.1 Campi user_data attualmente NON inviati

| Campo Meta | Campo nel form | File sorgente | Effort | Impatto EMQ |
|---|---|---|---|---|
| `fn` (first_name, hashed) | `firstName` | `ContactFormRequest.php:39`, `HealthCheckQuizRequest.php:39` | XS | **Alto** |
| `ln` (last_name, hashed) | `lastName` | idem | XS | **Alto** |
| `external_id` | UUID stabile per visitor (cookie own) | da generare | S | **Alto** |
| `ct` (city, hashed) | non raccolto, derivabile da IP geo-IP | nuovo | M | Medio |
| `country` (ISO-2, hashed) | derivabile da `Accept-Language` o GeoIP | nuovo | S | Medio |
| `zp` (postal code) | non raccolto (servirebbe campo opt-in) | nuovo | M | Medio |
| `db` (date of birth) | non applicabile a B2B | — | — | — |
| `ge` (gender) | non applicabile | — | — | — |

**Interventi consigliati:**

1. **Estendi `MetaPixelUserDataFactory::make()`** per accettare e popolare `firstName`, `lastName`, `externalId`, `city`, `country`, `zip`. La libreria `facebook/php-business-sdk` espone `setFirstName`, `setLastName`, `setExternalId`, `setCity`, `setCountryCode`, `setZipCode` — l'hashing rimane delegato al SDK.
2. **Passa `firstName` e `lastName`** dal `LeadService` al factory (oggi vengono solo concatenati nel campo `name` del `Lead` model: `app/Services/LeadService.php` — verifica linea attuale).
3. **Adotta un `external_id` persistente per visitor** — un UUID v4 salvato in cookie first-party HttpOnly (`coine_uid`, durata 24 mesi, set sia in middleware Laravel sia letto lato browser per coerenza Pixel/CAPI). Questo è il singolo parametro a maggior incremento EMQ secondo i benchmark Meta.
4. **Geo-arricchimento opzionale**: integra una libreria GeoIP (`geoip2/geoip2`) o un servizio (Cloudflare passa `CF-IPCountry` se proxato) per popolare `country` e `ct` quando assenti dal form. **Decisione da discutere prima** (CLAUDE.md richiede approvazione su nuove dipendenze).
5. **Normalizzazione PII prima dell'hash**: la libreria SDK fa hashing ma non sempre normalizza. Aggiungi normalizzazione esplicita in factory:
   - email: `strtolower(trim($email))` — ✅ già fatto a `MetaPixelUserDataFactory.php:29`.
   - phone: solo cifre, formato E.164 senza `+` — verifica copertura italiana (rimozione `0` iniziale, prefisso `39`).
   - first/last name: `mb_strtolower(trim(...))`, rimozione caratteri non alfabetici, rimozione accenti.

### 3.2 Quality del payload — checklist Meta

| Best practice | Stato | File |
|---|---|---|
| `event_time` in epoch UTC entro 7 giorni | ✅ delegato a SDK | — |
| `event_source_url` valorizzato | ❓ da verificare nel wrapper | `MetaPixel::send()` |
| `action_source: 'website'` | ❓ da verificare | idem |
| `client_user_agent` valorizzato | ✅ | `MetaPixelUserDataFactory.php:24` |
| `fbp` e `fbc` da cookie | ✅ | idem:25-26 |
| `event_id` univoco per evento browser+server | ✅ UUID condiviso | `useMetaPixel.ts`, `LeadService.php` |
| Event Deduplication window 48h | ✅ implicita (UUID stabile) | — |

**Azione:** apri il debug del wrapper combindma e logga il payload completo inviato a `https://graph.facebook.com/.../events` per un evento Lead di test, verifica che `action_source` e `event_source_url` siano popolati. Se non lo sono, vanno aggiunti esplicitamente prima della chiamata `MetaPixel::send()`.

---

## 4. Conversions API server-side — robustezza

Lo stato attuale ha **chiamate sincrone, nessun retry, errori solo loggati** — il che significa che ogni hiccup di rete o rate-limit Meta produce un evento perso, abbassando direttamente la copertura CAPI e quindi l'EMQ aggregato dell'account.

### 4.1 Problemi rilevati

1. **Sincronia HTTP nella request utente**
   - `LeadService::createAndTrack` chiama `MetaPixel::send()` nel ciclo della request (`app/Services/LeadService.php:63`).
   - Latenza Graph API tipica: 200–800 ms, con picchi >2 s.
   - Impatto: TTLB della submit form aumenta linearmente con il numero di servizi (Meta + GA + LinkedIn + Klaviyo).
2. **Nessun retry su failure transitorie**
   - `try/catch` con `Log::error()` in `TrackMetaPageView.php:41-43` e nel `LeadService` — l'evento viene perso definitivamente.
3. **Nessuna idempotency layer**
   - Un retry naive duplicherebbe l'evento. La dedup Meta funziona solo se `event_id` è invariato → serve store dell'event_id originale.
4. **Middleware `TrackMetaPageView` invia PageView server per OGNI request HTML**
   - Conferma con `bootstrap/app.php` quali request entrano nel middleware. Probabile rumore: PageView duplicati con browser, asset prefetch, navigazioni Inertia che non corrispondono a vere pagine, bot crawler.
   - Costo: chiamata HTTP CAPI per ogni request = degrado throughput + rumore in Events Manager.

### 4.2 Raccomandazioni

| Intervento | Priorità | Effort |
|---|---|---|
| **Estrai chiamate CAPI in job `ShouldQueue`** (`SendMetaCapiEvent`) | Alta | S |
| **Retry policy con backoff** (`$tries = 3`, `backoff() = [10, 30, 120]`) | Alta | XS (dentro al job) |
| **Deduplica via `event_id`** già presente nel payload — niente da fare se l'UUID è stabile | Alta | — |
| **Persisti gli event payload in tabella `meta_capi_events`** prima dell'enqueue, marca `sent_at`/`failed_at`/`error` dopo. Permette replay e dashboard. | Media | M |
| **Disabilita `TrackMetaPageView` server-side per i bot e per le navigazioni Inertia** (si affidi al solo Pixel client) | Alta | S |
| **Limita la frequenza PageView server** a navigazioni reali (escludi prefetch, partial reload Inertia) | Media | S |
| **Configura `test_event_code` solo se `META_TEST_MODE_ENABLED=true`** in non-production. Verifica che in prod sia sempre off (rischio: eventi taggati come test). | Alta | XS — già implementato, da verificare |

**Esempio job stub** (da definire in fase di implementazione, NON è un design finalizzato):

```php
final class SendMetaCapiEvent implements ShouldQueue
{
    public int $tries = 3;
    public array $backoff = [10, 30, 120];

    public function __construct(
        public string $eventName,
        public string $eventId,
        public array $userData,
        public array $customData,
        public ?string $eventSourceUrl,
    ) {}

    public function handle(): void
    {
        MetaPixel::send($this->eventName, $this->eventId, $this->userData, $this->customData);
    }
}
```

---

## 5. Eventi tracciati e mapping con eventi standard Meta

### 5.1 Mapping attuale

| Azione utente | Evento Pixel | Evento CAPI | Standard Meta? |
|---|---|---|---|
| Page load | PageView | PageView | ✅ standard |
| Submit Contact form | Lead | Lead | ✅ standard |
| Submit Quiz step 1 (lead capture) | Lead | Lead | ✅ standard ma **possibile duplicazione semantica** con Contact form |
| Quiz step 2 (PATCH complete) | CompleteRegistration | CompleteRegistration | ✅ standard |
| Quiz start (CTA click) | startQuiz | startQuiz | ⚠️ custom event (non standard) |

### 5.2 Problemi e raccomandazioni

1. **`startQuiz` come custom event**: corretto in sé (Meta supporta custom events), ma non rientra nei segnali ottimizzabili nelle campagne. Se l'inizio quiz non è un KPI per le campagne, considera di **rimuoverlo o di mappare su `ViewContent`** con `content_name='health-check-quiz'` — ottimizzabile come evento standard.
2. **Quiz step 1 e Contact form entrambi `Lead`**: corretto se entrambi sono lead generation puri. Differenzia tramite `content_name` / `content_category` (vedi §6).
3. **`CompleteRegistration` per quiz completion**: semanticamente discutibile — quel passo è il completamento del profilo + invio note libere, non una registrazione utente. **Alternativa:** `SubmitApplication` (più aderente al funnel "candidatura/audit") oppure `CompleteRegistration` con `content_name='health-check-complete'`. Da decidere con il team marketing in base alle campagne.
4. **Eventi mancanti che potrebbero essere utili:**
   - `ViewContent` su pagine progetti / blog post / case study (segnale di interesse top-of-funnel).
   - `Schedule` se viene aggiunto un flusso di booking call.
   - `Subscribe` per la newsletter standalone (oggi `newsletter_opt_in` è solo un flag su Lead).
   - `InitiateCheckout` non applicabile (no e-commerce).

### 5.3 Deduplicazione — verifica copertura

Il `metaEventId` è generato lato browser, passato come hidden field e ripreso server-side. Coperti correttamente:
- ✅ Contact form (un singolo `metaEventId` per submit).
- ✅ Quiz store (un singolo `metaEventId`).
- ✅ Quiz complete.

**Edge case da verificare:**
- PageView server vs client: usano lo **stesso event_id**? Se generato indipendentemente nel middleware, si avrà **doppio conteggio** PageView.
  - Verifica `app/Http/Middleware/TrackMetaPageView.php:40` — se l'event_id non viene passato dal client (es. via header `X-Meta-Event-Id` o flash session), Meta li tratta come due PageView distinti. **Questa è la causa più frequente di EMQ basso aggregato sul Pixel ID.**

---

## 6. Custom data — value, content_*, predicted_ltv

### 6.1 Stato attuale
`CustomData` è istanziato vuoto in `LeadService.php:66` e `TrackMetaPageView.php:40`. Tutti gli eventi arrivano a Meta **senza valore monetario, senza categoria, senza identificativi di contenuto**.

### 6.2 Cosa popolare

| Campo | Valore suggerito (Lead - Contact form) | Valore suggerito (Lead - Quiz) | Valore suggerito (CompleteRegistration) |
|---|---|---|---|
| `currency` | `'EUR'` | `'EUR'` | `'EUR'` |
| `value` | LTV medio cliente / probabilità conversione (es. `50.00`) | idem | LTV pieno (es. `500.00`) |
| `content_name` | `'contact-form'` | `'health-check-quiz'` | `'health-check-complete'` |
| `content_category` | `'lead'` | `'lead-quiz'` | `'application'` |
| `content_ids` | `['contact']` | `['health-check']` | idem |
| `predicted_ltv` | (opzionale, se hai modello dati) | idem | idem |

**Nota strategica sul `value`:** Meta usa `value` per ottimizzare le campagne **Value-Based** (Advantage+ Audience, ROAS). Anche un valore stimato fisso (lead score × LTV medio) è infinitamente meglio di nessun valore — perché abilita `value optimization`.

**Decisione da prendere prima di implementare:**
1. Quale valore usare per i Lead? (LTV medio cliente CoineDev / tasso conversione lead→cliente). Domanda da fare all'utente.
2. Differenziare per provenienza? (es. quiz score alto = value più alto).
3. Inviare `predicted_ltv` separato dal `value`?

### 6.3 Implementazione consigliata

Aggiungi un metodo statico `MetaCustomDataFactory::forLead(string $source, ?float $score = null): CustomData` che incapsula la logica di mapping e tieni i `value` configurabili in `config/meta-pixel.php`:

```php
'lead_values' => [
    'contact-form' => 50.0,
    'health-check-quiz' => 75.0,
    'health-check-complete' => 500.0,
],
```

---

## 7. Tracking accettazione/rifiuto cookie

**Stato attuale:** completamente assente lato persistenza. Le scelte vivono solo in un cookie client `cookie_consent`. Non c'è modo di rispondere a domande tipo "qual è il tasso di accettazione cookie marketing?", "quanti utenti rifiutano?", "qual è il trend mensile?".

### 7.1 Obiettivi

1. Quantificare il tasso di accept/reject per ciascuna categoria (necessary, marketing, analytics).
2. Distinguere tra "Accetta tutti", "Solo necessari", "Personalizzato" e "Nessuna scelta" (banner ignorato).
3. Avere un audit log delle scelte (compliance GDPR — onere di prova del consenso).
4. Esporre metriche in dashboard (Filament).

### 7.2 Modello dati proposto

Tabella `cookie_consents`:

| Colonna | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `consent_id` | uuid | identificativo univoco della scelta (cookie value) |
| `external_id` | uuid nullable | UID visitor stabile (vedi §3.1) — per join cross-tabella |
| `necessary` | boolean | sempre true |
| `marketing` | boolean | |
| `analytics` | boolean | |
| `choice_type` | enum | `accept_all`, `reject_all`, `custom`, `update` |
| `ip_hash` | char(64) | SHA-256 IP (compliance: minimizzazione dati) |
| `user_agent` | string | utile per debug, considera hash |
| `referer` | string nullable | |
| `path` | string | URL su cui è stato registrato il consenso |
| `version` | string | versione policy attiva (incremento ad ogni rimaneggiamento testo banner) |
| `created_at` | timestamp | |

**Endpoint:** `POST /cookie-consent` chiamato dal banner React quando l'utente conferma una scelta. Idempotente per `consent_id` (UPSERT).

**Vantaggi compliance:**
- Onere di prova del consenso GDPR Art. 7(1) soddisfatto.
- Versioning permette di sapere a quale testo l'utente ha acconsentito.

### 7.3 Tasso di acceptance — KPI suggerite

Espone in Filament una resource `CookieConsentReport` con:
- Conta per categoria nelle ultime 7d / 30d / 90d.
- Tasso accept/reject per categoria.
- Distribuzione `choice_type`.
- Trend giornaliero (chart).
- Filtro per `path` (varia il consent rate per landing?).

### 7.4 Integrazione con Meta — Limited Data Use & Granular Consent

Quando un utente **rifiuta marketing**, oggi il Pixel non viene caricato lato client e il CAPI non viene chiamato lato server. Corretto.

**Miglioramento: implementa Granular Consent per il Pixel** anche per i pochi eventi che potresti voler mantenere (PageView aggregato, anonymous). Meta supporta:
```js
fbq('consent', 'revoke');  // utente rifiuta
fbq('consent', 'grant');   // utente accetta
```
Già usi gating in app.blade.php (carichi solo se accetta), che è approccio corretto e più conservativo. Considera però il **`data_processing_options: ['LDU']`** sul payload CAPI per utenti con IP italiano in caso di richieste future di Limited Data Use (oggi GDPR-correct senza, ma utile sapere che è disponibile).

### 7.5 Cross-domain identity con `external_id`

Se generi `external_id` come UUID first-party (vedi §3.1), salvalo nella stessa cookie del consent. Quando l'utente accetta, alla prima request server con consenso, propaga l'ID anche al CAPI come `external_id`. Quando rifiuta, NON inviare nulla (rispetto preferenza).

---

## 8. Microsoft Clarity, GA4, LinkedIn — note correlate

Non sono oggetto primario dell'audit, ma intercettano la stessa pipeline:

- **GA4 server-side via Measurement Protocol** in `app/Services/GoogleAnalytics/GoogleAnalyticsService.php` — già ottimo. Aggiungi `user_id` o `external_id` come `client_id` deterministico per cross-device.
- **Clarity** già correttamente gated da `analytics` consent — non è critico per EMQ Meta, ma utile per debug funnel.
- **LinkedIn Conversions API** — applica gli stessi principi (queue, retry, custom data). Out of scope per questo audit.

---

## 9. Roadmap implementativa proposta

Suggerimento di ordine, non obbligatorio. Ogni step è atomico, deployabile e testabile in isolamento.

### Sprint 1 — EMQ low-hanging fruit (1–2 giorni)
- [ ] Estendi `MetaPixelUserDataFactory` con `fn`, `ln`, `external_id`.
- [ ] Genera `external_id` (UUID first-party) in middleware, salvalo in cookie 24 mesi.
- [ ] Passa `firstName`/`lastName` dal `LeadService` al factory.
- [ ] Verifica & forza `action_source: 'website'` e `event_source_url` nel payload CAPI.
- [ ] Test: usa Meta Events Manager → Test Events per verificare che EMQ aumenti.

### Sprint 2 — Custom data (1 giorno)
- [ ] Crea `MetaCustomDataFactory` con preset `forLead`, `forCompleteRegistration`, `forViewContent`.
- [ ] Aggiungi `lead_values` in `config/meta-pixel.php`.
- [ ] Popola `CustomData` in `LeadService` e `HealthCheckQuizController::complete`.

### Sprint 3 — Robustezza CAPI (2–3 giorni)
- [ ] Crea job `SendMetaCapiEvent implements ShouldQueue` con retry e backoff.
- [ ] Tabella `meta_capi_events` per persistenza (migration + model).
- [ ] Refactor `LeadService` e `TrackMetaPageView` per dispatchare job invece di chiamare sincrono.
- [ ] Comando artisan `meta:replay-failed-events` per replay.
- [ ] Decide se PageView server è ancora necessario; se sì, esclude prefetch/partial reload Inertia e bot.

### Sprint 4 — Cookie consent tracking (2 giorni)
- [ ] Migration `cookie_consents` (vedi §7.2).
- [ ] Endpoint `POST /cookie-consent` + Form Request + Controller.
- [ ] Patch `cookieConsentBanner.tsx` per chiamare endpoint dopo ogni scelta.
- [ ] Filament resource `CookieConsentReport` (read-only) con metriche §7.3.
- [ ] Test feature in `tests/Feature/CookieConsentTest.php` esteso.

### Sprint 5 — Eventi & mapping refinement (1 giorno)
- [ ] Discuti con marketing: `startQuiz` rimane custom o diventa `ViewContent`? `CompleteRegistration` vs `SubmitApplication`?
- [ ] Aggiungi `ViewContent` su pagine progetti, blog post, case study.
- [ ] Aggiungi `Subscribe` se / quando newsletter standalone va live.

### Sprint 6 — Geo arricchimento (opzionale, da approvare)
- [ ] Discuti l'aggiunta di `geoip2/geoip2` o uso di Cloudflare headers.
- [ ] Estendi factory con `country`, `ct`.

---

## 10. Verifiche e criteri di successo

### 10.1 Come misurare il successo

1. **Meta Events Manager → Diagnostics → Event Match Quality**
   - Baseline: rilevare oggi su `Lead` e `CompleteRegistration`.
   - Target post-Sprint 1: `+2 punti` minimo su entrambi.
   - Target post-Sprint 2: payload "Excellent" su Quality.
2. **Meta Events Manager → Test Events**
   - Per ogni Sprint, verifica che il Test Event mostri tutti i parametri attesi (espandere row dell'evento, controllare user_data e custom_data).
3. **Tasso di delivery CAPI**
   - Post-Sprint 3: `meta_capi_events.failed_at IS NULL` su >99.5% dei record.
4. **Tasso accettazione cookie marketing**
   - Post-Sprint 4: prima misurazione mensile in dashboard Filament.

### 10.2 Test automatici da aggiungere

- Test unit `MetaPixelUserDataFactoryTest`: verifica che `fn`, `ln`, `external_id` siano popolati e (se possibile) hashati correttamente.
- Test feature `LeadServiceCapiTest`: mock job dispatch, verifica payload completo.
- Test feature `CookieConsentPersistenceTest`: POST /cookie-consent crea row, idempotente per `consent_id`.

---

## 11. Decisioni da prendere (richiedono input utente/marketing)

> Da CLAUDE.md: "NEVER make architectural decisions autonomously - ALWAYS discuss first."

1. **Valore monetario lead** — quale `value` usare per `Lead`, `CompleteRegistration`? (LTV medio cliente, ARPU, valore stimato per lead source?)
2. **`startQuiz` rimane custom o passa a `ViewContent`?**
3. **`CompleteRegistration` vs `SubmitApplication`** per il completamento quiz?
4. **Geo arricchimento** — autorizzo dipendenza `geoip2/geoip2` (~5MB DB) oppure preferisci Cloudflare headers (richiede CF davanti) o servizio HTTP (latenza)?
5. **Consent banner refactor** — vuoi mantenere soluzione custom oppure passare a vendor (Iubenda, OneTrust, Cookiebot) — se sì impatta §7.
6. **Persistenza eventi CAPI** — tabella DB o NoSQL/log strutturato? (tabella DB consigliata per replay e Filament dashboard).
7. **Limited Data Use** — vogliamo abilitarla preventivamente per traffico EU? (oggi non strettamente necessaria ma consigliata).

---

## 12. Riferimenti

- Meta — [Conversions API best practices](https://developers.facebook.com/docs/marketing-api/conversions-api/guides/best-practices)
- Meta — [Customer Information Parameters](https://developers.facebook.com/docs/marketing-api/conversions-api/parameters/customer-information-parameters)
- Meta — [Event Match Quality (EMQ)](https://www.facebook.com/business/help/765081237991954)
- Meta — [Granular Consent for Pixel](https://developers.facebook.com/docs/meta-pixel/implementation/gdpr)
- Meta — [Standard Events reference](https://developers.facebook.com/docs/meta-pixel/reference)
- Libreria attualmente in uso: [`combindma/laravel-facebook-pixel`](https://github.com/combindma/laravel-facebook-pixel)
- SDK sottostante: [`facebook/php-business-sdk`](https://github.com/facebook/facebook-php-business-sdk)

---

## Appendice A — Mappa file rilevanti

| Area | File |
|---|---|
| Pixel script loading | `resources/views/vendor/meta-pixel/head.blade.php`, `resources/views/app.blade.php:39-46` |
| Pixel client hooks | `resources/js/hooks/useMetaPixel.ts` |
| Pixel client triggers | `resources/js/components/contactForm.tsx`, `resources/js/components/sections/healthCheckQuiz.tsx` |
| CAPI middleware (PageView) | `app/Http/Middleware/TrackMetaPageView.php` |
| CAPI lead trigger | `app/Services/LeadService.php` |
| CAPI quiz triggers | `app/Http/Controllers/HealthCheckQuizController.php` |
| User data factory | `app/Services/Meta/MetaPixelUserDataFactory.php` |
| Form requests | `app/Http/Requests/ContactFormRequest.php`, `app/Http/Requests/HealthCheckQuizRequest.php` |
| Validation rules | `app/Rules/NotFakeEmail.php`, `config/lead-validation.php` |
| Cookie consent helper | `app/Helpers/CookieConsent.php` |
| Cookie consent banner | `resources/js/components/cookieConsentBanner.tsx` |
| Config | `config/meta-pixel.php`, `config/google-analytics.php`, `config/clarity.php`, `config/linkedin.php` |
| Test correlati | `tests/Feature/CookieConsentTest.php`, `tests/Feature/GoogleAnalyticsTest.php`, `tests/Feature/HealthCheckQuizTest.php`, `tests/Feature/ContactFormTest.php` |
