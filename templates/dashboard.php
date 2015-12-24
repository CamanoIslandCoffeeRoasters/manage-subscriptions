<?php
    ?>
     <script type="text/javascript">
     // Show-Hide Content Divs
         jQuery(document).ready(function($) {
             // Create Array to hold number and id of buttons
             var divs = new Array();
             $('div .call-flow').each(function (index) {
              divs.push($(this).attr("id"));
             });
             // Show/Hide content divs
             $('.call-flow').live("click", function() {
                clicked_div = ($(this).attr("id"));
                $.each(divs, function(i, val){
                    if (val != clicked_div) {
                        $('#' + val + '_content').hide();
                        $('#' + clicked_div + '_content').show();
                    }
                });
            });
        });
     </script>

     <script type="text/javascript">
        jQuery(document).ready(function($) {
            /**
             * This javascript file checks for the brower/browser tab action.
             * It is based on the file menstioned by Daniel Melo.
             * Reference: http://stackoverflow.com/questions/1921941/close-kill-the-session-when-the-browser-or-tab-is-closed
             */
            var validNavigation = false;

            function wireUpEvents() {
              /**
               * For a list of events that triggers onbeforeunload on IE
               * check http://msdn.microsoft.com/en-us/library/ms536907(VS.85).aspx
               *
               * onbeforeunload for IE and chrome
               * check http://stackoverflow.com/questions/1802930/setting-onbeforeunload-on-body-element-in-chrome-and-ie-using-jquery
               */
              var dont_confirm_leave = 0; //set dont_confirm_leave to 1 when you want the user to be able to leave withou confirmation
              var leave_message = 'Your Portal Session will be cleared.'
              function goodbye(e) {
                if (!validNavigation) {
                  if (dont_confirm_leave!==1) {
                    if(!e) e = window.event;
                    //e.cancelBubble is supported by IE - this will kill the bubbling process.
                    e.cancelBubble = true;
                    e.returnValue = leave_message;
                    //e.stopPropagation works in Firefox.
                    if (e.stopPropagation) {
                      e.stopPropagation();
                      e.preventDefault();
                    }
                    //return works for Chrome and Safari
                    //return leave_message;
                    return clear_locked_ids();
                  }
                }
              }
              window.onbeforeunload = goodbye;

              // Attach the event keypress to exclude the F5 refresh
              $(document).bind('keypress', function(e) {
                if (e.keyCode == 116){
                    console.log(e);
                  validNavigation = true;
                }
              });

              // Attach the event click for all links in the page
              $("a").bind("click", function() {
                validNavigation = true;
              });

              // Attach the event submit for all forms in the page
              $("form").bind("submit", function() {
                validNavigation = true;
              });

              // Attach the event click for all inputs in the page
              $("input[type=submit]").bind("click", function() {
                validNavigation = true;
              });

            }

            // Wire up the events as soon as the DOM tree is ready
            $(document).ready(function() {
              wireUpEvents();
            });

            function clear_locked_ids() {
                user_id = "<?php echo wp_get_current_user()->ID ?>";
                $.getJSON("<?php echo site_url() ?>/wp-content/plugins/manage-subscriptions/js/ajax/clear_locked_ids.php?user_id="+user_id, function() {});
            }
        });

     </script>

<?php
    // GET TABLE DATA
