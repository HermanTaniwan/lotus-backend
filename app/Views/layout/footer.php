<footer>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="<?= base_url(); ?>asset/plugins/sliptree-bootstrap-tokenfield-v0.12.1/sliptree-bootstrap-tokenfield-ff5b929/dist/bootstrap-tokenfield.min.js"></script>
    <!-- <script src="<?= base_url(); ?>" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/highlight-within-textarea@2.0.5/jquery.highlight-within-textarea.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/jquery.mark.min.js" integrity="sha512-mhbv5DqBMgrWL+32MmsDOt/OAvqr/cHimk6B8y/bx/xS88MVkYGPiVv2ixKVrkywF2qHplNRUvFsAHUdxZ3Krg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.original-form, .published-form').on("keypress", function(e) {
                if ((e.keyCode == 10 || e.keyCode == 13) && e.ctrlKey) {
                    $(this).submit();
                }
            });


            $('.input-dd').change(function() {
                var el = $(this).closest('tr');
                $.ajax({
                    url: "<?= base_url(); ?>update-recipe",
                    type: "POST",
                    data: {
                        "id": $(this).attr('recipe_id'),
                        "data": {
                            "category": $(this).attr('category'),
                            "value": $(this).val()
                        }
                    },
                }).done(function(data) {
                    var response = $.parseJSON(data);
                    if (response.status == "success") {
                        // $('#myElement').animate({backgroundColor: '#FF0000'}, 'slow');
                        el.attr('style', 'background-color: #8cff8c !important').animate({
                            "backgroundColor": "rgba(255, 255, 255, 0.0) !important"
                        }, 1000);;
                    } else if (response.status == "error") {
                        alert(response.message);
                    } else {
                        alert("NO FEEDBACK FOUND");
                    }
                    // $(this).addClass("done");
                });
            })


            $(".alert").delay(2000).slideUp(300)
            $('.artist-selector').change((function() {
                // alert($(this).val());
                window.location.replace("<?= base_url(); ?>recipe-library?&artist_id=" + $(this).val());
            }))

            function formatState(state) {
                console.log(state.text);
                var str_arr = state.text.split('|');
                var $state = $(
                    '<div>' + str_arr[0] + '</div>' + '<div style="font-size:10px; color:#999">' + str_arr[1] + '</div>'
                );
                return $state;

            }

            $('.artist-selector').select2({
                height: 100,
                templateResult: formatState,
                templateSelection: formatState
            });

            var datasource = [];
            $.getJSON("<?= base_url(); ?>all-ingredients", function(data) {
                $.each(data, function(key, val) {
                    datasource.push(val.ingredient_id);
                });

                $('#tokenfield').on('tokenfield:createtoken', function(e) {
                    // console.log(e.attrs.value);
                    var valid = datasource.includes((e.attrs.value))
                    if (!valid) {
                        $(e.relatedTarget).addClass('invalid')
                    }
                    var existingTokens = $(this).tokenfield('getTokens');
                    $.each(existingTokens, function(index, token) {
                        console.log(token.value === e.attrs.value);
                        if (token.value === e.attrs.value)
                            e.preventDefault();
                    });
                })

                $('#tokenfield').tokenfield({
                    autocomplete: {
                        source: datasource,
                        delay: 100
                    },
                    showAutocompleteOnFocus: true,
                    delimiter: [' ']
                });
                // $('.list-ingredient').mark(datasource, {
                //     separateWordSearch: true,
                //     accuracy: "exactly"
                // });

            });

            var TagsDS = [];
            $.getJSON("<?= base_url(); ?>all-recipe-tags", function(data) {
                $.each(data, function(key, val) {
                    TagsDS.push(val);
                });



                $('.tags-tokenfield').tokenfield({
                    autocomplete: {
                        source: TagsDS,
                        delay: 100
                    },
                    showAutocompleteOnFocus: true,
                    delimiter: [' ']
                });

                $('.tags-tokenfield').on('tokenfield:createtoken', function(e) {
                    // console.log(e.attrs.value);
                    var valid = TagsDS.includes((e.attrs.value))
                    if (!valid) {
                        $(e.relatedTarget).addClass('invalid')
                    }

                    var existingTokens = $(this).tokenfield('getTokens');
                    $.each(existingTokens, function(index, token) {
                        console.log(token.value === e.attrs.value);
                        if (token.value === e.attrs.value)
                            e.preventDefault();
                    });
                })

                $('.tags-tokenfield').on('change', function(e) {

                    var el = $(this).closest('tr');

                    $.ajax({
                        url: "<?= base_url(); ?>update-recipe",
                        type: "POST",
                        data: {
                            "id": $(this).attr('recipe_id'),
                            "data": {
                                "category": 'tags',
                                "value": $(this).val()
                            }
                        },
                    }).done(function(data) {

                        el.attr('style', 'background-color: #8cff8c !important').animate({
                            "backgroundColor": "rgba(255, 255, 255, 0.0) !important"
                        }, 1000);
                    });

                    // alert('change');
                });

                // $('.list-ingredient').mark(TagsDS, {
                //     separateWordSearch: true,
                //     accuracy: "exactly"
                // });

            });

            $('.get-ingredient-btn').click(function() {
                console.log('execute');
                $('.ingredients').tokenfield('destroy');
                input = $('.raw-ingredient').val().toLowerCase().trim();
                arr_str = input.split(/\n/);
                str = ""
                units = ['gram', 'siung', 'sdm', 'kg', 'sdt', 'liter', 'ruas', 'jari', 'potong', 'butir', 'ml', 'gr', 'cm', 'pcs', 'pack', 'pak', 'lembar', 'buah', 'batang', 'lempeng', 'ekor', 'jempol', 'telunjuk']
                garbage = ['-', 'utuh', 'air', 'untuk', 'baluran', 'menggoreng', 'larutan', 'dan', 'bisa', 'di', 'tambahkan', 'ke', 'adonan', 'taburan', 'setengah',
                    'resep', 'cubit', 'genggam', 'cincang', 'cuci', 'bersih', 'boleh', 'secukupnya', 'menumis', 'geprek', 'sangrai', 'bagi', 'ukuran', 'sendok', 'makan'
                ]
                stopwords = units.concat(garbage);
                // stopwords = ['secukupnya']

                words = stopwords.join('|');
                regex = new RegExp('\\b(' + words + ')\\b', 'g');
                console.log(regex);
                arr_str.forEach(function(item, index, arr) {
                    item = item.replace(/  +/g, '').replace(/\s\s+/g, '').replace(/•|-|—/g, '').replace(/[0-9]/g, "").replace(/\//g, ' ').replace(/&/g, "").replace(/,/g, "").replace(/\+/g, "").replace(/ *\([^)]*\) */g, "").replace(":", "").trim();
                    item = item.replace(regex, '').trim();
                    item = item.replace('sereh', 'serai').replace('gula-pasir', 'gula').replace('cabai', 'cabe').replace('saos', 'saus').replace('merica', 'lada').replace('minyak-zaitun', 'olive-oil').replace('chili-powder', 'bubuk-cabe')
                    item = item.split(' ').join('-').trim()
                    item = item.replace('---', ' ')
                    str += item + " "
                })

                str = str.trim();
                $('.ingredients').val(str)

                $('.ingredients').tokenfield({
                    autocomplete: {
                        source: datasource,
                        delay: 100
                    },
                    showAutocompleteOnFocus: true,
                    delimiter: [' ']
                });





            });


        });
    </script>

</footer>