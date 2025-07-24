<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/../autoload.php';

use Config\RenderingConfig;
use Rendering\Infrastructure\RenderingKernel;

$projectRoot = dirname(__DIR__);
$templatesDir = $projectRoot . DIRECTORY_SEPARATOR . 'templates';
$cacheDir = $projectRoot . DIRECTORY_SEPARATOR . 'cache';
$resourcesDir = $projectRoot . DIRECTORY_SEPARATOR . 'resources';

$copyrightOwner = 'Rendering System';

$config = new RenderingConfig($templatesDir, $cacheDir, $resourcesDir, $copyrightOwner);

$kernel = new RenderingKernel($config, true);
$renderer = $kernel->renderer();

// Define navigation links for presentation page
$navigationLinks = [
    [
        'label' => 'Home',
        'url' => '/',
        'visible' => true,
        'active' => true,
    ],
    [
        'label' => 'Features',
        'url' => '/features',
        'visible' => true,
        'active' => false,
    ],
    [
        'label' => 'Documentation',
        'url' => '/documentation',
        'visible' => true,
        'active' => false,
    ],
    [
        'label' => 'Templates',
        'url' => '/templates',
        'visible' => true,
        'active' => false,
    ],
    [
        'label' => 'GitHub',
        'url' => 'https://github.com/rendering-system',
        'visible' => true,
        'active' => false,
    ]
];

$cardData = [
    'imageAlt' => 'Card image description',
    'title' => 'Card Title',
    'subtitle' => 'Card Subtitle',
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
    'cardClass' => 'shadow-lg',
    'bodyClass' => 'text-center',
    'headerClass' => 'bg-primary text-white',
];

$cardsSectionPartials = [];
for ($i = 0; $i < 3; $i++) {
    $cardsSectionPartials["card-{$i}"] = [
        'partial/components/_card', $cardData
    ];
}

$cardsSectionData = [
    'title' => 'Welcome to the Rendering System',
    'subtitle' => 'A powerful and flexible rendering engine',
];

$doubleColumnContent = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.';

$doubleColumnData = [
    'title' => 'Double Column Section',
    'subtitle' => 'This is a double column section with a nested partial.',
    'content' => $doubleColumnContent,
    'footer_text' => 'This is the footer text for the double column section.',
    'childPartial' => 'code-ide', // This is the name of the nested partial to include
];

$doubleColumnPartials = [
    'code-ide' => ['partial/components/code-ide.phtml', [
        'language' => 'php',
        'code' => '<?php echo "Hello, World!"; ?>'
    ]]
];

$viewPartials = [
    'cardsSection' => ['partial/section/_cards_section.phtml', $cardsSectionData, $cardsSectionPartials],
    'doubleColumnSection' => ['partial/section/double-column.phtml', $doubleColumnData, $doubleColumnPartials]
];

$renderer
    ->setTitle('Home')
    ->setHeader('partial/main/header')
    ->setNavigation('partial/main/navigation')
    ->setNavigationLinks($navigationLinks)
    ->setView('view/home', [], $viewPartials)
    //->setCopyright('Rendering System')
    ->setFooter('partial/main/footer')
;

$page = $renderer->build();
$output = $renderer->render($page);

echo $output;