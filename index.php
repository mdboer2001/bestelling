<?php
session_start();
require_once("dbconfig.php");
$db_handle = new DBController();
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            if (!empty($_POST["quantity"])) {
                $productByCode = $db_handle->runQuery("SELECT * FROM products WHERE code='" . $_GET["code"] . "'");
                $itemArray =  array(
                    'product_id' => $productByCode[0]["id"],
                    'name' => $productByCode[0]["name"],
                    'code' => $productByCode[0]["code"],
                    'quantity' => $_POST["quantity"],
                    'size' => $_POST["size"],
                    'price' => $productByCode[0]["price"],
                    'image' => $productByCode[0]["image"]
                );

                if (!empty($_SESSION["cart_item"])) {
                    $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], array($itemArray));
                } else {
                    $_SESSION["cart_item"] = array($itemArray);
                }
            }
            break;
        case "remove":
            if (!empty($_SESSION["cart_item"])) {
                unset($_SESSION["cart_item"][$_GET["code"]]);
                $_SESSION["cart_item"] = array_values($_SESSION["cart_item"]);
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            break;
    }
}

?>
<html>
<head>
    <title>Pizza Sopranos - Bestellingen</title>
    <link rel="icon" href="Logodesktop.svg">
    <link href="style.css" type="text/css" rel="stylesheet"/>
</head>
<body>
<pre>
    <?php var_dump($_SESSION["cart_item"]); ?>
</pre>

<div id="shopping-cart">
    <div class="txt-heading">Shopping Cart</div>

    <a id="btnOrder" href="order.php">Bestel</a>
    <a id="btnEmpty" href="index.php?action=empty">Leeg winkelmandje</a>
    <?php
    if (isset($_SESSION["cart_item"])) {
        $total_quantity = 0;
        $total_price = 0;
        ?>
        <table class="tbl-cart" cellpadding="10" cellspacing="1">
            <tbody>
            <tr>
                <th style="text-align:left;">Name</th>
                <th style="text-align:left;">Code</th>
                <th style="text-align:right;" width="5%">Size</th>
                <th style="text-align:right;" width="5%">Quantity</th>
                <th style="text-align:right;" width="5%">Unit Price</th>
                <th style="text-align:right;" width="10%">Price</th>
                <th style="text-align:center;" width="5%">Remove</th>
            </tr>
            <?php
            $items = $_SESSION["cart_item"];
            for($i = 0; $i < sizeof($items); $i++) {
                $korting = false;
                if($i > 0) {
                    $korting = true;
                }

                $price = $korting ? $items[$i]["price"] * 0.5 : $items[$i]["price"];
                $item_price = $items[$i]["quantity"] * $price;
                ?>
                <tr>
                    <td><img src="<?php echo $items[$i]["image"]; ?>" class="cart-item-image"/><?php echo $items[$i]["name"]; ?>
                    </td>
                    <td><?php echo $items[$i]["code"]; ?></td>
                    <td style="text-align:right;"><?php echo $items[$i]["size"]; ?></td>
                    <td style="text-align:right;"><?php echo $items[$i]["quantity"]; ?></td>
                    <td style="text-align:right;"><?php echo "$ " . $items[$i]["price"]; ?></td>
                    <td style="text-align:right;"><?php echo "$ " . number_format($item_price, 2); ?></td>
                    <td style="text-align:center;"><a href="index.php?action=remove&code=<?php echo $i; ?>" class="btnRemoveAction"><img src="icon-delete.png"
                                                                                   alt="Remove Item"/></a></td>
                </tr>
                <?php
                $total_quantity += $items[$i]["quantity"];
                $total_price += ($items[$i]["price"] * $items[$i]["quantity"]);
            }
            ?>

            <tr>
                <td colspan="3" align="right">Total:</td>
                <td align="right"><?php echo $total_quantity; ?></td>
                <td align="right" colspan="2"><strong><?php echo "$ " . number_format($total_price, 2); ?></strong></td>
                <td></td>
            </tr>
            </tbody>
        </table>
        <?php
    } else {
        ?>
        <div class="no-records">Uw mandje is op het moment leeg!</div>
        <?php
    }
    ?>
</div>

<div id="product-grid">
    <div class="txt-heading">Menu's</div>
    <?php
    $product_array = $db_handle->runQuery("SELECT * FROM products ORDER BY id ASC");
    if (!empty($product_array)) {
        foreach ($product_array as $product) {
            ?>
            <div class="product-item">
                <form method="post" action="index.php?action=add&code=<?php echo $product["code"]; ?>">
                    <div class="product-image">
                        <img class="product-image" src="<?php echo $product["image"]; ?>">
                    </div>
                    <div class="product-tile-footer">
                        <div class="product-title"><?php echo $product["name"]; ?></div>
                        <div class="product-price"><?php echo "€" . $product["price"]; ?></div>
                        <div class="cart-action">
                            <select name="quantity">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                            <select name="size">
                                <option value="medium">Medium (25 cm)</option>
                                <option value="large">Large (35 cm)</option>
                                <option value="calzone">Calzone (opgevouwen)</option>
                            </select>
                            <input type="submit" value="Add to Cart" class="btnAddAction"/>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }
    }
    ?>
</div>
</body>
</html>