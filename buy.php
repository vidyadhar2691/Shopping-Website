<!--
Name : Vidyadhar Angadiyavar
-->

<html>
<head><title>Buy Products</title></head>
<body>
<h2 align="center">Programming Assignment 3 PHP Scripting</h2>
<h4>Shopping Basket</h4>

<?php
session_start();
$sum = 0.0;

if (isset($_GET['delete']))  //Condition To check delete item from shopping cart
	{
	unset($_SESSION['cart'][$_GET['delete']]); 
	}

if (isset($_GET['id']))   //Insert item into cart
	{
	$id = $_GET['id'];
	$basket = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&productId=' . $id);
	$bas = new SimpleXMLElement($basket);
	if (empty($_SESSION['cart']))
		{
		$_SESSION['cart'] = array();
		}

	$_SESSION['cart'][$id] = array(
		"id" => (string)$bas->categories->category->items->product['id'],
		"prod_name" => (string)$bas->categories->category->items->product->name,
		"image" => (string)$bas->categories->category->items->product->images->image->sourceURL,
		"URL" => (string)$bas->categories->category->items->product->productOffersURL,
		"price" => (string)$bas->categories->category->items->product->minPrice
	);
	}

if (isset($_GET['clear'])) //Empty the whole cart
	{
	unset($_SESSION['cart']);
	}

if (!empty($_SESSION['cart'])) //Display the cart
	{
	echo "<table border=1>";
	echo "<tr>";
	echo "<td>&nbspWWW.SHOPPING.COM&nbsp</td><td>&nbspNAME</td><td>PRICE</td><td>DIDN'T LIKE ?</td>";
	echo "</tr>";
	foreach($_SESSION as $a)
		{
		foreach($a as $b)
			{
			echo "<tr>";
			echo "<td width='100'>";
			echo "<a href=" . $b['URL'] . 'target="_blank"><img src=' . $b['image'] . ">";
			echo "</a></td>";
			echo "<td width='300'>" . $b['prod_name'] . "</td>";
			echo "<td align=right width='50'>" . "$" . $b['price'] . "</td>";
			$sum = $sum + (float)$b['price'];
			echo "<td><a href=buy.php?delete=" . $b['id'] . ">REMOVE</a></td>";
			echo "</tr>";
			}
		}

	echo "</table>";
	}

echo "Total Amount:$" . $sum;
?>

<form action="buy.php" method="GET">
        <input type="hidden" name="clear" value="1" />
        <input type="submit" value="Empty Basket" />
</form>

<form method="get" action="<?php
echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<fieldset>
<legend>Go Shopping!!!</legend>
<label> Category:
<select name="category">

<?php
$category = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');
$cat = new SimpleXMLElement($category);
echo "<option value=" . $cat->category['id'] . ">" . $cat->category->name . "</option>";

foreach($cat->category->categories->category as $name) //Drop down menu
	{
	echo "<option value=" . $name['id'] . ">" . $name->name . "</option>";
	print '<optgroup label="' . $name->name . ':">';
	foreach($name->categories->category as $test)
		{
		echo "<option value=" . $test['id'] . ">" . $test->name . "</option>";
		}

	echo "</optgroup>";
	}

?>

</select>
</label>
Keyword:
<input type="text" name="keyword"/>
<input type="submit" name="submit" value="Submit">
</fieldset>
</form>

<?php
//Display the contents of searched query

if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
	if (isset($_GET["keyword"]))
		{
		$keyword = $_GET["keyword"];
		$category = $_GET["category"];
		echo "<table border=1>";
		echo "<tr><td>&nbspAdd To Cart&nbsp</td><td>&nbspNAME</td><td>PRICE</td><td>Description</td></tr>";
		$res = file_get_contents('http://sandbox.api.shopping.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&numItems=20&categoryId=' . $category . '&keyword=' . urlencode($keyword));
		$result = new SimpleXMLElement($res);
		foreach($result->categories->category->items->product as $prod)
			{
			echo "<tr>";
			echo "<td>";
			echo "<a href=buy.php?id=" . $prod['id'] . ">";
			echo "<img src=";
			echo $prod->images->image->sourceURL;
			echo "/>";
			echo "</a>";
			echo "</td>";
			echo "<td>";
			echo $prod->name;
			echo "</td>";
			echo "<td>$";
			echo $prod->minPrice;
			echo "</td>";
			echo "<td>";
			echo $prod->fullDescription;
			echo "</td>";
			echo "</tr>";
			}

		echo "</table>";
		}
	}

?>

</body>
</html>
