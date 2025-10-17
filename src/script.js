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

});
