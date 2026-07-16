  <!-- Grid view -->
    <div class="tab-pane active" id="grid-container">
      <div class="category-product">
        <div class="products-flex">
          <?php foreach($products as $p): ?>
            <?php
              $slug = urlencode($p['slug']);
              $finalPrice = !empty($p['discount_price']) ? $p['discount_price'] : $p['price'];
              $oldPrice   = !empty($p['discount_price']) ? $p['price'] : null;
              $descuento  = ($oldPrice && $finalPrice < $oldPrice)
                            ? round((($oldPrice - $finalPrice) / $oldPrice) * 100)
                            : 0;
            ?>
            <div class="item">
              <div class="products">
                <div class="product">

                  <!-- Imagen -->
                  <div class="product-image">
                    <div class="image">
                      <a href="<?= URLBASE ?>/product/<?= $slug ?>">
                        <img src="<?= htmlspecialchars($url.'/'.$p['main_image']) ?>" 
                             alt="<?= htmlspecialchars($p['name']) ?>">
                      </a>
                    </div>
                    <?php if ($descuento > 0): ?>
                      <div class="tag sale"><span>-<?= $descuento ?>%</span></div>
                    <?php endif; ?>
                  </div>

                  <!-- Info -->
                  <div class="product-info text-left">
                    <h3 class="name">
                      <a href="<?= URLBASE ?>/product/<?= $slug ?>">
                        <?= htmlspecialchars($p['name']) ?>
                      </a>
                    </h3>
                    <div class="rating rateit-small"></div>
                    <div class="description"><?= htmlspecialchars($p['short_desc'] ?? '') ?></div>
                    <div class="product-price">
                      <span class="price">$<?= number_format($finalPrice, 0) ?></span>
                      <?php if($oldPrice): ?>
                        <span class="price-before-discount">$<?= number_format($oldPrice, 0) ?></span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Botones -->
                  <div class="cart clearfix animate-effect">
                    <div class="action">
                      <ul class="list-unstyled">
                        <li class="add-cart-button btn-group">
                          <?php if ((int)$p['view_before_cart'] === 1): ?>
                            <a href="<?= URLBASE ?>/product/<?= $slug ?>" class="btn btn-primary">
                              <i class="fa-solid fa-eye"></i> Ver producto
                            </a>
                          <?php else: ?>
                            <button class="btn btn-primary add-to-cart-btn-single"
                                    data-slug="<?= $slug ?>" data-qty="1">
                              <i class="fas fa-shopping-cart"></i> Agregar al carrito
                            </button>
                          <?php endif; ?>
                        </li>
                      </ul>
                    </div>
                  </div>

                </div><!-- /.product -->
              </div><!-- /.products -->
            </div><!-- /.item -->
          <?php endforeach; ?>
        </div><!-- /.products-flex -->
      </div><!-- /.category-product -->
    </div>
    <!-- /.tab-pane -->
    
    <!-- List view -->
    <div class="tab-pane" id="list-container">
      <div class="category-product">
        <?php foreach($products as $p): ?>
          <?php
            $slug = urlencode($p['slug']);
            $oldPrice    = (float)$p['price'];
            $finalPrice  = (float)($p['discount_price'] ?: $p['price']);
            $descuento   = ($oldPrice > 0 && $finalPrice < $oldPrice)
                            ? round((($oldPrice - $finalPrice) / $oldPrice) * 100)
                            : 0;
          ?>
          <div class="category-product-inner">
            <div class="products">
              <div class="product-list product">
                <div class="row product-list-row">
                  
                  <!-- Imagen -->
                  <div class="col col-sm-4 col-lg-4">
                    <div class="product-image">
                      <div class="image">
                        <a href="<?= $url ?>/product/<?= htmlspecialchars($p['slug']) ?>">
                          <img src="<?= htmlspecialchars($url.'/'.$p['main_image']) ?>" 
                               alt="<?= htmlspecialchars($p['name']) ?>">
                        </a>
                      </div>
                      <?php if($descuento > 0): ?>
                        <div class="tag sale"><span>-<?= $descuento ?>%</span></div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Info -->
                  <div class="col col-sm-8 col-lg-8">
                    <div class="product-info">
                      <h3 class="name">
                        <a href="<?= $url ?>/product/<?= htmlspecialchars($p['slug']) ?>">
                          <?= htmlspecialchars($p['name']) ?>
                        </a>
                      </h3>
                      <div class="rating rateit-small"></div>
                      <div class="product-price"> 
                        <span class="price">$<?= number_format($finalPrice, 0) ?></span>
                        <?php if($descuento > 0): ?>
                          <span class="price-before-discount">$<?= number_format($oldPrice, 0) ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="description m-t-10">
                        <?= htmlspecialchars($p['short_desc'] ?? '') ?>
                      </div>

                      <!-- Botón principal -->
                      <div class="cart clearfix animate-effect mt-3">
                        <div class="action">
                          <?php if ((int)$p['view_before_cart'] === 1): ?>
                            <a href="<?= $url ?>/product/<?= htmlspecialchars($p['slug']) ?>" class="btn btn-primary">
                              <i class="fa-solid fa-eye"></i> Ver producto
                            </a>
                          <?php else: ?>
                            <button class="btn btn-primary add-to-cart-btn-single"
                                    data-slug="<?= htmlspecialchars($p['slug']) ?>"
                                    data-qty="1">
                              <i class="fas fa-shopping-cart"></i> Agregar al carrito
                            </button>
                          <?php endif; ?>
                        </div>
                      </div>

                    </div>
                  </div>
                </div><!-- /.row -->
              </div><!-- /.product-list -->
            </div><!-- /.products -->
          </div><!-- /.category-product-inner -->
        <?php endforeach; ?>
      </div><!-- /.category-product -->
    </div>
    <!-- /.tab-pane #list-container -->
