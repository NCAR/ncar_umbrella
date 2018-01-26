/**
 * Global Koru Javascript
 */

//Set a cookie when user closes alert callout
$(document).on('close.zf.trigger', 'header .callout', function(e) {
  //we use "Drupal.Visitor" because the Drupal user_cookie functions expect that name
  Cookies.set('Drupal.visitor.ucar-alert', $(this).attr('data-changed'), { expires: 1 });
});