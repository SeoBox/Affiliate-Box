<?php
$reviews = get_field("reviews", $post_id, false);
$style = get_field("style", null, false);

if (!empty($reviews)) {
    if (have_rows("reviews", $post_id)) {
        if ($style == "List") {
            ?>
            <ul>
            <?
        } elseif ($style == "Table") {
            ?>
            <style>
                table.affbox {
                    border: none;
                }

                .affbox thead th {
                    border: none;
                    text-align: center;
                }

                .affbox tbody td {
                    padding: 8px;
                    border: none;
                    border-top: 1px solid #ddd;
                }
            </style>
            <table class="affbox">
            <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Check Price</th>
            </tr>
            </thead>
            <?
        }

        while (have_rows("reviews", $post_id)) {
            the_row();
            $asin = get_sub_field('asin', false);
            $title = get_sub_field('title', false);
            $image = get_sub_field('image', true);
            $checkout = get_sub_field('checkout', true);

            if ($style == "List") {
                ?>
                <li>
                    <a rel="nofollow" href="<? echo Amazon::get_amazon_url($asin); ?>"><? echo $title; ?></a>
                </li>
                <?php
            } elseif ($style == "Table") {
                ?>
                <tr>
                    <td style="max-width: 100px; max-height: 100px;">
                        <? echo $image; ?>
                    </td>
                    <td style="max-width: 200px;">
                        <a rel="nofollow" href="<? echo Amazon::get_amazon_url($asin); ?>"><? echo $title; ?></a>
                    </td>
                    <td>
                        <? echo $checkout; ?>
                    </td>

                </tr>
                <?php
            }
        }

        if ($style == "List") {
            ?>
            </ul>
            <?
        } elseif ($style == "Table") {
            ?>
            </table>
            <?
        }
    }
}

?>
