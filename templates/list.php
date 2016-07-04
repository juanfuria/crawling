<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.css"/>
    <link rel="stylesheet" href="css/base.css"/>
    <title>Sitemap for: <?= $this->url ?></title>
</head>
<body>
<h1>Sitemap for: <?= $this->url ?></h1>

<h2>Internal Links [<?= count($this->sitemap->internal) ?>]</h2>
<?php foreach ($this->sitemap->internal as $key => $section) { ?>
    <h2 class="ui header">
        <i class="large folder icon"></i>

        <div class="content">
            <?= $key ?>
            <div class="ui teal label"><?= count($section) ?> pages</div>
        </div>
    </h2>
    <div class="ui relaxed selection divided list">
        <?php foreach ($section as $url) { ?>
            <div class="item">
                <i class="large linkify middle aligned icon"></i>

                <div class="content">
                    <a class="header" href="<?= $url->URL ?>"><?= $url->URL ?></a>

                    <div class="description"><?= $url->title ?></div>
                </div>
            </div>
        <?php } ?>
        </div>
<?php } ?>

<h2>External Links [<?= count($this->sitemap->external) ?>]</h2>

<div class="ui middle aligned selection divided list">
    <?php foreach ($this->sitemap->external as $url) { ?>
            <div class="item">
                <i class="large external share middle aligned icon"></i>

                <div class="content">
                    <a class="header" href="<?= $url ?>"><?= $url ?></a>
                </div>
            </div>
        <?php } ?>
    </div>
<div class="clear">This crawl took <?= $this->duration ?> seconds.</div>
<?php
include('footer.html');
?>
</body>
</html>