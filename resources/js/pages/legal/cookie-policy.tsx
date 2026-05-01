import { Head } from '@inertiajs/react';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';

export default function CookiePolicy() {
    return (
        <>
            <Head title="Cookie Policy" />
            <Navigation />
            <div className="container mt-20">
                <h1 className="page__title">Cookie Policy</h1>
                <div className="rich__text">
                    <p>
                        <em>(ai sensi dell&apos;art. 13 del Regolamento UE 2016/679 — GDPR e del Provvedimento Garante Privacy 8 maggio 2014)</em>
                    </p>
                    <p>
                        <strong>Titolare del trattamento:</strong> Luca Barbi — <a href="mailto:privacy@coine.it">privacy@coine.it</a>
                        <br />
                        <strong>Ultimo aggiornamento: maggio 2026</strong>
                    </p>

                    <h2>1. Cosa sono i cookie</h2>
                    <p>
                        I cookie sono piccoli file di testo che i siti web salvano nel browser dell&apos;utente durante la navigazione. Consentono al
                        sito di ricordare informazioni sulla visita (es. preferenze, sessione di navigazione) e, nel caso di cookie di terze parti, di
                        raccogliere dati per finalità di analisi e pubblicità.
                    </p>

                    <h2>2. Tipologie di cookie utilizzati su coine.it</h2>

                    <h3>2.1 Cookie tecnici — nessun consenso richiesto</h3>
                    <p>
                        I cookie tecnici sono necessari al funzionamento del sito e all&apos;erogazione del servizio. Non raccolgono dati a fini di
                        profilazione e non richiedono il consenso dell&apos;utente ai sensi dell&apos;art. 122 del Codice Privacy e del Provvedimento
                        Garante 8 maggio 2014.
                    </p>
                    <div className="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Finalità</th>
                                    <th>Durata</th>
                                    <th>Prima/Terza parte</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <code>coine_cookie_consent</code>
                                    </td>
                                    <td>Memorizza le preferenze di consenso espresse dall&apos;utente tramite il banner</td>
                                    <td>12 mesi</td>
                                    <td>Prima parte</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>PHPSESSID</code>
                                    </td>
                                    <td>Gestione della sessione di navigazione</td>
                                    <td>Sessione</td>
                                    <td>Prima parte</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3>2.2 Cookie analitici — categoria &quot;Analytics&quot;</h3>
                    <p>
                        Utilizzati per raccogliere informazioni aggregate sull&apos;utilizzo del sito (pagine visitate, durata della sessione,
                        provenienza del traffico). <strong>Attivati solo previo consenso.</strong>
                    </p>
                    <p>
                        Con <strong>Google Consent Mode v2</strong> attivo, in assenza di consenso GA4 opera in modalità cookieless (nessun cookie
                        depositato, solo segnali aggregati e modellati).
                    </p>
                    <div className="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th>Strumento</th>
                                    <th>Nome cookie</th>
                                    <th>Finalità</th>
                                    <th>Durata</th>
                                    <th>Gestore</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Google Analytics 4</td>
                                    <td>
                                        <code>_ga</code>
                                    </td>
                                    <td>Distingue gli utenti univoci</td>
                                    <td>2 anni</td>
                                    <td>Google Ireland Ltd.</td>
                                </tr>
                                <tr>
                                    <td>Google Analytics 4</td>
                                    <td>
                                        <code>_ga_&lt;ID&gt;</code>
                                    </td>
                                    <td>Mantiene lo stato della sessione</td>
                                    <td>2 anni</td>
                                    <td>Google Ireland Ltd.</td>
                                </tr>
                                <tr>
                                    <td>Google Analytics 4</td>
                                    <td>
                                        <code>_gid</code>
                                    </td>
                                    <td>Distingue gli utenti nelle 24h</td>
                                    <td>24 ore</td>
                                    <td>Google Ireland Ltd.</td>
                                </tr>
                                <tr>
                                    <td>Google Analytics 4</td>
                                    <td>
                                        <code>_gat</code>
                                    </td>
                                    <td>Limita la frequenza delle richieste</td>
                                    <td>1 minuto</td>
                                    <td>Google Ireland Ltd.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p>
                        <strong>Trasferimenti extra-UE:</strong> Google Ireland Ltd. può trasferire dati verso Google LLC (USA) con garanzie adeguate
                        tramite Data Privacy Framework UE–USA e Standard Contractual Clauses.
                        <br />
                        <strong>Opt-out:</strong>{' '}
                        <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener noreferrer">
                            tools.google.com/dlpage/gaoptout
                        </a>{' '}
                        oppure tramite le preferenze del banner.
                    </p>

                    <h4>Microsoft Clarity</h4>
                    <p>
                        Utilizziamo <strong>Microsoft Clarity</strong> (Microsoft Corporation) per analizzare l&apos;interazione degli utenti con il
                        sito tramite <strong>session replay</strong> (registrazione anonima della sessione di navigazione) e{' '}
                        <strong>heatmap</strong> (mappe di calore dei click e dello scroll). Le registrazioni non includono campi marcati come sensibili
                        (password, dati di pagamento) e i dati testuali inseriti nei form sono mascherati di default. <strong>Gli utenti autenticati
                        nell&apos;area amministrativa sono esclusi dal tracciamento.</strong>
                    </p>
                    <div className="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome cookie</th>
                                    <th>Finalità</th>
                                    <th>Durata</th>
                                    <th>Gestore</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <code>_clck</code>
                                    </td>
                                    <td>Identificatore utente Clarity (persistenza tra sessioni)</td>
                                    <td>1 anno</td>
                                    <td>Microsoft Corporation</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>_clsk</code>
                                    </td>
                                    <td>Identificatore di sessione Clarity (collega le pageview di una sessione)</td>
                                    <td>1 giorno</td>
                                    <td>Microsoft Corporation</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>CLID</code>
                                    </td>
                                    <td>Correlazione delle interazioni utente all&apos;interno del progetto Clarity</td>
                                    <td>1 anno</td>
                                    <td>Microsoft Corporation</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>MUID</code>
                                    </td>
                                    <td>Identificatore univoco del browser, condiviso con altri servizi Microsoft (es. Bing)</td>
                                    <td>1 anno</td>
                                    <td>Microsoft Corporation</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>ANONCHK</code>
                                    </td>
                                    <td>Verifica MUID e raccolta di dati statistici aggregati</td>
                                    <td>10 minuti</td>
                                    <td>Microsoft Corporation</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>SM</code>
                                    </td>
                                    <td>Sincronizzazione MUID tra domini Microsoft</td>
                                    <td>Sessione</td>
                                    <td>Microsoft Corporation</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p>
                        <strong>Trasferimenti extra-UE:</strong> i dati raccolti da Clarity sono trattati da Microsoft Corporation con sede negli Stati
                        Uniti. Le garanzie adeguate sono assicurate tramite l&apos;adesione di Microsoft al Data Privacy Framework UE–USA e tramite
                        Standard Contractual Clauses ai sensi dell&apos;art. 46 GDPR.
                        <br />
                        <strong>Privacy Statement Microsoft:</strong>{' '}
                        <a href="https://privacy.microsoft.com/privacystatement" target="_blank" rel="noopener noreferrer">
                            privacy.microsoft.com/privacystatement
                        </a>
                        <br />
                        <strong>Informativa Clarity:</strong>{' '}
                        <a
                            href="https://learn.microsoft.com/clarity/setup-and-installation/cookie-list"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            learn.microsoft.com/clarity/setup-and-installation/cookie-list
                        </a>
                    </p>

                    <h3>2.3 Cookie di marketing — categoria &quot;Marketing&quot;</h3>
                    <p>
                        Utilizzati per misurare l&apos;efficacia delle campagne pubblicitarie e per mostrare annunci personalizzati a utenti che hanno
                        già visitato il sito (remarketing). <strong>Attivati solo previo consenso.</strong>
                    </p>
                    <p>
                        Con <strong>Google Consent Mode v2</strong> attivo, in assenza di consenso Google Ads opera in modalità di misurazione
                        aggregata senza cookie di profilazione.
                    </p>

                    <h4>Google Ads</h4>
                    <div className="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome cookie</th>
                                    <th>Finalità</th>
                                    <th>Durata</th>
                                    <th>Gestore</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <code>_gcl_au</code>
                                    </td>
                                    <td>Misura le conversioni generate da annunci Google</td>
                                    <td>90 giorni</td>
                                    <td>Google Ireland Ltd.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>IDE</code>
                                    </td>
                                    <td>Remarketing e misurazione delle conversioni su rete Display</td>
                                    <td>13 mesi</td>
                                    <td>Google Ireland Ltd.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>DSID</code>
                                    </td>
                                    <td>Identificazione utente per remarketing su Google Display</td>
                                    <td>2 settimane</td>
                                    <td>Google Ireland Ltd.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4>Meta Pixel</h4>
                    <div className="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome cookie</th>
                                    <th>Finalità</th>
                                    <th>Durata</th>
                                    <th>Gestore</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <code>_fbp</code>
                                    </td>
                                    <td>Tracciamento delle visite per ottimizzazione campagne Meta</td>
                                    <td>3 mesi</td>
                                    <td>Meta Platforms Ireland Ltd.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>_fbc</code>
                                    </td>
                                    <td>Memorizza l&apos;ultimo click su un annuncio Meta</td>
                                    <td>3 mesi</td>
                                    <td>Meta Platforms Ireland Ltd.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p>
                        <strong>Privacy Policy Meta:</strong>{' '}
                        <a href="https://www.facebook.com/privacy/policy/" target="_blank" rel="noopener noreferrer">
                            facebook.com/privacy/policy
                        </a>
                        <br />
                        <strong>Gestione preferenze Meta:</strong>{' '}
                        <a href="https://www.facebook.com/ads/preferences" target="_blank" rel="noopener noreferrer">
                            facebook.com/ads/preferences
                        </a>
                    </p>

                    <h4>LinkedIn Insight Tag</h4>
                    <div className="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome cookie</th>
                                    <th>Finalità</th>
                                    <th>Durata</th>
                                    <th>Gestore</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <code>li_fat_id</code>
                                    </td>
                                    <td>Conversioni e remarketing da campagne LinkedIn Ads</td>
                                    <td>30 giorni</td>
                                    <td>LinkedIn Ireland Unlimited Co.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>bcookie</code>
                                    </td>
                                    <td>Identificazione browser per sicurezza e analytics</td>
                                    <td>2 anni</td>
                                    <td>LinkedIn Ireland Unlimited Co.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>lidc</code>
                                    </td>
                                    <td>Ottimizzazione selezione data center</td>
                                    <td>24 ore</td>
                                    <td>LinkedIn Ireland Unlimited Co.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <code>UserMatchHistory</code>
                                    </td>
                                    <td>Sincronizzazione ID per retargeting</td>
                                    <td>30 giorni</td>
                                    <td>LinkedIn Ireland Unlimited Co.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p>
                        <strong>Privacy Policy LinkedIn:</strong>{' '}
                        <a href="https://www.linkedin.com/legal/privacy-policy" target="_blank" rel="noopener noreferrer">
                            linkedin.com/legal/privacy-policy
                        </a>
                        <br />
                        <strong>Opt-out LinkedIn:</strong>{' '}
                        <a href="https://www.linkedin.com/psettings/guest-controls" target="_blank" rel="noopener noreferrer">
                            linkedin.com/psettings/guest-controls
                        </a>
                    </p>

                    <h2>3. Google Consent Mode v2</h2>
                    <p>
                        Il sito implementa <strong>Google Consent Mode v2</strong>, che adatta il comportamento dei prodotti Google (GA4, Google Ads)
                        in base alle scelte di consenso espresse:
                    </p>
                    <ul>
                        <li>
                            <strong>Consenso negato:</strong> nessun cookie di analytics o marketing viene depositato. Google utilizza la modellazione
                            dei dati (conversion modeling) per stimare le conversioni in forma aggregata e anonima.
                        </li>
                        <li>
                            <strong>Consenso accordato:</strong> i cookie descritti al §2.2 e §2.3 vengono attivati normalmente.
                        </li>
                    </ul>
                    <p>
                        I segnali gestiti sono <code>analytics_storage</code> e <code>ad_storage</code> (e relativi segnali ausiliari{' '}
                        <code>ad_user_data</code>, <code>ad_personalization</code>).
                    </p>

                    <h2>4. Come gestire le preferenze di consenso</h2>

                    <h3>Tramite il banner del sito</h3>
                    <p>Al primo accesso a coine.it, viene mostrato un banner che consente di:</p>
                    <ul>
                        <li>
                            <strong>Accettare tutte le categorie</strong> di cookie;
                        </li>
                        <li>
                            <strong>Accettare solo i cookie tecnici</strong> (rifiuto di analytics e marketing);
                        </li>
                        <li>
                            <strong>Gestire le preferenze per categoria</strong> (analytics e/o marketing separatamente);
                        </li>
                        <li>
                            <strong>Revocare o modificare</strong> le preferenze in qualsiasi momento cliccando sul link &quot;Gestisci preferenze
                            cookie&quot; presente nel footer del sito.
                        </li>
                    </ul>
                    <p>
                        Le preferenze espresse sono valide per <strong>12 mesi</strong>. Trascorso tale periodo, il banner verrà nuovamente mostrato.
                    </p>

                    <h3>Tramite le impostazioni del browser</h3>
                    <p>È possibile bloccare o eliminare i cookie direttamente dal browser. Le istruzioni variano per browser:</p>
                    <ul>
                        <li>
                            <strong>Chrome:</strong> Impostazioni → Privacy e sicurezza → Cookie e altri dati dei siti
                        </li>
                        <li>
                            <strong>Firefox:</strong> Impostazioni → Privacy e sicurezza → Cookie e dati dei siti web
                        </li>
                        <li>
                            <strong>Safari:</strong> Preferenze → Privacy → Gestisci dati dei siti web
                        </li>
                        <li>
                            <strong>Edge:</strong> Impostazioni → Cookie e autorizzazioni sito → Cookie e dati archiviati
                        </li>
                    </ul>
                    <p>
                        <strong>Attenzione:</strong> bloccare i cookie tecnici può compromettere il corretto funzionamento del sito.
                    </p>

                    <h3>Strumenti di opt-out dei singoli provider</h3>
                    <div className="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Link opt-out</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Google (tutti i prodotti)</td>
                                    <td>
                                        <a href="https://myaccount.google.com/data-and-privacy" target="_blank" rel="noopener noreferrer">
                                            myaccount.google.com/data-and-privacy
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Google Analytics</td>
                                    <td>
                                        <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener noreferrer">
                                            tools.google.com/dlpage/gaoptout
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Meta / Facebook</td>
                                    <td>
                                        <a href="https://www.facebook.com/ads/preferences" target="_blank" rel="noopener noreferrer">
                                            facebook.com/ads/preferences
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>LinkedIn</td>
                                    <td>
                                        <a href="https://www.linkedin.com/psettings/guest-controls" target="_blank" rel="noopener noreferrer">
                                            linkedin.com/psettings/guest-controls
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Microsoft (Clarity, Bing, ecc.)</td>
                                    <td>
                                        <a href="https://account.microsoft.com/privacy" target="_blank" rel="noopener noreferrer">
                                            account.microsoft.com/privacy
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pubblicità comportamentale (YourAdChoices)</td>
                                    <td>
                                        <a href="https://www.youradchoices.com" target="_blank" rel="noopener noreferrer">
                                            youradchoices.com
                                        </a>
                                        {' / '}
                                        <a href="https://www.youronlinechoices.eu" target="_blank" rel="noopener noreferrer">
                                            youronlinechoices.eu
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h2>5. Conservazione e sicurezza dei dati</h2>
                    <p>
                        I dati raccolti tramite cookie di prima parte sono conservati su server <strong>Hetzner Online GmbH</strong> con data center
                        localizzato in <strong>Germania (UE)</strong>. I dati gestiti da provider di terze parti (Google, Meta, LinkedIn) sono
                        soggetti alle rispettive informative privacy, richiamate al §2.
                    </p>

                    <h2>6. Modifiche alla Cookie Policy</h2>
                    <p>
                        Il titolare si riserva il diritto di aggiornare la presente Cookie Policy in caso di modifiche normative, aggiornamenti degli
                        strumenti utilizzati o variazioni nell&apos;infrastruttura del sito. La versione vigente è sempre disponibile a questa pagina
                        con indicazione della data di ultimo aggiornamento.
                    </p>
                    <p>
                        Per qualsiasi domanda o per esercitare i diritti previsti dal GDPR (accesso, cancellazione, opposizione, ecc.), consulta la{' '}
                        <a href="/privacy-policy">Privacy Policy</a> o scrivi a{' '}
                        <strong>
                            <a href="mailto:privacy@coine.it">privacy@coine.it</a>
                        </strong>
                        .
                    </p>
                </div>
            </div>
            <Colophon />
        </>
    );
}
