<?php
/**
 * Created by PhpStorm.
 * User: Artem
 * Date: 21.06.2018
 * Time: 21:42
 */

add_hook( 'ClientAreaFooterOutput', 1, function ( $vars ) {
	return '
<script type="text/javascript">
    function recalculation() {
        $.post("/?m=BoxesCartConfigOptionFix&" + $("form").serialize(), function(data) {
            var result = jQuery.parseJSON(data);
            $("select[name=\'billingcycle\'] > option").each(function() {
                var count = 0;
                window.type = $(this).val();
                $(this).text($(this).text().replace(/\d+\.\d+/g, function(a) {
                    ++count;
                    if (count === 1) {
                        return (result.price[window.type]).toFixed(2);
                    } else {
                        return a;
                    }
                }));

                count = 0;
                $(this).text($(this).text().replace(/\d+\.\d+/g, function(a) {
                    ++count;
                    if (count === 2) {
                        return (result.setupFee[window.type]).toFixed(2);
                    } else {
                        return a;
                    }
                }))
            });
        });
    }

    $("select").change(function() {
        recalculation();
    });
    $("input").keyup(function() {
        recalculation();
    });

    recalculation();
</script>
';
} );
