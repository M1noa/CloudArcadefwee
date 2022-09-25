<?php
class Game
{
	public $id = null;
	public $createdDate = null;
	public $title = null;
	public $description = null;
	public $instructions = null;
	public $category = null;
	public $source = null;
	public $thumb_1 = null;
	public $thumb_2 = null;
	public $url = null;
	public $width = null;
	public $height = null;
	public $tags = null;
	public $views = null;
	public $upvote = null;
	public $downvote = null;
	public $slug = null;

	public function __construct($data = array())
	{
		if (isset($data['id'])) $this->id = (int)$data['id'];
		if (isset($data['createdDate'])) $this->createdDate = (int)$data['createdDate'];
		if (isset($data['title'])) $this->title = htmlspecialchars($data['title']);
		if (isset($data['description'])) $this->description = htmlspecialchars($data['description']);
		if (isset($data['instructions'])) $this->instructions = htmlspecialchars($data['instructions']);
		if (isset($data['category'])) $this->category = $data['category'];
		if (isset($data['source'])) $this->source = $data['source'];
		if (isset($data['thumb_1'])) $this->thumb_1 = $data['thumb_1'];
		if (isset($data['thumb_2'])) $this->thumb_2 = $data['thumb_2'];
		if (isset($data['url'])) $this->url = $data['url'];
		if (isset($data['width'])) $this->width = $data['width'];
		if (isset($data['height'])) $this->height = $data['height'];
		if (isset($data['tags'])) $this->tags = $data['tags'];
		if (isset($data['views'])) $this->views = $data['views'];
		if (isset($data['upvote'])) $this->upvote = $data['upvote'];
		if (isset($data['downvote'])) $this->downvote = $data['downvote'];
		if (isset($data['slug'])){
			$this->slug = strtolower(str_replace(' ', '-', basename($data["slug"])));
		} else {
			if (isset($data['title'])) $this->slug = strtolower(str_replace(' ', '-', basename($data["title"])));
		}
	}

	public function storeFormValues($params)
	{
		$this->__construct($params);
		$this->createdDate = date('Y-m-d H:i:s');
		// Parse and store the publication date
		if (isset($params['cratedDate']))
		{

			/*if ( count($createdDate) == 3 ) {
			list ( $y, $m, $d ) = $createdDate;
			$this->createdDate = mktime ( 0, 0, 0, $m, $d, $y );
			}*/
		}
	}

