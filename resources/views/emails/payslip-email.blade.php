<div style="width:100%; margin:0;">
    <center style="width:100%; text-align:left; background-color:#f0f4f9;">
        <div style="display:none; font-size:1px; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; font-family:'Nunito',sans-serif;">
            Hi {{ $employee->user->name }}! Your payslip for {{ date('F Y', mktime(0, 0, 0, $payslipData['month'], 1, $payslipData['year'])) }} is ready.
        </div>

        <div style="max-width:750px; margin:auto;">
            <table role="presentation" aria-hidden="true" style="max-width:750px;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                    <tr>
                        <td style="padding-top:30px; padding-left:20px; padding-right:20px; text-align:left;">
                            <img style="display:block;" src="{{ $payslipData['business']->getImageUrl() }}" aria-hidden="true" alt="{{ $payslipData['business']->company_name }}" width="180" border="0">
                        </td>
                    </tr>
                </tbody>
            </table>

            <table role="presentation" aria-hidden="true" style="max-width: 750px;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                    <tr>
                        <td>
                            <table role="presentation" aria-hidden="true" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                    <tr>
                                        <td style="padding:40px 20px 0 20px; text-align:left; font-family:'Nunito',sans-serif;">
                                            <h2 style="font-size:20px; font-weight:700; letter-spacing:0.08em; margin:0 0 13px 0; color:#008060;">
                                                Hi <span style="color:#f2bd87;">{{ $employee->user->name }}!</span>
                                            </h2>
                                            <hr style="text-align:left; margin:0px; width:40px; height:3px; color:#000; background-color:#000; border-radius:4px; border:none;">

                                            <p style="font-size:15px; font-weight:300; color:#000; line-height: 1.6;">
                                                Your payslip for {{ date('F Y', mktime(0, 0, 0, $payslipData['month'], 1, $payslipData['year'])) }} is now available.
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

            <hr style="margin-top: 30px; margin-bottom: 20px; color:#000; height:1px; border:0; background-color:#000;">

            <p style="margin:0;padding:0;font-size:13px; color: #000">
                You are receiving this email because you are an employee of {{ config('app.name') }}.
            </p>

            <p style="margin:0;padding:0;font-size:13px; margin-top: 10px; color: #000">Note: This is a system generated mail. Please DO NOT reply to it.</p>

            <div style="display: flex; margin-top: 20px; padding-bottom: 30px">
                <a href="#" style="margin-right: 10px;" target="_blank">
                    <img style="display:block" src="{{ URL::asset('/assets/icons/whatsapp-square-brands.png') }}" width="32" height="32">
                </a>
                <a href="#" style="margin-right: 10px;" target="_blank">
                    <img style="display:block" src="{{ URL::asset('/assets/icons/phone-square-alt-solid.png') }}" width="32" height="32">
                </a>
                <a href="#" style="margin-right: 10px;" target="_blank">
                    <img style="display:block" src="{{ URL::asset('/assets/icons/youtube-square-brands.png') }}" width="32" height="32">
                </a>
                <a href="#" style="margin-right: 10px;" target="_blank">
                    <img style="display:block" src="{{ URL::asset('/assets/icons/envelope-square-solid.png') }}" width="32" height="32">
                </a>
            </div>
        </div>
    </center>
</div>
