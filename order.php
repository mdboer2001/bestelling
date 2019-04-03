<?php
session_start();
require_once("dbconfig.php");
$db_handle = new DBController();
if(!empty($_GET["action"])) {
    switch($_GET["action"]) {
        case "add":
            if(!empty($_POST["quantity"])) {
                $productByCode = $db_handle->runQuery("SELECT * FROM tblproduct WHERE code='" . $_GET["code"] . "'");
                $itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price"], 'image'=>$productByCode[0]["image"]));

                if(!empty($_SESSION["cart_item"])) {
                    if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
                        foreach($_SESSION["cart_item"] as $k => $v) {
                            if($productByCode[0]["code"] == $k) {
                                if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                    $_SESSION["cart_item"][$k]["quantity"] = 0;
                                }
                                $_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
                            }
                        }
                    } else {
                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
                    }
                } else {
                    $_SESSION["cart_item"] = $itemArray;
                }
            }
            break;
        case "remove":
            if(!empty($_SESSION["cart_item"])) {
                foreach($_SESSION["cart_item"] as $k => $v) {
                    if($_GET["code"] == $k)
                        unset($_SESSION["cart_item"][$k]);
                    if(empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
                }
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
    <link href="style.css" type="text/css" rel="stylesheet" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
<body>

<div id="shopping-cart">
    <div class="txt-heading">Shopping Cart</div>

    <a id="btnOrder" href="index.php?action=order">Bestel</a>
    <a id="btnEmpty" href="index.php">Terug</a>
    <?php
    if(isset($_SESSION["cart_item"])){
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
            foreach ($_SESSION["cart_item"] as $item){
                $item_price = $item["quantity"]*$item["price"];
                ?>
                <tr>
                    <td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
                    <td><?php echo $item["code"]; ?></td>
                    <td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
                    <td  style="text-align:right;"><?php echo "$ ".$item["price"]; ?></td>
                    <td  style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
                    <td style="text-align:center;"><a href="index.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
                </tr>
                <?php
                $total_quantity += $item["quantity"];
                $total_price += ($item["price"]*$item["quantity"]);
            }


            ?>
            <tr>
                <td colspan="2" align="right">Total:</td>
                <td align="right"><?php echo $total_quantity; ?></td>
                <td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
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

    <!--News Container-->
    <div class="container-fluid">
        <h1 class="text-center bot-buffer font-weight-bold" id="headNews">Bestel</h1>
        <div class="row d-flex justify-content-center">
            <form method="post">
                <div class="form-group">
                    <label for="formNaam">Voornaam</label>
                    <input required type="text" class="form-control" name="formNaam" placeholder="Voornaam">
                </div>
                <div class="form-group">
                    <label for="formNaam">Tussenvoegsel</label>
                    <input required type="text" class="form-control" name="formNaam" placeholder="Voornaam">
                </div>
                <div class="form-group">
                    <label for="formAnaam">Achternaam</label>
                    <input required type="text" class="form-control" name="formAnaam" placeholder="Achternaam">
                </div>
                <div class="form-group">
                    <label for="formEmail">E-Mail</label>
                    <input required type="email" class="form-control" name="formEmail" placeholder="robertpronk@denhaag.nl">
                </div>
                <div class="form-group">
                    <label for="formDate">Straat</label>
                    <input type="text" id="data" class="form-control" name="formDate" placeholder="Straatnaam">
                </div>
                <div class="form-group">
                    <label for="formDate">Postcode</label>
                    <input type="text" id="data" class="form-control" name="formDate" placeholder="Straatnaam">
                </div>



                <input type="submit" class="btn btn-success" value="Bestel" formmethod="post"></input>
            </form>
</div>


</body>
</html>
