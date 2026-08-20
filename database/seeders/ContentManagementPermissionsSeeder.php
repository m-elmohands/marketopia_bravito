<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ContentManagementPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all resource names
        $resources = [
            'about_sections',
            'blog_posts',
            'faqs',
            'services',
            'testimonials',
            'navigation_menus',
            'shipping_returns',
            'privacy_policies',
            'terms_of_services',
            'contact_messages',
            'settings',
        ];

        // Define permission actions
        $actions = [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
        ];

        // Create permissions for each resource
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$resource}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create Super Admin Role
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        // Give all permissions to super admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Create Content Manager Role
        $contentManagerRole = Role::firstOrCreate([
            'name' => 'content_manager',
            'guard_name' => 'web',
        ]);

        // Give content management permissions to content manager
        $contentPermissions = Permission::where('name', 'like', '%_about_sections')
            ->orWhere('name', 'like', '%_blog_posts')
            ->orWhere('name', 'like', '%_faqs')
            ->orWhere('name', 'like', '%_services')
            ->orWhere('name', 'like', '%_testimonials')
            ->get();

        $contentManagerRole->givePermissionTo($contentPermissions);

        // Create Website Manager Role
        $websiteManagerRole = Role::firstOrCreate([
            'name' => 'website_manager',
            'guard_name' => 'web',
        ]);

        // Give website management permissions to website manager
        $websitePermissions = Permission::where('name', 'like', '%_navigation_menus')
            ->orWhere('name', 'like', '%_shipping_returns')
            ->orWhere('name', 'like', '%_privacy_policies')
            ->orWhere('name', 'like', '%_terms_of_services')
            ->get();

        $websiteManagerRole->givePermissionTo($websitePermissions);

        // Create Editor Role (can view and edit, but not delete)
        $editorRole = Role::firstOrCreate([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);

        $editorPermissions = Permission::whereIn('name', [
            // About Sections
            'view_any_about_sections',
            'view_about_sections',
            'create_about_sections',
            'update_about_sections',
            'reorder_about_sections',

            // Blog Posts
            'view_any_blog_posts',
            'view_blog_posts',
            'create_blog_posts',
            'update_blog_posts',

            // FAQs
            'view_any_faqs',
            'view_faqs',
            'create_faqs',
            'update_faqs',
            'reorder_faqs',

            // Services
            'view_any_services',
            'view_services',
            'create_services',
            'update_services',
            'reorder_services',

            // Testimonials
            'view_any_testimonials',
            'view_testimonials',
            'create_testimonials',
            'update_testimonials',
            'reorder_testimonials',

            // Navigation Menus
            'view_any_navigation_menus',
            'view_navigation_menus',
            'create_navigation_menus',
            'update_navigation_menus',

            // Shipping Returns
            'view_any_shipping_returns',
            'view_shipping_returns',
            'update_shipping_returns',

            // Privacy Policies
            'view_any_privacy_policies',
            'view_privacy_policies',
            'update_privacy_policies',

            // Terms of Services
            'view_any_terms_of_services',
            'view_terms_of_services',
            'update_terms_of_services',
        ])->get();

        $editorRole->givePermissionTo($editorPermissions);

        $this->command->info('Content Management permissions created successfully!');
    }
}
