<?php
/**
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @package    BitsyBay Engine
 * @copyright  Copyright (c) 2015 The BitsyBay Project (http://bitsybay.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License, Version 3
 */
 ?>

<?php echo $header; ?>
<div class="row">
  <div class="col-lg-12 home">
    <?php if ($user_is_logged) { ?>
      <div class="page-header text-center">
        <h2><?php echo $total_products ?> by <?php echo $total_sellers ?> for <?php echo $total_buyers ?></h2>
      </div>
      <?php echo $module_search ?>
    <?php } else { ?>
      <div class="bs-component welcome">
        <div class="jumbotron">
          <div class="col-lg-7">
            <h3>Looking for a Marketplace to buy or sell digital creative in Bitcoin?</h3>

            <div class="social-buttons">
              <div class="fb-follow" data-href="https://www.facebook.com/bitsybay" data-layout="button_count" data-show-faces="true"></div>
              <a href="https://twitter.com/BitsyBayCom" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @BitsyBayCom</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
              <iframe frameborder="0" border="0" scrolling="no" allowtransparency="true" height="20" width="65" src="https://platform.tumblr.com/v2/follow_button.html?type=follow&amp;tumblelog=bitsybay&amp;color=white"></iframe>
              <a class="github-button" href="https://github.com/bitsybay/bitsybay" data-count-href="/bitsybay/bitsybay/stargazers" data-count-api="/repos/bitsybay/bitsybay#stargazers_count" data-count-aria-label="# stargazers on GitHub" aria-label="Star bitsybay/bitsybay on GitHub">Star</a>
            </div>

              Then here you are:
              <ul>
                <li><?php echo $total_products ?> by <?php echo $total_sellers ?> for <?php echo $total_buyers ?></li>
                <li>Trade from any country without bank commissions and other restrictions</li>
                <li>Royalty Free and Exclusive licenses</li>
                <li><?php echo QUOTA_FILE_SIZE_BY_DEFAULT ?> Mb free disk space for all new sellers and +<?php echo QUOTA_BONUS_SIZE_PER_ORDER ?> Mb for every next sale</li>
                <li>0% seller commission up to 2016, 11% later</li>
              </ul>
            </p>
          </div>
          <div class="col-lg-4 col-lg-offset-1">
            <div class="bs-component">
              <form class="form-horizontal" action="<?php echo $login_action ?>" method="POST">
                <fieldset>
                  <legend><?php echo tt('Already have an account?') ?></legend>
                  <div class="form-group">
                    <div class="col-lg-12">
                      <input type="text" name="login" class="form-control" id="inputLogin" placeholder="<?php echo tt('Email or username') ?>" value="">
                      <?php if (isset($error['login'])) { ?>
                        <div class="text-danger"><?php echo $error['login'] ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-12">
                      <input type="password" name="password" class="form-control" id="inputPassword" placeholder="<?php echo tt('Password') ?>" value="">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-12">
                      <button type="submit" class="btn btn-primary sign-in-button"><?php echo tt('Sign In') ?></button>
                      <div class="col-lg-offset-2">
                       &nbsp;&nbsp; or <a href="<?php echo $href_account_create ?>"><?php echo tt('Join Us') ?></a>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="bs-component latest">
      <?php echo $module_latest; ?>
    </div>
  </div>
</div>
<?php echo $footer; ?>
