<?php
/**
	Block Patters for How I Built It
**/

function hibi_block_patterns() {

	register_block_pattern(
		'wp-podcatcher/hibi-content-upgrade',
		array(
			'title'       => __( 'Content Upgrade', 'wp-podcatcher' ),
			
			'description' => _x( 'A simple block group to encourage membership sign up', 'wp-podcatcher' ),
			
			'content'     => "<!-- wp:group {\"style\":{\"color\":{\"gradient\":\"radial-gradient(rgba(195,231,244,0.32) 0%,rgb(196,231,244) 100%)\"}}} -->\n<div class=\"wp-block-group has-background\" style=\"background:radial-gradient(rgba(195,231,244,0.32) 0%,rgb(196,231,244) 100%)\"><!-- wp:spacer {\"height\":35} -->\n<div style=\"height:35px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer -->\n\n<!-- wp:heading {\"textAlign\":\"center\"} -->\n<h2 class=\"has-text-align-center\">[[[TEASER TITLE]]]</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"align\":\"center\"} -->\n<p class=\"has-text-align-center\">[[[TEASTER TEXT]]] by joining the <a href=\"https://buildsomething.club\">Build Something Club</a>.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:restrict-content-pro/content-upgrade-redirect {\"redirectUrl\":\"https://howibuilt.it/putting-together-the-build-something-club-membership/\",\"registrationUrl\":\"https://howibuilt.it/club/\",\"loginUrl\":\"https://howibuilt.it/club/your-membership/?rcp_redirect=https%3A%2F%2Fhowibuilt.it%2Fputting-together-the-build-something-club-membership%2F\"} -->\n<div class=\"restrict-content-pro-content-upgrade-redirect__inner-content wp-block-restrict-content-pro-content-upgrade-redirect\"><!-- wp:button {\"placeholder\":\"Register\",\"align\":\"center\"} -->\n<div class=\"wp-block-button aligncenter\"><a class=\"wp-block-button__link\" href=\"https://howibuilt.it/club/?rcp_redirect=https%3A%2F%2Fhowibuilt.it%2Fputting-together-the-build-something-club-membership%2F\">Become a Member for just $5/mo</a></div>\n<!-- /wp:button -->\n\n<!-- wp:paragraph {\"align\":\"center\",\"placeholder\":\"Already a Member? Example Text\",\"className\":\"restrict-content-pro-content-login-link\"} -->\n<p class=\"has-text-align-center restrict-content-pro-content-login-link\"><a href=\'https://howibuilt.it/club/your-membership/?rcp_redirect=https%3A%2F%2Fhowibuilt.it%2Fputting-together-the-build-something-club-membership%2F\'>Already a member? Fantastic! Just sign in! </a></p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:restrict-content-pro/content-upgrade-redirect -->\n\n<!-- wp:spacer {\"height\":35} -->\n<div style=\"height:35px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer --></div>\n<!-- /wp:group -->",
			
			'categories'  => array('buttons'),
		)
	);

}    
add_action( 'init', 'hibi_block_patterns' );