@extends('template.main')

@section('title', trans('boards.add_template'))
@section('css_before')
    <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
@endsection
@section('css_after')
    <style>
        #result_pinya div {
            position: absolute;
            text-align: center;
            line-height: 28px;
            border: 1px solid grey;
            display: block;
            overflow: hidden;
            font-size: 11.5px;
            font-family: Helvetica, Verdana, sans-serif;
        }
    </style>
@endsection

@section('content')

<div class="block">
    <div class="block-header block-header-default">
       <h3 class="block-title">
           <div class="row">
               <div class="col-md-12"><b>{!! trans('boards.add_template') !!}:</b></div>
               <div class="col-md-3 text-success">{!! trans('boards.import_step_1') !!}</div>
               <div class="col-md-3 text-warning">{!! trans('boards.import_step_2') !!}</div>
               <div class="col-md-3 text-warning">{!! trans('boards.import_step_3') !!}</div>
               <div class="col-md-3 text-warning">{!! trans('boards.import_step_4') !!}</div>
           </div>
       </h3>
    </div>
    <div class="block-content">
        <div class="row">
            <div class="col-md-9">
                <h5 class="text-info">{!! trans('boards.step_upload_svg_txt', ['BASE' => $type_map, 'NAME' => $board->getName()]) !!}</h5>
            </div>
            <div class="col-md-3 text-right">
                @if($board->getSvgUrl($type_map))
                    <a href="{!! route('boards.tag-row-map', ['board' => $board->getId(), 'map' => $type_map]) !!}" class="btn btn-secondary">
                        {!! trans('boards.omit_next_step') !!} <i class="pl-5 fa fa-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>

        {!! Form::open(['id' => 'formLoadSVGFile', 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}

            <div class="row form-group">
                <div class="col-md-3">
                    <label class="control-label">{!! trans('boards.select_svg_file') !!}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="svg_file" id="svg_file" accept="image/svg+xml" form="formLoadSVGFile" required>
                        <label class="custom-file-label" for="svg_file">{!! trans('general.select_file') !!}</label>
                    </div>
                </div>
                <div class="col-md-1" style="padding-top: 25px;">
                    <button class="btn btn-alt-primary" id="loadSVGFile">{!! trans('boards.start') !!}</button>
                </div>
                <div class="col-md-1"  style="padding-top: 25px;">
                    <div class="spinner-border" role="status" id="spinnerLoadSVGFile" style="display: none;"><span class="sr-only">Loading...</span></div>
                </div>
                <div class="col-md-3 offset-md-4 text-right" id="divUploadSVG" style="padding-top: 25px; display: none;">
                    <label class="control-label">{!! trans('boards.is_correct') !!}</label>
                    <button class="btn btn-success btn-load-svg">{!! trans('general.yes') !!}</button>
                </div>
            </div>

        {!! Form::close() !!}
        <div class="row">
            <div id="result_pinya" style="position: relative; height: 2000px;"></div>
        </div>

    </div>
</div>

@endsection

@section('js')
    <script src="{!! asset('js/plugins/select2/js/select2.full.min.js') !!}"></script>
    <script type="text/javascript">
        $(function () {
            $.ajaxPrefilter(function(options, originalOptions, xhr) { // this will run before each request
                const token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using
                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                }
            });
        });
    </script>
<script>
    $(function ()
    {
        $('#svg_file').on('change',function(){
            //get the file name
            let fieldVal = $(this).val();
            // Change the node's value by removing the fake path (Chrome)
            fieldVal = fieldVal.replace("C:\\fakepath\\", "");
            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fieldVal);
        });
        $("#formLoadSVGFile").on("submit", function(e)
        {
            e.preventDefault()
            $('#spinnerLoadSVGFile').show();
            $('#result_pinya').html('');
            let file = document.getElementById('svg_file').files[0];				
			
			let FR = new FileReader();
            let svg;
            FR.readAsText(file);
            FR.onload = function(data)
            {
                xml = data.target.result;
                xml = $.parseXML(xml);
				
				/* Aquesta prova torna el primer text que apareix
				let data_text = xml.getElementsByTagName('text')[0].textContent;
			    console.log(data_text);*/
                				
                svg = $(xml).find('rect'); /*funcions formes rect*/
				txt = $(xml).find('tspan'); /*funcions text*/
								
				/*Obtenim la mida de la imatge del SVG importat fent servir l'atribut ViewBox  */				
				data_viewBox = $(xml).find('svg');				
				let size_viewBox = data_viewBox[0].attributes.viewBox.nodeValue;
				size_viewBox = size_viewBox.split(' ');
				console.log('Mida SVG:', +size_viewBox);
				if (+size_viewBox[2] > +size_viewBox[3]) {
					svg_max_size = +size_viewBox[2];
				}
				else { svg_max_size = +size_viewBox[3]
					console.log(svg_max_size);
				}
				/*determinem l'escala òptima segons la informació del SVG i la mida del CSS*/				
				CSS_XY_SIZE = 1000
				factor_escala = CSS_XY_SIZE / svg_max_size;
				//factor_escala = 1;
				console.log('Factor escala:', +factor_escala);
								
                			
				txt.each(function ()
                {
                    let item = this;										
					let id = item.attributes.id.nodeValue;					
					let x = item.attributes.x.nodeValue * factor_escala;					
					let y = item.attributes.y.nodeValue * factor_escala-30;					
					let data_text = item.textContent;
					font_size = 8 * factor_escala; /* faltaria llegir la mida de la font del svg, però està en un nivell superior <text ... > <tspan...>text</tspan></text> */
					//let data_style = item.attributes.style.nodeValue;					
					let div = $('<div></div>');	 /*div = return document.getElementById('<div></div>');*/					
					//$(div).attr('style', 'border:0px;text-align: left;font-size: ',font_size,'px;');				
					$(div).attr('style', 'border:0px;text-align: left;font-size: 1em;'); /*1em és la font per defecte del navegador, que és 16 px */
					$(div).attr('id', id);				
					$(div).css('position', 'absolute');
					$(div).css('top', y + 'px');
					$(div).css('left', x + 'px');
					//$(div).css('width', width + 'px');
					//$(div).css('height', height + 'px');		
					$(div).css('width', 200 + 'px');
					$(div).css('height', 30 + 'px');		
					$(div).text(data_text);
					//$(div).css('text', data_text);
					$('#result_pinya').append(div);	
					});
				
				let id = 1;
                svg.each(function ()
                {
                    let item = this;
										
					let x = item.attributes.x.nodeValue * factor_escala;
                    let y = item.attributes.y.nodeValue * factor_escala;
                    let width = item.attributes.width.nodeValue * factor_escala;
                    let height = item.attributes.height.nodeValue * factor_escala;
					
                    let div = $('<div></div>');					
                    if (item.attributes.transform) {
                        let transform = item.attributes.transform.nodeValue;
                        svg_function = transform.split('(')[0];						
						if (svg_function == 'matrix') {
							/* tracta funció matrix */
							/*transform.replace(/,/g, ' '); /*si hi ha comes posem espais perquè ho entengui el CSS NOOOO EL CSS NECESSITA COMES!!!!!*/
							transform = transform.replaceAll(' ', ','); /*si hi ha espais posem comes perquè ho entengui el CSS*/
							/*var_matrix = transform.split(',');*/							
							let var_matrix = transform.match(/\(([^()]*)\)/)[1]; /*tot el que hi ha entre parèntesi, queda separat per comes */
							var_matrix = var_matrix.split(',');														
							var_matrix[4] = +(var_matrix[4]) * factor_escala;
							var_matrix[5] = +(var_matrix[5]) * factor_escala;							
							var_matrix = var_matrix.toString();
							transform = '';
							transform = transform.concat('matrix(',var_matrix,')');							
							console.log(id, 'tipus ' + transform); 													
							$(div).css('top', y + 'px');
							$(div).css('left', x + 'px');
							$(div).css('width', width + 'px');
							$(div).css('height', height + 'px');																			
							$(div).css('transform-origin', -x + 'px ' + -y +'px');
							$(div).css('transform', transform);																					
							
						} else
						if (svg_function == 'rotate') {
							/* tracta funció rotate */																					
							$(div).css('top', y + 'px');
							$(div).css('left', x + 'px');
							$(div).css('width', width + 'px');
							$(div).css('height', height + 'px');																			
							$(div).css('transform-origin', -x + 'px ' + -y +'px');
							transform = transform.split(')')[0];							
							transform = transform.toString() + 'deg)';					
							console.log(id, 'tipus ' + transform); 													
							$(div).css('transform', transform);
						} else
						if (svg_function == 'translate') {
							/* tracta funció translate */
						} else
						if (svg_function == 'scale') {
							/* tracta funció scale */							
							transform = transform.replaceAll(' ', ','); /*si hi ha espais posem comes perquè ho entengui el CSS*/
							let var_scale = transform.match(/\(([^()]*)\)/)[1];  /*tot el que hi ha entre parèntesi, queda separat per comes */
							var_scale = var_scale.split(',');
							
							if (var_scale[1]){}		/* pot haver-hi escales definides amb un sol coeficient, que aplica a x i a y*/
							else {
								var_scale[1] = var_scale[0];
							}
							
							if (var_scale[0] < 0) {
								x = (x +width) * var_scale[0];
							}
							else {
								x = (x) * var_scale[0];
							}
							if (var_scale[1] < 0) {
								y = (y + height) * var_scale[1];							
							}
							else {
								y = (y) * var_scale[1];							
							}
							console.log(id, ' tipus scale: xf:', +x, ' yf:', +y);							
							$(div).css('top', y + 'px');
                            $(div).css('left', x + 'px');
							$(div).css('width', width + 'px');
							$(div).css('height', height + 'px');			
						} else
						if (svg_function == 'skew') {
							/* tracta funció skew - pendent */
						} 
					}	else {
							/*$(div).css('position', 'absolute');	*/
                            console.log(id, ' tipus rectangle directe');							
							$(div).css('top', y + 'px');
                            $(div).css('left', x + 'px');
							$(div).css('width', width + 'px');
							$(div).css('height', height + 'px');					
					}
					$(div).attr('id', id);					
					id++;
					$('#result_pinya').append(div);
	
					
                });
				
            }
            $('#spinnerLoadSVGFile').hide();
            $('#divUploadSVG').show();
        });
        $('.btn-load-svg').on('click', function (e)
        {
            e.preventDefault()
            $('#spinnerLoadSVGFile').show();
            let html = $('#result_pinya').html();
            let file = document.getElementById('svg_file').files[0];
            let fd = new FormData();
            fd.append('svg', file);
            fd.append('html', html);
            fd.append('type_map', '{!! $type_map !!}');
            $.ajax({
                url: "{!! route('boards.upload-svg', $board) !!}",
                type: "post",
                data: fd,
                contentType: false,
                processData: false
            }).then(function(res) {
                if(res) {
                    window.location.replace("{!! route('boards.tag-row-map', ['board' => $board, 'map' => $type_map]) !!}");
                }
            });
        });
    });
</script>
@endsection