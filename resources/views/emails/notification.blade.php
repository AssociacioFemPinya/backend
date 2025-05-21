@include('emails.header')

          <!-- HEADING -->
          <tr>
            <td class="one-column" style="padding: 0;">
              <table width="100%" style="border-spacing: 0;">
                <tr class="main-text">
                  <td class="inner contents" style="width: 100%; padding: 20px; text-align: left; margin-bottom: 10px;" width="100%" align="left">
                    <p class="h1" style="margin: 0; font-family: sans-serif; font-weight: bold; margin-bottom: 10px; text-align: center; font-size: 30px;">{!! trans('notifications.new_notification') !!}
                    </p>
                    <p style="margin: 0; font-family: sans-serif; margin-bottom: 10px; text-align: center;">
                      <span style="font-family: sans-serif; color: #666;">
                        <br>{!! $notification_message !!}
                      </span>
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

@include('emails.footer')
