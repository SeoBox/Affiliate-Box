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
                    border: 1px solid rgba(34, 36, 38, 0.15);
                }

                .affbox thead th {
                    border: none;
                    text-align: center;
                    background: #f9fafb;
                }

                .affbox tbody tr:nth-child(2n) {
                    background-color: rgba(0, 0, 50, .02);
                }

                .affbox tbody tr:hover {
                    background-color: rgba(0, 0, 0, .05);
                }

                .affbox tbody td {
                    padding: .5em .8em;
                    border: none;
                    border-top: 1px solid #ddd;
                }

                .affbox tbody td img {
                    max-height: 64px;
                    max-width: 64px;
                }

                .affbox a {
                    font-size: 16px;
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
                    <td style="max-width: 64px;">
                        <? echo $image; ?>
                    </td>
                    <td style="max-width: 200px;">
                        <a rel="nofollow" href="<? echo Amazon::get_amazon_url($asin); ?>"><? echo $title; ?></a>
                    </td>
                    <td class="text-center">
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
