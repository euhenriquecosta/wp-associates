jQuery(document).ready(function ($) {
    let $search = $('#associates_search_place');
    let $hiddenLocal = $('#associates_location');
    let $lat = $('#associates_lat');
    let $lng = $('#associates_lng');

    let $suggestions = $('<div id="associates_suggestions" style="border:1px solid #ccc; background:#fff; position:absolute; z-index:9999; max-height:200px; overflow:auto; width:100%"></div>').insertAfter($search).hide();

    let debounceTimer = null;

    $search.on('input', function () {
        // Limpar timer anterior
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }
        
        let q = $(this).val();
        
        if (q.length < 3) {
            $suggestions.hide();
            return;
        }

        // Debounce de 500ms - capturar valor antes do timeout
        debounceTimer = setTimeout(function() {
            // API Nominatim com foco na Bahia
            // Bounding box da Bahia: sudoeste(lon,lat) nordeste(lon,lat)
            let url = "https://nominatim.openstreetmap.org/search?q=" + encodeURIComponent(q + ", Bahia, Brasil") + "&format=json&limit=5&countrycodes=br&bounded=1&viewbox=-46.8,-18.3,-38.0,-8.4";

            $.get(url, function (data) {
                $suggestions.empty().show();
                if (data && data.length > 0) {
                    data.forEach(function(place) {
                        let name = place.display_name || '';
                        let lat = parseFloat(place.lat);
                        let lon = parseFloat(place.lon);
                        
                        // Verificar se está realmente na Bahia (filtro adicional)
                        if (lat >= -18.3 && lat <= -8.4 && lon >= -46.8 && lon <= -38.0) {
                            $('<div style="padding:5px; cursor:pointer; border-bottom:1px solid #eee;">' + name + '</div>')
                                .appendTo($suggestions)
                                .on('click', function () {
                                    $search.val(name);
                                    $hiddenLocal.val(name);
                                    $lat.val(lat);
                                    $lng.val(lon);
                                    $suggestions.hide();
                                });
                        }
                    });
                }
            });
        }, 500);
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#associates_suggestions, #associates_search_place').length) {
            $suggestions.hide();
        }
    });

    // Funcionalidade para fotos
    let photosFrame;
    let photos = [];

    // Inicializar fotos existentes
    function initPhotos() {
        let $input = $('#associates-photos-input');
        if ($input.length && $input.val()) {
            photos = $input.val().split(',').filter(id => id !== '');
        }
    }

    // Atualizar input hidden
    function updatePhotosInput() {
        $('#associates-photos-input').val(photos.join(','));
    }

    // Adicionar foto
    function addPhoto(photoId, photoUrl) {
        if (photos.indexOf(photoId.toString()) === -1) {
            photos.push(photoId.toString());
            
            let $preview = $('#associates-photos-preview');
            let $photoItem = $('<div class="associates-photo-item" data-photo-id="' + photoId + '" style="position: relative; display: inline-block; margin: 2px;">');
            $photoItem.append('<img src="' + photoUrl + '" alt="Foto" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">');
            $photoItem.append('<button type="button" class="associates-remove-photo" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px;">×</button>');
            
            $preview.append($photoItem);
            updatePhotosInput();
        }
    }

    // Remover foto
    function removePhoto(photoId) {
        photos = photos.filter(id => id !== photoId.toString());
        $('.associates-photo-item[data-photo-id="' + photoId + '"]').remove();
        updatePhotosInput();
    }

    // Event listener para adicionar fotos
    $('#associates-add-photos').on('click', function(e) {
        e.preventDefault();
        
        // Verificar se wp.media está disponível
        if (typeof wp === 'undefined' || !wp.media) {
            alert('Erro: WordPress Media Library não está disponível. Verifique se o plugin está ativo.');
            return;
        }
        
        // Criar frame de mídia com configuração simples
        photosFrame = wp.media({
            title: 'Selecionar Fotos',
            button: {
                text: 'Adicionar Fotos'
            },
            multiple: true,
            library: {
                type: 'image'
            }
        });

        // Evento quando uma seleção é feita
        photosFrame.on('select', function() {
            let selection = photosFrame.state().get('selection');
            if (selection && selection.length > 0) {
                selection.map(function(attachment) {
                    let attachmentData = attachment.toJSON();
                    addPhoto(attachmentData.id, attachmentData.url);
                });
            }
        });

        // Abrir o frame
        photosFrame.open();
    });

    // Event listener para remover fotos
    $(document).on('click', '.associates-remove-photo', function(e) {
        e.preventDefault();
        let photoId = $(this).closest('.associates-photo-item').data('photo-id');
        removePhoto(photoId);
    });

    // Inicializar
    initPhotos();
});
