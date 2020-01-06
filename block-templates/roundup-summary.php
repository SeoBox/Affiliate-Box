<?php
$reviews = get_field( "reviews", $post_id, false );


if ( empty( $reviews ) ) {
//    go over gutenberg blocks
}

if ( ! empty( $reviews ) ) {
	while ( have_rows( "reviews", $post_id ) ) {
		the_row();
		$asin  = get_sub_field( 'asin', false );
		$title = get_sub_field( 'title', false );
		?>
        <ul>
			<?php

			foreach ( $reviews as $review ) {
				?>
                <li>
                    <a href="<? echo get_amazon_url( $asin ); ?>"><? echo $title; ?></a>

                </li>
				<?
			}
			?>
        </ul>
		<?php
	}

}
?>
