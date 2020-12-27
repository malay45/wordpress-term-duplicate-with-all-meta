<?php
add_filter('tag_row_actions', function ($actions, $tag) {
	$taxonomy = $tag->taxonomy;
	$actions['duplicate'] = sprintf(
		'<a href="%s" class=" aria-button-if-js" aria-label="%s">%s</a>',
		wp_nonce_url("edit-tags.php?action=duplicate_term&amp;taxonomy=$taxonomy&amp;tag_ID=$tag->term_id", 'duplicate-term_' . $tag->term_id),
		/* translators: %s: Taxonomy term name. */
		esc_attr(sprintf(__('Duplicate &#8220;%s&#8221;'), $tag->name)),
		__('Duplicate')
	);
	return $actions;
}, 10, 2);


add_action('admin_init', function () {
	if (isset($_GET['action']) && $_GET['action'] == 'duplicate_term') {
		$term_data = get_term($_GET['tag_ID']);
		$term_id = wp_insert_term(
			$term_data->name . ' ' . 'New' . rand(0, 8),   // Replace New  rand with custom details
			$_GET['taxonomy'], // taxonomy
			array(
				'description' => $term_data->description,
				'parent'      => $term_data->parent,
			)
		);
		if (!is_wp_error($term_id) && isset($term_id['term_id'])) {
			$metas = get_term_meta($_GET['tag_ID']);
			foreach ($metas as $key => $meta) {
				update_term_meta($term_id['term_id'], $key, $meta[0]);
			}
		}
	}
});