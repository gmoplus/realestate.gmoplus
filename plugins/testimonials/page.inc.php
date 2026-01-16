<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: RLTESTIMONIALS.CLASS.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is considered to be illegal and breach of Flynax License End
 *  User Agreement.
 *  
 *  You are not allowed to remove this information from the file without permission
 *  of Flynax respective owners.
 *  
 *  Flynax Classifieds Software 2024 | All copyrights reserved.
 *  
 *  https://www.flynax.com
 ******************************************************************************/

$reefless->loadClass('Testimonials', null, 'testimonials');
$rlTestimonials->get();

if ($rlSmarty->_tpl_vars['countTestimonials'] && $rlSmarty->_tpl_vars['countTestimonials'] >= 20) {
    $navIcons[] = '<a class="post_ad" title="' . $lang['testimonials_add_testimonial'] . '" href="#add-testimonial"><span></span></a>';
    $rlSmarty->assign('navIcons', $navIcons);
}
