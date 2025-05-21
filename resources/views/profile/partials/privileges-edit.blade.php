<table class="table table-striped">
    <thead>
    <th></th>
    @foreach($existingPermissions as $section => $existingPermission)
        <th>{!! trans('user.permission_'.strtolower($section)) !!}</th>
    @endforeach
    <th>
    </thead>
    <tbody>
    <tr>
        <td>{!! trans('user.permission_read') !!}</td>
        @foreach($permissionsView as $permissionView)
            <td>
                <?php $shortName = str_replace(' ','_',$permissionView->name) ?>
                {!! \App\Helpers\RenderHelper::fieldSwitcher((isset($user)) && (bool)$user->can($permissionView->name),'data-'.$shortName,(isset($user))?$user->getId():null,'permissions[]',$shortName,null,$permissionView->name) !!}
            </td>
        @endforeach
    </tr>
    <tr>
        <td>{!! trans('user.permission_write') !!}</td>
        @foreach($permissionsEdit as $permissionEdit)
            <td>
                <?php $shortName = str_replace(' ','_',$permissionEdit->name) ?>
                {!! \App\Helpers\RenderHelper::fieldSwitcher((isset($user)) && (bool)$user->can($permissionEdit->name),'data-'.$shortName,(isset($user))?$user->getId():null,'permissions[]',$shortName,null,$permissionEdit->name) !!}
            </td>
        @endforeach
    </tr>
    </tbody>
</table>

<script>
    $(function ()
    {
        let elems = Array.prototype.slice.call(document.querySelector('#modalUpdateUserContent').querySelectorAll('.js-switchery'));
        elems.forEach(function (html) {
        new Switchery(html, {size: 'small'});
        });
    });
</script>
