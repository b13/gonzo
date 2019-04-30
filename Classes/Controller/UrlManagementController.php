<?php
declare(strict_types=1);

namespace B13\Gonzo\Controller;

/*
 * This file is part of TYPO3 CMS-based extension Gonzo by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\Template\Components\Menu\MenuItem;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Tree\Repository\PageTreeRepository;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Show all pages with slugs
 */
class UrlManagementController
{
    /**
     * @var ModuleTemplate
     */
    private $moduleTemplate;

    /**
     * @var IconFactory
     */
    private $iconFactory;

    /**
     * @var ViewInterface
     */
    private $view;

    public function __construct()
    {
        $this->moduleTemplate = GeneralUtility::makeInstance(ModuleTemplate::class);
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $this->initializeView();
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return HtmlResponse
     */
    public function mainAction(ServerRequestInterface $request)
    {
        $currentPageId = (int)$request->getQueryParams()['id'] ?? 0;
        $backendUser = $GLOBALS['BE_USER'];
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $context = GeneralUtility::makeInstance(Context::class);

        // should only work for pages within one Site
        try {
            $site = $siteFinder->getSiteByPageId($currentPageId);
            $repository = GeneralUtility::makeInstance(
                PageTreeRepository::class,
                (int)$context->getPropertyFromAspect('workspace', 'id'),
                ['slug']
            );
            $tree = $repository->getTree($currentPageId, function ($page) use ($siteFinder, $site, $backendUser) {
                try {
                    $inSameSite = $siteFinder->getSiteByPageId((int)$page['uid']) === $site;
                    // check each page if the user has permission to access it and if the user is within the site.
                    return $inSameSite && $backendUser->doesUserHaveAccess($page, Permission::PAGE_SHOW);
                } catch (SiteNotFoundException $e) {
                    return false;
                }
            });
            $this->view->assign('tree', $tree);
            $this->moduleTemplate->getPageRenderer()->addJsInlineCode('gonzo', 'var gonzo=' . json_encode($tree, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) . ';');
            $this->initializeMenuFromSite($site, $backendUser);
        } catch (SiteNotFoundException $e) {
            $this->view->assign('chooseSite', true);
        }

        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * @param SiteInterface $site
     */
    protected function initializeMenuFromSite(SiteInterface $site, BackendUserAuthentication $user)
    {
        $menu = new Menu();
        $menu->setLabel('Select language');
        $menu->setIdentifier('language');

        foreach ($site->getAvailableLanguages($user) as $language) {
            $menuItem = new MenuItem();
            $menuItem->setTitle($language->getTitle());
            $menuItem->setHref('#');
            $menu->addMenuItem($menuItem);
        }
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     *
     */
    protected function initializeView()
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:gonzo/Resources/Private/Templates/Overview.html'));
        $this->view->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:gonzo/Resources/Private/Partials/')]);
    }
}
