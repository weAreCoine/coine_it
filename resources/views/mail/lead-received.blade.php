@php
    /** @var \App\Models\Lead $lead */
    /** @var array<int, array{question: string, answer: string, points: int|null, maxPoints: int}> $quizRecap */
    /** @var ?string $quizRangeLabel */
    /** @var ?string $adminUrl */
@endphp
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo lead da coine.it</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1a1a1a;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f5f5f5;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color:#ffffff;max-width:600px;width:100%;">
                <tr>
                    <td style="padding:32px 32px 8px 32px;">
                        <p style="margin:0;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#737373;">Coiné — Lead Generation</p>
                        <h1 style="margin:8px 0 0 0;font-size:22px;line-height:1.3;font-weight:600;">Nuovo lead da coine.it</h1>
                        <p style="margin:8px 0 0 0;font-size:14px;color:#525252;">
                            Ricevuto il {{ optional($lead->created_at)->translatedFormat('d M Y \a\l\l\e H:i') ?? '—' }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 32px 0 32px;">
                        <h2 style="margin:0 0 12px 0;font-size:14px;letter-spacing:0.08em;text-transform:uppercase;color:#737373;font-weight:600;">Contatto</h2>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top:1px solid #e5e5e5;">
                            @foreach (array_filter([
                                'Nome' => $lead->name,
                                'Email' => $lead->email,
                                'Telefono' => $lead->phone,
                                'Sito web' => $lead->website,
                                'Newsletter' => $lead->newsletter_opt_in ? 'Sì, vuole iscriversi' : null,
                            ]) as $label => $value)
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e5e5e5;font-size:13px;color:#737373;width:30%;vertical-align:top;">{{ $label }}</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e5e5e5;font-size:14px;color:#1a1a1a;vertical-align:top;">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                @if (! empty($lead->project))
                    <tr>
                        <td style="padding:24px 32px 0 32px;">
                            <h2 style="margin:0 0 12px 0;font-size:14px;letter-spacing:0.08em;text-transform:uppercase;color:#737373;font-weight:600;">Messaggio</h2>
                            <div style="padding:16px;background-color:#fafafa;border:1px solid #e5e5e5;font-size:14px;line-height:1.6;white-space:pre-wrap;">{{ $lead->project }}</div>
                        </td>
                    </tr>
                @endif

                @if (! empty($lead->notes))
                    <tr>
                        <td style="padding:24px 32px 0 32px;">
                            <h2 style="margin:0 0 12px 0;font-size:14px;letter-spacing:0.08em;text-transform:uppercase;color:#737373;font-weight:600;">Note dal Health Check</h2>
                            <div style="padding:16px;background-color:#fafafa;border:1px solid #e5e5e5;font-size:14px;line-height:1.6;white-space:pre-wrap;">{{ $lead->notes }}</div>
                        </td>
                    </tr>
                @endif

                @if (! empty($quizRecap))
                    <tr>
                        <td style="padding:32px 32px 0 32px;">
                            <h2 style="margin:0 0 12px 0;font-size:14px;letter-spacing:0.08em;text-transform:uppercase;color:#737373;font-weight:600;">Health Check</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fafafa;border:1px solid #e5e5e5;">
                                <tr>
                                    <td style="padding:20px;">
                                        <p style="margin:0;font-size:13px;color:#737373;">Punteggio totale</p>
                                        <p style="margin:4px 0 0 0;font-size:32px;font-weight:600;line-height:1;">{{ (int) $lead->quiz_score }}<span style="font-size:18px;color:#737373;font-weight:400;">/100</span></p>
                                        @if ($quizRangeLabel)
                                            <p style="margin:8px 0 0 0;font-size:14px;color:#1a1a1a;">{{ $quizRangeLabel }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 32px 0 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top:1px solid #e5e5e5;">
                                @foreach ($quizRecap as $item)
                                    <tr>
                                        <td style="padding:14px 0;border-bottom:1px solid #e5e5e5;vertical-align:top;">
                                            <p style="margin:0 0 6px 0;font-size:13px;color:#737373;">{{ $item['question'] }}</p>
                                            <p style="margin:0;font-size:14px;color:#1a1a1a;">
                                                {{ $item['answer'] }}
                                                @if ($item['points'] !== null)
                                                    <span style="color:#737373;font-size:13px;">&nbsp;·&nbsp;{{ $item['points'] }}/{{ $item['maxPoints'] }} pt</span>
                                                @endif
                                            </p>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endif

                @if ($adminUrl)
                    <tr>
                        <td style="padding:32px;">
                            <a href="{{ $adminUrl }}" style="display:inline-block;padding:12px 20px;background-color:#1a1a1a;color:#ffffff;text-decoration:none;font-size:14px;font-weight:500;">Apri nel backoffice &rarr;</a>
                        </td>
                    </tr>
                @else
                    <tr><td style="padding-bottom:32px;"></td></tr>
                @endif
            </table>

            <p style="margin:16px 0 0 0;font-size:12px;color:#a3a3a3;">Coiné · coine.it</p>
        </td>
    </tr>
</table>
</body>
</html>
