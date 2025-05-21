@include('emails.header')

          <!-- HEADING -->
          <tr>
            <td class="one-column" style="padding: 0;">
              <table width="100%" style="border-spacing: 0;">
                <tr class="main-text">
                  <td class="inner contents" style="width: 100%; padding: 20px; text-align: left; margin-bottom: 10px;" width="100%" align="left">
                    <p class="h1" style="margin: 0; font-family: sans-serif; font-weight: bold; margin-bottom: 10px; text-align: center; font-size: 30px;">{!! trans('casteller.credentials_mail_welcome_message') !!} {{ $casteller->getName() }}!
                    </p>
                    <p style="margin: 0; font-family: sans-serif; margin-bottom: 10px; text-align: center;">
                      <span style="font-family: sans-serif; color: #666;">
                        <br>{!! trans('casteller.credentials_mail_introduction_message') !!}
                      </span>
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

@if ($casteller->castellerConfig->getTelegramEnabled())
         <!-- MANAGE-->
         <tr>
          <td class="one-column" style="padding: 0;">
            <!--[if (gte mso 9)|(IE)]>
              <table width="100%">
              <tr>
              <td width="50%" valign="top">
              <![endif]-->

                    <table width="100%" class="contents" style="border-spacing: 0; width: 100%; text-align: left; margin-bottom: 10px;" align="left">
                      <tr>
                        <td class="grey-link-block" style="background-color: #ececec; border-radius: 8px; padding: 20px; margin-bottom: 10px;">
                          <p style="float: right;"><a href="{{ config('app.telegram_bot_url') }}"><b>{{ config('app.telegram_bot_user') }}</b></a href></p>
                          <p class="h2" style="margin: 0; font-family: sans-serif; font-size: 18px; font-weight: bold; line-height: 1.2; margin-bottom: 4px;">{!! trans('casteller.credentials_mail_access_through_telegram') !!}</p>
                          <p>{!! trans('casteller.credentials_mail_your_telegram_token') !!}: <b>{{ $casteller->castellerConfig->getTelegramToken() }}</b></p>
                          <p style="margin: 0; font-family: sans-serif; margin-bottom: 4px;">

                            <a href="https://blog.fempinya.cat/manual/fempinyabot/" style="font-family: sans-serif; font-size: 15px; color: #243235; text-decoration: none; vertical-align: middle;" target="_blank">
                                <i style="width: 16px; display: inline-block; vertical-align: middle;">
                                    <img src="{!! asset('media/img/document-icon.png') !!}" width="16" alt="" style="border: 0;">
                                </i>
                                {!! trans('casteller.credentials_mail_telegram_documentation') !!}
                            </a>
                          </p>
                        </td>
                      </tr>
                    </table>

            <!--[if (gte mso 9)|(IE)]>
              </td><td width="50%" valign="top">
              <![endif]-->
          </td>
        </tr>
@endif

@if (false)
<?php //@if ($casteller->castellerConfig->getApiTokenEnabled()) ?>
         <!-- MANAGE-->
         <tr>
          <td class="one-column" style="padding: 0;">
            <!--[if (gte mso 9)|(IE)]>
              <table width="100%">
              <tr>
              <td width="50%" valign="top">
              <![endif]-->

                    <table width="100%" class="contents" style="border-spacing: 0; width: 100%; text-align: left; margin-bottom: 10px;" align="left">
                      <tr>
                        <td class="grey-link-block" style="background-color: #ececec; border-radius: 8px; padding: 20px; margin-bottom: 10px;">
                          <p class="h2" style="margin: 0; font-family: sans-serif; font-size: 18px; font-weight: bold; line-height: 1.2; margin-bottom: 4px;">{!! trans('casteller.credentials_mail_access_through_api') !!}</p>
                          <p>{!! trans('casteller.credentials_mail_your_api_token') !!}: <b>{{ $casteller->castellerConfig->getApiToken() }}</b></p>
                        </td>
                      </tr>
                    </table>

            <!--[if (gte mso 9)|(IE)]>
              </td><td width="50%" valign="top">
              <![endif]-->
          </td>
        </tr>
@endif


@if ($casteller->castellerConfig->getAuthTokenEnabled())
          <!-- ACCÉS WEB-->
          <tr>
            <td class="one-column baby-blue-block" style="border-radius: 9px; background-color: #e8f2ff; display: flex; padding: 15px; flex-wrap: wrap; justify-content: center; margin-bottom: 16px; align-items: center;">
              <table width="100%" style="border-spacing: 0;">
                <tr>
                  <td class="inner contents" style="padding: 0 0px; width: 100%; text-align: left; margin-bottom: 10px;" width="100%" align="left">
                    <p class="h2" style="margin: 0; font-family: sans-serif; font-size: 18px; font-weight: bold; line-height: 1.2; margin-bottom: 10px; margin-top: 12px;">{!! trans('casteller.credentials_mail_access_through_web') !!}</p>
                    <div class="body-access-web" style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <p style="margin: 0; font-family: sans-serif; margin-bottom: 10px; width: 100%; font-size: 15px; color: #666666; display: flex; flex-direction: column;">
                        {!! trans('casteller.credentials_mail_access_through_web_message') !!}
                        <a class="access-button" href="{!! $casteller->castellerConfig->getWebUrl() !!}" target="_blank" style="font-family: sans-serif; border-radius: 5px; border: solid 1px #666666; padding: 6px 20px; text-align: center; font-size: 16px; color: #666666; background: none; text-decoration: none; width: fit-content; margin: auto; margin-top: 15px;">
                        {!! trans('casteller.credentials_mail_web_access_button') !!}
                      </a>
                    </p>

                    </div>
                    <p style="margin: 0; font-family: sans-serif; margin-bottom: 10px;"></p>

                  </td>
                </tr>
              </table>
            </td>
          </tr>
@endif

@include('emails.footer')
