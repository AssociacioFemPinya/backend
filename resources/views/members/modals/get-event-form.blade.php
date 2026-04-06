<div class="container-fluid">
    <!-- Close button -->
    <div class="block-options d-flex justify-content-end">
        <button type="button" class="btn btn-lg btn-block-option" data-dismiss="modal" aria-label="Close">
            <i class="si si-close"></i>
        </button>
    </div>

    <!-- Event Header -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h1 class="text-center display-4" style="border-bottom: 1px solid #ccc;">
                {!! $event->getName() !!} - {!! trans('general.form') !!}
            </h1>
        </div>
    </div>

    <!-- FormRender -->
    <div class="row mt-4 mb-4 info-section">
        <div class="col-md-12">
            <form id="fb-rendered-form">
                <div id="fb-reader"></div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-success btn-lg" id="submit-form-btn">
                        <i class="fa fa-save"></i> Guardar Respuestas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        var formData = {!! $schema !!} || [];
        var userOptions = {!! $userOptions !!} || {};

        if (formData && formData.length > 0) {
            var formRenderInstance = $('#fb-reader').formRender({
                formData: formData,
                notify: {
                    error: function(message) { return console.error(message); },
                    success: function(message) { return console.log(message); },
                    warning: function(message) { return console.warn(message); }
                }
            });

            // Set user options if exist
            if (Object.keys(userOptions).length > 0) {
                setTimeout(function() {
                     $.each(userOptions, function(key, val) {
                         var field = $('[name="'+key+'"], [name="'+key+'[]"]');
                         if(field.length > 0) {
                             if(field.is(':checkbox') || field.is(':radio')){
                                 if(Array.isArray(val)) {
                                     field.each(function(){
                                         if(val.includes($(this).val())) {
                                             $(this).prop('checked', true);
                                         }
                                     });
                                 } else {
                                     field.filter('[value="'+val+'"]').prop('checked', true);
                                 }
                             } else {
                                 field.val(val);
                             }
                         }
                     });
                }, 100);
            }
        }
        
        // Use event delegation to prevent any dynamic loading binding issues
        $(document).off('click', '#submit-form-btn').on('click', '#submit-form-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

            try {
                // Serialize form to JSON object
                var formArray = $("#fb-rendered-form").serializeArray();
                var answers = {};
                for (var i = 0; i < formArray.length; i++){
                    var param = formArray[i];
                    var name = param.name;
                    if (name.endsWith('[]')) {
                        name = name.slice(0, -2);
                    }
                    if (answers[name] === undefined) {
                        answers[name] = param.value;
                    } else {
                        if (!Array.isArray(answers[name])) {
                            answers[name] = [answers[name]];
                        }
                        answers[name].push(param.value);
                    }
                }

                $.post("{{ route('member.edit.event-set-answers') }}", {
                    'id_event': {{ $event->getId() }},
                    'answers': answers
                }).done(function (data) {
                    $('#modalEventInfo').modal('hide');
                    if ($.fn.DataTable.isDataTable('#events_upcoming')) {
                        $("#events_upcoming").DataTable().ajax.reload(null, false);
                    }
                }).fail(function(xhr) {
                    var msj = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al guardar el formulario.';
                    alert(msj);
                    btn.prop('disabled', false).html(originalText);
                });
            } catch(error) {
                console.error(error);
                alert("Error local: " + error.message);
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
</script>
