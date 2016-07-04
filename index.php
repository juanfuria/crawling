<?php

include_once('php/Constants.php');
include_once('php/Template.php');
include_once('php/URLUtils.php');
include_once('php/Crawler.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = array();

    if (!isset($_POST[URL_FIELD]) || URLUtils::validateURL($_POST[URL_FIELD]) == false) {
        include_once('php/Errors.php');

        $errors[] = ERROR_WRONG_URL_MESSAGE;
    }

    if (count($errors) == 0) {
        $crawler = new Crawler($_POST[URL_FIELD], MAX_DEPTH);
        $crawler->crawl();

        $page_template = new Template(TEMPLATE_DIR . '/list.php');
        $page_template->url = $_POST[URL_FIELD];
        $page_template->sitemap = $crawler->sitemap;

        echo $page_template;
    } else {
        print_r($errors);
        renderFormTemplate($errors);
    }
} else {
    renderFormTemplate();
}

function renderFormTemplate($errors = array())
{
    $page_template = new Template(TEMPLATE_DIR . '/form.php');
    $page_template->errors = $errors;

    echo $page_template;
}