	public static function getById($id)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT *, UNIX_TIMESTAMP(createdDate) AS createdDate FROM games WHERE id = :id limit 1";
		$st = $conn->prepare($sql);
		$st->bindValue(":id", $id, PDO::PARAM_INT);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ($row) return new Game($row); //$row
	}

	public static function getByTitle($title)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = 'SELECT * FROM games WHERE title = :title';
		$st = $conn->prepare($sql);
		$st->bindValue(":title", $title, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ($row) return new Game($row);
	}

	public static function getBySlug($slug)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = 'SELECT * FROM games WHERE slug = :slug';
		$st = $conn->prepare($sql);
		$st->bindValue(":slug", $slug, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ($row) return new Game($row);
	}

	public static function getList($amount = 1000, $sort, $page = 0)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM games
			ORDER BY " . $sort . " LIMIT :amount OFFSET :page";

		$st = $conn->prepare($sql);
		$st->bindValue(":amount", $amount, PDO::PARAM_INT);
		$st->bindValue(":page", $page, PDO::PARAM_INT);
		$st->execute();
		$list = array();

		while ($row = $st->fetch())
		{
			$games = new Game($row);
			$list[] = $games;
		}

		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch();
		$conn = null;
		$totalPages = 0;
		if (count($list))
		{
			$totalPages = ceil($totalRows[0] / $amount);
		}
		return (array(
			"results" => $list,
			"totalRows" => $totalRows[0],
			"totalPages" => $totalPages
		));
	}

	public static function searchGame($keyword, $amount = 20, $page = 0){
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM games WHERE title LIKE :keyword
			ORDER BY id DESC LIMIT :amount OFFSET :page";

		$st = $conn->prepare($sql);
		$st->bindValue(":amount", $amount, PDO::PARAM_INT);
		$st->bindValue(":page", $page, PDO::PARAM_INT);
		$st->bindValue(":keyword", '%'. $keyword .'%', PDO::PARAM_STR);
		$st->execute();
		$list = array();

		while ($row = $st->fetch())
		{
			$games = new Game($row);
			$list[] = $games;
		}

		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch();
		$conn = null;
		$totalPages = 0;
		if (count($list))
		{
			$totalPages = ceil($totalRows[0] / $amount);
		}
		
		return (array(
			"results" => $list,
			"totalRows" => $totalRows[0],
			"totalPages" => $totalPages
		));
	}

	public function update_views($slug)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = 'UPDATE games SET views = views + 1 WHERE slug = :slug';
		$st = $conn->prepare($sql);
		$st->bindValue(":slug", $slug, PDO::PARAM_STR);
		$st->execute();
		$conn = null;
	}

	public function insert()
	{
		if (!is_null($this->id)) trigger_error("Game::insert(): Attempt to insert an Game object that already has its ID property set (to $this->id).", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = 'INSERT INTO games ( createdDate, title, description, instructions, category, source, thumb_1, thumb_2, url, width, height, tags, slug, views, upvote, downvote, data ) 
				  VALUES ( :createdDate, :title, :description, :instructions, :category, :source, :thumb_1, :thumb_2, :url, :width, :height, :tags, :slug, 0, 0, 0, "" )';
		$st = $conn->prepare($sql);
		$st->bindValue(":createdDate", $this->createdDate, PDO::PARAM_STR);
		$st->bindValue(":title", $this->title, PDO::PARAM_STR);
		$st->bindValue(":description", $this->description, PDO::PARAM_STR);
		$st->bindValue(":instructions", $this->instructions, PDO::PARAM_STR);
		$st->bindValue(":category", $this->category, PDO::PARAM_STR);
		$st->bindValue(":source", $this->source, PDO::PARAM_STR);
		$st->bindValue(":thumb_1", $this->thumb_1, PDO::PARAM_STR);
		$st->bindValue(":thumb_2", $this->thumb_2, PDO::PARAM_STR);
		$st->bindValue(":url", $this->url, PDO::PARAM_STR);
		$st->bindValue(":width", $this->width, PDO::PARAM_STR);
		$st->bindValue(":height", $this->height, PDO::PARAM_STR);
		$st->bindValue(":tags", $this->tags, PDO::PARAM_STR);
		$st->bindValue(":slug", $this->slug, PDO::PARAM_STR);
		$st->execute();
		$this->id = $conn->lastInsertId();
		$conn = null;
	}

	public function update()
	{
		if (is_null($this->id)) trigger_error("Game::update(): Attempt to update an Game object that does not have its ID property set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE games SET createdDate=FROM_UNIXTIME(:createdDate),
							 title=:title, description=:description, 
							 instructions=:instructions WHERE id = :id";

		$st = $conn->prepare($sql);
		$st->bindValue(":createdDate", $this->createdDate, PDO::PARAM_INT);
		$st->bindValue(":title", $this->title, PDO::PARAM_STR);
		$st->bindValue(":description", $this->summary, PDO::PARAM_STR);
		$st->bindValue(":instructions", $this->content, PDO::PARAM_STR);
		$st->bindValue(":category", $this->category, PDO::PARAM_STR);
		$st->bindValue(":source", $this->source, PDO::PARAM_STR);
		$st->bindValue(":thumb_1", $this->thumb_1, PDO::PARAM_STR);
		$st->bindValue(":thumb_2", $this->thumb_2, PDO::PARAM_STR);
		$st->bindValue(":url", $this->url, PDO::PARAM_STR);
		$st->bindValue(":width", $this->width, PDO::PARAM_STR);
		$st->bindValue(":height", $this->height, PDO::PARAM_STR);
		$st->bindValue(":tags", $this->tags, PDO::PARAM_STR);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}

	public function delete()
	{
		if (is_null($this->id)) trigger_error("Game::delete(): Attempt to delete an Game object that does not have its ID property set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$st = $conn->prepare("DELETE FROM games WHERE id = :id LIMIT 1");
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();

		$st = $conn->prepare("DELETE FROM cat_links WHERE gameid = :id");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
		if ($this->source == 'self') // Remove game files
		{
			$src = '..' . $this->url;
			$this->remove_game_folder($src);
		}
		if(substr($this->thumb_1, 0, 8) == '/thumbs/'){ // Remove thumbnail files
			unlink('..'.$this->thumb_1);
		}
		if(substr($this->thumb_2, 0, 8) == '/thumbs/'){ // Remove thumbnail files
			unlink('..'.$this->thumb_2);
		}
	}
	public function remove_game_folder($dir)
	{
		if (is_null($this->id)) trigger_error("Does not have its ID property set.", E_USER_ERROR);
		if (is_dir($dir))
		{
			$files = scandir($dir);
			foreach ($files as $file) if ($file != "." && $file != "..") $this->remove_game_folder("$dir/$file");
			rmdir($dir);
		}
		else if (file_exists($dir)) unlink($dir);
	}
}

?>
