<?php
include('../../inc/Autoloader.php');
session_start();

header('Content-Type: application/json;');

use Mapper\ReactionMapper;
use Model\Reaction;
use Model\Comment;
use Mapper\CommentMapper;
$action = $_POST['action'] ?? null;
if (!array_key_exists('user', $_SESSION) || $_SESSION['user'] === NULL) {
    header('Location: login.php');
    exit();
}

$output = array("error" => false);
$user = $_SESSION['user'];
$userID = $user->getUserId();
switch($action){
    case 'upvote':
		$pictureID = $_POST['PictureID'] ?? null;
        $reaction = new Reaction(null, $userID, $pictureID, "UPVOTE",null);
        ReactionMapper::add($reaction);
        break;
    case 'downvote':
        $pictureID = $_POST['PictureID'] ?? null;
        $reaction = new Reaction(null, $userID, $pictureID, "DOWNVOTE",null);
        ReactionMapper::add($reaction);
        break;
	case 'addcomment':
		$pictureID = $_POST['PictureID'] ?? null;
		$content = $_POST['comment'] ?? null;
		$comment = new Comment(null, $userID, $pictureID, $content, null);
		CommentMapper::add($comment);
}

echo json_encode($output);
