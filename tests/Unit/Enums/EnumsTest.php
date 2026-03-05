<?php

use App\Enums\ProjectBudgets;
use App\Enums\Services;

test('Services enum has 11 cases', function () {
    expect(Services::cases())->toHaveCount(11);
});

test('Services enum cases are correctly defined', function () {
    $expected = [
        'Teaching' => 'Teaching',
        'WebDeveloping' => 'WebDeveloping',
        'DigitalMarketing' => 'DigitalMarketing',
        'AppDeveloping' => 'AppDeveloping',
        'Localization' => 'Localization',
        'WebDesign' => 'WebDesign',
        'Content' => 'Content',
        'SocialMedia' => 'SocialMedia',
        'MarketingConsulting' => 'MarketingConsulting',
        'SEO' => 'SEO',
        'Notion' => 'Notion',
    ];

    foreach ($expected as $name => $value) {
        $case = Services::from($value);
        expect($case->name)->toBe($name)
            ->and($case->value)->toBe($value);
    }
});

test('ProjectBudgets enum has 5 cases', function () {
    expect(ProjectBudgets::cases())->toHaveCount(5);
});

test('ProjectBudgets enum cases have correct string values', function () {
    expect(ProjectBudgets::Under5->value)->toBe('<5k')
        ->and(ProjectBudgets::From5To10->value)->toBe('5 ÷ 10k')
        ->and(ProjectBudgets::From10To20->value)->toBe('10 ÷ 20k')
        ->and(ProjectBudgets::From20To50->value)->toBe('20 ÷ 50k')
        ->and(ProjectBudgets::Over50->value)->toBe('>50k');
});
