<?php

function set_error( $error_code ){
    switch ($error_code) {
        case 400:
            $return_response['status_code'] = 'HTTP/1.1 400 Bad request';
            $return_response['body'] = '';
            break;
        case 406:
            $return_response['status_code'] = 'HTTP/1.1 406 Not acceptable';
            $return_response['body'] = '';
            break;
        default:
            $return_response['status_code'] = 'HTTP/1.1 400 bad request';
            $return_response['body'] = '';
            break;
    }

    return $return_response;
}


try {
    $db = new PDO("mysql:host=localhost;dbname=cloud4", 'root', '');
    $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
    $db->query("SET NAMES utf8");
} catch(PDOException $e) {
    header('HTTP/1.1 503 Server unavailable');
    echo json_encode("Server unavailable. Database error.");
    exit(0);
}


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// toate apelurile trebuie sa inceapa cu /cloud
if ($uri[1] !== 'cloud' && $uri[2] !== 'books') {
    header("HTTP/1.1 404 Not Found");
    exit();
}


$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        $book_id = null;
        if ( isset($uri[3]) ) {
            $book_id = (int) $uri[3]; // with slash
        } elseif ( isset($_GET['id']) ) {
            $book_id = (int) $_GET['id']; // with params
        }
        if ( $book_id ) {
            $response['status_code'] = 'HTTP/1.1 200 OK';
            $books = $db->query("SELECT * FROM books WHERE id='" . $book_id . "';")->fetchAll(PDO::FETCH_ASSOC);
            $response['body'] = json_encode($books);
        } else {
            $response['status_code'] = 'HTTP/1.1 200 OK';
            $books = $db->query("SELECT * FROM books ORDER BY name;")->fetchAll(PDO::FETCH_ASSOC);
            $response['body'] = json_encode($books);
        }
        break;

    case 'POST':
        if ( isset($_POST['name']) && trim($_POST['name']) != '' // with form-data
            && isset($_POST['pages']) && trim($_POST['pages']) != ''
            && isset($_POST['author']) && trim($_POST['author']) != ''
            && isset($_POST['type']) && trim($_POST['type']) != '') {

            $response['status_code'] = 'HTTP/1.1 200 OK';
            $books = $db->exec("INSERT INTO books (name, author, pages, type) VALUES ('" . $_POST['name'] . "','" . $_POST['author'] . "',
                                                                                            '" . $_POST['pages'] . "','" . $_POST['type'] . "');");
            $response['body'] = json_encode('Book added to collection.');
        }elseif ( isset($_GET['name']) && trim($_GET['name']) != '' // with params
            && isset($_GET['pages']) && trim($_GET['pages']) != ''
            && isset($_GET['author']) && trim($_GET['author']) != ''
            && isset($_GET['type']) && trim($_GET['type']) != '') {

            $response['status_code'] = 'HTTP/1.1 200 OK';
            $books = $db->exec("INSERT INTO books (name, author, pages, type) VALUES ('".$_GET['name']."','".$_GET['author']."',
                                                                                            '".$_GET['pages']."','".$_GET['type']."');");
            $response['body'] = json_encode('Book added to collection.');

        } else {
            $response = set_error(406);
        }

        break;

    case 'PUT':
        // with params
        if ( ( isset($_GET['id']) && trim($_GET['id'] != '') )
           ) {

            if ( isset($_GET['id']) && trim($_GET['id']) != '' ) {
                $book_id = (int)trim( $_GET['id'] );
            }

            $sql_update = '';
            if ( isset($_GET['name']) && trim($_GET['name']) != '' ) {
                $sql_update .= ", name='".trim( $_GET['name'] )."' ";
            }
            if ( isset($_GET['author']) && trim($_GET['author']) != '' ) {
                $sql_update .= ", author='".trim( $_GET['author'] )."' ";
            }
            if ( isset($_GET['pages']) && trim($_GET['pages']) != '' ) {
                $sql_update .= ", pages='".trim( $_GET['pages'] )."' ";
            }
            if ( isset($_GET['type']) && trim($_GET['type']) != '' ) {
                $sql_update .= ", type='".trim( $_GET['type'] )."' ";
            }
            if ( $sql_update != '' ) {
                $sql = "UPDATE books SET id=".$book_id." ".$sql_update." WHERE id=".$book_id.";";
                $db->exec( $sql );
            }
            $response['status_code'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode('Book with id='.$book_id.' updated.');
        } else {
            $response = set_error(406);
        }
        break;


    case 'DELETE':
        // with slash
        if ( isset($uri[3]) ) {
            $sql = "DELETE FROM books WHERE id=".(int)$uri[3].";";
            $db->exec( $sql );
            $response['status_code'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode('Book with id='.$uri[3].' deleted.');
        }elseif ( ( isset($_GET['id']) && trim($_GET['id'] != '') ) ) {  // with params (by id)

            if ( isset($_GET['id']) && trim($_GET['id']) != '' ) {
                $book_id = (int)trim( $_GET['id'] );
            }

            $sql = "DELETE FROM books WHERE id=".$book_id.";";
            $db->exec( $sql );
            $response['status_code'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode('Book with id='.$book_id.' deleted.');
        }elseif ( ( isset($_GET['type']) && trim($_GET['type'] != '') ) ) { // with params (by type)

            if ( isset($_GET['type']) && trim($_GET['type']) != '' ) {
                $book_type = trim( $_GET['type'] );
            }

            $sql = "DELETE FROM books WHERE type='".$book_type."';";
            $db->exec( $sql );
            $response['status_code'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode('Books with type='.$book_type.' deleted.');
        }
        else {
            $response = set_error(406);
        }
        break;

error_def:
    default:
        set_error(400);
        break;
}


header($response['status_code']);
if ($response['body']) {
    echo $response['body'];
}
