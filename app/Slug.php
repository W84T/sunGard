<?php

namespace App;

enum Slug: string
{
    case Admin = 'admin';
    case BranchManager = 'branch manager';
    case CustomerServiceManager = 'customer service manager';
    case CustomerService = 'customer service';
    case Marketer = 'marketer';

    case Agent = 'agent';
    case ReportManager = 'report manager';
    case ManagerOfCustomerServiceManager = 'manager of customer service manager';


    public static function options(): array{
        return array_reduce(self::cases(), function (array $carry, self $case){
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }

    public function label(): string{
        return match ($this) {
            self::Admin => __('user.slug.admin'),
            self::BranchManager => __('user.slug.branch_manager'),
            self::CustomerServiceManager => __('user.slug.customer_service_manager'),
            self::CustomerService => __('user.slug.customer_service'),
            self::Marketer => __('user.slug.marketer'),
            self::Agent => __('user.slug.agent'),
            self::ReportManager => __('user.slug.report_manager'),
            self::ManagerOfCustomerServiceManager => __('user.slug.manager_of_customer_service_manager'),
        };
    }

    public function hasRoleSlug(Slug|string $slug): bool
    {
        $slugValue = $slug instanceof Slug ? $slug->value : $slug;

        return $this->roles->contains('slug', $slugValue);
    }

    public function hasAnyRoleSlug(array $slugs): bool
    {
        $slugs = array_map(fn ($s) => $s instanceof Slug ? $s->value : $s, $slugs);

        return $this->roles->pluck('slug')->intersect($slugs)->isNotEmpty();
    }

}
