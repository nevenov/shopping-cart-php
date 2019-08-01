 
    <?php foreach(array_chunk($products, 3, true) as $chunked_products): ?>
    <div class="card-deck">

        <?php foreach($chunked_products as $product): ?>
        <div class="card" style="width: 18rem; margin-bottom:30px;">
            <img src="<?= $product['url256']; ?>" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= $product['name'] . ' ' . $product['subname']; ?> <strong>$<?= $product['price']; ?></strong></h5>
                <p class="card-text"><?= $product['description']; ?></p>
                <a href="?action=add-to-cart&id=<?= $product['id']; ?>" class="btn btn-warning">Add to cart</a>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
    <?php endforeach; ?>