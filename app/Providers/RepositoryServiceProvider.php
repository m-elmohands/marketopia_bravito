<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Import Repository Interfaces
use App\Repository\Contracts\AboutSectionRepositoryInterface;
use App\Repository\Contracts\BlogPostRepositoryInterface;
use App\Repository\Contracts\ContactMessageRepositoryInterface;
use App\Repository\Contracts\FaqRepositoryInterface;
use App\Repository\Contracts\ServiceRepositoryInterface;
use App\Repository\Contracts\SettingRepositoryInterface;
use App\Repository\Contracts\TestimonialRepositoryInterface;
use App\Repository\Contracts\NavigationMenuRepositoryInterface;
use App\Repository\Contracts\ShippingReturnRepositoryInterface;
use App\Repository\Contracts\PrivacyPolicyRepositoryInterface;
use App\Repository\Contracts\TermsOfServiceRepositoryInterface;

// Import Repository Implementations
use App\Repository\Eloquent\AboutSectionRepository;
use App\Repository\Eloquent\BlogPostRepository;
use App\Repository\Eloquent\ContactMessageRepository;
use App\Repository\Eloquent\FaqRepository;
use App\Repository\Eloquent\ServiceRepository;
use App\Repository\Eloquent\SettingRepository;
use App\Repository\Eloquent\TestimonialRepository;
use App\Repository\Eloquent\NavigationMenuRepository;
use App\Repository\Eloquent\ShippingReturnRepository;
use App\Repository\Eloquent\PrivacyPolicyRepository;
use App\Repository\Eloquent\TermsOfServiceRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind About Section Repository
        $this->app->bind(
            AboutSectionRepositoryInterface::class,
            AboutSectionRepository::class
        );

        // Bind Blog Post Repository
        $this->app->bind(
            BlogPostRepositoryInterface::class,
            BlogPostRepository::class
        );

        // Bind FAQ Repository
        $this->app->bind(
            FaqRepositoryInterface::class,
            FaqRepository::class
        );

        // Bind Service Repository
        $this->app->bind(
            ServiceRepositoryInterface::class,
            ServiceRepository::class
        );

        // Bind Testimonial Repository
        $this->app->bind(
            TestimonialRepositoryInterface::class,
            TestimonialRepository::class
        );

        // Bind Navigation Menu Repository
        $this->app->bind(
            NavigationMenuRepositoryInterface::class,
            NavigationMenuRepository::class
        );

        // Bind Shipping Return Repository
        $this->app->bind(
            ShippingReturnRepositoryInterface::class,
            ShippingReturnRepository::class
        );

        // Bind Privacy Policy Repository
        $this->app->bind(
            PrivacyPolicyRepositoryInterface::class,
            PrivacyPolicyRepository::class
        );

        // Bind Terms of Service Repository
        $this->app->bind(
            TermsOfServiceRepositoryInterface::class,
            TermsOfServiceRepository::class
        );

        // Bind Contact Message Repository
        $this->app->bind(
            ContactMessageRepositoryInterface::class,
            ContactMessageRepository::class
        );

        // Bind Setting Repository
        $this->app->bind(
            SettingRepositoryInterface::class,
            SettingRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
