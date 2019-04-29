<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
    'web',
    'gonzo',
    '',
    null,
    [
        'labels' => 'LLL:EXT:gonzo/Resources/Private/Language/locallang_module.xlf',
        'icon' => 'EXT:gonzo/Resources/Public/Icons/Extension.png',
        'name' => 'web_gonzo',
        'path' => '/module/urls',
        'routeTarget' => \B13\Gonzo\Controller\UrlManagementController::class . '::mainAction',
        'access' => 'user,group',
    ]
);

if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_BE) {
    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Page\PageRenderer::class
    )->addRequireJsConfiguration([
        'shim' => [
            'Vue' => ['exports' => 'Vue']
        ],
        'paths' => [
            'Vue' => 'https://cdn.jsdelivr.net/npm/vue/dist/vue',
            'vue' => 'https://rawgit.com/edgardleal/require-vue/master/dist/require-vuejs'
        ]
    ]);
}
