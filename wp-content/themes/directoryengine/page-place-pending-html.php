<?php
/**
 *  Template Name: Pending Place HTML
 */
get_header();

$cat = new AE_Category(array('taxonomy' => 'place_category'));
$category = $cat->getAll($args);

?>
    
    <div class="container">
        <div class="pending-place-wrapper">
            <h2>Pending Place</h2>
            <div class="pending-place-filter-wrap">
                <p class="pull-left"><span>125</span>pending places</p>
                <div class="pending-place-filter pull-right">
                    <div class="place-category-filter">
                        <?php
                            ae_tax_dropdown('place_category', array('hide_empty' => true,
                                'class' => 'chosen-single tax-item',
                                'hierarchical' => true,
                                'show_option_all' => __("All categories", ET_DOMAIN),
                                'taxonomy' => 'place_category',
                                'value' => 'slug'
                            )); ?>
                    </div>
                    <div class="place-payment-filter">
                        <select class="chosen-single tax-item" name="" id="">
                            <option value="">All Payment Status</option>
                            <option value="">Paid</option>
                            <option value="">Unpaid</option>
                        </select>
                    </div>
                    <div class="place-newlest-filter">
                        <select class="chosen-single tax-item" name="" id="">
                            <option value="">Newest to Oldest</option>
                            <option value="">Oldest to Newlest</option>
                        </select>
                    </div>
                </div>
            </div>
            <ul class="pending-place-list">
                <li>
                    <div class="pending-place-wrap">
                        <a class="pending-place-img" href="">
                            <img src="http://lab.enginethemes.com/de/wp-content/uploads/sites/53/2017/02/hinh-4-270x280.jpg" alt="">
                        </a>
                        <div class="pending-place-content">
                            <div class="pending-place-title-wrap">
                                <span style="background-color: #d35400; color: #FFF;">Bartender</span>
                                <span style="background-color: #e74c3c; color: #FFF;">water</span>
                                <h2><a href="">Aquafina</a></h2>
                                <p><i class="fa fa-map-marker"></i>Roi ngay em ra di tren con duong mua gay em ra di tren con duong mua</p>
                            </div>
                            <div class="pending-place-location-wrap">
                                <div class="pending-place-location">
                                    <p><i class="fa fa-globe"></i>Viet Congfsa sfsafsa sa</p>
                                    <p><i class="fa fa-phone"></i>1021254254</p>
                                </div>
                            </div>
                            <div class="pending-place-author-wrap">
                                <div class="pending-place-author">
                                    <p>
                                        <a href="">
                                            <img src="http://lab.enginethemes.com/de/wp-content/uploads/sites/53/2017/02/hinh-4-270x280.jpg" alt="">
                                            no hosafi fsa sa dsafas fdsa
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="pending-place-status">
                                <span class="unpaid">Unpaid</span>
                            </div>
                            <div class="pending-place-action-wrap">
                                <div class="pending-place-action">
                                    <span class="action-approve"><i class="fa fa-check"></i></span>
                                    <span class="action-remove"><i class="fa fa-times"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="pending-place-wrap">
                        <a class="pending-place-img" href="">
                            <img src="http://lab.enginethemes.com/de/wp-content/uploads/sites/53/2017/02/hinh-4-270x280.jpg" alt="">
                        </a>
                        <div class="pending-place-content">
                            <div class="pending-place-title-wrap">
                                <span style="background-color: #352400; color: #FFF;">Bartender</span>
                                <h2><a href="">Aquafina</a></h2>
                                <p><i class="fa fa-map-marker"></i>Roi ngay em ra di tren con duong mua gay em ra di tren con duong mua</p>
                            </div>
                            <div class="pending-place-location-wrap">
                                <div class="pending-place-location">
                                    <p><i class="fa fa-globe"></i>Viet Congfsa sfsafsa sa</p>
                                    <p><i class="fa fa-phone"></i>1021254254</p>
                                </div>
                            </div>
                            <div class="pending-place-author-wrap">
                                <div class="pending-place-author">
                                    <p>
                                        <a href="">
                                            <img src="http://lab.enginethemes.com/de/wp-content/uploads/sites/53/2017/02/hinh-4-270x280.jpg" alt="">
                                            no hosafi fsa sa dsafas fdsa
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="pending-place-status">
                                <span class="paid">Paid</span>
                            </div>
                            <div class="pending-place-action-wrap">
                                <div class="pending-place-action">
                                    <span class="action-approve"><i class="fa fa-check"></i></span>
                                    <span class="action-remove"><i class="fa fa-times"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="pending-place-wrap">
                        <a class="pending-place-img" href="">
                            <img src="http://lab.enginethemes.com/de/wp-content/uploads/sites/53/2017/02/hinh-4-270x280.jpg" alt="">
                        </a>
                        <div class="pending-place-content">
                            <div class="pending-place-title-wrap">
                                <span style="background-color: #d35400; color: #FFF;">Bartender</span>
                                <span style="background-color: #e74c3c; color: #FFF;">water</span>
                                <h2><a href="">Aquafina</a></h2>
                                <p><i class="fa fa-map-marker"></i>Roi ngay em ra di tren con duong mua gay em ra di tren con duong mua</p>
                            </div>
                            <div class="pending-place-location-wrap">
                                <div class="pending-place-location">
                                    <p><i class="fa fa-globe"></i>Viet Congfsa sfsafsa sa</p>
                                    <p><i class="fa fa-phone"></i>1021254254</p>
                                </div>
                            </div>
                            <div class="pending-place-author-wrap">
                                <div class="pending-place-author">
                                    <p>
                                        <a href="">
                                            <img src="http://lab.enginethemes.com/de/wp-content/uploads/sites/53/2017/02/hinh-4-270x280.jpg" alt="">
                                            no hosafi fsa sa dsafas fdsa
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="pending-place-status">
                                <span class="unpaid">Unpaid</span>
                            </div>
                            <div class="pending-place-action-wrap">
                                <div class="pending-place-action">
                                    <span class="action-approve"><i class="fa fa-check"></i></span>
                                    <span class="action-remove"><i class="fa fa-times"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="search-location-pagination">
                <div class="paginations-wrapper main-pagination">
                    <a href="javascript:void(0)" class="page-link prev">Prev</a>
                    <a href="javascript:void(0)" class="page-link">1</a>
                    <a href="javascript:void(0)" class="page-link">2</a>
                    <a href="javascript:void(0)" class="page-link">3</a>
                    <a href="javascript:void(0)" class="page-link next">Next</a>
                </div>
            </div>
        </div>   
    </div>

<?php
get_footer();