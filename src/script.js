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
            let url = "https://photon.komoot.io/api/?q=" + encodeURIComponent(q) + "&limit=5&lang=default";

            $.get(url, function (data) {
                $suggestions.empty().show();
                if (data && data.features) {
                    data.features.forEach(function(f) {
                        let name = f.properties.name || '';
                        let state = f.properties.state || '';
                        let country = f.properties.country || '';
                        let full = [name, state, country].filter(Boolean).join(', ');

                        $('<div style="padding:5px; cursor:pointer;">' + full + '</div>')
                            .appendTo($suggestions)
                            .on('click', function () {
                                $search.val(full);
                                $hiddenLocal.val(full);
                                $lat.val(f.geometry.coordinates[1]);
                                $lng.val(f.geometry.coordinates[0]);
                                $suggestions.hide();
                            });
                    });
                }
            });
        }, 500);
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#ai_suggestions, #ai_search_place').length) {
            $suggestions.hide();
        }
    });
});
