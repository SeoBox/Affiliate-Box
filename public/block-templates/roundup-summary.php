<?php
$reviews = get_field( "reviews", $post_id, false );

if ( ! empty( $reviews ) ) {
	while ( have_rows( "reviews", $post_id ) ) {
		the_row();
		$asin  = get_sub_field( 'asin', false );
		$title = get_sub_field( 'title', false );
		?>
        <ul>
            <li>
                <a rel="nofollow" href="<? echo Amazon::get_amazon_url( $asin ); ?>"><? echo $title; ?></a>
            </li>
        </ul>
		<?php
	}
}
?>
