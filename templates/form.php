<?php
include_once('../php/Constants.php');
include_once('../php/Errors.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.css"/>
    <link rel="stylesheet" href="css/base.css"/>

    <script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/semantic.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.1/components/form.js"></script>

    <title><?= PAGE_TITLE ?></title>
</head>
<body>
<div class="ui segment">
    <h2 class="ui header">Webcrawler demo</h2>

    <form class="ui form" method="post" id="url_form">
        <div class="ui error message">
            <ul class="list">
                <?php foreach ($this->errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="ui fluid action input">
            <input type="text" name="<?= URL_FIELD ?>" placeholder="<?= EXAMPLE_URL ?>"/>
            <button type="submit" class="ui button blue">Crawl</button>
        </div>
    </form>
    <script>
        $('#url_form')
            .form({
                on: 'blur',
                fields: {
                    url: {
                        identifier: '<?=URL_FIELD?>',
                        rules: [
                            {
                                type: 'url',
                                prompt: '<?=ERROR_WRONG_URL_MESSAGE?>'
                            }
                        ]
                    }
                }
            });
    </script>
</div>
<?php
include('footer.html');
?>
</body>
</html>