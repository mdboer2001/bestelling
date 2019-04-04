<?php
session_start();
require_once("dbconfig.php");
require_once('mailer/PHPMailer.php');
$db_handle = new DBController();

if(!empty($_POST)) {
    $bestellingId = $db_handle->insert("INSERT INTO bestelling (voornaam, tussen) VALUES ('".$_POST["voornaam"]."', '".$_POST["tussenvoegsel"]."')");
    if(!empty($bestellingId)) {
        foreach($_SESSION["cart_item"] as $item) {
            $db_handle->insert("INSERT INTO bestel_regel (bestelling_id, product_id, quantity, product_price, total_price) VALUES ('".$bestellingId."', '".$item["product_id"]."')");
        }
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
                <th style="text-align:right;" width="5%">Size</th>
                <th style="text-align:right;" width="5%">Quantity</th>
                <th style="text-align:right;" width="10%">Unit Price</th>
                <th style="text-align:right;" width="10%">Price</th>
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
                    <td><img src="<?php echo $items[$i]["image"]; ?>" class="cart-item-image" /><?php echo $items[$i]["name"]; ?></td>
                    <td><?php echo $items[$i]["code"]; ?></td>
                    <td style="text-align:right;"><?php echo $items[$i]["size"]; ?></td>
                    <td style="text-align:right;"><?php echo $items[$i]["quantity"]; ?></td>
                    <td  style="text-align:right;"><?php echo "$ ".$items[$i]["price"]; ?></td>
                    <td  style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
                </tr>
                <?php
                $total_quantity += $items[$i]["quantity"];
                $total_price += ($items[$i]["price"]*$items[$i]["quantity"]);
            }


            ?>
            <tr>
                <td colspan="3" align="right">Total:</td>
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



                <input type="submit" class="btn btn-success" value="Bestel" formmethod="post" />
            </form>
</div>


</body>
</html>
