@foreach($existingPermissions as $section => $permissions)
    <span class="badge">
        <span style="display:block; margin:8px; font-weight: bold">{!! trans('user.permission_'.strtolower($section)) !!}</span>
        @foreach($permissions as $index => $permission)
                <?php $perm = (str_contains($permission->name,'view')) ? trans('user.permission_read') : trans('user.permission_write') ?>
            @if($user->can($permission->name))
                <span class="badge badge-pill badge-success" style="display:block; margin:3px; padding:5px">{!! $perm !!}</span>
            @else
                <span class="badge badge-pill badge-danger" style="display:block; margin:3px; padding:5px">{!! $perm !!}</span>
            @endif
        @endforeach
    </span>
@endforeach



