<?php
/**
 * Class to handle game categories
 */

class Category
{
	public $id = null;
	public $name = null;
	public $slug = null;

	public function __construct($data = array())
	{
		if (isset($data['id'])) $this->id = (int)$data['id'];
		if (isset($data['name'])) $this->name = htmlspecialchars($data['name']);
    	if ( isset( $data['slug'] ) ) {
    		$this->slug = htmlspecialchars(strtolower(str_replace(' ', '-', basename($data["slug"]))));
    	} else {
    		if ( isset( $data['name'] ) ) $this->slug = htmlspecialchars(strtolower(str_replace(' ', '-', basename($data["name"]))));
    	}
	}

	public function storeFormValues($params)
	{
		$this->__construct($params);
	}

	public static function getById($id)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM categories WHERE id = :id";
		$st = $conn->prepare($sql);
		$st->bindValue(":id", $id, PDO::PARAM_INT);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ($row) return new Category($row);
	}

	public static function getBySlug($slug)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM categories WHERE slug = :slug";
		$st = $conn->prepare($sql);
		$st->bindValue(":slug", $slug, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ($row) return new Category($row);
	}

	public static function getByName($name)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM categories WHERE name = :name";
		$st = $conn->prepare($sql);
		$st->bindValue(":name", $name, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ($row) return new Category($row);
	}

	public static function getIdByName($name)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM categories WHERE name = :name limit 1";
		$st = $conn->prepare($sql);
		$st->bindValue(":name", $name, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		return $row['id'];
	}

	public static function getIdBySlug($slug)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM categories WHERE slug = :slug limit 1";
		$st = $conn->prepare($sql);
		$st->bindValue(":slug", $slug, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if( $row ) {
			return $row['id'];
		} else {
			return null;
		}
	}

	public static function getList($numRows = 1000000)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM categories
			ORDER BY name ASC LIMIT :numRows";

		$st = $conn->prepare($sql);
		$st->bindValue(":numRows", $numRows, PDO::PARAM_INT);
		$st->execute();
		$list = array();

		while ($row = $st->fetch())
		{
			$category = new Category($row);
			$list[] = $category;
		}

		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch();
		$conn = null;
		return (array(
			"results" => $list,
			"totalRows" => $totalRows[0]
		));
	}

	public static function getCategoryCount($id)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM cat_links
			WHERE categoryid = :id LIMIT 10000";

		$st = $conn->prepare($sql);
		$st->bindValue(":id", $id, PDO::PARAM_INT);
		$st->execute();

		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch();
		$conn = null;
		return $totalRows['totalRows'];
	}

	public static function getListByCategory($id, $amount, $page = 0)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM cat_links WHERE categoryid = :id ORDER BY id DESC LIMIT :amount OFFSET :page";
		$st = $conn->prepare($sql);
		$st->bindValue(":id", $id, PDO::PARAM_INT);
		$st->bindValue(":amount", $amount, PDO::PARAM_INT);
		$st->bindValue(":page", $page, PDO::PARAM_INT);
		$st->execute();
		$list = array();
		$row = $st->fetchAll();
		foreach ($row as $item)
		{
			$game = new Game;
			$res = $game->getById($item['gameid']);
			array_push($list, $res);
		}
		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch();
		$conn = null;
		return (array(
			"results" => $list,
			"totalRows" => $totalRows[0],
			"totalPages" => ceil($totalRows[0] / $amount)
		));
	}

	public function addToCategory($gameID, $catID)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO cat_links ( gameid, categoryid ) VALUES ( :gameID, :catID )";
		$st = $conn->prepare($sql);
		$st->bindValue(":gameID", $gameID, PDO::PARAM_INT);
		$st->bindValue(":catID", $catID, PDO::PARAM_INT);
		$st->execute();
		$this->id = $conn->lastInsertId();
		$conn = null;
	}

	public function isCategoryExist($name)
	{
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = 'SELECT * FROM categories WHERE name = :name limit 1';
		$st = $conn->prepare($sql);
		$st->bindValue(":name", $name, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		if ($row)
		{
			$this->id = $row['id'];
		}
		$conn = null;
		if ($row)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function insert()
	{ 
		if (!is_null($this->id)) trigger_error("Category::insert(): Attempt to insert a Category object that already has its ID property set (to $this->id).", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO categories ( name, slug ) VALUES ( :name, :slug )";
		$st = $conn->prepare($sql);
		$st->bindValue(":name", $this->name, PDO::PARAM_STR);
		$st->bindValue(":slug", $this->slug, PDO::PARAM_STR);
		$st->execute();
		$this->id = $conn->lastInsertId();
		$conn = null;
	}

	public function update()
	{
		if (is_null($this->id)) trigger_error("Category::update(): Attempt to update a Category object that does not have its ID property set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE categories SET name=:name WHERE id = :id";
		$st = $conn->prepare($sql);
		$st->bindValue(":name", $this->name, PDO::PARAM_STR);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}

	public function delete()
	{
		if (is_null($this->id)) trigger_error("Category::delete(): Attempt to delete a Category object that does not have its ID property set.", E_USER_ERROR);

		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$st = $conn->prepare("DELETE FROM categories WHERE id = :id LIMIT 1");
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}

}

?>
