@php
    $colla = Auth::user()->getColla();

    // Get banner message from current colla if available
    $bannerMessage = $colla->getBannerNotificationMessage();
@endphp

@if($bannerMessage != null)
<div class="bg-warning text-dark text-center py-2" style="width: 100%;">
    <div class="content">
        <strong><i class="fa fa-exclamation-triangle mr-1"></i> {!! $bannerMessage !!}</strong>
    </div>
</div>
@endif
