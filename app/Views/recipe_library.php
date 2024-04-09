<?= $this->include('layout/header'); ?>

<body>
    <main>
        <?= $this->include('layout/navigation'); ?>
        <br><br>
        <div class="container">
            <h3 class="d-inline">Artist: &nbsp;</h3>
            <span class="d-inline">
                <?php
                helper('form');
                $options = [];
                foreach ($all_artist as $eachArtist) {
                    $options[$eachArtist->id] = $eachArtist->name;
                }

                echo form_dropdown('artist', $options, $artist->id, ['class' => 'form-control d-inline w-auto artist-selector']);
                ?>
            </span>
        </div>
        <br><br>
        <div class="container">
            <?= $pager_links ?>
        </div>
        <div class="col-12 container">
            <table class="table">
                <thead>
                    <th class="col-1">id</th>
                    <th class="col-5">title</th>
                    <th class="col-2">published_time</th>
                    <th>content_duration</th>
                    <th>published</th>
                </thead>
                <?php foreach ($result as $r) {
                    echo "<tr>";
                    echo "<td> {$r->id}</td>";
                    echo "<td> <a href='http://youtube.com/watch?v={$r->video_id}' target='_blank'>{$r->title}</a></td>";
                    echo "<td> {$r->published_time}</td>";
                    echo "<td> {$r->content_duration}</td>";
                    echo "<td> <h5><span class='badge " . ($r->published == 'yes' ? 'bg-success' : 'badge-not-success') . "'>{$r->published}</span></h5></td>";
                    echo "<td> <a target='_blank' href='" . base_url() . "recipe-editor?video_id=$r->video_id'>edit</a>";
                    echo "</tr>";
                } ?>

            </table>
        </div>
        <div class="container">
            <?= $pager_links ?>
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