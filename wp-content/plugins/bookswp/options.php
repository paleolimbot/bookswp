<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="wrap">
<h1>Books WP</h1>

<form method="post" action="options.php"> 
    
    <?php 
    settings_fields( 'bookswp-usersettings' ); 
    do_settings_sections( 'bookswp-usersettings' );
    ?>
    
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Treat books like regular posts</th>
        <td><input type="checkbox" name="bookswp_books_like_posts" 
                   value="1" <?php checked(get_option('bookswp_books_like_posts'), 1) ; ?>/>
            This option allows the separation of books from the regular content of the blog (unchecked).
            Check to include books in normal search results, in archive pages, author posts, and on the main page.
            The default is unchecked.
        </td>
        </tr>
    </table>
    
    <?php submit_button(); ?>
</form>
</div>