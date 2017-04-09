<?php

namespace App\UserBundle\Services;

use Symfony\Component\Config\Definition\Exception\Exception;
use App\UserBundle\Services\dateService;

class textService {

	protected $mode_date;

	public function __construct(dateService $mode_date) {
		$this->mode_date = $mode_date;
	}

	public function replace_text($message){

		// bb tag
		$patternB = "/\[b\](.*?)\[\/b\]/s";
		$message = preg_replace($patternB, "<strong>$1</strong>", $message);
		$patternB = "/\[i\](.*?)\[\/i\]/s";
		$message = preg_replace($patternB, "<em>$1</em>", $message);
		$patternB = "/\[u\](.*?)\[\/u\]/s";
		$message = preg_replace($patternB, "<span class='bbunderline'>$1</span>", $message);
		$patternB = "/\[s\](.*?)\[\/s\]/s";
		$message = preg_replace($patternB, "<del>$1</del>", $message);
		$patternB = "/\[left\](.*?)\[\/left\]/s";
		$message = preg_replace($patternB, "<div class='bb' style='text-align: left'>$1</div>", $message);
		$patternB = "/\[right\](.*?)\[\/right\]/s";
		$message = preg_replace($patternB, "<div class='bb' style='text-align: right'>$1</div>", $message);
		$patternB = "/\[center\](.*?)\[\/center\]/s";
		$message = preg_replace($patternB, "<div class='bb' style='text-align: center'>$1</div>", $message);

		$how_spoiler = substr_count($message, "[spoiler");		
		$patternB = "/\[spoiler\](.*?)\[\/spoiler\]/s";
		for($i=0;$i<$how_spoiler;$i++)
			$message = preg_replace($patternB, "<div class='spoiler'><span class='sign'>+</span><span class='spoiler-name'> спойлер</span><div class='spoiler-body'>$1</div></div>", $message);
	
		$pattern_quote = "/(\[quote)(?: author=([a-zA-Z]+)?)?(?: date=([0-9.\ :]+[0-9]+)?)?(?: post=([0-9]+)?)?\]([\s\S]+)(\[\/quote\])/sUu";
		$how_quote = substr_count($message, "[quote");
		for($i=0;$i<$how_quote;$i++) {
			$message = preg_replace_callback($pattern_quote, function($matches) {
				# matches 1 open quote
				# matches 2 author
				# matches 3 date
				# matches 4 post id
				# matches 5 message
				# matches 6 close quote

				$start_quote = $matches[1];
				$content_quote = trim($matches[5]);
				$end_quote = $matches[6];

				if(!$matches[2] and !$matches[3]) {
					$result = "<div class='bookquote'>".$content_quote."</div>";
				} else {

					if($matches[2])
						$author = $matches[2];
					else
						$author = "";

					if($matches[3]) {

						$date = $this->mode_date->replace_date($matches[3]);

						if($matches[2])
							$date = ", ".$date;
					} else {
						$date = "";
					}

					if($matches[4]) {
						$post = $matches[4];
						$post = "<a href='/post/".$post."'>".$author.$date."</a>";
					} else {
						$post = $author.$date;
					}

					$result = "<div class='quote-author'>".$post."</div>
							   <div class='bookquote'>".$content_quote."</div>";

				}


				return $result;
			}, $message);
		}

		// links //
		$pattern = "/(https?\:\/\/)?([\.a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}(?:\/(?:[^\s\]\[\'\"\<\>]+)?)?)/ui";
		
		$message = preg_replace_callback($pattern, function($matches) {
			$linkfullpath = $matches[1].$matches[2];
			$http = $matches[1];
			$link = $matches[2];

			if(strlen($linkfullpath) > 60) {
				$first = substr($linkfullpath, 0, 40);
				$middle = "...";
				$end = substr($linkfullpath, -10);
				$modelink = $first.$middle.$end;
			} else {
				$modelink = $linkfullpath;
			}

			return "<a class='glink' target='_blank' href='http://{$link}'>{$modelink}</a>";
		}, $message);

		// smiles
		$patternS = "/(\:[a-z]+)/u";
		$message = preg_replace_callback($patternS, function($matches) {
			$smile = substr($matches[1], 1);
			$path = 'public/images/smiles/'.$smile.'.gif';
			$img_smile = "<img class='smile' src='/{$path}'>";
			if(file_exists($path))
				return $img_smile;
			else
				return $matches[1];
		}, $message);

		return $message;
	}

}
