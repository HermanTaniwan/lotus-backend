<?= $this->include('layout/header'); ?>

<body>
    <?= $this->include('layout/navigation'); ?>
    <main>

        <br>
        <div class="col-12 container">
            <table class="table">
                <thead>
                    <th class="col-1">id</th>
                    <th class="col-2">name</th>
                    <th class="col-3">ingredients</th>
                    <th class="col-1">region</th>
                    <th class="col-1">preparation</th>
                    <th class="col-1">artist_id</th>
                    <th></th>
                </thead>
                <?php foreach ($result as $r) {
                    echo "<tr>";
                    echo "<td> {$r->id}</td>";
                    echo "<td> <a href='http://youtube.com/watch?v={$r->video_id}' target='_blank'>{$r->name}</a></td>";
                    echo "<td> {$r->ingredients}</td>";
                    echo "<td> {$r->region}</td>";
                    echo "<td> {$r->preparation}</td>";
                    echo "<td> {$r->artist_id}</td>";
                    echo "<td> <a target='_blank' href='" . base_url() . "recipe-editor?video_id=$r->video_id'>edit</a>";
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