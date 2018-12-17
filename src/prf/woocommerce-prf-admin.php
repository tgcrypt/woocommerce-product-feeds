<?php

class WoocommercePrfAdmin {

	/**
	 * Constructor.
	 *
	 * Registers some always used actions (Such as registering endpoints). Also checks to see
	 * if this is a feed request, and if so registers the hooks needed to generate the feed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_filter( 'comment_edit_redirect', array( $this, 'save_comment_meta' ), 1, 2 );
	}

	/**
	 * Show a metabox on the comment edit pages.
	 */
	public function add_meta_boxes() {
		if ( 'comment' == get_current_screen()->id && isset( $_GET['c'] ) ) {
			if ( ! $this->is_review_comment( $_GET['c'] ) ) {
				return;
			}
			add_meta_box( 'wc-prf-rating', __( 'Product Review feed settings', 'woocommerce_gpf' ), array( $this, 'render_meta_box' ), 'comment', 'normal', 'high' );
		}
	}

	/**
	 * Render the metabox on the comment edit pages.
	 */
	public function render_meta_box( $comment ) {
		$excluded = get_comment_meta( $comment->comment_ID, '_wc_prf_no_feed', true );
		$anonymised = get_comment_meta( $comment->comment_ID, '_wc_prf_anonymised', true );
		?>
        <p>
            <input type="checkbox" name="_wc_prf_no_feed" <?php checked( $excluded, 1 ); ?> id="_wc_prf_no_feed">
            <label for="_wc_prf_no_feed"><?php _e( 'Exclude from feed', 'woocommerce_gpf' ); ?></label>
        </p>
        <p>
            <input type="checkbox" name="_wc_prf_anonymised" <?php checked( $anonymised, 1 ); ?> id="_wc_prf_anonymised">
            <label for="_wc_prf_anonymised"><?php _e( 'Anonymise reviewer in feed', 'woocommerce_gpf' ); ?></label>
        </p>

		<?php
	}

	/**
	 * Save the metabox info on the comment edit pages.
	 */
	public function save_comment_meta( $location, $comment_id ) {
		$excluded = isset( $_POST['_wc_prf_no_feed'] ) ? ( $_POST['_wc_prf_no_feed'] == 'on' ) : 0;
		if ( $excluded ) {
			update_comment_meta( $comment_id, '_wc_prf_no_feed', $excluded );
		} else {
			delete_comment_meta( $comment_id, '_wc_prf_no_feed' );
		}
		$anonymised = isset( $_POST['_wc_prf_anonymised'] ) ? ( $_POST['_wc_prf_anonymised'] == 'on' ) : 0;
		if ( $anonymised ) {
			update_comment_meta( $comment_id, '_wc_prf_anonymised', $anonymised );
		} else {
			delete_comment_meta( $comment_id, '_wc_prf_anonymised' );
		}
		return $location;
	}

	private function is_review_comment( $comment_id ) {
		$meta = get_comment_meta( $comment_id, 'rating', true );
		return is_numeric( $meta );
	}
}
