<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Observer;

use Magento\Backend\App\AbstractAction;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\PhpCookieReader;

class BackendAuthUserLoginSuccess implements ObserverInterface
{
    private const SESSION_NAME = 'PHPSESSID';

    /** @var AclBuilder */
    private $aclBuilder;

    /** @var PhpCookieReader */
    private $cookieReader;

    /** @var SessionManagerInterface */
    private $sessionManager;

    /**
     * BackendAuthUserLoginSuccess constructor.
     * @param AclBuilder $aclBuilder
     * @param PhpCookieReader $cookieReader
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        AclBuilder $aclBuilder,
        PhpCookieReader $cookieReader,
        SessionManagerInterface $sessionManager
    ) {
        $this->aclBuilder = $aclBuilder;
        $this->cookieReader = $cookieReader;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->hasFrontendSession()) {
            $user = $observer->getEvent()->getUser();
            $isAllowed = $this->isAllowed($user->getAclRole());

            if ($isAllowed) {
                $backSessionId = $this->sessionManager->getSessionId();
                $frontendSessionId = $this->cookieReader->getCookie(self::SESSION_NAME);

                $this->switchSession($frontendSessionId);

                $_SESSION[AbstractAction::SESSION_NAMESPACE]['cieAllowed'] = $this->isAllowed($user->getAclRole());

                $this->switchSession($backSessionId);
            }
        }
    }

    /**
     * @param string $aclRole
     * @return bool
     */
    private function isAllowed(string $aclRole): bool
    {
        return $this->aclBuilder->getAcl()->isAllowed($aclRole, 'Ajourquin_CmsInlineEditor::edit');
    }

    /**
     * @return bool
     */
    private function hasFrontendSession(): bool
    {
        return $this->cookieReader->getCookie(self::SESSION_NAME) !== null;
    }

    /**
     * @param string $sessionId
     */
    private function switchSession(string $sessionId): void
    {
        \session_write_close();
        \session_id($sessionId);
        \session_start();
    }
}
