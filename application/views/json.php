<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Matthew
 * Date: 11/02/14
 * Time: 00:20
 * To change this template use File | Settings | File Templates.
 */
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode($json);