<footer>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="<?= base_url(); ?>asset/plugins/sliptree-bootstrap-tokenfield-v0.12.1/sliptree-bootstrap-tokenfield-ff5b929/dist/bootstrap-tokenfield.min.js"></script>
    <!-- <script src="<?= base_url(); ?>" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script> -->

    <script>
        $(function() {
            $(".alert").delay(2000).slideUp(300)
            $('.artist-selector').change((function() {
                // alert($(this).val());
                window.location.replace("<?= base_url(); ?>recipe-library?&artist_id=" + $(this).val());
            }))

            var datasource = [];
            $.getJSON("<?= base_url(); ?>all-ingredients", function(data) {
                $.each(data, function(key, val) {
                    datasource.push(val.ingredient_id);
                });

                $('#tokenfield').on('tokenfield:createdtoken', function(e) {
                    // console.log(e.attrs.value);
                    var valid = datasource.includes((e.attrs.value))
                    if (!valid) {
                        $(e.relatedTarget).addClass('invalid')
                    }
                })

                $('#tokenfield').tokenfield({
                    autocomplete: {
                        source: datasource,
                        delay: 100
                    },
                    showAutocompleteOnFocus: true,
                    delimiter: [' ']
                });

            });

            $('.get-ingredient-btn').click(function() {
                console.log('execute');
                $('.ingredients').tokenfield('destroy');
                input = $('.raw-ingredient').val().toLowerCase().trim();
                arr_str = input.split(/\n/);
                str = ""
                units = ['gram', 'siung', 'sdm', 'kg', 'sdt', 'liter', 'ruas', 'jari', 'potong', 'butir', 'ml', 'gr', 'cm', 'pcs', 'pack', 'pak', 'lembar', 'buah', 'batang', 'lempeng', 'ekor', 'jempol', 'telunjuk']
                garbage = ['-', 'utuh', 'air', 'untuk', 'baluran', 'menggoreng', 'larutan', 'dan', 'bisa', 'di', 'tambahkan', 'ke', 'adonan', 'taburan', 'setengah',
                    'resep', 'cubit', 'genggam', 'cincang', 'cuci', 'bersih', 'boleh', 'secukupnya', 'menumis', 'geprek', 'sangrai', 'bagi', 'ukuran'
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