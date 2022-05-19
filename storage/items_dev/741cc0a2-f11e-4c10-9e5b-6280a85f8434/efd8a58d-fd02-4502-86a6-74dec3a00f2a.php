<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';

$data = [
	'page_title' => "Best template engine of what you've seen",
	'logo_text_1' => "Доброго Вечора!",
	'logo_text_2' => "Ми З України!",
	'video_url' => "https://www.youtube.com/embed/BvgNgTPTkSo",
	'user_name' => "Neo",
	'quote_image' => "images/quote.jpg",
	'quote' => "Never send a human to do a machine's job"
];

renderPage($data);