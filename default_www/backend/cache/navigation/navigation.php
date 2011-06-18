<?php
/**
 *
 * This file should be generated by Fork CMS, it contains
 * more information about the navigation in the backend. Do NOT edit.
 *
 * REMARK: do NOT delete this file
 *
 * @author		Fork CMS
 * @generated	not
 */

$navigation = array(
	array(
		'url' => 'dashboard/index',
		'label' => 'Dashboard'
	),
	array(
		'url' => 'pages/index',
		'label' => 'Pages',
		'selected_for' => array(
			'pages/add',
			'pages/edit'
		)
	),
	array(
		'url' => 'content_blocks/index',
		'label' => 'Modules',
		'children' => array(
			array(
				'url' => 'content_blocks/index',
				'label' => 'ContentBlocks',
				'selected_for' => array(
					'content_blocks/add',
					'content_blocks/edit'
				)
			),
			array(
				'url' => 'tags/index',
				'label' => 'Tags',
				'selected_for' => array(
					'tags/edit'
				)
			),
			array(
				'url' => 'blog/index',
				'label' => 'Blog',
				'children' => array(
					array(
						'url' => 'blog/index',
						'label' => 'Articles',
						'selected_for' => array(
							'blog/add',
							'blog/edit',
							'blog/import_blogger'
						)
					),
					array(
						'url' => 'blog/comments',
						'label' => 'Comments',
						'selected_for' => array(
							'blog/edit_comment'
						)
					),
					array(
						'url' => 'blog/categories',
						'label' => 'Categories',
						'selected_for' => array(
							'blog/add_category',
							'blog/edit_category'
						)
					)
				)
			),
			array(
				'url' => 'search/statistics',
				'label' => 'Search',
				'children' => array(
					array(
						'url' => 'search/statistics',
						'label' => 'Statistics'
					),
					array(
						'url' => 'search/synonyms',
						'label' => 'Synonyms',
						'selected_for' => array(
							'search/add_synonym',
							'search/edit_synonym'
						)
					)
				)
			),
			array(
				'url' => 'location/index',
				'label' => 'Location',
				'selected_for' => array(
					'location/add',
					'location/edit'
				)
			),
			array(
				'url' => 'faq/index',
				'label' => 'Faq',
				'children' => array(
					array(
						'url' => 'faq/index',
						'label' => 'Questions',
						'selected_for' => array(
							'faq/add',
							'faq/edit'
						)
					),
					array(
						'url' => 'faq/categories',
						'label' => 'Categories',
						'selected_for' => array(
							'faq/add_category',
							'faq/edit_category'
						)
					)
				)
			),
			array(
				'url' => 'form_builder/index',
				'label' => 'FormBuilder',
				'selected_for' => array(
					'form_builder/add',
					'form_builder/edit',
					'form_builder/data',
					'form_builder/data_details'
				)
			),
			array(
				'url' => 'events/index',
				'label' => 'Events',
				'children' => array(
					array(
						'url' => 'events/index',
						'label' => 'Events',
						'selected_for' => array(
							'events/add',
							'events/edit'
						)
					),
					array(
						'url' => 'events/comments',
						'label' => 'Comments',
						'selected_for' => array(
							'events/edit_comment'
						)
					),
					array(
						'url' => 'events/subscriptions',
						'label' => 'Subscriptions',
						'selected_for' => array(
							'events/edit_subscription'
						)
					),
					array(
						'url' => 'events/categories',
						'label' => 'Categories',
						'selected_for' => array(
							'events/add_category',
							'events/edit_category'
						)
					)
				)
			),
			array(
				'url' => 'profiles/index',
				'label' => 'Profiles',
				'children' => array(
					array(
						'url' => 'profiles/index',
						'selected_for' => array(
							'profiles/edit',
							'profiles/add_profile_group',
							'profiles/edit_profile_group'
						),
						'label' => 'Profiles'
					),
					array(
						'url' => 'profiles/groups',
						'selected_for' => array(
							'profiles/add_group',
							'profiles/edit_group'
						),
						'label' => 'Groups'
					)
				)
			),
		)
	),
	array(
		'url' => 'analytics/index',
		'label' => 'Marketing',
		'children' => array(
			array(
				'url' => 'analytics/index',
				'label' => 'Analytics',
				'selected_for' => 'analytics/loading',
				'children' => array(
					array(
						'url' => 'analytics/content',
						'label' => 'Content'
					),
					array(
						'url' => 'analytics/all_pages',
						'label' => 'AllPages'
					),
					array(
						'url' => 'analytics/exit_pages',
						'label' => 'ExitPages'
					),
					array(
						'url' => 'analytics/landing_pages',
						'label' => 'LandingPages',
						'selected_for' => array(
							'analytics/add_landing_page',
							'analytics/edit_landing_page',
							'analytics/detail_page'
						)
					)
				)
			)
		)
	),
	array(
		'url' => 'mailmotor/index',
		'label' => 'Mailmotor',
		'children' => array(
			array(
				'url' => 'mailmotor/index',
				'label' => 'Newsletters',
				'selected_for' => array(
					'mailmotor/add',
					'mailmotor/edit',
					'mailmotor/edit_mailing_campaign',
					'mailmotor/statistics',
					'mailmotor/statistics_link',
					'mailmotor/statistics_bounces',
					'mailmotor/statistics_campaign',
					'mailmotor/statistics_opens'
				)
			),
			array(
				'url' => 'mailmotor/campaigns',
				'label' => 'Campaigns',
				'selected_for' => array(
					'mailmotor/add_campaign',
					'mailmotor/edit_campaign',
					'mailmotor/statistics_campaign'
				)
			),
			array(
				'url' => 'mailmotor/groups',
				'label' => 'MailmotorGroups',
				'selected_for' => array(
					'mailmotor/add_group',
					'mailmotor/edit_group',
					'mailmotor/custom_fields',
					'mailmotor/add_custom_field',
					'mailmotor/import_groups'
				)
			),
			array(
				'url' => 'mailmotor/addresses',
				'label' => 'Addresses',
				'selected_for' => array(
					'mailmotor/add_address',
					'mailmotor/edit_address',
					'mailmotor/import_addresses'
				)
			)
		)
	),
	array(
		'url' => 'settings/index',
		'label' => 'Settings',
		'children' => array(
			array(
				'url' => 'settings/index',
				'label' => 'General'
			),
			array(
				'url' => 'settings/email',
				'label' => 'Advanced',
				'children' => array(
					array(
						'url' => 'settings/email',
						'label' => 'Email'
					)
				)
			),
			array(
				'url' => 'users/index',
				'label' => 'Users',
				'selected_for' => array(
					'users/add',
					'users/edit'
				)
			),
			array(
				'url' => 'groups/index',
				'label' => 'Groups',
				'selected_for' => array(
					'groups/add',
					'groups/edit'
				)
			),
			array(
				'url' => 'settings/themes',
				'label' => 'Themes',
				'children' => array(
					array(
						'url' => 'settings/themes',
						'label' => 'ThemesSelection'
					),
					array(
						'url' => 'pages/templates',
						'label' => 'Templates',
						'selected_for' => array(
							'pages/add_template',
							'pages/edit_template'
						)
					)
				)
			),
			array(
				'url' => 'locale/index',
				'label' => 'Translations',
				'selected_for' => array(
					'locale/add',
					'locale/edit',
					'locale/analyse',
					'locale/import'
				)
			),
			array(
				'url' => 'analytics/settings',
				'label' => 'Modules',
				'children' => array(
					array(
						'url' => 'analytics/settings',
						'label' => 'Analytics'
					),
					array(
						'url' => 'blog/settings',
						'label' => 'Blog'
					),
					array(
						'url' => 'search/settings',
						'label' => 'Search'
					),
					array(
						'url' => 'pages/settings',
						'label' => 'Pages'
					),
					array(
						'url' => 'mailmotor/settings',
						'label' => 'Mailmotor'
					),
					array(
						'url' => 'location/settings',
						'label' => 'Location'
					),
					array(
						'url' => 'events/settings',
						'label' => 'Events'
					)
				)
			)
		)
	)
);

?>