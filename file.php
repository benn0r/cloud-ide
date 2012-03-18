<?php

header('Content-Type: text/plain; charset=utf-8');
echo utf8_encode(file_get_contents($_GET['file']));