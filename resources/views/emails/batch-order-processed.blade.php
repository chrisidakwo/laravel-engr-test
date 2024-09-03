@component('mail::message')
# Hello!

A new batch order has been processed for you. See the details below:

**Provider Name**: {{ $batch->provider_name }}

**Batch**: {{ $batch->name }}

Click on the button below to view all orders in this batch

@component('mail::button', ['url' => config('app.url')])
    {{ __('View batch orders') }}
@endcomponent

@endcomponent
