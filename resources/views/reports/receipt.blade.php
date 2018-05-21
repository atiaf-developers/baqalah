<div style="position: relative;width: 8cm; margin: 0 auto; color: #555555; background: #FFFFFF; font-family: 'Roboto Condensed', sans-serif;font-size: 14px;">
    <header class="clearfix" style="padding: 10px 0;margin-bottom: 10px;border-bottom: 1px solid #AAAAAA;">
        <div id="logo" style="margin-top: 8px;">
            <img src="{{ url('public/backend/images/logo.png')  }}" style="height: 70px;margin: 0 auto;display: block;">
        </div>
    </header>
    <main>
        <div id="details" class="clearfix" style="margin-bottom: 50px;">
            <div id="invoice" style="float: none;width:100%;text-align: center;margin-bottom: 20px; text-align: center;">
                <div class="date" style="font-size: 1.1em;color: #777777;">Date: {{  $order->created_at  }}</div>
                <!--<div class="date" style="font-size: 1.1em;color: #777777;">Time: 15:44:18</div>-->
                <div class="date" style="font-size: 1.1em;color: #777777;">Order no: {{  $order->id  }}</div>
                <div class="date" style="font-size: 1.1em;color: #777777;">Store:  {{$order->store_name}}</div>
            </div>
            <div id="client" style="padding-left: 6px;border-left: 6px solid #d5344a;float: left;margin: 5px 0 15px;">
                <div class="to" style="color: #777777;">ORDER TO:</div>
                <h2 class="name" style="font-size: 1.4em;font-weight: normal;margin: 0;">{{$order->client_name}}</h2>
                <div class="address">{{$order->long_address}}</div>
                <div class="color" style="color:#D5344A;">Delivery Type :   {{$order->delivery_type==1?_lang('app.delivery_by_store'):_lang('app.receive_order')}}</div>
            </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; border-spacing: 0;margin-bottom: 20px; table-layout: fixed; width:8cm;">
            <thead styl="font-weight: bold;">
                <tr>
                    <th class="desc" style="white-space: pre-wrap;font-weight: normal;padding: 5px; background: #EEEEEE;text-align: center;border-bottom: 1px solid #FFFFFF;text-align: left;width:35%;">PRODUCT</th>
                    <th class="unit" style="white-space: pre-wrap;font-weight: normal;padding: 5px;text-align: center;border-bottom: 1px solid #FFFFFF;background: #DDDDDD;width:20%;font-size: 1.1em;">UNIT PRICE</th>
                    <th class="qty" style="white-space: pre-wrap;font-weight: normal;padding: 5px; background: #EEEEEE;text-align: center;border-bottom: 1px solid #FFFFFF;width:15%;font-size: 1.1em;">QTY</th>
                    <th class="total" style="white-space: pre-wrap;font-weight: normal;padding: 5px;text-align: center;border-bottom: 1px solid #FFFFFF;background: #D5344A;color: #FFFFFF;width:30%;font-size: 1.1em;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($order->details as $key=> $one)
                    <td class="desc" style="text-align: center;padding: 5px; background: #EEEEEE;text-align: center;border-bottom: 1px solid #FFFFFF;text-align: left;width:35%;">
                        {{$one->name}}
                        <!--<h3 style="color: #D5344A;font-size: 1.2em;font-weight: normal;margin: 0 0 0.2em 0;">meal 1</h3>Potatos ( 1 * 30.00 )--> 
                    </td>
                    <td class="unit" style="text-align: center;padding: 5px;text-align: center;border-bottom: 1px solid #FFFFFF;background: #DDDDDD;width:20%;font-size: 1.1em;">
                        {{$one->price}}
                    </td>
                    <td class="qty" style="text-align: center;padding: 5px; background: #EEEEEE;text-align: center;border-bottom: 1px solid #FFFFFF;width:15%;font-size: 1.1em;">
                        {{$one->quantity}}
                    </td>
                    <td class="total" style="text-align: center;padding: 5px;text-align: center;border-bottom: 1px solid #FFFFFF;background: #D5344A;color: #FFFFFF;width:30%;font-size: 1.1em;">
                        {{$one->total_price}}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="text-align: left;">
                
                <tr>
                    <td colspan="2" style="text-align: center;padding: 5px; background: #EEEEEE;text-align: center;border-bottom: 1px solid #FFFFFF;padding: 10px 5px;background: #FFFFFF;border-bottom: none;font-size: 1.2em;white-space: pre-wrap;border-top: 1px solid #AAAAAA;text-align: left;color: #D5344A;font-size: 1.4em; border-top: 1px solid #D5344A; ">TOTAL COST</td>
                    <td colspan="2" style="text-align: center;padding: 5px; background: #EEEEEE;text-align: center;border-bottom: 1px solid #FFFFFF;padding: 10px 5px;background: #FFFFFF;border-bottom: none;font-size: 1.2em;white-space: pre-wrap;border-top: 1px solid #AAAAAA;text-align: left;color: #D5344A;font-size: 1.4em; border-top: 1px solid #D5344A; ">{{ $order->total_price }} {{ $currency_sign }}</td>
                </tr>
            </tfoot>
        </table>
    </main>
</div>
