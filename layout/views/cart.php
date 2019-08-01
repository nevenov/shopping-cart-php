
            <form method="POST" action="?action=update-quantities">
                <table class="table table-hover table-responsive">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_products as $id=>$product): ?>
                        <tr>
                            <td class="col-sm-8 col-md-6">
                                <div class="media">
                                    <img alt=""
                                        src="<?= $product['url72'] ?>">
                                    <div class="ml-2">
                                        <h4><?= $product['name'] ?></h4>
                                        <h5><?= $product['subname'] ?></h5>
                                    </div>
                                </div>
                            </td>
                            <td class="col-sm-1 col-md-1">
                                <input type="number" name="amounts[<?=$id?>]" min="0" class="form-control" value="<?=$product['amount']?>">
                            </td>
                            <td class="col-sm-1 col-md-1 text-center"><strong>$<?= $product['price'] ?></strong></td>
                            <td class="col-sm-1 col-md-1 text-center"><strong>$<?= $product['total_price'] ?></strong></td>
                            <td class="col-sm-1 col-md-1">
                                <a href="?action=remove-from-cart&id=<?= $id ?>"
                                    onclick="return confirm('Delete the product from the cart?');" role="button"
                                    class="btn btn-danger">
                                    Remove
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                <button type="submit" class="btn btn-warning">
                                    <span>Update&nbsp;quantities</span>
                                </button>
                            </td>
                            <td class="text-right">
                                <h3>Total price:</h3>
                            </td>
                            <td class="text-right">
                                <h3><strong>$<?= $total_price ?></strong></h3>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="?action=products" role="button" class="btn btn-info">
                                    Continue&nbsp;shopping
                                </a>
                            </td>
                            <td>
                                <a href="?action=checkout" role="button" class="btn btn-success">
                                    Checkout
                                </a></td>
                        </tr>
                    </tbody>
                </table>
            </form>
