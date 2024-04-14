<?= $this->include('layout/header'); ?>

<body>
    <?php $session = \Config\Services::session();

    if ($session->getFlashdata("submit-success") != null) { ?>
        <div id="success-alert" class="alert-fixed alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success Update/Insert Videos</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>
    <main>
        <?= $this->include('layout/navigation'); ?>

        <div class="container">

            <br>
            <div class="row">
                <div class=" card p-3 col-5 ">
                    <form action="<?= base_url() . 'recipe-youtube-submit' ?>" method="post" accept-charset="utf-8">
                        <input type="hidden" name="video_id" value="<?= $published->video_id ?>" class="form-control btn btn-primary" />
                        <h3>ORIGINAL</h3>
                        <br>
                        <div class=" form-group row">

                            <label for="inputTitle" class="col-sm-2 col-form-label">Title</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="title" id="inputTitle" rows="3"><?= $original->title ?></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="description" id="inputDescription" rows="30"><?= $original->description ?></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="inputDescription" class="col-sm-2 col-form-label">Video References</label>
                            <div class="col-sm-10">
                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/<?= $original->video_id ?>">
                                </iframe>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputDescription" class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-10">
                                <input type="submit" value="submit" class="form-control btn btn-primary" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-1"></div>


                <div class=" card p-3 col-5 m-1">
                    <form action="<?= base_url() . 'recipe-submit' ?>" method="post" accept-charset="utf-8">
                        <input type="hidden" name="artist_id" value="<?= $published->artist_id ?>" />
                        <input type="hidden" name="video_id" value="<?= $published->video_id ?>" class="form-control btn btn-primary" />
                        <div class="row">
                            <div class="col-9">
                                <h3>PUBLISHED</h3>
                            </div>

                        </div>

                        <br>
                        <div class="form-group row">

                            <label for="inputTitle" class="col-sm-2 col-form-label">Alias Title</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="name" id="inputTitle" rows="3"><?= $published->name ?>
                                </textarea>
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="inputDescription" class="col-sm-2 col-form-label">Summarized Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="instructions" id="inputDescription" rows="30"><?= $published->instructions ?>
                                </textarea>
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="inputTags" class="col-sm-2 col-form-label">Tags</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control tags-tokenfield" name="tags" recipe_id="<?= $published->video_id ?>" value="<?= $published->tags ?>" />
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="inputIngredient" class="col-sm-2 col-form-label">Raw Ingredient</label>
                            <div class="col-sm-10">
                                <textarea class="raw-ingredient form-control" name="" id="" cols="30" rows="10"></textarea>
                                <input class="get-ingredient-btn" type="button" value="Get Ingredient" />
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="inputIngredient" class="col-sm-2 col-form-label">Ingredient</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control ingredients" name="ingredients" id="tokenfield" value="<?= $published->ingredients ?>" />
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="inputDescription" class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-10">
                                <input type="submit" value="submit" class="form-control btn btn-primary" />
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>

            <br><br><br>

            <div class="row">
                <div class="col-3">

                </div>
            </div>

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
        </pre>
            </div>
        </div>
    </div>

    <?= $this->include('layout/footer'); ?>

</body>

</html>