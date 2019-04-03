<?php
session_start();
require_once("dbconfig.php");
$db_handle = new DBController();
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            if (!empty($_POST["quantity"])) {
                $productByCode = $db_handle->runQuery("SELECT * FROM products WHERE code='" . $_GET["code"] . "'");
                $itemArray = array(
                    $productByCode[0]["code"] => array(
                        'name' => $productByCode[0]["name"],
                        'code' => $productByCode[0]["code"],
                        'quantity' => $_POST["quantity"],
                        'price' => $productByCode[0]["price"],
                        'image' => $productByCode[0]["image"]
                    )
                );

                if (!empty($_SESSION["cart_item"])) {
                    if (in_array($productByCode[0]["code"], array_keys($_SESSION["cart_item"]))) {
                        foreach ($_SESSION["cart_item"] as $k => $v) {
                            if ($productByCode[0]["code"] == $k) {
                                if (empty($_SESSION["cart_item"][$k]["quantity"])) {
                                    $_SESSION["cart_item"][$k]["quantity"] = 0;
                                }
                                $_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
                            }
                        }
                    } else {
                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                    }
                } else {
                    $_SESSION["cart_item"] = $itemArray;
                }
            }
            break;
        case "remove":
            if (!empty($_SESSION["cart_item"])) {
                foreach ($_SESSION["cart_item"] as $k => $v) {
                    if ($_GET["code"] == $k)
                        unset($_SESSION["cart_item"][$k]);
                    if (empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            break;
    }
}

if (isset($_SESSION["cart_item"])) {
    for ($i = 0; $i < sizeof($_SESSION["cart_item"]); $i++) {
        if ($i == 0) {
            echo $_SESSION["cart_item"]["Menu-1"]["price"];
            echo $i . "hello world";
        } else {
            echo $i . "bye world";
        }
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
                <th style="text-align:right;" width="5%">Quantity</th>
                <th style="text-align:right;" width="10%">Unit Price</th>
                <th style="text-align:right;" width="10%">Price</th>
                <th style="text-align:center;" width="5%">Remove</th>
            </tr>
            <?php
            foreach ($_SESSION["cart_item"] as $item) {
                $item_price = $item["quantity"] * $item["price"];
                ?>
                <tr>
                    <td><img src="<?php echo $item["image"]; ?>" class="cart-item-image"/><?php echo $item["name"]; ?>
                    </td>
                    <td><?php echo $item["code"]; ?></td>
                    <td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
                    <td style="text-align:right;"><?php echo "$ " . $item["price"]; ?></td>
                    <td style="text-align:right;"><?php echo "$ " . number_format($item_price, 2); ?></td>
                    <td style="text-align:center;"><a href="index.php?action=remove&code=<?php echo $item["code"]; ?>"
                                                      class="btnRemoveAction"><img src="icon-delete.png"
                                                                                   alt="Remove Item"/></a></td>
                </tr>
                <?php
                $total_quantity += $item["quantity"];
                $total_price += ($item["price"] * $item["quantity"]);
            }


            ?>

            <tr>
                <td colspan="2" align="right">Total:</td>
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
        foreach ($product_array as $key => $value) {
            ?>
            <div class="product-item">
                <form method="post" action="index.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>">
                    <div class="product-image"><img class="product-image"
                                                    src="<?php echo $product_array[$key]["image"]; ?>"></div>
                    <div class="product-tile-footer">
                        <div class="product-title"><?php echo $product_array[$key]["name"]; ?></div>
                        <div class="product-price"><?php echo "$" . $product_array[$key]["price"]; ?></div>
                        <div class="cart-action"><input type="text" class="product-quantity" name="quantity" value="1"
                                                        size="2"/><input type="submit" value="Add to Cart"
                                                                         class="btnAddAction"/></div>
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