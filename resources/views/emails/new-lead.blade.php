<!doctype html>
<html>
<body style="margin:0; padding:0; background:#f4f4f6; font-family:Arial, Helvetica, sans-serif; color:#1a1a1a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f6; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="background:#5A8CF5; padding:20px 28px;">
                            <span style="color:#ffffff; font-size:18px; font-weight:bold;">
                                New {{ $lead->source === \App\Models\Lead::SOURCE_CALLBACK ? 'callback request' : 'contact form' }} submission
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:8px 0; border-bottom:1px solid #eee; color:#666; width:140px;">Name</td>
                                    <td style="padding:8px 0; border-bottom:1px solid #eee;"><strong>{{ $lead->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; border-bottom:1px solid #eee; color:#666;">Phone</td>
                                    <td style="padding:8px 0; border-bottom:1px solid #eee;"><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
                                </tr>
                                @if ($lead->service)
                                    <tr>
                                        <td style="padding:8px 0; border-bottom:1px solid #eee; color:#666;">Service</td>
                                        <td style="padding:8px 0; border-bottom:1px solid #eee;">{{ $lead->service }}</td>
                                    </tr>
                                @endif
                                @if ($lead->zip)
                                    <tr>
                                        <td style="padding:8px 0; border-bottom:1px solid #eee; color:#666;">ZIP</td>
                                        <td style="padding:8px 0; border-bottom:1px solid #eee;">{{ $lead->zip }}</td>
                                    </tr>
                                @endif
                                @if ($lead->preferred_date)
                                    <tr>
                                        <td style="padding:8px 0; border-bottom:1px solid #eee; color:#666;">Preferred date</td>
                                        <td style="padding:8px 0; border-bottom:1px solid #eee;">{{ $lead->preferred_date }}</td>
                                    </tr>
                                @endif
                                @if ($lead->message)
                                    <tr>
                                        <td style="padding:8px 0; color:#666; vertical-align:top;">Message</td>
                                        <td style="padding:8px 0; white-space:pre-line;">{{ $lead->message }}</td>
                                    </tr>
                                @endif
                            </table>

                            <div style="margin-top:24px;">
                                <a href="{{ route('admin.leads.index') }}" style="display:inline-block; background:#5A8CF5; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:6px; font-weight:bold;">View in admin</a>
                            </div>
                        </td>
                    </tr>
                </table>
                <p style="color:#999; font-size:12px; margin-top:16px;">Sent automatically by {{ setting('site.name') }} — received {{ $lead->created_at->format('M j, Y g:i A') }}.</p>
            </td>
        </tr>
    </table>
</body>
</html>
