<?= $this->include('layout/header'); ?>

<body>
    <?= $this->include('layout/navigation'); ?>
    <main>

        <div class="col-12 container">
            <div><strong>Video Count: </strong><?= count($result); ?></div>
            <table class="table">
                <thead>
                    <th>id</th>
                    <th class="col-1">creator</th>
                    <th class="col-1">original name</th>
                    <th class="col-1">search alias</th>
                    <th class="col-1">description</th>
                    <th class="col-2">ingredients</th>
                    <th class="col-1">aspect_ratio</th>
                    <th class="col-2">tags</th>
                    <th class="col-1">preparation</th>
                    <th class="col-1">artist_id</th>
                    <th class="col-1">scraped</th>
                </thead>
                <?php foreach ($result as $r) {
                    echo "<tr>";
                    echo "<td class='the-id'> {$r->id}</td>";
                    echo "<td class='the-id'> {$r->artist}</td>";
                    echo "<td> {$r->title}</td>";
                    echo "<td> <a href=' " . base_url() . "recipe-editor?video_id=" . $r->video_id . "' target='_blank'>{$r->name}</a></td>";
                    echo "<td> <div class='list-ingredient'>{$r->description}</div></td>";
                    echo "<td> <div class='list-ingredient'>{$r->ingredients}</div></td>";
                    // echo "<td>";
                    // helper('form');
                    // $options = [
                    //     '' => 'Please Select',
                    //     'Indonesia'  => 'Indonesia',
                    //     'Chinese'    => 'Chinese',
                    //     'Barat'  => 'Barat',
                    //     'Jepang' => 'Jepang',
                    //     'Korea' => 'Korea',
                    //     'Indian' => 'India',
                    //     'Thai' => 'Thai'
                    // ];

                    // echo form_dropdown('region', $options, $r->region, ['class' => 'form-control input-dd', 'recipe_id' => $r->id, 'category' => 'region']);
                    // echo "</td>";
                    // echo "<td>";
                    // helper('form');
                    // $options = [
                    //     '' => 'Please Select',
                    //     'Goreng' => 'Goreng',
                    //     'Rebus' => 'Rebus',
                    //     'Bakar' => 'Bakar',
                    // ];
                    // echo form_dropdown('category', $options, $r->types, ['class' => 'form-control input-dd', 'recipe_id' => $r->id, 'category' => 'types']);
                    // echo "</td>";
                    // echo "<td>";
                    // helper('form');
                    // $options = [
                    //     '' => 'Please Select',
                    //     'Ayam' => 'Ayam',
                    //     'Sapi' => 'Sapi',
                    //     'Ikan' => 'Seafood',
                    //     'Sayur' => 'Sayur',
                    // ];
                    // echo form_dropdown('key_food', $options, $r->key_food, ['class' => 'form-control input-dd', 'recipe_id' => $r->id, 'category' => 'key_food']);
                    // echo "</td>";
                    echo "<td>";
                    if (isset($r->aspect_ratio)) {
                        helper('form');
                        $options = [
                            '' => 'Please Select',
                            'horizontal' => 'horizontal',
                            'vertical' => 'vertical',
                        ];

                        echo form_dropdown('aspect_ratio', $options, $r->aspect_ratio, ['class' => 'form-control input-dd', 'recipe_id' => $r->id, 'category' => 'aspect_ratio']);
                        echo "</td>";
                        echo "<td>";
                        echo '<input type="text" class="form-control tags-tokenfield" name="ingredients" recipe_id="' . $r->id . '" value="' . $r->tags . '" />';
                        echo "</td>";
                    }
                    echo "<td> {$r->preparation}</td>";
                    echo "<td> {$r->artist_id}</td>";
                    echo "<td> <h5><span class='badge " . ($r->yt_video_id != null ? 'bg-success' : 'bg-danger') . "'> &nbsp;&nbsp;&nbsp;&nbsp;</span></h5></td>";
                    echo "</tr>";
                } ?>

            </table>
        </div>
    </main>
    <br><br><br>
    <div class="container">
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            Reveal Raw Result
        </button>
        <div id="collapseExample">
            <div class="card card-body">
                <pre>
        <?php var_dump($result) ?>
        </pre>
            </div>
        </div>
    </div>

    <?= $this->include('layout/footer'); ?>

</body>

</html>